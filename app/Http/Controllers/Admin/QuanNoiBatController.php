<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use App\Notifications\QuanStatusUpdatedNotification;
use App\Services\AdminAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class QuanNoiBatController extends Controller
{
    public function __construct(private readonly AdminAuditService $adminAudit) {}

    /**
     * Kiểm tra phân quyền truy cập quản lý Quán Nổi Bật
     */
    protected function authorizeAccess(): void
    {
        $user = auth()->user();
        if (! $user) {
            abort(401, 'Vui lòng đăng nhập hệ thống.');
        }

        if (! $user->isAdmin() && ! $user->hasRole('admin') && ! (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo('quan_ly_quan'))) {
            abort(403, 'Bạn không có quyền quản lý quán nổi bật.');
        }
    }

    /**
     * Màn hình chính Quản lý Quán Nổi Bật (Thiết kế Single-screen Viewport Fit)
     */
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $q = trim((string) $request->input('q', ''));
        $loaiHinh = $request->input('loai_hinh');
        $status = $request->input('status');

        $featuredQuery = Quan::where('is_noi_bat', true)
            ->with(['chuQuan:id,ho_ten,email,so_dien_thoai']);

        if ($q !== '') {
            $featuredQuery->where(function ($sub) use ($q) {
                $sub->where('ten_quan', 'like', "%{$q}%")
                    ->orWhere('dia_chi_chi_tiet', 'like', "%{$q}%")
                    ->orWhere('ten_quan_huyen', 'like', "%{$q}%")
                    ->orWhere('ten_tinh_thanh', 'like', "%{$q}%")
                    ->orWhereHas('chuQuan', function ($cSub) use ($q) {
                        $cSub->where('ho_ten', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        if (! empty($loaiHinh)) {
            $featuredQuery->where('loai_hinh_kinh_doanh', $loaiHinh);
        }

        if (! empty($status)) {
            $featuredQuery->where('trang_thai', $status);
        }

        $featuredQuan = $featuredQuery->orderByDesc('updated_at')->get();

        // Kho quán sẵn sàng để đề cử thêm vào nổi bật (trạng thái đã duyệt, chưa nổi bật)
        $allQuan = Quan::where('is_noi_bat', false)
            ->where('trang_thai', 'da_duyet')
            ->with(['chuQuan:id,ho_ten'])
            ->latest('updated_at')
            ->take(60)
            ->get();

        // Metrics & KPI cards
        $totalFeatured = Quan::where('is_noi_bat', true)->count();
        $totalActiveFeatured = Quan::where('is_noi_bat', true)->where('trang_thai', 'da_duyet')->count();
        $totalVerifiedFeatured = Quan::where('is_noi_bat', true)->where('is_xac_thuc', true)->count();
        $totalViewsFeatured = (int) Quan::where('is_noi_bat', true)->sum('luot_xem');
        $openNowFeatured = $featuredQuan->filter(fn ($item) => $item->isDangMoCua())->count();

        // Danh sách loại hình kinh doanh cho bộ lọc
        $businessTypes = Quan::select('loai_hinh_kinh_doanh')
            ->whereNotNull('loai_hinh_kinh_doanh')
            ->where('loai_hinh_kinh_doanh', '!=', '')
            ->distinct()
            ->pluck('loai_hinh_kinh_doanh');

        return view('admin.quan-noi-bat.index', compact(
            'featuredQuan',
            'allQuan',
            'totalFeatured',
            'totalActiveFeatured',
            'totalVerifiedFeatured',
            'totalViewsFeatured',
            'openNowFeatured',
            'businessTypes'
        ));
    }

    /**
     * API Tìm kiếm quán khả dụng để thêm nhanh vào danh sách nổi bật
     */
    public function searchAvailable(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $q = trim((string) $request->input('q', ''));
        $loaiHinh = $request->input('loai_hinh');

        $query = Quan::where('is_noi_bat', false)
            ->where('trang_thai', 'da_duyet')
            ->with(['chuQuan:id,ho_ten']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('ten_quan', 'like', "%{$q}%")
                    ->orWhere('dia_chi_chi_tiet', 'like', "%{$q}%")
                    ->orWhere('ten_quan_huyen', 'like', "%{$q}%")
                    ->orWhere('ten_tinh_thanh', 'like', "%{$q}%")
                    ->orWhereHas('chuQuan', function ($cSub) use ($q) {
                        $cSub->where('ho_ten', 'like', "%{$q}%");
                    });
            });
        }

        if (! empty($loaiHinh)) {
            $query->where('loai_hinh_kinh_doanh', $loaiHinh);
        }

        $quans = $query->latest('updated_at')->take(40)->get()->map(function ($quan) {
            return [
                'id' => (string) $quan->id,
                'ten_quan' => $quan->ten_quan,
                'loai_hinh_kinh_doanh' => $quan->loai_hinh_kinh_doanh ?? 'Khác',
                'dia_chi' => trim($quan->dia_chi_chi_tiet . ', ' . $quan->ten_quan_huyen . ', ' . $quan->ten_tinh_thanh),
                'anh_bia_url' => $quan->anh_bia_url,
                'is_xac_thuc' => (bool) $quan->is_xac_thuc,
                'luot_xem' => (int) $quan->luot_xem,
                'chu_quan' => $quan->chuQuan?->ho_ten ?? 'N/A',
                'slug' => $quan->slug,
                'detail_url' => $quan->slug ? route('quan.detail', $quan->slug) : '#',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $quans,
        ]);
    }

    /**
     * Thêm quán vào danh sách nổi bật
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $request->validate([
            'quan_id' => 'required|string|exists:quan,id',
        ]);

        $quan = Quan::findOrFail($request->input('quan_id'));
        $quan->is_noi_bat = true;
        $quan->save();

        if (method_exists($quan, 'searchable')) {
            try {
                $quan->searchable();
            } catch (\Throwable $e) {
                Log::warning('QuanNoiBat store Scout sync error: ' . $e->getMessage());
            }
        }

        $this->adminAudit->record($request->user(), 'venue.featured_added', $quan, $request, [
            'featured' => true,
            'ten_quan' => $quan->ten_quan,
        ]);

        if ($quan->chuQuan) {
            try {
                $quan->chuQuan->notify(new QuanStatusUpdatedNotification($quan, 'featured_granted'));
            } catch (\Throwable $e) {
                Log::warning('QuanNoiBat store notification error: ' . $e->getMessage());
            }
        }

        $message = "Đã thêm quán '{$quan->ten_quan}' vào danh sách quán nổi bật!";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_noi_bat' => true,
                'message' => $message,
                'quan' => [
                    'id' => (string) $quan->id,
                    'ten_quan' => $quan->ten_quan,
                    'loai_hinh_kinh_doanh' => $quan->loai_hinh_kinh_doanh ?? 'Khác',
                    'dia_chi' => trim($quan->dia_chi_chi_tiet . ', ' . $quan->ten_quan_huyen . ', ' . $quan->ten_tinh_thanh),
                    'anh_bia_url' => $quan->anh_bia_url,
                    'is_xac_thuc' => (bool) $quan->is_xac_thuc,
                    'trang_thai' => $quan->trang_thai,
                    'gio_mo_cua' => $quan->gio_mo_cua,
                    'gio_dong_cua' => $quan->gio_dong_cua,
                    'is_dang_mo_cua' => $quan->isDangMoCua(),
                    'luot_xem' => (int) $quan->luot_xem,
                    'slug' => $quan->slug,
                    'chu_quan' => $quan->chuQuan?->ho_ten ?? 'N/A',
                ],
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Sửa nhanh thông tin quán nổi bật
     */
    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $quan = Quan::findOrFail($id);

        $validated = $request->validate([
            'ten_quan'             => 'required|string|max:255',
            'loai_hinh_kinh_doanh' => 'required|string|max:100',
            'so_dien_thoai'        => 'nullable|string|max:20',
            'gio_mo_cua'           => 'nullable|date_format:H:i',
            'gio_dong_cua'         => 'nullable|date_format:H:i',
            'shopeefood_url'       => 'nullable|url|max:255',
            'tiktok_url'           => 'nullable|url|max:255',
            'is_xac_thuc'          => 'nullable|boolean',
            'is_noi_bat'           => 'nullable|boolean',
        ]);

        $validated['is_xac_thuc'] = $request->boolean('is_xac_thuc');
        if ($request->has('is_noi_bat')) {
            $validated['is_noi_bat'] = $request->boolean('is_noi_bat');
        }

        $quan->update($validated);

        if (method_exists($quan, 'searchable')) {
            try {
                $quan->searchable();
            } catch (\Throwable $e) {
                Log::warning('QuanNoiBat update Scout sync error: ' . $e->getMessage());
            }
        }

        $this->adminAudit->record($request->user(), 'venue.featured_updated', $quan, $request, [
            'updated_fields' => array_keys($validated),
            'ten_quan' => $quan->ten_quan,
        ]);

        $message = "Đã cập nhật thông tin quán nổi bật '{$quan->ten_quan}'!";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'quan' => [
                    'id' => (string) $quan->id,
                    'ten_quan' => $quan->ten_quan,
                    'loai_hinh_kinh_doanh' => $quan->loai_hinh_kinh_doanh,
                    'so_dien_thoai' => $quan->so_dien_thoai,
                    'gio_mo_cua' => $quan->gio_mo_cua,
                    'gio_dong_cua' => $quan->gio_dong_cua,
                    'is_xac_thuc' => (bool) $quan->is_xac_thuc,
                    'is_noi_bat' => (bool) $quan->is_noi_bat,
                    'shopeefood_url' => $quan->shopeefood_url,
                    'tiktok_url' => $quan->tiktok_url,
                    'dia_chi' => trim($quan->dia_chi_chi_tiet . ', ' . $quan->ten_quan_huyen . ', ' . $quan->ten_tinh_thanh),
                    'anh_bia_url' => $quan->anh_bia_url,
                    'trang_thai' => $quan->trang_thai,
                    'is_dang_mo_cua' => $quan->isDangMoCua(),
                    'luot_xem' => (int) $quan->luot_xem,
                    'chu_quan' => $quan->chuQuan?->ho_ten ?? 'Hệ thống',
                    'slug' => $quan->slug,
                ],
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Bật / tắt nhanh trạng thái nổi bật của quán
     */
    public function toggle(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $quan = Quan::findOrFail($id);
        $quan->is_noi_bat = ! $quan->is_noi_bat;
        $quan->save();

        if (method_exists($quan, 'searchable')) {
            try {
                $quan->searchable();
            } catch (\Throwable $e) {
                Log::warning('QuanNoiBat toggle Scout sync error: ' . $e->getMessage());
            }
        }

        $this->adminAudit->record($request->user(), 'venue.featured_toggled', $quan, $request, [
            'featured' => $quan->is_noi_bat,
            'ten_quan' => $quan->ten_quan,
        ]);

        if ($quan->chuQuan) {
            try {
                $quan->chuQuan->notify(new QuanStatusUpdatedNotification(
                    $quan,
                    $quan->is_noi_bat ? 'featured_granted' : 'featured_revoked'
                ));
            } catch (\Throwable $e) {
                Log::warning('QuanNoiBat toggle notification error: ' . $e->getMessage());
            }
        }

        $status = $quan->is_noi_bat ? 'Đã thêm vào danh sách nổi bật' : 'Đã gỡ khỏi danh sách nổi bật';
        $message = "{$status} cho quán '{$quan->ten_quan}'!";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_noi_bat' => $quan->is_noi_bat,
                'message' => $message,
                'quan' => [
                    'id' => (string) $quan->id,
                    'ten_quan' => $quan->ten_quan,
                    'loai_hinh_kinh_doanh' => $quan->loai_hinh_kinh_doanh,
                    'dia_chi' => trim($quan->dia_chi_chi_tiet . ', ' . $quan->ten_quan_huyen . ', ' . $quan->ten_tinh_thanh),
                    'anh_bia_url' => $quan->anh_bia_url,
                    'is_xac_thuc' => (bool) $quan->is_xac_thuc,
                    'luot_xem' => (int) $quan->luot_xem,
                    'chu_quan' => $quan->chuQuan?->ho_ten ?? 'N/A',
                ],
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Gỡ quán khỏi danh sách nổi bật
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $quan = Quan::findOrFail($id);
        $quan->is_noi_bat = false;
        $quan->save();

        if (method_exists($quan, 'searchable')) {
            try {
                $quan->searchable();
            } catch (\Throwable $e) {
                Log::warning('QuanNoiBat destroy Scout sync error: ' . $e->getMessage());
            }
        }

        $this->adminAudit->record($request->user(), 'venue.featured_removed', $quan, $request, [
            'featured' => false,
            'ten_quan' => $quan->ten_quan,
        ]);

        if ($quan->chuQuan) {
            try {
                $quan->chuQuan->notify(new QuanStatusUpdatedNotification($quan, 'featured_revoked'));
            } catch (\Throwable $e) {
                Log::warning('QuanNoiBat destroy notification error: ' . $e->getMessage());
            }
        }

        $message = "Đã gỡ quán '{$quan->ten_quan}' khỏi danh sách quán nổi bật!";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_noi_bat' => false,
                'message' => $message,
                'quan' => [
                    'id' => (string) $quan->id,
                    'ten_quan' => $quan->ten_quan,
                    'loai_hinh_kinh_doanh' => $quan->loai_hinh_kinh_doanh,
                    'dia_chi' => trim($quan->dia_chi_chi_tiet . ', ' . $quan->ten_quan_huyen . ', ' . $quan->ten_tinh_thanh),
                    'anh_bia_url' => $quan->anh_bia_url,
                    'is_xac_thuc' => (bool) $quan->is_xac_thuc,
                    'luot_xem' => (int) $quan->luot_xem,
                    'chu_quan' => $quan->chuQuan?->ho_ten ?? 'N/A',
                ],
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}
