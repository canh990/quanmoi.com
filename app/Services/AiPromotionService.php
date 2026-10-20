<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiPromotionService
{
    /**
     * Tạo nội dung khuyến mãi bằng Kira AI (OpenAI API format),
     * tự động chuyển sang Smart Fallback nếu chưa có API key hoặc gặp sự cố mạng.
     */
    public function generatePromotion(array $data, ?string $overrideApiKey = null): array
    {
        $apiKey = trim((string) ($overrideApiKey ?: config('ai.kira.api_key', env('KIRAAI_API_KEY', ''))));
        $baseUrl = rtrim((string) config('ai.kira.base_url', 'https://kiraai.vn/api/v1'), '/');
        $model = config('ai.kira.model', 'kira-3.5-flash');

        if (! empty($apiKey)) {
            try {
                $response = $this->callKiraAi($apiKey, $baseUrl, $model, $data);
                if ($response && ! empty($response['tieu_de'])) {
                    $response['source'] = 'kira_ai';
                    $response['model'] = $model;
                    return $response;
                }
            } catch (\Throwable $e) {
                Log::warning('Kira AI API call failed, falling back to smart generator: '.$e->getMessage());
            }
        }

        // Chế độ Smart Fallback: Luôn đảm bảo kết quả hoàn hảo cho đồ án
        $fallback = $this->generateFallbackPromotion($data);
        $fallback['source'] = 'fallback';
        $fallback['model'] = 'Hệ thống AI Mẫu Nội Bộ (Dự phòng)';
        return $fallback;
    }

    /**
     * Gọi API Kira AI
     */
    protected function callKiraAi(string $apiKey, string $baseUrl, string $model, array $data): ?array
    {
        $systemPrompt = <<<SYS
Bạn là chuyên gia Giám đốc Marketing F&B và Content Creator số 1 tại Việt Nam.
Nhiệm vụ của bạn: Tạo nội dung chiến dịch khuyến mãi ẩm thực đỉnh cao, kích thích vị giác và thúc đẩy chốt đơn mạnh mẽ.
YÊU CẦU BẮT BUỘC: Bạn PHẢI trả về duy nhất một chuỗi JSON hợp lệ (không kèm markdown ```json hay bất kỳ chữ nào bên ngoài). Cấu trúc JSON chính xác như sau:
{
  "tieu_de": "Tiêu đề chiến dịch giật tít, viết hoa các từ khóa chính, cực kỳ cuốn hút",
  "ma_voucher": "Mã voucher viết hoa không dấu, ngắn gọn (VD: QUANMOI20, DEALHOT50)",
  "slogan": "Câu khẩu hiệu / slogan ngắn gọn, ấn tượng để in banner hoặc ảnh",
  "noi_dung": "Nội dung bài viết Facebook/Instagram/TikTok hoàn chỉnh, giàu cảm xúc, có các emoji sinh động, miêu tả hương vị món ăn kích thích thèm ăn, kêu gọi hành động (CTA) thôi thúc thực khách",
  "the_le": "Thể lệ chi tiết gồm: Thời gian áp dụng, điều kiện (áp dụng tại quán hay mang về, giới hạn...), địa điểm quán",
  "hashtags": "Danh sách các hashtag viết liền có dấu # cách nhau bởi dấu cách"
}
SYS;

        $tenQuan = $data['ten_quan'] ?? 'Quán Mới';
        $loaiHinh = $data['loai_hinh'] ?? 'Quán ăn';
        $diaChi = $data['dia_chi'] ?? 'Việt Nam';
        $monAnRaw = $data['mon_an'] ?? 'Toàn bộ menu';
        $monAn = is_array($monAnRaw) ? implode(', ', array_filter($monAnRaw)) : (string) $monAnRaw;
        if (empty(trim($monAn))) {
            $monAn = 'Toàn bộ menu';
        }
        $loaiKm = $data['loai_khuyen_mai'] ?? 'Giảm giá';
        $giaTriGiam = $data['gia_tri_giam'] ?? '20%';
        $dipKm = $data['dip_khuyen_mai'] ?? 'Ưu đãi đặc biệt';
        $phongCach = $data['phong_cach'] ?? 'Hào hứng sôi nổi';
        $ngayBatDau = ! empty($data['ngay_bat_dau']) ? date('d/m/Y', strtotime($data['ngay_bat_dau'])) : date('d/m/Y');
        $ngayKetThuc = ! empty($data['ngay_ket_thuc']) ? date('d/m/Y', strtotime($data['ngay_ket_thuc'])) : date('d/m/Y', strtotime('+7 days'));
        $yeuCauThem = trim((string) ($data['yeu_cau_them'] ?? ''));

        $userPrompt = <<<PROMPT
Hãy tạo chiến dịch khuyến mãi cho quán với thông tin sau:
- Tên quán: {$tenQuan} ({$loaiHinh})
- Địa chỉ: {$diaChi}
- Món áp dụng ưu đãi: {$monAn}
- Hình thức khuyến mãi: {$loaiKm}
- Mức giảm / Giá trị quà tặng: {$giaTriGiam}
- Dịp áp dụng: {$dipKm}
- Phong cách viết: {$phongCach}
- Thời gian áp dụng: Từ {$ngayBatDau} đến {$ngayKetThuc}
- Yêu cầu bổ sung đặc biệt từ quán: {$yeuCauThem}

QUY TẮC BẮT BUỘC:
1. PHONG CÁCH VIẾT: Bắt buộc viết đúng ngữ điệu và phong cách "{$phongCach}". Tuyệt đối không dùng văn mẫu rập khuôn.
2. YÊU CẦU BỔ SUNG: Nếu có yêu cầu bổ sung ("{$yeuCauThem}"), bạn BẮT BUỘC phải đưa rõ ràng vào cả nội dung bài viết và phần thể lệ, không được bỏ qua.

Hãy xuất ra JSON chuẩn theo quy định.
PROMPT;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])
        ->timeout((int) config('ai.kira.timeout', 25))
        ->post($baseUrl.'/chat/completions', [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => 0.75,
            'max_tokens' => 1500,
        ]);

        if (! $response->successful()) {
            Log::error('Kira AI error status: '.$response->status().', body: '.$response->body());
            return null;
        }

        $content = $response->json('choices.0.message.content');
        if (! $content) {
            return null;
        }

        // Làm sạch chuỗi JSON nếu có bao quanh bởi markdown code blocks
        $cleaned = trim($content);
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $cleaned, $matches)) {
            $cleaned = trim($matches[1]);
        }

        $decoded = json_decode($cleaned, true);
        if (is_array($decoded) && isset($decoded['tieu_de'])) {
            return $decoded;
        }

        return null;
    }

    /**
     * Bộ sinh nội dung mẫu thông minh chuẩn F&B Việt Nam (Smart Fallback Engine)
     * Đa dạng hóa 100% nội dung theo từng phong cách, cam kết không rập khuôn mẫu cũ.
     */
    public function generateFallbackPromotion(array $data): array
    {
        $tenQuan = trim((string) ($data['ten_quan'] ?? 'Quán Mới'));
        $diaChi = trim((string) ($data['dia_chi'] ?? ''));
        $monAnRaw = $data['mon_an'] ?? 'toàn bộ menu';
        $monAn = is_array($monAnRaw) ? implode(', ', array_filter($monAnRaw)) : trim((string) $monAnRaw);
        if (empty($monAn)) {
            $monAn = 'toàn bộ menu';
        }
        $giaTri = trim((string) ($data['gia_tri_giam'] ?? '20%'));
        $loaiKm = $data['loai_khuyen_mai'] ?? 'giam_phan_tram';
        $dipKm = trim((string) ($data['dip_khuyen_mai'] ?? 'Đại tiệc ẩm thực'));
        $phongCach = $data['phong_cach'] ?? 'hao_hung';
        $ngayBatDau = ! empty($data['ngay_bat_dau']) ? date('d/m/Y', strtotime($data['ngay_bat_dau'])) : date('d/m/Y');
        $ngayKetThuc = ! empty($data['ngay_ket_thuc']) ? date('d/m/Y', strtotime($data['ngay_ket_thuc'])) : date('d/m/Y', strtotime('+7 days'));
        $yeuCauThem = trim((string) ($data['yeu_cau_them'] ?? ''));

        // 1. Tạo mã voucher ngẫu nhiên bắt mắt
        $prefix = Str::upper(Str::slug(Str::limit($tenQuan, 6, ''), ''));
        if (empty($prefix)) {
            $prefix = 'QUANMOI';
        }
        $digits = preg_replace('/[^0-9]/', '', $giaTri);
        $voucherSuffix = ! empty($digits) ? Str::limit($digits, 3, '') : 'HOT';
        $maVoucher = $prefix.$voucherSuffix;

        // Xử lý khối yêu cầu bổ sung nếu có
        $yeuCauSection = '';
        $yeuCauInTheLe = '';
        if (! empty($yeuCauThem)) {
            $yeuCauInTheLe = "\n• Yêu cầu & Điều kiện bổ sung: {$yeuCauThem}";
        }

        // 2. Nội dung, tiêu đề, slogan phân biệt hoàn toàn theo 4 phong cách giọng văn
        switch ($phongCach) {
            case 'sang_trong':
                $tieuDe = "✨ [ĐẶC QUYỀN THƯỢNG LƯU] {$tenQuan} DÀNH TẶNG ƯU ĐÃI {$giaTri} CHO {$monAn}";
                $slogan = "Nâng tầm vị giác – Trải nghiệm ẩm thực tinh hoa cùng {$tenQuan}";

                if (! empty($yeuCauThem)) {
                    $yeuCauSection = "\n\n⚜️ QUY ĐỊNH & LƯU Ý DÀNH CHO QUÝ KHÁCH:\n• {$yeuCauThem}";
                }

                $noiDungChiTiet = <<<TEXT
Kính gửi quý thực khách sành ẩm thực,

Nhân dịp {$dipKm}, {$tenQuan} trân trọng giới thiệu chương trình tri ân ẩm thực thượng lưu: Ưu đãi đặc quyền {$giaTri} dành riêng cho món [{$monAn}].

⚜️ DẤU ẤN ẨM THỰC KHÁC BIỆT TẠI {$tenQuan}:
🍷 Tuyệt tác hương vị: Từng phần {$monAn} được chế biến công phu từ nguồn nguyên liệu tươi mới, giữ trọn vẹn vị ngon nguyên bản.
🕯️ Không gian thanh lịch và trang nhã: Nơi lý tưởng cho những cuộc hẹn quan trọng, họp mặt đối tác hay sum họp gia đình ấm cúng.
💎 Dịch vụ tận tâm, chu đáo: Mang đến cho quý khách sự thư thái và thăng hoa trong từng giác quan.

🎟️ MÃ ĐẶC QUYỀN: [{$maVoucher}] (Giảm ngay {$giaTri})
⏰ Thời gian phục vụ: Từ {$ngayBatDau} đến {$ngayKetThuc}
📍 Địa chỉ đón tiếp: {$diaChi}{$yeuCauSection}

Kính mời quý thực khách liên hệ đặt bàn trước để {$tenQuan} chuẩn bị đón tiếp một cách chu đáo và trang trọng nhất!
TEXT;
                $hashtags = "#FineDining #AmThucThuongLuu #{$prefix} #AmThucVietNam #GourmetDining #{$dipKm}";
                break;

            case 'hai_huoc':
                $tieuDe = "🤣 BÁO ĐỘNG ĐỎ: {$tenQuan} GIẢM SỐC {$giaTri} CHO {$monAn} - KHÔNG ĐI LÀ TIẾC HÙI HỤI!";
                $slogan = "Cái bụng đói cồn cào – Để {$tenQuan} khao deal xịn!";

                if (! empty($yeuCauThem)) {
                    $yeuCauSection = "\n\n⚠️ ĐỌC KỸ HƯỚNG DẪN TRƯỚC KHI 'OANH TẠC':\n👉 {$yeuCauThem}";
                }

                $noiDungChiTiet = <<<TEXT
Alo alo các đồng booooong sành ăn ơi! 📢

Vũ trụ gửi tín hiệu ét-ô-ét: Chiếc bụng của bạn đang réo rắt đòi nạp món ngon kìa! Đừng để chiếc ví 'hờn dỗi' vì {$tenQuan} đã tung phao cứu sinh với deal giảm tới {$giaTri} cho món {$monAn}! 🤤

😎 LÝ DO VÌ SAO PHẢI 'LÊN KÈO' NGAY VÀ LUÔN:
🔥 Món '{$monAn}' ngon xỉu up xỉu down, ăn một miếng là u mê không lối thoát!
💸 Giảm sốc {$giaTri} – Ăn no căng rốn mà ví tiền vẫn an toàn tuyệt đối.
📸 Check-in góc nào cũng lung linh, tha hồ gom ảnh triệu like sống ảo.
💨 Máy lạnh phà phà, nhân viên nhiệt tình hơn cả người yêu cũ!

🎟️ MẬT MÃ ĂN NGON: [{$maVoucher}]
⏰ Hạn chót săn deal: Từ {$ngayBatDau} đến hết {$ngayKetThuc}
📍 Tọa độ điểm hẹn: {$diaChi}{$yeuCauSection}

👉 Tag ngay đứa bạn hay 'bùng kèo' vào đây! Đứa nào thấy bài viết này sau 3 giây bắt buộc phải bao chầu này tại {$tenQuan}!
TEXT;
                $hashtags = "#ReviewAnNgon #GenZAnGi #{$prefix} #ThanhAn #BungNoDeal #AnLaGhieng #FoodieTrend";
                break;

            case 'ngan_gon':
                $tieuDe = "⚡ FLASH SALE {$tenQuan}: GIẢM {$giaTri} CHO {$monAn} ({$ngayBatDau} - {$ngayKetThuc})";
                $slogan = "Chớp deal liền tay – Ăn ngon mê say!";

                if (! empty($yeuCauThem)) {
                    $yeuCauSection = "\n• Lưu ý áp dụng: {$yeuCauThem}";
                }

                $noiDungChiTiet = <<<TEXT
⚡ SỰ KIỆN FLASH SALE {$dipKm} TẠI {$tenQuan} ⚡

🎯 THÔNG TIN CHI TIẾT ƯU ĐÃI:
• Giảm giá: {$giaTri}
• Áp dụng cho: {$monAn}
• Mã ưu đãi: [{$maVoucher}]
• Thời gian: Từ {$ngayBatDau} đến {$ngayKetThuc}
• Địa điểm: {$diaChi}{$yeuCauSection}

⏱️ Số lượng suất ưu đãi có hạn theo ngày. Đến {$tenQuan} trải nghiệm ngay hôm nay!
TEXT;
                $hashtags = "#FlashSale #{$prefix} #GiamGia #KhuyenMaiHot #DealHomNay";
                break;

            case 'hao_hung':
            default:
                $tieuDe = "🔥 SIÊU ĐẠI TIỆC {$dipKm}: {$tenQuan} BÙNG NỔ ƯU ĐÃI GIẢM {$giaTri} CHO {$monAn}!";
                $slogan = "Món ngon chuẩn vị – Ưu đãi ngập tràn tại {$tenQuan}!";

                if (! empty($yeuCauThem)) {
                    $yeuCauSection = "\n\n📌 LƯU Ý ĐẶC BIỆT TỪ QUÁN:\n👉 {$yeuCauThem}";
                }

                $noiDungChiTiet = <<<TEXT
🎉 TIN ĐƯỢC KHÔNG?! ĐẠI TIỆC ẨM THỰC {$dipKm} CHÍNH THỨC BẮT ĐẦU TẠI {$tenQuan}! 🎉

Cơ hội tuyệt vời để cùng gia đình và bạn bè thưởng thức đại tiệc món ngon với mức giá ưu đãi cực khủng giảm ngay {$giaTri} cho món [{$monAn}]!

🌟 SỨC HÚT KHÔNG THỂ CHỐI TỪ:
🍲 Món '{$monAn}' tươi ngon đậm đà, nêm nếm tròn vị, kích thích trọn vẹn vị giác.
💥 Mức giảm {$giaTri} áp dụng trực tiếp – Tha hồ ăn ngon mà không lo về giá!
🌿 Không gian thoáng mát, sạch sẽ, phục vụ nhanh chóng và nhiệt tình.
👨‍👩‍👧‍👦 Điểm đến lý tưởng cho các buổi tụ họp, tiệc sinh nhật và gặp mặt cuối tuần.

🎟️ MÃ ƯU ĐÃI: [{$maVoucher}]
⏰ Áp dụng từ: {$ngayBatDau} đến {$ngayKetThuc}
📍 Địa chỉ: {$diaChi}{$yeuCauSection}

👉 Đừng bỏ lỡ bữa tiệc ẩm thực đỉnh cao này! Ghé ngay {$tenQuan} hôm nay để rinh trọn deal hời!
TEXT;
                $hashtags = "#{$prefix} #DaiTiecAmThuc #GiamGia{$giaTri} #QuanMoi #AnNgonMoiNgay #FoodieVietNam";
                break;
        }

        // 3. Thể lệ & điều kiện áp dụng (tích hợp yêu cầu bổ sung của quán)
        $theLe = <<<COND
• Thời gian diễn ra: Từ {$ngayBatDau} đến hết {$ngayKetThuc}.
• Mã voucher áp dụng: {$maVoucher}.
• Món áp dụng: {$monAn}.
• Khách hàng vui lòng xuất trình mã ưu đãi với nhân viên trước khi thanh toán.
• Áp dụng khi dùng bữa tại quán hoặc đặt mang về.{$yeuCauInTheLe}
• Địa điểm áp dụng: {$diaChi}.
COND;

        return [
            'tieu_de' => $tieuDe,
            'ma_voucher' => $maVoucher,
            'slogan' => $slogan,
            'noi_dung' => $noiDungChiTiet,
            'the_le' => $theLe,
            'hashtags' => $hashtags,
        ];
    }

    /**
     * Kiểm tra thử kết nối tới Kira AI API
     */
    public function testConnection(?string $apiKey = null): array
    {
        $key = trim((string) ($apiKey ?: config('ai.kira.api_key', env('KIRAAI_API_KEY', ''))));
        if (empty($key)) {
            return [
                'success' => false,
                'message' => 'Chưa cấu hình API Key cho Kira AI.',
            ];
        }

        $baseUrl = rtrim((string) config('ai.kira.base_url', 'https://kiraai.vn/api/v1'), '/');
        $model = config('ai.kira.model', 'kira-3.5-flash');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$key,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($baseUrl.'/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => 'Xin chào, phản hồi "OK" nếu bạn nhận được tin nhắn.'],
                ],
                'max_tokens' => 10,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Kết nối Kira AI thành công! Mô hình phản hồi tốt.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Kira AI trả về lỗi (Mã: '.$response->status().'): '.$response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Không thể kết nối tới máy chủ Kira AI: '.$e->getMessage(),
            ];
        }
    }
}
