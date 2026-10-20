<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Models\KhuyenMai;
use App\Models\Quan;
use App\Services\AiPromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KhuyenMaiController extends Controller
{
    protected AiPromotionService $aiService;

    public function __construct(AiPromotionService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Lấy quán thuộc quyền sở hữu của chủ quán
     */
    protected function getQuan(string $slug): Quan
    {
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return Quan::where('slug', $slug)->firstOrFail();
        }

        return Quan::where('slug', $slug)
            ->where('chu_quan_id', Auth::id())
            ->firstOrFail();
    }

    /**
     * Sinh nội dung khuyến mãi bằng AI (Kira AI / Smart Fallback)
     */
    public function generateAi(Request $request, string $slug)
    {
        $quan = $this->getQuan($slug);

        $validated = $request->validate([
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

        $fullAddress = trim("{$quan->dia_chi_chi_tiet}, {$quan->ten_quan_huyen}, {$quan->ten_tinh_thanh}", ', ');

        $params = [
            'ten_quan' => $quan->ten_quan,
            'loai_hinh' => $quan->loai_hinh_kinh_doanh ?? 'Quán ăn',
            'dia_chi' => $fullAddress,
            'mon_an' => ! empty($validated['mon_an']) ? $validated['mon_an'] : 'Toàn bộ menu',
            'loai_khuyen_mai' => $validated['loai_khuyen_mai'] ?? 'giam_phan_tram',
            'gia_tri_giam' => $validated['gia_tri_giam'] ?? '20%',
            'dip_khuyen_mai' => $validated['dip_khuyen_mai'] ?? 'Ưu đãi đặc biệt',
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
     * Lưu chương trình khuyến mãi vào cơ sở dữ liệu
     */
    public function store(Request $request, string $slug)
    {
        $quan = $this->getQuan($slug);

        $validated = $request->validate([
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
            'ai_provider' => 'nullable|string|max:50',
        ], [
            'ngay_bat_dau.after_or_equal' => 'Ngày bắt đầu áp dụng không được ở trong quá khứ (phải từ hôm nay trở đi).',
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc áp dụng không được ở trong quá khứ và phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        $khuyenMai = KhuyenMai::create([
            'quan_id' => $quan->id,
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
            'ai_provider' => $validated['ai_provider'] ?? 'kira_ai',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã lưu và kích hoạt chương trình khuyến mãi thành công!',
                'data' => $khuyenMai,
            ]);
        }

        return back()->with('success', 'Đã lưu và kích hoạt chương trình khuyến mãi thành công!');
    }

    /**
     * Bật/tắt trạng thái khuyến mãi
     */
    public function toggle(Request $request, string $slug, string $id)
    {
        $quan = $this->getQuan($slug);
        $km = KhuyenMai::where('id', $id)->where('quan_id', $quan->id)->firstOrFail();

        $km->trang_thai = ! $km->trang_thai;
        $km->save();

        return response()->json([
            'success' => true,
            'trang_thai' => $km->trang_thai,
            'message' => $km->trang_thai ? 'Đã kích hoạt ưu đãi' : 'Đã tạm dừng ưu đãi',
        ]);
    }

    /**
     * Xóa khuyến mãi
     */
    public function destroy(Request $request, string $slug, string $id)
    {
        $quan = $this->getQuan($slug);
        $km = KhuyenMai::where('id', $id)->where('quan_id', $quan->id)->firstOrFail();
        $km->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khuyến mãi thành công.',
        ]);
    }

    /**
     * Kiểm tra và xác thực voucher khách đưa (Dành cho chủ quán)
     */
    public function kiemTraVoucher(Request $request, string $slug)
    {
        $quan = $this->getQuan($slug);
        $code = trim(strtoupper((string) $request->input('ma_voucher')));

        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập mã voucher cần kiểm tra!',
            ], 422);
        }

        $km = KhuyenMai::where('quan_id', $quan->id)
            ->where('ma_voucher', $code)
            ->first();

        if (! $km) {
            return response()->json([
                'success' => false,
                'message' => "Mã [{$code}] KHÔNG tồn tại trong hệ thống quán của bạn.",
            ], 404);
        }

        $isExpired = $km->ngay_ket_thuc && $km->ngay_ket_thuc->endOfDay()->isPast();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $km->id,
                'ma_voucher' => $km->ma_voucher,
                'tieu_de' => $km->tieu_de,
                'gia_tri_khuyen_mai' => $km->gia_tri_khuyen_mai,
                'trang_thai' => (bool) $km->trang_thai,
                'is_expired' => $isExpired,
                'ngay_bat_dau' => $km->ngay_bat_dau ? $km->ngay_bat_dau->format('d/m/Y') : null,
                'ngay_ket_thuc' => $km->ngay_ket_thuc ? $km->ngay_ket_thuc->format('d/m/Y') : 'Không giới hạn',
                'dieu_kien_ap_dung' => $km->dieu_kien_ap_dung,
                'slogan' => $km->slogan,
            ],
            'message' => $isExpired ? 'Mã này đã hết hạn!' : ($km->trang_thai ? 'Mã voucher hợp lệ!' : 'Mã này đang bị tạm dừng!'),
        ]);
    }
}

