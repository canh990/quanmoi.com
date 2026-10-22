<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KhuyenMai;
use App\Models\Quan;
use App\Services\AiPromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KhuyenMaiAiController extends Controller
{
    protected AiPromotionService $aiService;

    public function __construct(AiPromotionService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Kiểm tra quyền truy cập tính năng AI Khuyến Mãi
     */
    protected function authorizeAccess(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Vui lòng đăng nhập.');
        }

        if (method_exists($user, 'hasPermissionExplicitlyDenied') && $user->hasPermissionExplicitlyDenied('quan_ly_khuyen_mai_ai')) {
            abort(403, 'Bạn không có quyền truy cập chức năng AI Tạo Khuyến Mãi & Voucher.');
        }

        if (! $user->hasPermissionTo('quan_ly_khuyen_mai_ai')) {
            abort(403, 'Bạn không có quyền truy cập chức năng AI Tạo Khuyến Mãi & Voucher.');
        }
    }

    /**
     * Màn hình Studio Tạo & Quản Lý Khuyến Mãi AI
     */
    public function index()
    {
        $this->authorizeAccess();

        $danhSachQuan = Quan::with(['danhMucMenu.monAn:id,danh_muc_id,ten_mon'])
            ->select('id', 'ten_quan', 'loai_hinh_kinh_doanh', 'dia_chi_chi_tiet', 'ten_quan_huyen', 'ten_tinh_thanh')
            ->where('trang_thai', 'da_duyet')
            ->orderBy('ten_quan')
            ->get()
            ->map(function ($quan) {
                $monAnList = $quan->danhMucMenu->flatMap(function ($dm) {
                    return $dm->monAn->pluck('ten_mon');
                })->unique()->filter()->values();
                $quan->danh_sach_mon = $monAnList;
                return $quan;
            });

        $promotions = KhuyenMai::with('quan:id,ten_quan,slug')
            ->orderBy('created_at', 'desc')
            ->get();

        $promotionsCount = $promotions->count();

        return view('admin.khuyen-mai-ai.index', compact('danhSachQuan', 'promotions', 'promotionsCount'));
    }

    /**
     * Sinh nội dung khuyến mãi tiếp thị bằng AI
     */
    public function generate(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'quan_id' => 'nullable|string',
            'ten_quan' => 'nullable|string|max:255',
            'loai_hinh' => 'nullable|string|max:100',
            'dia_chi' => 'nullable|string|max:500',
            'mon_an' => 'nullable|string|max:1000',
            'loai_khuyen_mai' => 'nullable|string|max:100',
            'gia_tri_giam' => 'nullable|string|max:100',
            'dip_khuyen_mai' => 'nullable|string|max:255',
            'phong_cach' => 'nullable|string|max:50',
            'ngay_bat_dau' => 'nullable|date|after_or_equal:today',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:today' . ($request->filled('ngay_bat_dau') ? '|after_or_equal:ngay_bat_dau' : ''),
            'yeu_cau_them' => 'nullable|string|max:5000',
            'api_key' => 'nullable|string|max:255',
        ], [
            'ngay_bat_dau.after_or_equal' => 'Ngày bắt đầu áp dụng không được ở trong quá khứ (phải từ hôm nay trở đi).',
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc áp dụng không được ở trong quá khứ và phải lớn hơn hoặc bằng ngày bắt đầu.',
            'yeu_cau_them.max' => 'Yêu cầu bổ sung không được vượt quá 5.000 ký tự.',
            'mon_an.max' => 'Danh sách món ăn không được vượt quá 1.000 ký tự.',
        ]);

        $tenQuan = $validated['ten_quan'] ?? '';
        $loaiHinh = $validated['loai_hinh'] ?? 'Quán ăn';
        $diaChi = $validated['dia_chi'] ?? '';

        if (! empty($validated['quan_id'])) {
            $quan = Quan::with(['danhMucMenu.monAn:id,danh_muc_id,ten_mon'])->find($validated['quan_id']);
            if ($quan) {
                $tenQuan = $quan->ten_quan;
                $loaiHinh = $quan->loai_hinh_kinh_doanh ?? 'Quán ăn';
                $diaChi = trim("{$quan->dia_chi_chi_tiet}, {$quan->ten_quan_huyen}, {$quan->ten_tinh_thanh}", ', ');

                $menuDishes = $quan->danhMucMenu->flatMap(function ($dm) {
                    return $dm->monAn->pluck('ten_mon');
                })->unique()->filter()->values();

                $monAnInput = trim($validated['mon_an'] ?? '');
                $isAllMenu = empty($monAnInput) || in_array(mb_strtolower($monAnInput, 'UTF-8'), [
                    'toàn bộ menu', 'toan bo menu', 'tất cả món', 'tat ca mon', 'toàn bộ', 'all', 'menu', 'tất cả'
                ]);

                // Kiểm tra tính hợp lệ: Món áp dụng phải thuộc thực đơn của quán
                if (! $isAllMenu && $menuDishes->isNotEmpty()) {
                    $matched = $menuDishes->first(function ($dish) use ($monAnInput) {
                        return mb_stripos($dish, $monAnInput) !== false || mb_stripos($monAnInput, $dish) !== false;
                    });

                    if (! $matched) {
                        $sampleDishes = $menuDishes->take(5)->implode(', ');
                        return response()->json([
                            'success' => false,
                            'message' => "Món '{$monAnInput}' không thuộc thực đơn của quán '{$tenQuan}'. Thực đơn quán gồm: {$sampleDishes}" . (count($menuDishes) > 5 ? '...' : '') . ". Vui lòng chọn món có trong menu hoặc nhập 'Toàn bộ menu'.",
                        ], 422);
                    }
                }
            }
        }

        if (empty($tenQuan)) {
            $tenQuan = 'Hệ thống Quán Mới';
        }

        $params = [
            'ten_quan' => $tenQuan,
            'loai_hinh' => $loaiHinh,
            'dia_chi' => $diaChi,
            'mon_an' => ! empty($validated['mon_an']) ? $validated['mon_an'] : 'Toàn bộ menu',
            'loai_khuyen_mai' => $validated['loai_khuyen_mai'] ?? 'giam_phan_tram',
            'gia_tri_giam' => $validated['gia_tri_giam'] ?? '20%',
            'dip_khuyen_mai' => $validated['dip_khuyen_mai'] ?? 'Ưu đãi ẩm thực độc quyền',
            'phong_cach' => $validated['phong_cach'] ?? 'hao_hung',
            'ngay_bat_dau' => $validated['ngay_bat_dau'] ?? date('d/m/Y'),
            'ngay_ket_thuc' => $validated['ngay_ket_thuc'] ?? date('d/m/Y', strtotime('+7 days')),
            'yeu_cau_them' => $validated['yeu_cau_them'] ?? '',
        ];

        $overrideApiKey = ! empty($validated['api_key']) ? trim($validated['api_key']) : null;
        $result = $this->aiService->generatePromotion($params, $overrideApiKey);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Lưu ưu đãi & voucher vào cơ sở dữ liệu
     */
    public function store(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'quan_id' => 'required|uuid|exists:quan,id',
            'tieu_de' => 'required|string|max:255',
            'ma_voucher' => 'nullable|string|max:50',
            'loai_khuyen_mai' => 'required|string|max:100',
            'gia_tri_giam' => 'nullable|string|max:100',
            'slogan' => 'nullable|string|max:255',
            'noi_dung' => 'required|string',
            'the_le' => 'nullable|string',
            'ngay_bat_dau' => 'nullable|date|after_or_equal:today',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:today' . ($request->filled('ngay_bat_dau') ? '|after_or_equal:ngay_bat_dau' : ''),
            'mon_ap_dung' => 'nullable|string|max:255',
        ], [
            'ngay_bat_dau.after_or_equal' => 'Ngày bắt đầu áp dụng không được ở trong quá khứ (phải từ hôm nay trở đi).',
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc áp dụng không được ở trong quá khứ và phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        if (! empty($validated['quan_id'])) {
            $quan = Quan::with(['danhMucMenu.monAn:id,danh_muc_id,ten_mon'])->find($validated['quan_id']);
            if ($quan) {
                $menuDishes = $quan->danhMucMenu->flatMap(fn($dm) => $dm->monAn->pluck('ten_mon'))->unique()->filter()->values();
                $monAnInput = trim($validated['mon_ap_dung'] ?? '');
                $isAllMenu = empty($monAnInput) || in_array(mb_strtolower($monAnInput, 'UTF-8'), [
                    'toàn bộ menu', 'toan bo menu', 'tất cả món', 'tat ca mon', 'toàn bộ', 'all', 'menu', 'tất cả'
                ]);

                if (! $isAllMenu && $menuDishes->isNotEmpty()) {
                    $matched = $menuDishes->first(fn($dish) => mb_stripos($dish, $monAnInput) !== false || mb_stripos($monAnInput, $dish) !== false);
                    if (! $matched) {
                        return response()->json([
                            'success' => false,
                            'message' => "Không thể lưu! Món '{$monAnInput}' không thuộc thực đơn của quán '{$quan->ten_quan}'.",
                        ], 422);
                    }
                }
            }
        }

        $km = KhuyenMai::create([
            'quan_id' => $validated['quan_id'],
            'tieu_de' => $validated['tieu_de'],
            'ma_voucher' => $validated['ma_voucher'] ? strtoupper(trim($validated['ma_voucher'])) : null,
            'loai_khuyen_mai' => $validated['loai_khuyen_mai'],
            'gia_tri_giam' => $validated['gia_tri_giam'],
            'slogan' => $validated['slogan'],
            'noi_dung' => $validated['noi_dung'],
            'the_le' => $validated['the_le'],
            'ngay_bat_dau' => $validated['ngay_bat_dau'],
            'ngay_ket_thuc' => $validated['ngay_ket_thuc'],
            'mon_ap_dung' => $validated['mon_ap_dung'],
            'trang_thai' => true,
            'ai_provider' => 'kira_ai',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu chương trình khuyến mãi cho quán thành công!',
            'data' => $km,
        ]);
    }

    /**
     * Xóa khuyến mãi / voucher
     */
    public function destroy(string $id)
    {
        $this->authorizeAccess();

        $km = KhuyenMai::findOrFail($id);
        $km->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa voucher thành công.',
        ]);
    }

    /**
     * Bật / Tắt trạng thái khuyến mãi
     */
    public function toggle(string $id)
    {
        $this->authorizeAccess();

        $km = KhuyenMai::findOrFail($id);
        $km->trang_thai = ! $km->trang_thai;
        $km->save();

        return response()->json([
            'success' => true,
            'trang_thai' => $km->trang_thai,
            'message' => $km->trang_thai ? 'Đã kích hoạt voucher' : 'Đã tạm dừng voucher',
        ]);
    }
}
