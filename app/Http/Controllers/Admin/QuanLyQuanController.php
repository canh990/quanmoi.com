<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapNhatQuanRequest;
use App\Http\Requests\TaoQuanRequest;
use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class QuanLyQuanController extends Controller
{
    public function __construct(private readonly AdminAuditService $adminAudit) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Quan::class);
        $query = Quan::with('chuQuan')->withTrashed();

        // 1. Search keyword (ten_quan, dia_chi_chi_tiet, so_dien_thoai, email, chu_quan name/email)
        $rawQ = $request->input('q');
        if (! empty($rawQ) && ! is_array($rawQ)) {
            $q = trim((string) $rawQ);
            $query->where(function ($sub) use ($q) {
                $sub->where('ten_quan', 'like', "%{$q}%")
                    ->orWhere('dia_chi_chi_tiet', 'like', "%{$q}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhereHas('chuQuan', function ($ownerSub) use ($q) {
                        $ownerSub->where('ho_ten', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        // 2. Filter by status (chua_duyet, da_duyet, bi_khoa, soft_deleted)
        $rawStatus = $request->input('status');
        if (! is_array($rawStatus)) {
            if ($rawStatus === 'soft_deleted') {
                $query->onlyTrashed();
            } elseif (! empty($rawStatus)) {
                $query->whereNull('ngay_xoa')->where('trang_thai', (string) $rawStatus);
            }
        }

        // 3. Filter by business type (loai_hinh_kinh_doanh)
        $rawLoaiHinh = $request->input('loai_hinh');
        if (! empty($rawLoaiHinh) && ! is_array($rawLoaiHinh)) {
            $query->where('loai_hinh_kinh_doanh', (string) $rawLoaiHinh);
        }

        // 4. Filter by is_noi_bat
        $rawNoiBat = $request->input('is_noi_bat');
        if ($rawNoiBat !== null && $rawNoiBat !== '' && ! is_array($rawNoiBat)) {
            $query->where('is_noi_bat', (bool) $rawNoiBat);
        }

        // 5. Filter by is_xac_thuc (Tick Xanh)
        $rawXacThuc = $request->input('is_xac_thuc');
        if (! is_array($rawXacThuc) && $rawXacThuc !== null && $rawXacThuc !== '') {
            $query->where('is_xac_thuc', (bool) $rawXacThuc);
        }

        $quanList = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Counter stats for enterprise filter tabs
        $counts = $this->getVenueCounts();

        $businessTypes = $this->getBusinessTypes();

        return view('admin.quan.index', compact('quanList', 'counts', 'businessTypes'));
    }

    public function create()
    {
        $this->authorize('create', Quan::class);

        $users = User::where('trang_thai', 'hoat_dong')->orderBy('ho_ten')->get();
        $businessTypes = $this->getBusinessTypes();

        return view('admin.quan.create', compact('users', 'businessTypes'));
    }

    public function store(TaoQuanRequest $request)
    {
        $this->authorize('create', Quan::class);
        $validated = $request->validated();

        // 1. Assign owner (default to current admin if not specified)
        if (empty($validated['chu_quan_id'])) {
            $validated['chu_quan_id'] = $request->user()->id;
        }

        // 2. Generate unique slug
        $baseSlug = Str::slug($validated['ten_quan']);
        $slug = $baseSlug ?: 'quan-'.Str::lower(Str::random(6));
        $counter = 1;
        while (Quan::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }
        $validated['slug'] = $slug;

        // 3. Handle Cover Image upload
        if ($request->hasFile('anh_bia')) {
            $file = $request->file('anh_bia');
            $filename = Str::uuid().'.webp';
            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file->getRealPath());
            $encoded = $image->encodeUsingFileExtension('webp', 85);

            $objectKey = 'quan/anh-bia/'.$filename;
            [$validated['anh_bia'], $validated['anh_bia_key']] = $this->storeVenueImage($objectKey, (string) $encoded);
        }

        // 4. Set boolean flags
        $validated['is_noi_bat'] = $request->boolean('is_noi_bat');
        $validated['is_xac_thuc'] = $request->boolean('is_xac_thuc');

        // 5. Create in DB transaction
        $quan = DB::transaction(function () use ($validated) {
            $q = Quan::create($validated);

            $owner = User::find($q->chu_quan_id);
            $ownerRole = VaiTro::where('ten', 'chu_quan')->first();
            if ($owner && $ownerRole && ! $owner->hasRole('admin') && ! $owner->hasRole('chu_quan')) {
                $owner->update(['vai_tro_id' => $ownerRole->id]);
            }

            return $q;
        });

        $this->adminAudit->record($request->user(), 'venue.created', $quan, $request, [
            'name' => $quan->ten_quan,
            'status' => $quan->trang_thai,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm địa điểm quán mới thành công!',
                'redirect_to' => route('admin.quan.index'),
                'data' => $quan,
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Thêm địa điểm quán mới thành công!');
    }

    public function show(Request $request, string $id)
    {
        $quan = Quan::withTrashed()
            ->with(['chuQuan', 'hinhAnh', 'danhMucMenu.monTrongMenu', 'danhGia.nguoiDung'])
            ->findOrFail($id);

        $this->authorize('view', $quan);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $quan->id,
                    'ten_quan' => $quan->ten_quan,
                    'loai_hinh_kinh_doanh' => $quan->loai_hinh_kinh_doanh ?: 'Nhà hàng',
                    'slug' => $quan->slug,
                    'detail_url' => route('quan.detail', ['slug' => $quan->slug]),
                    'so_dien_thoai' => $quan->so_dien_thoai,
                    'email' => $quan->email,
                    'dia_chi_chi_tiet' => $quan->dia_chi_chi_tiet,
                    'dia_chi_day_du' => trim($quan->dia_chi_chi_tiet.($quan->ten_phuong_xa ? ', '.$quan->ten_phuong_xa : '').($quan->ten_quan_huyen ? ', '.$quan->ten_quan_huyen : '').($quan->ten_tinh_thanh ? ', '.$quan->ten_tinh_thanh : ''), ', '),
                    'gio_mo_cua' => $quan->gio_mo_cua ?: '08:00',
                    'gio_dong_cua' => $quan->gio_dong_cua ?: '22:00',
                    'gia_nho_nhat' => $quan->gia_nho_nhat ? number_format($quan->gia_nho_nhat, 0, ',', '.').'đ' : 'Chưa cập nhật',
                    'gia_lon_nhat' => $quan->gia_lon_nhat ? number_format($quan->gia_lon_nhat, 0, ',', '.').'đ' : 'Chưa cập nhật',
                    'trang_thai' => $quan->trang_thai,
                    'is_noi_bat' => (bool) $quan->is_noi_bat,
                    'is_xac_thuc' => (bool) $quan->is_xac_thuc,
                    'is_deleted' => $quan->trashed(),
                    'anh_bia_url' => $quan->anh_bia_url,
                    'mo_ta' => $quan->mo_ta ?: 'Chưa có mô tả chi tiết.',
                    'tiktok_url' => $quan->tiktok_url,
                    'shopeefood_url' => $quan->shopeefood_url,
                    'chu_quan' => $quan->chuQuan ? [
                        'ho_ten' => $quan->chuQuan->ho_ten,
                        'email' => $quan->chuQuan->email,
                        'so_dien_thoai' => $quan->chuQuan->so_dien_thoai,
                    ] : null,
                    'hinh_anh_count' => $quan->hinhAnh->count(),
                    'menu_count' => $quan->danhMucMenu->sum(fn ($dm) => $dm->monTrongMenu->count()),
                    'danh_gia_count' => $quan->danhGia->count(),
                    'diem_trung_binh' => $quan->danhGia->avg('so_sao') ? round($quan->danhGia->avg('so_sao'), 1) : null,
                ],
            ]);
        }

        return redirect()->route('admin.quan.edit', $quan->id);
    }

    public function edit(string $id)
    {
        $quan = Quan::withTrashed()->with(['chuQuan', 'hinhAnh', 'danhMucMenu.monTrongMenu', 'danhGia'])->findOrFail($id);
        $this->authorize('update', $quan);

        $users = User::where('trang_thai', 'hoat_dong')->orderBy('ho_ten')->get();
        $businessTypes = $this->getBusinessTypes();

        return view('admin.quan.edit', compact('quan', 'users', 'businessTypes'));
    }

    public function update(CapNhatQuanRequest $request, string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        $this->authorize('update', $quan);
        $validated = $request->validated();

        // 1. Handle Cover Image upload
        if ($request->hasFile('anh_bia')) {
            $this->deleteVenueImage($quan->anh_bia_key);

            $file = $request->file('anh_bia');
            $filename = Str::uuid().'.webp';
            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file->getRealPath());
            $encoded = $image->encodeUsingFileExtension('webp', 85);

            $objectKey = 'quan/anh-bia/'.$filename;
            [$validated['anh_bia'], $validated['anh_bia_key']] = $this->storeVenueImage($objectKey, (string) $encoded);
        }

        // 2. Set boolean flags
        if ($request->has('is_noi_bat')) {
            $validated['is_noi_bat'] = $request->boolean('is_noi_bat');
        }
        if ($request->has('is_xac_thuc')) {
            $validated['is_xac_thuc'] = $request->boolean('is_xac_thuc');
        }

        DB::transaction(function () use ($quan, $validated) {
            $quan->update($validated);

            if ($quan->trang_thai === 'da_duyet' && $quan->chu_quan_id) {
                $owner = $quan->chuQuan;
                $ownerRole = VaiTro::where('ten', 'chu_quan')->first();

                if ($owner && $ownerRole && ! $owner->hasRole('admin') && ! $owner->hasRole('chu_quan')) {
                    $owner->update(['vai_tro_id' => $ownerRole->id]);
                }
            }
        });

        $this->adminAudit->record($request->user(), 'venue.updated', $quan, $request, [
            'status' => $quan->trang_thai,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin quán thành công!',
                'redirect_to' => route('admin.quan.index'),
                'data' => $quan,
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Cập nhật quán thành công!');
    }

    public function destroy(Request $request, string $id)
    {
        $quan = Quan::findOrFail($id);
        $this->authorize('delete', $quan);
        $quan->update(['trang_thai' => 'bi_khoa']);
        $quan->delete(); // Soft delete via ngay_xoa
        $this->adminAudit->record($request->user(), 'venue.deleted', $quan, $request);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã ẩn (xóa tạm) địa điểm quán thành công!',
                'counts' => $this->getVenueCounts(),
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Đã xóa tạm quán!');
    }

    public function restore(Request $request, string $id)
    {
        $quan = Quan::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $quan);
        $quan->restore();
        $quan->update(['trang_thai' => 'da_duyet']);
        $this->adminAudit->record($request->user(), 'venue.restored', $quan, $request);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã khôi phục địa điểm quán thành công!',
                'counts' => $this->getVenueCounts(),
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Đã khôi phục quán!');
    }

    public function forceDestroy(Request $request, string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $quan);

        // Delete cover image on storage if stored
        $this->deleteVenueImage($quan->anh_bia_key);

        $this->adminAudit->record($request->user(), 'venue.force_deleted', $quan, $request);
        $quan->forceDelete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa vĩnh viễn địa điểm quán thành công!',
                'counts' => $this->getVenueCounts(),
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Đã xóa vĩnh viễn quán!');
    }

    public function toggleNoiBat(Request $request, string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        $this->authorize('update', $quan);
        $quan->is_noi_bat = ! $quan->is_noi_bat;
        $quan->save();
        $this->adminAudit->record($request->user(), 'venue.featured_toggled', $quan, $request, [
            'featured' => $quan->is_noi_bat,
        ]);

        $statusStr = $quan->is_noi_bat ? 'Đã thêm quán vào danh sách nổi bật' : 'Đã gỡ quán khỏi danh sách nổi bật';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_noi_bat' => $quan->is_noi_bat,
                'message' => $statusStr,
            ]);
        }

        return back()->with('success', $statusStr);
    }

    public function toggleXacThuc(Request $request, string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        $this->authorize('update', $quan);
        $quan->is_xac_thuc = ! $quan->is_xac_thuc;
        $quan->save();
        $this->adminAudit->record($request->user(), 'venue.verification_toggled', $quan, $request, [
            'verified' => $quan->is_xac_thuc,
        ]);

        $statusStr = $quan->is_xac_thuc ? 'Đã cấp Tick Xanh cho quán' : 'Đã gỡ Tick Xanh của quán';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_xac_thuc' => $quan->is_xac_thuc,
                'message' => $statusStr,
            ]);
        }

        return back()->with('success', $statusStr);
    }

    public function getVenueCounts(): array
    {
        return [
            'all' => Quan::withTrashed()->count(),
            'da_duyet' => Quan::whereNull('ngay_xoa')->where('trang_thai', 'da_duyet')->count(),
            'chua_duyet' => Quan::whereNull('ngay_xoa')->where('trang_thai', 'chua_duyet')->count(),
            'bi_khoa' => Quan::whereNull('ngay_xoa')->where('trang_thai', 'bi_khoa')->count(),
            'soft_deleted' => Quan::onlyTrashed()->count(),
        ];
    }

    private function getBusinessTypes(): array
    {
        return [
            'Nhà hàng',
            'Cà phê & Trà',
            'Billiards & Giải trí',
            'Lẩu & Nướng',
            'Ăn vặt',
            'Quán nhậu & Bar',
            'Trà sữa & Nước ép',
            'Tiệm bánh',
            'Quán ăn bình dân',
        ];
    }

    private function storeVenueImage(string $objectKey, string $contents): array
    {
        $uploadDisk = config('filesystems.upload_disk', 'r2');
        $r2Configured = ! empty(config('filesystems.disks.r2.key'))
            && ! empty(config('filesystems.disks.r2.secret'))
            && ! empty(config('filesystems.disks.r2.bucket'))
            && ! empty(config('filesystems.disks.r2.endpoint'));

        $targetDisk = ($uploadDisk === 'r2' && ! $r2Configured) ? 'uploads' : $uploadDisk;

        try {
            $saved = Storage::disk($uploadDisk)->put($objectKey, $contents);
            if ($saved) {
                $url = Storage::disk($uploadDisk)->url($objectKey);
                $storedKey = ($uploadDisk === 'uploads' || $uploadDisk === 'public' || $uploadDisk === 'local') ? 'local:'.$objectKey : $objectKey;

                return [$url, $storedKey];
            }
            throw new \RuntimeException("Disk {$uploadDisk} returned false on put.");
        } catch (\Throwable $e) {
            Log::warning('Admin venue image upload failed on primary disk, falling back to local uploads.', [
                'disk' => $uploadDisk,
                'key' => $objectKey,
                'error' => $e->getMessage(),
            ]);

            if ($uploadDisk !== 'uploads') {
                try {
                    Storage::disk('uploads')->put($objectKey, $contents);
                    $url = Storage::disk('uploads')->url($objectKey);

                    return [$url, 'local:'.$objectKey];
                } catch (\Throwable $fallbackEx) {
                    Log::error('Admin venue image local fallback also failed.', [
                        'error' => $fallbackEx->getMessage(),
                    ]);
                }
            }

            throw new \RuntimeException('Lỗi lưu ảnh lên hệ thống lưu trữ. Vui lòng thử lại sau.');
        }
    }

    private function deleteVenueImage(?string $key): void
    {
        if (! $key) {
            return;
        }

        if (Str::startsWith($key, 'local:')) {
            $path = Str::after($key, 'local:');
            $disk = Storage::disk('uploads')->exists($path) ? 'uploads' : 'public';
            Storage::disk($disk)->delete($path);

            return;
        }

        try {
            $r2Configured = ! empty(config('filesystems.disks.r2.key'))
                && ! empty(config('filesystems.disks.r2.secret'))
                && ! empty(config('filesystems.disks.r2.bucket'))
                && ! empty(config('filesystems.disks.r2.endpoint'));
            if ($r2Configured) {
                Storage::disk('r2')->delete($key);
            }
        } catch (\Throwable $e) {
            Log::error('Admin venue image deletion on R2 failed.', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

