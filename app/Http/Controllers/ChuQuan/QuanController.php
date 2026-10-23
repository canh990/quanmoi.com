<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangKyQuanRequest;
use App\Models\HinhAnhQuan;
use App\Models\Quan;
use App\Models\VideoShort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class QuanController extends Controller
{
    /**
     * Form dang quan
     */
    public function create()
    {
        return view('chu-quan.dang-quan');
    }

    /**
     * Xu ly luu quan moi vao database
     */
    public function store(DangKyQuanRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();

        if (! $user) {
            abort(403, 'Bạn chưa đăng nhập hoặc phiên làm việc đã hết hạn.');
        }

        $anhBiaPath = null;
        $anhBiaKey = null;
        if ($request->hasFile('anh_bia')) {
            $file = $request->file('anh_bia');
            $filename = Str::uuid().'.webp';

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file->getRealPath());
            $encoded = $image->encodeUsingFileExtension('webp', 80);

            $objectKey = 'quan/anh-bia/'.$filename;
            [$anhBiaPath, $anhBiaKey] = $this->storeVenueImage($objectKey, (string) $encoded);
        }

        $galleryKeys = [];
        $galleryPaths = [];
        if ($request->hasFile('danh_sach_anh')) {
            $manager = new ImageManager(new Driver);
            foreach ($request->file('danh_sach_anh') as $file) {
                $filename = Str::uuid().'.webp';

                $image = $manager->decode($file->getRealPath());
                $encoded = $image->encodeUsingFileExtension('webp', 80);

                $objectKey = 'quan/gallery/'.$filename;
                [$path, $key] = $this->storeVenueImage($objectKey, (string) $encoded);
                $galleryPaths[] = $path;
                $galleryKeys[] = $key;
            }
        }

        $baseSlug = Str::slug($validated['ten_quan']);
        $slug = $baseSlug;
        $counter = 1;

        while (Quan::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        $chuQuanId = $user->id;

        try {
            $quan = DB::transaction(function () use ($validated, $chuQuanId, $slug, $anhBiaPath, $anhBiaKey, $galleryKeys, $galleryPaths) {
                $q = Quan::create([
                    'chu_quan_id' => $chuQuanId,
                    'ten_quan' => $validated['ten_quan'],
                    'loai_hinh_kinh_doanh' => $validated['loai_hinh_kinh_doanh'] ?? 'Quan an',
                    'slug' => $slug,
                    'mo_ta' => $validated['mo_ta'] ?? null,
                    'so_dien_thoai' => $validated['so_dien_thoai'],
                    'email' => $validated['email'] ?? null,
                    'dia_chi_chi_tiet' => $validated['dia_chi_chi_tiet'],
                    'tinh_thanh_id' => $validated['tinh_thanh_id'] ?? null,
                    'ten_tinh_thanh' => $validated['ten_tinh_thanh'],
                    'quan_huyen_id' => $validated['quan_huyen_id'] ?? null,
                    'ten_quan_huyen' => $validated['ten_quan_huyen'],
                    'phuong_xa_id' => $validated['phuong_xa_id'] ?? null,
                    'ten_phuong_xa' => $validated['ten_phuong_xa'] ?? null,
                    'kinh_do' => $validated['kinh_do'] ?? 106.7009,
                    'vi_do' => $validated['vi_do'] ?? 10.7769,
                    'gio_mo_cua' => $validated['gio_mo_cua'],
                    'gio_dong_cua' => $validated['gio_dong_cua'],
                    'gia_nho_nhat' => $validated['gia_nho_nhat'] ?? 0,
                    'gia_lon_nhat' => $validated['gia_lon_nhat'] ?? 0,
                    'anh_bia' => $anhBiaPath,
                    'anh_bia_key' => $anhBiaKey,
                    'tiktok_url' => $validated['tiktok_url'] ?? null,
                    'shopeefood_url' => $validated['shopeefood_url'] ?? null,
                    'trang_thai' => 'chua_duyet',
                ]);

                // Save Gallery Images
                foreach ($galleryKeys as $index => $key) {
                    HinhAnhQuan::create([
                        'quan_id' => $q->id,
                        'duong_dan' => $galleryPaths[$index],
                        'object_key' => $key,
                        'tieu_de' => 'Hình ảnh quán',
                    ]);
                }

                return $q;
            });
        } catch (\Exception $e) {
            // Roll back uploaded images if the database transaction fails.
            if ($anhBiaKey) {
                $this->deleteVenueImage($anhBiaKey);
            }
            foreach ($galleryKeys as $key) {
                $this->deleteVenueImage($key);
            }
            throw $e;
        }

        try {
            $user->notify(new \App\Notifications\QuanStatusUpdatedNotification($quan, 'submitted'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Venue submit notification error: ' . $e->getMessage());
        }

        try {
            \App\Models\AdminAuditLog::create([
                'actor_id' => $user->id,
                'action' => 'venue.submitted',
                'target_type' => Quan::class,
                'target_id' => (string) $quan->id,
                'metadata' => ['ten_quan' => $quan->ten_quan, 'chu_quan' => $user->ho_ten],
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Venue submit audit log error: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            $roleChuQuan = DB::table('vai_tro')->where('ten', 'chu_quan')->value('id');
            if ($roleChuQuan && ! $user->hasRole('admin')) {
                $user->update(['vai_tro_id' => $roleChuQuan]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Hồ sơ quán đã được gửi và đang chờ duyệt.',
                'redirect_to' => route('chu-quan.quan.show', ['slug' => $quan->slug]),
                'data' => $quan,
            ]);
        }

        return redirect()
            ->route('home')
            ->with('success', 'Hồ sơ quán đã được gửi và đang chờ duyệt.');
    }

    /**
     * Danh sach quan cua chu quan
     */
    public function ownerIndex(Request $request)
    {
        $quanList = $request->user()
            ->quan()
            ->latest()
            ->get();

        return view('chu-quan.index', compact('quanList'));
    }

    /**
     * Xem chi tiet quan danh cho chu quan
     */
    public function show($slug)
    {
        $user = Auth::user();
        $query = Quan::where('slug', $slug);
        if ($user && ! $user->isAdmin()) {
            $query->where('chu_quan_id', $user->id);
        }

        $quan = $query->with([
            'hinhAnh',
            'danhMucMenu.monAn',
            'danhGia.nguoiDung',
            'khuyenMais',
            'videos' => fn ($q) => $q->orderBy('created_at', 'desc'),
        ])->firstOrFail();

        $this->authorize('update', $quan);

        $luotLuu = $quan->savedByUsers()->count();
        $luotVideo = $quan->videos->count();
        $videos = $quan->videos;

        return view('chu-quan.chi-tiet-quan', compact('quan', 'luotLuu', 'luotVideo', 'videos'));
    }

    private function storeVenueImage(string $objectKey, string $contents): array
    {
        $uploadDisk = config('filesystems.upload_disk', 'r2');
        $r2Configured = !empty(config('filesystems.disks.r2.key'))
            && !empty(config('filesystems.disks.r2.secret'))
            && !empty(config('filesystems.disks.r2.bucket'))
            && !empty(config('filesystems.disks.r2.endpoint'));

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
            \Illuminate\Support\Facades\Log::warning('Venue image upload failed on primary disk, falling back to local uploads.', [
                'disk' => $uploadDisk,
                'key' => $objectKey,
                'error' => $e->getMessage(),
                'previous' => $e->getPrevious()?->getMessage(),
            ]);

            // Tự động fallback sang disk 'uploads' (local) nếu cloud storage (R2) gặp sự cố mạng hoặc lỗi
            if ($uploadDisk !== 'uploads') {
                try {
                    Storage::disk('uploads')->put($objectKey, $contents);
                    $url = Storage::disk('uploads')->url($objectKey);
                    return [$url, 'local:'.$objectKey];
                } catch (\Throwable $fallbackEx) {
                    \Illuminate\Support\Facades\Log::error('Venue image local fallback also failed.', [
                        'error' => $fallbackEx->getMessage(),
                    ]);
                }
            }

            throw new \RuntimeException('Lỗi lưu ảnh lên Cloud Storage. Vui lòng thử lại sau.');
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
            $r2Configured = !empty(config('filesystems.disks.r2.key'))
                && !empty(config('filesystems.disks.r2.secret'))
                && !empty(config('filesystems.disks.r2.bucket'))
                && !empty(config('filesystems.disks.r2.endpoint'));
            if ($r2Configured) {
                Storage::disk('r2')->delete($key);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Venue image deletion on R2 failed.', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /** Cap nhat thong tin quan (AJAX). */
    public function update(Request $request, $slug)
    {
        $quan = Quan::where('slug', $slug)
            ->where('chu_quan_id', Auth::id())
            ->firstOrFail();
        $this->authorize('update', $quan);

        $validated = $request->validate([
            'so_dien_thoai' => 'required|string|max:20',
            'dia_chi_chi_tiet' => 'nullable|string|max:255',
            'vi_do' => 'nullable|numeric|between:-90,90',
            'kinh_do' => 'nullable|numeric|between:-180,180',
            'mo_ta' => 'nullable|string|max:2000',
            'gio_mo_cua' => 'required|string',
            'gio_dong_cua' => 'required|string',
            'gia_nho_nhat' => 'nullable|numeric|min:0',
            'gia_lon_nhat' => 'nullable|numeric|min:0',
            'tiktok_url' => 'nullable|url|max:255',
            'shopeefood_url' => 'nullable|url:http,https|max:500',
            'anh_bia' => 'nullable|image|max:5120',
        ], [
            'so_dien_thoai.required' => 'Số điện thoại không được để trống.',
            'so_dien_thoai.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'vi_do.numeric' => 'Vĩ độ (latitude) phải là định dạng số.',
            'vi_do.between' => 'Vĩ độ (latitude) phải nằm trong khoảng từ -90 đến 90.',
            'kinh_do.numeric' => 'Kinh độ (longitude) phải là định dạng số.',
            'kinh_do.between' => 'Kinh độ (longitude) phải nằm trong khoảng từ -180 đến 180.',
            'gio_mo_cua.required' => 'Vui lòng chọn giờ mở cửa.',
            'gio_dong_cua.required' => 'Vui lòng chọn giờ đóng cửa.',
            'gia_nho_nhat.numeric' => 'Giá nhỏ nhất phải là định dạng số.',
            'gia_lon_nhat.numeric' => 'Giá lớn nhất phải là định dạng số.',
            'tiktok_url.url' => 'Đường dẫn TikTok không hợp lệ.',
            'shopeefood_url.url' => 'Đường dẫn ShopeeFood không hợp lệ.',
            'anh_bia.image' => 'Ảnh bìa phải là định dạng hình ảnh.',
            'anh_bia.max' => 'Dung lượng ảnh bìa không được vượt quá 5MB.',
        ]);

        // Chỉ ghi đè các trường tùy chọn khi được gửi kèm giá trị hợp lệ
        foreach (['dia_chi_chi_tiet', 'vi_do', 'kinh_do'] as $optionalField) {
            if (array_key_exists($optionalField, $validated) && ($validated[$optionalField] === null || $validated[$optionalField] === '')) {
                unset($validated[$optionalField]);
            }
        }

        // Upload ảnh bìa mới nếu có
        if ($request->hasFile('anh_bia')) {
            // Xóa ảnh cũ
            if ($quan->anh_bia_key && (Str::startsWith($quan->anh_bia_key, 'quan/anh-bia/') || Str::startsWith($quan->anh_bia_key, 'local:quan/anh-bia/'))) {
                $this->deleteVenueImage($quan->anh_bia_key);
            }

            $file = $request->file('anh_bia');
            $filename = Str::uuid().'.webp';
            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file->getRealPath());
            $encoded = $image->encodeUsingFileExtension('webp', 85);

            $objectKey = 'quan/anh-bia/'.$filename;
            [$validated['anh_bia'], $validated['anh_bia_key']] = $this->storeVenueImage($objectKey, (string) $encoded);
        }

        $quan->update($validated);

        try {
            Auth::user()->notify(new \App\Notifications\QuanStatusUpdatedNotification($quan, 'updated'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Venue update notification error: ' . $e->getMessage());
        }

        try {
            \App\Models\AdminAuditLog::create([
                'actor_id' => Auth::id(),
                'action' => 'venue.updated',
                'target_type' => Quan::class,
                'target_id' => (string) $quan->id,
                'metadata' => [
                    'name' => $quan->ten_quan,
                    'owner' => Auth::user()?->ho_ten ?? 'Chủ quán',
                ],
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin quán thành công!',
            ]);
        }

        return back()->with('success', 'Cập nhật thông tin quán thành công!');
    }
}
