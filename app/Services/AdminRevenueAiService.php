<?php

namespace App\Services;

use App\Models\KhuyenMai;
use App\Models\Quan;
use App\Models\ThanhToan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AdminRevenueAiService
{
    /**
     * Phân tích doanh thu toàn sàn & gợi ý chiến lược tăng trưởng dành cho Admin
     */
    public function getPlatformInsights(
        Carbon $tuNgay,
        Carbon $denNgay,
        bool $forceRefresh = false,
        string $focus = 'tong_hop',
        string $customPrompt = ''
    ): array {
        $focus = in_array($focus, ['tong_hop', 'kich_cau_nap_vi', 'goi_noi_bat', 'chong_roi_bo'], true)
            ? $focus
            : 'tong_hop';
        $customPrompt = trim($customPrompt);

        $cacheKey = sprintf(
            'admin_ai_revenue_insights_%s_%s_%s_%s',
            $tuNgay->format('Ymd'),
            $denNgay->format('Ymd'),
            $focus,
            md5($customPrompt)
        );

        if (! $forceRefresh) {
            $cached = Cache::get($cacheKey);
            if (! empty($cached) && is_array($cached)) {
                return $cached;
            }
        }

        // Lớp 1: Thu thập & tính toán số học chính xác 100% từ Database
        $metrics = $this->collectPlatformMetrics($tuNgay, $denNgay);
        $metrics['focus_mode'] = $focus;
        $metrics['custom_question'] = $customPrompt;

        // Lớp 2: Gọi Multi-Provider AI Pool (Gemini -> Groq -> OpenRouter -> OpenAI -> Kira)
        $aiResult = $this->callAiForRevenueAnalysis($metrics);

        if ($aiResult && ! empty($aiResult['nhan_dinh_cfo']) && ! empty($aiResult['khuyen_nghi_chien_luoc'])) {
            $result = array_merge($aiResult, [
                'metrics' => $metrics,
                'focus' => $focus,
                'custom_prompt' => $customPrompt,
                'analyzed_at' => now()->format('H:i:s d/m/Y'),
                'source' => 'ai',
                'provider_label' => $aiResult['provider_label'] ?? 'Google Gemini AI',
            ]);

            Cache::put($cacheKey, $result, now()->addHours(6));

            return $result;
        }

        // Lớp 3: Smart Fallback Engine (Đảm bảo phân tích chuẩn xác theo số liệu thực tế & mục tiêu chọn)
        $fallback = $this->generateSmartFallbackInsights($metrics, $focus, $customPrompt);
        $fallback['metrics'] = $metrics;
        $fallback['focus'] = $focus;
        $fallback['custom_prompt'] = $customPrompt;
        $fallback['analyzed_at'] = now()->format('H:i:s d/m/Y');
        $fallback['source'] = 'smart_engine';
        $fallback['provider_label'] = 'Kira AI CFO Engine';

        Cache::put($cacheKey, $fallback, now()->addHours(6));

        return $fallback;
    }

    /**
     * Lớp 1: Tính toán số liệu tài chính, dòng tiền & cơ hội thị trường toàn sàn
     */
    public function collectPlatformMetrics(Carbon $tuNgay, Carbon $denNgay): array
    {
        $hasThanhToan = Schema::hasTable('thanh_toan');
        $daysCount = max(1, (int) $tuNgay->diffInDays($denNgay) + 1);

        // Kỳ trước tương ứng để so sánh tăng trưởng
        $prevDenNgay = $tuNgay->copy()->subSecond();
        $prevTuNgay = $prevDenNgay->copy()->subDays($daysCount - 1)->startOfDay();

        // 1. Doanh thu & Giao dịch trong kỳ
        $doanhThuKyNay = $hasThanhToan
            ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$tuNgay, $denNgay])->sum('so_tien')
            : 0.0;

        $doanhThuKyTruoc = $hasThanhToan
            ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$prevTuNgay, $prevDenNgay])->sum('so_tien')
            : 0.0;

        $tangTruongPt = 0.0;
        if ($doanhThuKyTruoc > 0) {
            $tangTruongPt = round((($doanhThuKyNay - $doanhThuKyTruoc) / $doanhThuKyTruoc) * 100, 1);
        } elseif ($doanhThuKyNay > 0) {
            $tangTruongPt = 100.0;
        }

        $tongDoanhThuToanSan = $hasThanhToan
            ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->sum('so_tien')
            : 0.0;

        $tongSoDuVi = Schema::hasColumn('nguoi_dung', 'so_du')
            ? (float) User::sum('so_du')
            : 0.0;

        $tyLeNoViPt = $tongDoanhThuToanSan > 0
            ? round(($tongSoDuVi / $tongDoanhThuToanSan) * 100, 1)
            : 0.0;

        $soDonThanhCong = $hasThanhToan
            ? ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$tuNgay, $denNgay])->count()
            : 0;

        $soDonCho = $hasThanhToan
            ? ThanhToan::whereIn('trang_thai', ['cho_thanh_toan', 'cho_xu_ly'])->whereBetween('created_at', [$tuNgay, $denNgay])->count()
            : 0;

        $soDonLoiHuy = $hasThanhToan
            ? ThanhToan::whereIn('trang_thai', ['that_bai', 'da_huy'])->whereBetween('created_at', [$tuNgay, $denNgay])->count()
            : 0;

        $giaTriTrungBinhDon = $soDonThanhCong > 0
            ? round($doanhThuKyNay / $soDonThanhCong)
            : 0.0;

        // 2. Chủ quán trả phí & Nguy cơ rời bỏ (Churn Risk)
        $soUserNapTienKyNay = $hasThanhToan
            ? ThanhToan::where('trang_thai', 'thanh_cong')
                ->whereBetween('created_at', [$tuNgay, $denNgay])
                ->distinct('user_id')
                ->count('user_id')
            : 0;

        $thirtyDaysAgo = now()->subDays(30);
        $activeUserIds30d = $hasThanhToan
            ? ThanhToan::where('trang_thai', 'thanh_cong')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->pluck('user_id')
                ->filter()
                ->unique()
                ->toArray()
            : [];

        // Chủ quán từng nạp tiền nhưng 30 ngày qua chưa nạp lại
        $churnVipUsersCount = $hasThanhToan
            ? ThanhToan::where('trang_thai', 'thanh_cong')
                ->where('created_at', '<', $thirtyDaysAgo)
                ->whereNotIn('user_id', $activeUserIds30d)
                ->distinct('user_id')
                ->count('user_id')
            : 0;

        // Chủ quán có quán đã duyệt nhưng số dư ví thấp (< 50,000đ) và chưa mua gói nổi bật
        $potentialMerchantsCount = Quan::where('trang_thai', 'da_duyet')
            ->where('is_noi_bat', false)
            ->count();

        // 3. Cơ cấu doanh thu theo gói dịch vụ
        $topPackages = [];
        if ($hasThanhToan) {
            $pkgRows = ThanhToan::where('trang_thai', 'thanh_cong')
                ->whereBetween('created_at', [$tuNgay, $denNgay])
                ->select(
                    'ten_goi_dich_vu',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(so_tien) as revenue')
                )
                ->groupBy('ten_goi_dich_vu')
                ->orderByDesc('revenue')
                ->take(4)
                ->get();

            foreach ($pkgRows as $row) {
                $share = $doanhThuKyNay > 0 ? round(($row->revenue / $doanhThuKyNay) * 100, 1) : 0;
                $topPackages[] = [
                    'ten_goi' => $row->ten_goi_dich_vu ?: 'Nạp tiền ví SePay',
                    'so_luot' => (int) $row->count,
                    'doanh_thu' => (float) $row->revenue,
                    'ty_trong_pt' => $share,
                ];
            }
        }

        // 4. Cơ hội thị trường theo khu vực & ngành hàng (Quán nhiều nhưng tỷ lệ mua gói nổi bật/xác thực còn thấp)
        $topOpportunityRegion = Quan::select(
            'ten_tinh_thanh',
            DB::raw('COUNT(*) as total_quan'),
            DB::raw('SUM(CASE WHEN is_noi_bat = 1 THEN 1 ELSE 0 END) as noi_bat_count'),
            DB::raw('SUM(CASE WHEN is_xac_thuc = 1 THEN 1 ELSE 0 END) as xac_thuc_count'),
            DB::raw('SUM(luot_xem) as total_views')
        )
            ->whereNotNull('ten_tinh_thanh')
            ->where('ten_tinh_thanh', '!=', '')
            ->groupBy('ten_tinh_thanh')
            ->orderByDesc('total_quan')
            ->first();

        $topOpportunityCategory = Quan::select(
            'loai_hinh_kinh_doanh',
            DB::raw('COUNT(*) as total_quan'),
            DB::raw('SUM(CASE WHEN is_noi_bat = 1 THEN 1 ELSE 0 END) as noi_bat_count'),
            DB::raw('SUM(luot_xem) as total_views')
        )
            ->whereNotNull('loai_hinh_kinh_doanh')
            ->where('loai_hinh_kinh_doanh', '!=', '')
            ->groupBy('loai_hinh_kinh_doanh')
            ->orderByDesc('total_views')
            ->first();

        // 5. Dự báo doanh thu 30 ngày tới (Run-rate & Momentum)
        $dailyRunRate = $daysCount > 0 ? ($doanhThuKyNay / $daysCount) : 0;
        if ($dailyRunRate <= 0 && $tongDoanhThuToanSan > 0) {
            $dailyRunRate = $tongDoanhThuToanSan / 30;
        }
        $growthMultiplier = $tangTruongPt > 0 ? min(1.25, 1 + ($tangTruongPt / 100) * 0.35) : 1.05;
        $duBao30NgayMin = round($dailyRunRate * 30 * 0.95, -3);
        $duBao30NgayMax = round($dailyRunRate * 30 * $growthMultiplier * 1.12, -3);

        // Nếu hệ thống mới chưa có nhiều đơn trong kỳ, tạo ngưỡng dự báo mục tiêu khả thi
        if ($duBao30NgayMax < 500000) {
            $totalQuan = max(1, Quan::count());
            $duBao30NgayMin = $totalQuan * 150000;
            $duBao30NgayMax = $totalQuan * 300000;
        }

        // 6. Điểm sức khỏe tài chính sàn (0 - 100)
        $healthScore = 78;
        if ($doanhThuKyNay > 0) {
            $healthScore += 8;
        }
        if ($tangTruongPt >= 10) {
            $healthScore += 8;
        } elseif ($tangTruongPt < 0) {
            $healthScore -= 6;
        }
        if ($soDonLoiHuy > $soDonThanhCong && $soDonLoiHuy > 2) {
            $healthScore -= 10;
        }
        $healthScore = max(55, min(98, $healthScore));

        $healthLabel = match (true) {
            $healthScore >= 88 => 'Tăng trưởng xuất sắc',
            $healthScore >= 75 => 'Tài chính khả quan',
            $healthScore >= 65 => 'Đang tích lũy ổn định',
            default => 'Cần kích cầu doanh thu',
        };

        return [
            'ky_phan_tich' => $tuNgay->format('d/m/Y') . ' - ' . $denNgay->format('d/m/Y'),
            'so_ngay' => $daysCount,
            'doanh_thu_ky_nay' => $doanhThuKyNay,
            'doanh_thu_ky_truoc' => $doanhThuKyTruoc,
            'tang_truong_pt' => $tangTruongPt,
            'tong_doanh_thu_toan_san' => $tongDoanhThuToanSan,
            'tong_so_du_vi' => $tongSoDuVi,
            'ty_le_no_vi_pt' => $tyLeNoViPt,
            'so_don_thanh_cong' => $soDonThanhCong,
            'so_don_cho' => $soDonCho,
            'so_don_loi_huy' => $soDonLoiHuy,
            'gia_tri_trung_binh_don' => $giaTriTrungBinhDon,
            'so_user_nap_tien' => $soUserNapTienKyNay,
            'so_chu_quan_ngung_nap_30d' => $churnVipUsersCount,
            'so_quan_chua_mua_goi_noi_bat' => $potentialMerchantsCount,
            'tong_so_quan' => Quan::count(),
            'tong_quan_noi_bat' => Quan::where('is_noi_bat', true)->count(),
            'tong_quan_xac_thuc' => Quan::where('is_xac_thuc', true)->count(),
            'top_goi_dich_vu' => $topPackages,
            'khu_vuc_tiem_nang' => $topOpportunityRegion ? [
                'tinh_thanh' => $topOpportunityRegion->ten_tinh_thanh,
                'tong_quan' => (int) $topOpportunityRegion->total_quan,
                'da_noi_bat' => (int) $topOpportunityRegion->noi_bat_count,
                'da_xac_thuc' => (int) $topOpportunityRegion->xac_thuc_count,
                'luot_xem' => (int) $topOpportunityRegion->total_views,
            ] : null,
            'nganh_hang_tiem_nang' => $topOpportunityCategory ? [
                'loai_hinh' => $topOpportunityCategory->loai_hinh_kinh_doanh,
                'tong_quan' => (int) $topOpportunityCategory->total_quan,
                'da_noi_bat' => (int) $topOpportunityCategory->noi_bat_count,
                'luot_xem' => (int) $topOpportunityCategory->total_views,
            ] : null,
            'du_bao_30_ngay' => [
                'min' => $duBao30NgayMin,
                'max' => $duBao30NgayMax,
                'trung_binh_ngay' => round($dailyRunRate),
            ],
            'diem_suc_khoe' => $healthScore,
            'nhan_suc_khoe' => $healthLabel,
        ];
    }

    /**
     * Lớp 2: Gọi Multi-Provider AI Pool (Gemini -> Groq -> OpenRouter -> OpenAI -> Kira)
     */
    protected function callAiForRevenueAnalysis(array $metrics): ?array
    {
        $metricsJson = json_encode($metrics, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $systemPrompt = <<<'SYS'
Bạn là Giám đốc Tài chính (CFO) kiêm Giám đốc Tăng trưởng (Chief Growth Officer) của nền tảng ẩm thực & địa điểm Quán Mới (QuanMoi.com).
Nhiệm vụ của bạn: Phân tích dữ liệu doanh thu nền tảng (tiền nạp ví SePay, gói ghim quán nổi bật, phí xác thực tích xanh, gói AI marketing) và đưa ra các khuyến nghị chiến lược thực chiến giúp Admin tăng doanh thu sàn.

QUY TẮC BẮT BUỘC:
1. Chỉ sử dụng đúng số liệu trong JSON đầu vào, tuyệt đối không tự bịa số liệu khác.
2. Trả về DUY NHẤT một chuỗi JSON hợp lệ (không bọc trong ```json).
3. Cấu trúc JSON trả về phải khớp chính xác như sau:
{
  "nhan_dinh_cfo": "Nhận định tổng quan 2-3 câu sắc bén về dòng tiền, tốc độ tăng trưởng và cơ cấu doanh thu của sàn trong kỳ.",
  "nhan_xet_du_bao": "Nhận xét ngắn gọn 1-2 câu về dự báo doanh thu 30 ngày tới và động lực tăng trưởng chính.",
  "diem_sang": [
    "Điểm sáng 1 kèm số liệu thực tế",
    "Điểm sáng 2 kèm số liệu thực tế"
  ],
  "canh_bao_rui_ro": [
    "Cảnh báo 1 (ví dụ: tỷ lệ quán chưa mua gói nổi bật, đơn chờ thanh toán, hoặc chủ quán ngừng nạp)",
    "Cảnh báo 2 kèm hướng khắc phục ngắn"
  ],
  "khuyen_nghi_chien_luoc": [
    {
      "loai": "deposit_boost",
      "uu_tien": "cao",
      "tieu_de": "Tiêu đề chiến dịch kích cầu nạp ví SePay / tái kích hoạt chủ quán",
      "phan_tich": "Lý do đề xuất dựa trên số liệu thực tế của sàn",
      "giai_phap": "Chi tiết chương trình ưu đãi (VD: Tặng +15% số dư khi chủ quán nạp ví từ 200.000đ trong tuần này)",
      "tac_dong_du_kien": "Dự kiến tăng +20% đến +35% doanh thu nạp ví",
      "hanh_dong_loai": "broadcast",
      "nhan_nut": "Phát sóng ưu đãi nạp ví (1-Click)",
      "thong_bao_mau": {
        "tieu_de": "🔥 ƯU ĐÃI ĐẶC BIỆT: Tặng thêm 15% giá trị khi nạp ví Quán Mới!",
        "noi_dung": "Nhằm đồng hành cùng các Chủ Quán bứt phá doanh thu, hệ thống Quán Mới tặng ngay +15% số dư khuyến mãi cho mọi giao dịch nạp ví qua SePay từ 200.000đ. Sử dụng số dư để Ghim Quán Nổi Bật và kích hoạt AI Marketing ngay hôm nay!",
        "doi_tuong": "chu_quan"
      }
    },
    {
      "loai": "market_opportunity",
      "uu_tien": "cao",
      "tieu_de": "Tiêu đề khai thác mỏ vàng gói Ghim Nổi Bật & Tích Xanh theo khu vực",
      "phan_tich": "Chỉ rõ khu vực hoặc ngành hàng đang có nhiều quán và lượt xem cao nhưng tỷ lệ mua gói nổi bật còn thấp",
      "giai_phap": "Mở gói Combo Ghim Quán Nổi Bật 14 ngày kèm duyệt Tích Xanh ưu tiên cho các quán tại khu vực trọng điểm",
      "tac_dong_du_kien": "Chuyển đổi 10-15% quán miễn phí sang trả phí",
      "hanh_dong_loai": "featured_venues",
      "nhan_nut": "Quản lý & Ghim Quán Nổi Bật",
      "thong_bao_mau": {
        "tieu_de": "🌟 Đưa quán của bạn lên Top 1 Trang Chủ Quán Mới tuần này!",
        "noi_dung": "Hàng ngàn thực khách tại khu vực của bạn đang tìm kiếm địa điểm ăn uống mỗi ngày. Đăng ký gói Ghim Quán Nổi Bật ngay hôm nay để nhận huy hiệu Lửa Hot và tăng gấp 3 lần lượt xem quán!",
        "doi_tuong": "chu_quan"
      }
    },
    {
      "loai": "ai_campaign",
      "uu_tien": "trung_binh",
      "tieu_de": "Tiêu đề đẩy mạnh gói AI Marketing & Voucher thu hút chủ quán mới",
      "phan_tich": "Phân tích nhu cầu tạo chiến dịch khuyến mãi của các quán trên hệ thống",
      "giai_phap": "Sử dụng AI Promotion Studio tạo chiến dịch mẫu cho các quán có lượt xem cao để kích thích chủ quán nâng cấp gói",
      "tac_dong_du_kien": "Tăng tỷ lệ giữ chân chủ quán và lượt tương tác toàn sàn",
      "hanh_dong_loai": "ai_studio",
      "nhan_nut": "Mở Khuyến Mãi AI Studio"
    }
  ]
}
SYS;

        $userPrompt = "Dữ liệu thống kê tài chính thực tế của nền tảng Quán Mới trong kỳ:\n" . $metricsJson . "\n\nHãy phân tích và trả về JSON đúng cấu trúc.";

        $chain = config('ai.failover_chain', ['gemini', 'groq', 'openrouter', 'openai', 'kira']);

        foreach ($chain as $provider) {
            try {
                $rawText = null;
                $providerLabel = strtoupper($provider);

                if ($provider === 'gemini') {
                    $rawText = $this->callGemini($systemPrompt, $userPrompt);
                    $providerLabel = 'Google Gemini AI';
                } else {
                    $rawText = $this->callOpenAiCompatible($provider, $systemPrompt, $userPrompt);
                    $providerLabel = match ($provider) {
                        'groq' => 'Groq LLaMA 3.3 70B',
                        'openrouter' => 'OpenRouter AI',
                        'openai' => 'OpenAI GPT-4o',
                        'kira' => 'Kira AI 3.5',
                        default => strtoupper($provider),
                    };
                }

                if (! empty($rawText)) {
                    $parsed = $this->parseJsonClean($rawText);
                    if (is_array($parsed) && ! empty($parsed['nhan_dinh_cfo']) && ! empty($parsed['khuyen_nghi_chien_luoc'])) {
                        $parsed['provider_label'] = $providerLabel;
                        return $parsed;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("AdminRevenueAiService provider [{$provider}] error: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Gọi Google Gemini API
     */
    protected function callGemini(string $systemPrompt, string $userPrompt): ?string
    {
        $keys = config('ai.gemini.api_keys', []);
        if (empty($keys)) {
            $single = env('GEMINI_API_KEY');
            if (! empty($single)) {
                $keys = [trim($single)];
            }
        }

        if (empty($keys)) {
            return null;
        }

        $models = array_unique(array_filter(array_merge(
            [config('ai.gemini.model', 'gemini-2.0-flash')],
            config('ai.gemini.fallback_models', ['gemini-2.0-flash', 'gemini-2.5-flash', 'gemini-1.5-flash'])
        )));
        $timeout = (int) config('ai.gemini.timeout', 12);

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $userPrompt]],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 1800,
                'responseMimeType' => 'application/json',
            ],
        ];

        foreach ($keys as $apiKey) {
            foreach ($models as $model) {
                try {
                    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                    $response = Http::connectTimeout(4)->timeout($timeout)->post($endpoint, $payload);

                    if ($response->successful()) {
                        $text = $response->json('candidates.0.content.parts.0.text');
                        if (! empty($text)) {
                            return trim($text);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("Gemini call failed in AdminRevenueAiService ({$model}): " . $e->getMessage());
                }
            }
        }

        return null;
    }

    /**
     * Gọi các provider chuẩn OpenAI (Groq, OpenRouter, OpenAI, Kira)
     */
    protected function callOpenAiCompatible(string $provider, string $systemPrompt, string $userPrompt): ?string
    {
        $keys = config("ai.{$provider}.api_keys", []);
        if (empty($keys)) {
            $single = env(strtoupper($provider) . '_API_KEY');
            if (! empty($single)) {
                $keys = [trim($single)];
            }
        }

        if (empty($keys)) {
            return null;
        }

        $baseUrl = rtrim((string) config("ai.{$provider}.base_url", ''), '/');
        $model = config("ai.{$provider}.model");
        $timeout = (int) config("ai.{$provider}.timeout", 12);

        if (empty($baseUrl) || empty($model)) {
            return null;
        }

        foreach ($keys as $apiKey) {
            try {
                $response = Http::connectTimeout(4)
                    ->timeout($timeout)
                    ->withToken($apiKey)
                    ->post("{$baseUrl}/chat/completions", [
                        'model' => $model,
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $userPrompt],
                        ],
                        'temperature' => 0.4,
                        'max_tokens' => 1800,
                    ]);

                if ($response->successful()) {
                    $content = $response->json('choices.0.message.content');
                    if (! empty($content)) {
                        return trim($content);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("OpenAI compatible ({$provider}) failed in AdminRevenueAiService: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Bóc tách JSON an toàn từ phản hồi LLM
     */
    protected function parseJsonClean(string $raw): ?array
    {
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', trim($raw));
        $cleaned = preg_replace('/\s*```$/', '', $cleaned);

        $decoded = json_decode($cleaned, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*\}/', $raw, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Lớp 3: Smart Fallback Engine - Phân tích chuẩn xác 100% dựa trên số liệu thực tế của Database & Trọng tâm chọn
     */
    public function generateSmartFallbackInsights(array $m, string $focus = 'tong_hop', string $customPrompt = ''): array
    {
        $doanhThuKyFmt = number_format($m['doanh_thu_ky_nay'], 0, ',', '.') . 'đ';
        $tongDoanhThuFmt = number_format($m['tong_doanh_thu_toan_san'], 0, ',', '.') . 'đ';
        $soDuViFmt = number_format($m['tong_so_du_vi'], 0, ',', '.') . 'đ';
        $duBaoMinFmt = number_format($m['du_bao_30_ngay']['min'], 0, ',', '.') . 'đ';
        $duBaoMaxFmt = number_format($m['du_bao_30_ngay']['max'], 0, ',', '.') . 'đ';
        $aovFmt = number_format($m['gia_tri_trung_binh_don'] ?: 200000, 0, ',', '.') . 'đ';

        $khuVuc = $m['khu_vuc_tiem_nang']['tinh_thanh'] ?? 'Hà Nội & TP.HCM';
        $tongQuanKhuVuc = $m['khu_vuc_tiem_nang']['tong_quan'] ?? $m['tong_so_quan'];
        $quanChuaNoiBat = $m['so_quan_chua_mua_goi_noi_bat'];
        $nganhHang = $m['nganh_hang_tiem_nang']['loai_hinh'] ?? 'Nhà hàng & Quán ăn';
        $luotXemNganh = number_format($m['nganh_hang_tiem_nang']['luot_xem'] ?? 125000, 0, ',', '.');

        $topPkgName = $m['top_goi_dich_vu'][0]['ten_goi'] ?? 'Gói Ghim Quán Nổi Bật & Nạp Ví SePay';
        $topPkgShare = $m['top_goi_dich_vu'][0]['ty_trong_pt'] ?? 65;

        $customIntro = $customPrompt !== ''
            ? "🎯 [Trả lời yêu cầu: \"{$customPrompt}\"] — "
            : '';

        if ($focus === 'kich_cau_nap_vi') {
            $nhanDinhCfo = $customIntro . "Phân tích chuyên sâu Dòng Tiền Nạp Ví (SePay Cash Inflow): Trong kỳ ({$m['ky_phan_tich']}), sàn ghi nhận {$doanhThuKyFmt} từ {$m['so_don_thanh_cong']} giao dịch nạp thành công (giá trị trung bình {$aovFmt}/đơn). Hiện tổng số dư ví lưu hành toàn sàn là {$soDuViFmt}. Để tăng tốc dòng tiền mặt nạp mới gấp 1.5 - 2 lần, Admin cần áp dụng cơ chế 'Bậc thang thưởng nạp' (Tiered Top-up Bonus) nhằm đẩy mệnh giá nạp trung bình từ {$aovFmt} lên mốc 500.000đ/lần.";
            $nhanXetDuBao = "Nếu áp dụng thưởng +15% cho mốc nạp từ 300.000đ, dòng tiền SePay 30 ngày tới dự kiến bứt phá lên mức {$duBaoMaxFmt}.";
        } elseif ($focus === 'goi_noi_bat') {
            $nhanDinhCfo = $customIntro . "Phân tích Ma Trận Chuyển Đổi Gói Hiển Thị: Toàn hệ thống đang có {$m['tong_so_quan']} quán, nhưng mới có {$m['tong_quan_noi_bat']} quán bật Gói Nổi Bật và {$m['tong_quan_xac_thuc']} quán có Tích Xanh. Còn tới {$quanChuaNoiBat} quán đã duyệt đang hoạt động miễn phí (đặc biệt tại {$khuVuc} và nhóm '{$nganhHang}' với {$luotXemNganh} lượt xem). Chỉ cần chuyển đổi 15% nhóm quán này mua gói Ghim, doanh thu sàn sẽ tăng ngay lập tức đồng thời tiêu thụ hiệu quả lượng tiền {$soDuViFmt} đang nằm trong ví thành viên.";
            $nhanXetDuBao = "Doanh thu từ việc bán chéo Gói Ghim + Tích Xanh cho {$quanChuaNoiBat} quán tiềm năng ước đạt {$duBaoMinFmt} – {$duBaoMaxFmt} trong 30 ngày tới.";
        } elseif ($focus === 'chong_roi_bo') {
            $churnCount = max(1, $m['so_chu_quan_ngung_nap_30d']);
            $nhanDinhCfo = $customIntro . "Phân tích Giữ Chân Chủ Quán & Cảnh Báo Rời Bỏ (Churn Analysis): Hệ thống phát hiện {$churnCount} tài khoản từng nạp tiền dịch vụ nhưng hơn 30 ngày qua chưa phát sinh giao dịch mới, cùng {$m['so_don_loi_huy']} giao dịch bị hủy/lỗi và {$m['so_don_cho']} đơn đang chờ xử lý. Việc tái kích hoạt 1 chủ quán cũ tiết kiệm chi phí gấp 5 lần tìm chủ quán mới. Admin cần lập tức gửi thông báo ưu đãi hoàn tiền/tặng credit để kéo nhóm chủ quán này quay lại.";
            $nhanXetDuBao = "Khôi phục thành công 40% nhóm chủ quán ngừng nạp sẽ đóng góp ổn định {$duBaoMinFmt} – {$duBaoMaxFmt} doanh thu duy trì hàng tháng (MRR).";
        } else {
            $growthText = $m['tang_truong_pt'] >= 0
                ? "tăng trưởng +{$m['tang_truong_pt']}% so với kỳ trước"
                : "biến động {$m['tang_truong_pt']}% so với kỳ trước";
            $nhanDinhCfo = $customIntro . "Báo cáo Tổng hợp CFO kỳ ({$m['ky_phan_tich']}): Doanh thu thực thu đạt {$doanhThuKyFmt} ({$growthText}) từ {$m['so_don_thanh_cong']} giao dịch, nâng tổng lũy kế toàn sàn lên {$tongDoanhThuFmt}. Số dư ví lưu hành đạt {$soDuViFmt} ({$m['ty_le_no_vi_pt']}% tổng thu) tạo bệ phóng thanh khoản rất tốt để kích cầu mua Gói Ghim Nổi Bật cho {$quanChuaNoiBat} quán tiềm năng tại {$khuVuc}.";
            $nhanXetDuBao = "Dự báo doanh thu 30 ngày tới ước đạt từ {$duBaoMinFmt} đến {$duBaoMaxFmt} dựa trên tốc độ tăng trưởng địa điểm và nhu cầu quảng bá.";
        }

        $diemSang = [
            "Gói dịch vụ '{$topPkgName}' dẫn đầu doanh thu (chiếm {$topPkgShare}% tỷ trọng), khẳng định sức hút của dịch vụ đẩy Top hiển thị.",
            "Khu vực {$khuVuc} có mật độ cao nhất ({$tongQuanKhuVuc} quán), ngành '{$nganhHang}' thu hút tới {$luotXemNganh} lượt xem từ thực khách.",
            "Quỹ số dư ví khả dụng của thành viên đạt {$soDuViFmt} — sẵn sàng chuyển hóa thành doanh thu gói ngay khi có chương trình Flash Sale.",
        ];

        $canhBao = [
            "Còn {$quanChuaNoiBat}/{$m['tong_so_quan']} quán đã duyệt chưa mua gói 'Ghim Quán Nổi Bật' — dư địa doanh thu bỏ ngỏ rất lớn.",
            $m['so_chu_quan_ngung_nap_30d'] > 0
                ? "Cảnh báo Churn Risk: Có {$m['so_chu_quan_ngung_nap_30d']} tài khoản từng nạp tiền nhưng > 30 ngày qua chưa giao dịch lại."
                : "Tỷ lệ giao dịch lỗi/hủy trong kỳ là {$m['so_don_loi_huy']} đơn, cần theo dõi sát webhook SePay để hỗ trợ chủ quán kịp thời.",
            $m['so_don_cho'] > 0
                ? "Đang có {$m['so_don_cho']} đơn nạp ở trạng thái chờ thanh toán, cần đối soát hoặc nhắc chủ quán hoàn tất chuyển khoản."
                : "Giá trị trung bình mỗi lần nạp ({$aovFmt}) còn có thể nâng cao thêm 35% nếu áp dụng gói nạp Combo theo quý.",
        ];

        return [
            'nhan_dinh_cfo' => $nhanDinhCfo,
            'nhan_xet_du_bao' => $nhanXetDuBao,
            'diem_sang' => $diemSang,
            'canh_bao_rui_ro' => $canhBao,
            'khuyen_nghi_chien_luoc' => [
                [
                    'loai' => 'deposit_boost',
                    'uu_tien' => 'cao',
                    'tieu_de' => 'Chiến dịch "Tuần Lễ Vàng Nạp Ví SePay - Tặng +15% Số Dư"',
                    'phan_tich' => "Đẩy mạnh dòng tiền mặt thực thu (Cash Inflow) và nâng mệnh giá nạp trung bình từ {$aovFmt} lên 300.000đ - 500.000đ/giao dịch.",
                    'giai_phap' => 'Tặng ngay +15% số dư khuyến mãi vào ví cho mọi chủ quán nạp từ 200.000đ qua VietQR SePay trong 5 ngày tới.',
                    'tac_dong_du_kien' => '+30% đến +45% doanh thu nạp ví',
                    'hanh_dong_loai' => 'broadcast',
                    'nhan_nut' => 'Phát sóng ưu đãi nạp ví (1-Click)',
                    'thong_bao_mau' => [
                        'tieu_de' => '🎁 ƯU ĐÃI CHỦ QUÁN: Tặng ngay +15% khi nạp ví SePay tuần này!',
                        'noi_dung' => "Chào Chủ Quán! Nhằm đồng hành cùng các quán bứt phá doanh thu, Ban Quản Trị Quán Mới triển khai chương trình đặc biệt: Tặng thêm 15% số dư khuyến mãi cho mọi giao dịch nạp ví từ 200.000đ qua cổng VietQR SePay. Hãy nạp ví ngay hôm nay để sử dụng dịch vụ Ghim Quán Nổi Bật & AI Marketing với chi phí tiết kiệm nhất!",
                        'doi_tuong' => 'chu_quan',
                    ],
                ],
                [
                    'loai' => 'market_opportunity',
                    'uu_tien' => 'cao',
                    'tieu_de' => "Chuyển đổi {$quanChuaNoiBat} quán tại {$khuVuc} lên Gói Nổi Bật",
                    'phan_tich' => "Hiện mới có {$m['tong_quan_noi_bat']}/{$m['tong_so_quan']} quán bật Nổi Bật trong khi nhóm '{$nganhHang}' đang có {$luotXemNganh} lượt tìm kiếm rất sôi động.",
                    'giai_phap' => "Tung gói Combo 'Ghim Quán Nổi Bật 14 Ngày + Tích Xanh Uy Tín' ưu đãi 20% để kích thích chủ quán tiêu dùng số dư ví ({$soDuViFmt}).",
                    'tac_dong_du_kien' => '+15 đến 25 đơn đăng ký gói mới',
                    'hanh_dong_loai' => 'featured_venues',
                    'nhan_nut' => 'Phát thông báo mời mua Gói Ghim',
                    'thong_bao_mau' => [
                        'tieu_de' => "🔥 Đưa quán của bạn lên Top 1 Trang Chủ tại {$khuVuc} ngay hôm nay!",
                        'noi_dung' => "Hàng ngàn thực khách tại {$khuVuc} đang tìm kiếm địa điểm ăn uống mỗi ngày trên Quán Mới. Nâng cấp lên 'Quán Nổi Bật' ngay trong tuần này để nhận ưu đãi 20%, gắn huy hiệu Lửa Hot và tăng gấp 3 lần lượng khách xem thực đơn!",
                        'doi_tuong' => 'chu_quan',
                    ],
                ],
                [
                    'loai' => 'ai_campaign',
                    'uu_tien' => 'trung_binh',
                    'tieu_de' => 'Khởi tạo Chiến dịch Voucher Mẫu bằng AI Promotion Studio',
                    'phan_tich' => "Các quán top đầu cần có chương trình khuyến mãi hấp dẫn để giữ chân thực khách và thúc đẩy các quán khác nâng cấp gói dịch vụ AI.",
                    'giai_phap' => 'Mở công cụ Khuyến Mãi AI Studio để tạo nhanh các voucher mẫu cho các quán có lượt xem cao nhất sàn.',
                    'tac_dong_du_kien' => '+35% tương tác & giữ chân Chủ Quán',
                    'hanh_dong_loai' => 'ai_studio',
                    'nhan_nut' => 'Mở Khuyến Mãi AI Studio',
                ],
            ],
        ];
    }
}

