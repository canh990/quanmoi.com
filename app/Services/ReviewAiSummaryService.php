<?php

namespace App\Services;

use App\Models\Quan;
use App\Models\QuanDanhGia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReviewAiSummaryService
{
    /**
     * Lấy bản tóm tắt & phân tích đánh giá dành riêng cho Chủ Quán
     */
    public function getOwnerSummary(Quan $quan, bool $forceRefresh = false): array
    {
        $cacheKey = "chu_quan_ai_review_summary_{$quan->id}";

        if (! $forceRefresh) {
            $cached = Cache::get($cacheKey);
            if (! empty($cached) && is_array($cached)) {
                return $cached;
            }
        }

        // Tải danh sách đánh giá mới nhất kèm tên khách hàng
        $reviews = $quan->danhGia()
            ->with('nguoiDung:id,ho_ten')
            ->latest('updated_at')
            ->take(60)
            ->get();

        $totalReviews = $reviews->count();

        // Nếu quán chưa có hoặc chỉ có 1 đánh giá -> Chưa đủ dữ liệu để AI phân tích toàn diện
        if ($totalReviews < 2) {
            return [
                'eligible' => false,
                'message' => 'Quán cần ít nhất 2 đánh giá để Kira AI có thể tổng hợp và phân tích xu hướng phục vụ.',
                'review_count' => $totalReviews,
            ];
        }

        // Thử gọi qua hệ thống LLM đa tầng (Gemini -> Groq -> OpenRouter -> OpenAI -> Kira)
        $aiResult = $this->callAiForSummary($quan, $reviews);

        if ($aiResult && ! empty($aiResult['tong_quan'])) {
            $aiResult['eligible'] = true;
            $aiResult['review_count'] = $totalReviews;
            $aiResult['analyzed_at'] = now()->format('H:i d/m/Y');
            $aiResult['source'] = 'ai';

            // Cache trong 24 giờ để tối ưu chi phí & tốc độ
            Cache::put($cacheKey, $aiResult, now()->addHours(24));

            return $aiResult;
        }

        // Smart Fallback Engine: Tự động phân tích thống kê chuyên sâu khi không có mạng hoặc chưa có key
        $fallback = $this->generateSmartFallbackSummary($quan, $reviews);
        $fallback['eligible'] = true;
        $fallback['review_count'] = $totalReviews;
        $fallback['analyzed_at'] = now()->format('H:i d/m/Y');
        $fallback['source'] = 'fallback';

        Cache::put($cacheKey, $fallback, now()->addHours(24));

        return $fallback;
    }

    /**
     * Tự động phân tích xem đánh giá là KHEN, CHÊ hay GÓP Ý và nhận diện vấn đề cụ thể
     */
    public function detectReviewIssue(QuanDanhGia $danhGia): array
    {
        $soSao = (int) $danhGia->so_sao;
        $cmt = mb_strtolower(trim((string) $danhGia->binh_luan), "UTF-8");

        if ($soSao >= 4) {
            $phanLoai = "khen";
            if (preg_match('/(nhân viên|thái độ|nhiệt tình|chu đáo|dễ thương|lịch sự|thân thiện)/u', $cmt)) {
                $vanDe = "Khen ngợi thái độ phục vụ chu đáo, nhiệt tình";
            } elseif (preg_match('/(ngon|vừa miệng|đậm đà|nêm|nước dùng|sốt|thịt|lẩu|nướng|món)/u', $cmt)) {
                $vanDe = "Khen ngợi hương vị món ăn ngon miệng & chuẩn vị";
            } elseif (preg_match('/(không gian|thoáng|mát|sạch|đẹp|chill|view|rộng)/u', $cmt)) {
                $vanDe = "Khen ngợi không gian quán thoáng đãng & thoải mái";
            } elseif (preg_match('/(nhanh|lên món|tốc độ|chờ)/u', $cmt)) {
                $vanDe = "Khen ngợi tốc độ phục vụ nhanh chóng";
            } else {
                $vanDe = "Khen ngợi và hài lòng về chất lượng quán";
            }
        } elseif ($soSao <= 2) {
            $phanLoai = "che";
            if (preg_match('/(nhân viên|thái độ|khó chịu|mặt nặng|mặt lạnh|phục vụ tệ|kém)/u', $cmt)) {
                $vanDe = "Phàn nàn về thái độ phục vụ của nhân viên";
            } elseif (preg_match('/(chờ|đợi|lâu|chậm|lên món lâu|mòn mỏi)/u', $cmt)) {
                $vanDe = "Phàn nàn về thời gian chờ đợi lên món lâu";
            } elseif (preg_match('/(dở|mặn|nhạt|nguội|dai|tanh|cháy|chua|khó ăn)/u', $cmt)) {
                $vanDe = "Phàn nàn về chất lượng & hương vị món ăn";
            } elseif (preg_match('/(xe|bãi xe|giữ xe|chật|khó dắt)/u', $cmt)) {
                $vanDe = "Phàn nàn về bãi đỗ & trông giữ xe";
            } elseif (preg_match('/(bẩn|vệ sinh|rác|ruồi|bàn dơ)/u', $cmt)) {
                $vanDe = "Phản ánh về vấn đề vệ sinh của quán";
            } else {
                $vanDe = "Phản ánh trải nghiệm chưa tốt cần khắc phục khẩn cấp";
            }
        } else {
            $phanLoai = "gop_y";
            if (preg_match('/(chờ|đợi|lâu|nhân viên|thái độ)/u', $cmt)) {
                $vanDe = "Góp ý cần cải thiện tốc độ và tác phong phục vụ";
            } elseif (preg_match('/(món|vị|nêm|mặn|nhạt)/u', $cmt)) {
                $vanDe = "Góp ý nêm nếm lại khẩu vị món ăn";
            } else {
                $vanDe = "Góp ý trải nghiệm trung tính cần quán hoàn thiện hơn";
            }
        }

        return [
            "phan_loai" => $phanLoai,
            "van_de_nhan_dien" => $vanDe,
        ];
    }

    /**
     * Tự động hiểu khen/chê và xuất ngay câu trả lời phù hợp (Đa phong cách, không trùng lặp)
     */
    public function suggestReply(Quan $quan, QuanDanhGia $danhGia, ?string $tone = null, ?int $seed = null): array
    {
        $tenKhach = $danhGia->nguoiDung?->ho_ten ?? "quý khách";
        $soSao = (int) $danhGia->so_sao;
        $binhLuan = trim((string) $danhGia->binh_luan);
        $seedVal = $seed ?? rand(1, 9999);

        // Tự động nhận diện ngữ cảnh và vấn đề khen/chê
        $detection = $this->detectReviewIssue($danhGia);
        $phanLoai = $detection["phan_loai"];
        $vanDeNhanDien = $detection["van_de_nhan_dien"];

        $systemPrompt = <<<SYS
Bạn là Trợ lý Quản lý Chăm sóc Khách hàng & Quan hệ Công chúng F&B chuyên nghiệp tại Việt Nam cho quán "{$quan->ten_quan}".
Nhiệm vụ: Tự động hiểu sâu sắc đánh giá của khách hàng (KHEN hay CHÊ, vấn đề cụ thể là gì) để tạo ra 6 câu phản hồi hoàn chỉnh, tự nhiên, chuẩn mực F&B Việt Nam theo 6 phong cách khác biệt:

6 PHONG CÁCH BẮT BUỘC (ĐA DẠNG, KHÔNG TRÙNG LẶP):
1. "lich_su": Lịch sự & Chuẩn mực (Phong thái quản lý trang trọng, nghiêm túc, đại diện thương hiệu)
2. "than_thien": Thân thiện & Ấm áp (Tình cảm, gần gũi như người nhà, xưng hô thân mật tự nhiên)
3. "tri_an": Tri ân & Đền bù (Nếu khen 4-5 sao: tri ân/tặng quà/ưu đãi lần sau; Nếu chê 1-3 sao: ngỏ ý đền bù thỏa đáng/chuộc lỗi)
4. "chuyen_sau": Trực diện vấn đề & Tinh tế (Đi thẳng vào điểm khách khen hoặc chê, giải thích hoặc cam kết chấn chỉnh cụ thể)
5. "ngan_gon": Ngắn gọn & Súc tích (Đi thẳng vào trọng tâm, cô đọng)
6. "vui_ve": Tươi vui & Hào hứng (Duyên dáng, tích cực, cầu thị)

QUY TẮC CỐT LÕI (TUYỆT ĐỐI KHÔNG TRÙNG LẶP):
- Nếu KHEN (4 - 5 sao): Cảm ơn chân thành, thể hiện niềm vui của quán, nhắc đúng món/điểm khách khen, chúc vui vẻ và hẹn gặp lại.
- Nếu CHÊ (1 - 3 sao): Chân thành xin lỗi, tuyệt đối KHÔNG bao biện hay cãi lý, tiếp thu đúng vấn đề khách chê, nêu rõ quán đã chấn chỉnh/kiểm tra ngay khâu đó, ngỏ ý mời khách quay lại để quán phục vụ chu đáo chuộc lỗi.

YÊU CẦU ĐỊNH DẠNG:
Trả về DUY NHẤT một chuỗi JSON hợp lệ (không kèm markdown ```json hay văn bản bên ngoài).
Cấu trúc JSON:
{
  "phan_loai": "{$phanLoai}",
  "van_de_nhan_dien": "Tóm tắt ngắn gọn vấn đề AI nhận diện",
  "replies": [
    {
      "tone": "lich_su",
      "label": "Lịch sự & Chuẩn mực",
      "icon": "workspace_premium",
      "reply": "Nội dung câu trả lời..."
    },
    {
      "tone": "than_thien",
      "label": "Thân thiện & Ấm áp",
      "icon": "favorite",
      "reply": "Nội dung câu trả lời..."
    },
    {
      "tone": "tri_an",
      "label": "Tri ân & Ưu đãi",
      "icon": "card_giftcard",
      "reply": "Nội dung câu trả lời..."
    },
    {
      "tone": "chuyen_sau",
      "label": "Trực diện vấn đề",
      "icon": "psychology",
      "reply": "Nội dung câu trả lời..."
    },
    {
      "tone": "ngan_gon",
      "label": "Ngắn gọn & Súc tích",
      "icon": "bolt",
      "reply": "Nội dung câu trả lời..."
    },
    {
      "tone": "vui_ve",
      "label": "Duyên dáng & Hào hứng",
      "icon": "sentiment_satisfied",
      "reply": "Nội dung câu trả lời..."
    }
  ]
}
SYS;

        $userPrompt = "Đánh giá từ khách {$tenKhach} ({$soSao}/5 sao): \"" . (! empty($binhLuan) ? $binhLuan : "Không có bình luận") . "\". Hãy tự hiểu khen/chê và xuất ra JSON 6 câu trả lời phong cách khác biệt.";

        $aiRaw = $this->callLlmFailover($systemPrompt, $userPrompt, 1100, 0.4);

        if (! empty($aiRaw)) {
            $cleaned = trim($aiRaw);
            if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $cleaned, $matches)) {
                $cleaned = trim($matches[1]);
            }
            $decoded = json_decode($cleaned, true);

            $repliesList = [];
            if (is_array($decoded)) {
                if (isset($decoded['replies']) && is_array($decoded['replies'])) {
                    $repliesList = $decoded['replies'];
                    if (! empty($decoded['van_de_nhan_dien'])) {
                        $vanDeNhanDien = $decoded['van_de_nhan_dien'];
                    }
                    if (! empty($decoded['phan_loai'])) {
                        $phanLoai = $decoded['phan_loai'];
                    }
                } elseif (isset($decoded[0]['reply'])) {
                    $repliesList = $decoded;
                }
            }

            if (! empty($repliesList) && count($repliesList) >= 2) {
                $suggestions = [];
                foreach ($repliesList as $item) {
                    if (is_array($item) && ! empty($item['reply'])) {
                        $suggestions[] = [
                            'tone' => $item['tone'] ?? 'lich_su',
                            'label' => $item['label'] ?? 'Gợi ý AI',
                            'icon' => $item['icon'] ?? 'auto_awesome',
                            'reply' => trim(trim($item['reply'], "\"'` \t\n\r\0\x0B")),
                        ];
                    }
                }

                if (! empty($suggestions)) {
                    // Mặc định tự động điền câu đầu tiên
                    $primaryReply = $suggestions[0]['reply'];
                    if ($tone) {
                        foreach ($suggestions as $s) {
                            if ($s['tone'] === $tone) {
                                $primaryReply = $s['reply'];
                                break;
                            }
                        }
                    }

                    return [
                        'success' => true,
                        'reply' => $primaryReply,
                        'phan_loai' => $phanLoai,
                        'van_de_nhan_dien' => $vanDeNhanDien,
                        'suggestions' => $suggestions,
                        'source' => 'ai',
                    ];
                }
            }
        }

        // Fallback tự động nhận diện và xuất câu trả lời đa dạng
        $fallbackSuggestions = $this->generateSmartFallbackSuggestions($quan, $danhGia, $seedVal);
        $primaryReply = $fallbackSuggestions[0]['reply'] ?? "Dạ {$quan->ten_quan} chân thành cảm ơn bạn đã gửi đánh giá ạ!";
        if ($tone) {
            foreach ($fallbackSuggestions as $s) {
                if ($s['tone'] === $tone) {
                    $primaryReply = $s['reply'];
                    break;
                }
            }
        }

        return [
            'success' => true,
            'reply' => $primaryReply,
            'phan_loai' => $phanLoai,
            'van_de_nhan_dien' => $vanDeNhanDien,
            'suggestions' => $fallbackSuggestions,
            'source' => 'fallback',
        ];
    }


    public function clearCache(string|int $quanId): void
    {
        Cache::forget("chu_quan_ai_review_summary_{$quanId}");
    }

    /**
     * Gọi LLM để tạo bản tóm tắt có cấu trúc JSON
     */
    protected function callAiForSummary(Quan $quan, Collection $reviews): ?array
    {
        $tenQuan = $quan->ten_quan;
        $loaiHinh = $quan->loai_hinh_kinh_doanh ?? 'Quán ăn';
        $tongSo = $reviews->count();
        $diemTb = round($reviews->avg('so_sao'), 1);

        // Chuẩn bị danh sách trích đoạn đánh giá nén
        $reviewLines = [];
        foreach ($reviews->take(40) as $idx => $r) {
            $sao = $r->so_sao;
            $cmt = trim((string) $r->binh_luan);
            if (empty($cmt)) {
                continue;
            }
            $cmtShort = Str::limit($cmt, 160, '...');
            $reviewLines[] = "- [{$sao} sao] {$cmtShort}";
        }

        $reviewText = implode("\n", $reviewLines);

        $systemPrompt = <<<SYS
Bạn là Giám đốc Vận hành F&B và Chuyên gia Quản trị Trải nghiệm Khách hàng (Customer Experience) hàng đầu Việt Nam.
Nhiệm vụ của bạn: Đọc toàn bộ các đánh giá thực tế từ khách hàng của quán "{$tenQuan}" ({$loaiHinh}), sau đó lập báo cáo phân tích quản trị SÚC TÍCH - THỰC CHIẾN dành riêng cho CHỦ QUÁN để giúp chủ quán cải thiện chất lượng phục vụ và tối ưu doanh thu.

YÊU CẦU BẮT BUỘC: Bạn PHẢI trả về duy nhất một chuỗi JSON hợp lệ (không kèm markdown ```json hay bất kỳ chữ nào bên ngoài).
Cấu trúc JSON chính xác như sau:
{
  "tong_quan": "Đoạn văn 2-3 câu đánh giá khách quan tình hình trải nghiệm khách hàng hiện tại, nêu rõ phong độ chung của quán",
  "ti_le_hai_long": {
    "tich_cuc": 80,
    "trung_tinh": 15,
    "can_cai_thien": 5
  },
  "diem_khen": [
    "Điểm khen cụ thể 1 của khách (về hương vị, thái độ, không gian...)",
    "Điểm khen cụ thể 2...",
    "Điểm khen cụ thể 3..."
  ],
  "diem_can_khac_phuc": [
    "Vấn đề khách phàn nàn/góp ý 1 (thời gian chờ, bãi xe, giá, thái độ...)",
    "Vấn đề khách phàn nàn/góp ý 2...",
    "Vấn đề khách phàn nàn/góp ý 3..."
  ],
  "top_mon_an": [
    "Tên món ăn hoặc thức uống được khen nhiều nhất 1",
    "Tên món ăn 2",
    "Tên món ăn 3"
  ],
  "khuyen_nghi_chuyen_gia": [
    "Hành động thực tế và khả thi chủ quán nên làm ngay 1",
    "Hành động thực tế 2",
    "Hành động thực tế 3"
  ]
}
SYS;

        $userPrompt = <<<PROMPT
Thông tin quán:
- Tên quán: {$tenQuan}
- Loại hình: {$loaiHinh}
- Tổng số đánh giá: {$tongSo} (Điểm trung bình: {$diemTb}/5)

Dưới đây là các đánh giá thực tế của thực khách:
{$reviewText}

Hãy xuất ra bản phân tích JSON chuẩn cho chủ quán.
PROMPT;

        $responseContent = $this->callLlmFailover($systemPrompt, $userPrompt, 1200, 0.3);

        if (! empty($responseContent)) {
            $cleaned = trim($responseContent);
            if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $cleaned, $matches)) {
                $cleaned = trim($matches[1]);
            }
            $decoded = json_decode($cleaned, true);
            if (is_array($decoded) && isset($decoded['tong_quan']) && isset($decoded['ti_le_hai_long'])) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Gọi chuỗi luân phiên các nhà cung cấp AI theo config/ai.php
     */
    protected function callLlmFailover(string $systemPrompt, string $userPrompt, int $maxTokens = 800, float $temperature = 0.3): ?string
    {
        $primary = config('ai.provider', 'gemini');
        $chain = config('ai.failover_chain', ['gemini', 'groq', 'openrouter', 'openai', 'kira']);
        $providers = array_values(array_unique(array_merge([$primary], $chain)));

        foreach ($providers as $provider) {
            try {
                $res = null;
                if ($provider === 'gemini') {
                    $res = $this->callGemini($systemPrompt, $userPrompt, $maxTokens, $temperature);
                } else {
                    $res = $this->callOpenAiCompatible($provider, $systemPrompt, $userPrompt, $maxTokens, $temperature);
                }

                if (! empty($res)) {
                    return $res;
                }
            } catch (\Throwable $e) {
                Log::warning("Review AI Summary: Provider {$provider} failed: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Gọi Google Gemini API
     */
    protected function callGemini(string $systemPrompt, string $userPrompt, int $maxTokens, float $temperature): ?string
    {
        $keys = config('ai.gemini.api_keys', []);
        if (empty($keys)) {
            $single = config('ai.gemini.api_key', env('GEMINI_API_KEY'));
            if (! empty($single)) {
                $keys = [$single];
            }
        }

        if (empty($keys)) {
            return null;
        }

        $models = array_merge(
            [config('ai.gemini.model', 'gemini-1.5-flash')],
            config('ai.gemini.fallback_models', ['gemini-1.5-flash-8b', 'gemini-2.0-flash-exp'])
        );
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
                'temperature' => $temperature,
                'maxOutputTokens' => $maxTokens,
            ],
        ];

        foreach ($keys as $apiKey) {
            foreach ($models as $model) {
                try {
                    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                    $response = Http::connectTimeout(4)
                        ->timeout($timeout)
                        ->post($endpoint, $payload);

                    if ($response->successful()) {
                        $text = $response->json('candidates.0.content.parts.0.text');
                        if (! empty($text)) {
                            return trim($text);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("Gemini API call failed in ReviewAiSummaryService: " . $e->getMessage());
                }
            }
        }

        return null;
    }

    /**
     * Gọi các nhà cung cấp theo chuẩn OpenAI (Groq, OpenRouter, OpenAI, Kira AI)
     */
    protected function callOpenAiCompatible(string $provider, string $systemPrompt, string $userPrompt, int $maxTokens, float $temperature): ?string
    {
        $keys = config("ai.{$provider}.api_keys", []);
        if (empty($keys)) {
            $single = config("ai.{$provider}.api_key", env(strtoupper($provider) . '_API_KEY'));
            if (! empty($single)) {
                $keys = [$single];
            }
        }

        if (empty($keys)) {
            return null;
        }

        $baseUrl = rtrim((string) config("ai.{$provider}.base_url", ''), '/');
        if (empty($baseUrl)) {
            return null;
        }

        $model = config("ai.{$provider}.model");
        $timeout = (int) config("ai.{$provider}.timeout", 15);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        foreach ($keys as $apiKey) {
            try {
                $response = Http::connectTimeout(4)
                    ->timeout($timeout)
                    ->withToken($apiKey)
                    ->post("{$baseUrl}/chat/completions", [
                        'model' => $model,
                        'messages' => $messages,
                        'temperature' => $temperature,
                        'max_tokens' => $maxTokens,
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('choices.0.message.content');
                    if (! empty($reply)) {
                        return trim($reply);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("OpenAI-compatible ({$provider}) call failed: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Smart Fallback Engine: Phân tích ngữ nghĩa & số liệu thực tế dựa trên nội dung đánh giá thật
     */
    public function generateSmartFallbackSummary(Quan $quan, Collection $reviews): array
    {
        $total = $reviews->count();
        $avgStar = round($reviews->avg('so_sao'), 1);

        $positiveReviews = $reviews->filter(fn ($r) => (int) $r->so_sao >= 4);
        $neutralReviews = $reviews->filter(fn ($r) => (int) $r->so_sao === 3);
        $negativeReviews = $reviews->filter(fn ($r) => (int) $r->so_sao <= 2);

        $positiveCount = $positiveReviews->count();
        $neutralCount = $neutralReviews->count();
        $negativeCount = $negativeReviews->count();

        $positivePercent = $total > 0 ? (int) round(($positiveCount / $total) * 100) : 80;
        $neutralPercent = $total > 0 ? (int) round(($neutralCount / $total) * 100) : 15;
        $negativePercent = max(0, 100 - $positivePercent - $neutralPercent);

        $positiveComments = mb_strtolower($positiveReviews->pluck('binh_luan')->filter()->implode(' '), 'UTF-8');
        $criticalReviews = $reviews->filter(fn ($r) => (int) $r->so_sao <= 3);
        $criticalComments = mb_strtolower($criticalReviews->pluck('binh_luan')->filter()->implode(' '), 'UTF-8');
        $allComments = mb_strtolower($reviews->pluck('binh_luan')->filter()->implode(' '), 'UTF-8');

        // ========================================================
        // 1. PHÂN TÍCH VẤN ĐỀ VỀ NHÂN VIÊN & PHỤC VỤ (Cực kỳ chính xác)
        // ========================================================
        $staffNegativeKeywords = [
            'khó chịu', 'thái độ', 'cọc', 'gắt', 'mặt quạu', 'mặt nặng', 'chảnh',
            'lồi lõm', 'hách dịch', 'coi thường', 'khinh', 'vô lễ', 'bất lịch sự',
            'kém nhiệt tình', 'không nhiệt tình', 'chưa nhiệt tình', 'phục vụ tệ',
            'phục vụ kém', 'nhân viên tệ', 'lơ là', 'lơ đễnh', 'không thèm',
        ];

        $hasStaffComplaint = false;
        foreach ($staffNegativeKeywords as $kw) {
            if (str_contains($allComments, $kw)) {
                $hasStaffComplaint = true;
                break;
            }
        }
        // Nếu review 1-3 sao có nhắc đến nhân viên hoặc phục vụ
        if (! $hasStaffComplaint && (str_contains($criticalComments, 'nhân viên') || str_contains($criticalComments, 'phục vụ') || str_contains($criticalComments, 'bảo vệ'))) {
            $hasStaffComplaint = true;
        }

        $staffPositiveKeywords = [
            'nhiệt tình', 'dễ thương', 'chu đáo', 'thân thiện', 'lịch sự',
            'nhanh nhẹn', 'vui vẻ', 'chuyên nghiệp', 'ân cần', 'lễ phép', 'ngoan',
        ];
        $hasStaffPraise = false;
        if (! $hasStaffComplaint) {
            foreach ($staffPositiveKeywords as $kw) {
                if (str_contains($positiveComments, $kw) && ! str_contains($positiveComments, 'không ' . $kw) && ! str_contains($positiveComments, 'chưa ' . $kw)) {
                    $hasStaffPraise = true;
                    break;
                }
            }
        }

        // ========================================================
        // 2. PHÂN TÍCH CÁC KHÍA CẠNH KHÁC
        // ========================================================
        // Thời gian chờ
        $hasWaitComplaint = str_contains($allComments, 'chờ lâu') || str_contains($allComments, 'đợi lâu') || str_contains($allComments, 'chậm') || str_contains($allComments, 'lâu quá') || str_contains($criticalComments, 'chờ') || str_contains($criticalComments, 'lâu');
        $hasWaitPraise = (str_contains($positiveComments, 'lên món nhanh') || str_contains($positiveComments, 'nhanh chóng') || str_contains($positiveComments, 'không phải chờ')) && ! $hasWaitComplaint;

        // Bãi xe
        $hasParkingComplaint = str_contains($criticalComments, 'xe') || str_contains($criticalComments, 'bãi') || str_contains($allComments, 'bãi xe chật') || str_contains($allComments, 'gửi xe khó');

        // Món ăn & hương vị
        $hasFoodComplaint = str_contains($criticalComments, 'mặn') || str_contains($criticalComments, 'nhạt') || str_contains($criticalComments, 'nguội') || str_contains($criticalComments, 'cay') || str_contains($criticalComments, 'tanh') || str_contains($criticalComments, 'dở') || str_contains($criticalComments, 'chán');
        $hasFoodPraise = str_contains($positiveComments, 'ngon') || str_contains($positiveComments, 'đậm đà') || str_contains($positiveComments, 'vừa miệng') || str_contains($positiveComments, 'chuẩn vị') || str_contains($positiveComments, 'tươi') || str_contains($positiveComments, 'hấp dẫn');

        // Không gian & vệ sinh
        $hasSpaceComplaint = str_contains($criticalComments, 'nóng') || str_contains($criticalComments, 'máy lạnh') || str_contains($criticalComments, 'ồn') || str_contains($criticalComments, 'bẩn') || str_contains($criticalComments, 'dơ') || str_contains($criticalComments, 'chật');
        $hasSpacePraise = str_contains($positiveComments, 'sạch') || str_contains($positiveComments, 'chill') || str_contains($positiveComments, 'không gian') || str_contains($positiveComments, 'view') || str_contains($positiveComments, 'thoáng') || str_contains($positiveComments, 'đẹp');

        // Giá cả
        $hasPriceComplaint = str_contains($criticalComments, 'đắt') || str_contains($criticalComments, 'giá cao') || str_contains($criticalComments, 'chém');
        $hasPricePraise = str_contains($positiveComments, 'giá hợp lý') || str_contains($positiveComments, 'giá rẻ') || str_contains($positiveComments, 'giá mềm') || str_contains($positiveComments, 'phải chăng') || str_contains($positiveComments, 'bình dân') || str_contains($positiveComments, 'no căng');

        // ========================================================
        // 3. TỔNG HỢP DANH SÁCH ĐIỂM KHEN (Đảm bảo chính xác tuyệt đối)
        // ========================================================
        $diemKhen = [];
        if ($hasFoodPraise) {
            $diemKhen[] = 'Hương vị món ăn được khen đậm đà, chế biến vừa miệng và hợp khẩu vị số đông.';
        }
        if ($hasStaffPraise) {
            $diemKhen[] = 'Thái độ của nhân viên phục vụ niềm nở, thân thiện và hỗ trợ khách hàng chu đáo.';
        }
        if ($hasSpacePraise) {
            $diemKhen[] = 'Không gian quán sạch sẽ, thoáng mát, tạo cảm giác thoải mái khi dùng bữa.';
        }
        if ($hasPricePraise) {
            $diemKhen[] = 'Mức giá hợp lý so với khẩu phần ăn đầy đặn, tạo ấn tượng tốt về chi phí.';
        }
        if ($hasWaitPraise) {
            $diemKhen[] = 'Tốc độ ra món nhanh nhẹn, thực khách không mất nhiều thời gian chờ đợi.';
        }
        if (empty($diemKhen)) {
            if ($positiveCount > 0) {
                $diemKhen[] = 'Chất lượng món ăn và phong cách phục vụ duy trì sự ổn định, hài lòng đa số khách.';
                $diemKhen[] = 'Quán tạo được ấn tượng tích cực ban đầu và nhận được sự ủng hộ từ thực khách.';
            } else {
                $diemKhen[] = 'Quán có tiềm năng phát triển nếu lắng nghe và cải thiện sớm các khâu vận hành hiện tại.';
            }
        }

        // ========================================================
        // 4. TỔNG HỢP DANH SÁCH ĐIỂM CẦN KHẮC PHỤC
        // ========================================================
        $diemCanKhacPhuc = [];
        if ($hasStaffComplaint) {
            $diemCanKhacPhuc[] = 'Thái độ phục vụ của nhân viên bị khách phàn nàn (khách phản ánh nhân viên có biểu hiện khó chịu, chưa niềm nở hoặc thiếu nhiệt tình).';
        }
        if ($hasWaitComplaint) {
            $diemCanKhacPhuc[] = 'Thời gian lên món vào các khung giờ cao điểm còn chậm khiến một số thực khách phải đợi lâu.';
        }
        if ($hasParkingComplaint) {
            $diemCanKhacPhuc[] = 'Khu vực bãi giữ xe có thời điểm bị chật hoặc khâu dắt xe còn bất tiện khi đông khách.';
        }
        if ($hasFoodComplaint) {
            $diemCanKhacPhuc[] = 'Độ nêm nếm gia vị đôi khi chưa đồng đều giữa các ca bếp (khách lưu ý vị cay, mặn hoặc món bị nguội).';
        }
        if ($hasSpaceComplaint) {
            $diemCanKhacPhuc[] = 'Không gian vào giờ cao điểm cần điều chỉnh nhiệt độ phòng mát mẻ hơn và giữ vệ sinh liên tục.';
        }
        if ($hasPriceComplaint) {
            $diemCanKhacPhuc[] = 'Một số khách hàng cảm thấy mức giá hơi cao so với khẩu phần món ăn nhận được.';
        }
        if (empty($diemCanKhacPhuc)) {
            if ($negativeCount > 0) {
                $diemCanKhacPhuc[] = 'Một số khách hàng mong muốn quán duy trì chất lượng dịch vụ đồng đều hơn vào giờ cao điểm.';
            } else {
                $diemCanKhacPhuc[] = 'Chưa ghi nhận phàn nàn nghiêm trọng; cần duy trì phong độ vệ sinh và tốc độ phục vụ hiện tại.';
            }
        }

        // ========================================================
        // 5. TRÍCH XUẤT TOP MÓN ĂN ĐƯỢC KHEN
        // ========================================================
        $topMonAn = [];
        try {
            $monTrongMenu = $quan->danhMucMenu()->with('monAn')->get()->pluck('monAn')->flatten();
            foreach ($monTrongMenu as $mon) {
                if ($mon && ! empty($mon->ten_mon) && str_contains($allComments, mb_strtolower($mon->ten_mon, 'UTF-8'))) {
                    $topMonAn[] = $mon->ten_mon;
                }
            }
        } catch (\Throwable $e) {}

        if (empty($topMonAn)) {
            $commonDishes = ['Lẩu', 'Nướng', 'Bò', 'Gà', 'Hải sản', 'Bún', 'Phở', 'Cơm', 'Trà sữa', 'Cà phê', 'Nem rán', 'Chả ram'];
            foreach ($commonDishes as $dish) {
                if (str_contains($allComments, mb_strtolower($dish, 'UTF-8'))) {
                    $topMonAn[] = $dish . ' đặc trưng của quán';
                }
            }
        }
        if (empty($topMonAn)) {
            $topMonAn = ['Các món đặc sản chủ đạo của quán', 'Món khai vị & Nước giải khát'];
        }
        $topMonAn = array_slice(array_unique($topMonAn), 0, 3);

        // ========================================================
        // 6. ĐỀ XUẤT HÀNH ĐỘNG TỪ CHUYÊN GIA F&B AI (Luôn có 3 - 4 đề xuất thực chiến)
        // ========================================================
        $khuyenNghi = [];

        // Nếu có phàn nàn nhân viên -> Đưa lên ưu tiên số 1
        if ($hasStaffComplaint) {
            $khuyenNghi[] = 'Họp chấn chỉnh tác phong và đào tạo lại kỹ năng giao tiếp, mỉm cười "Chào - Cảm ơn" cho toàn bộ nhân viên phục vụ bàn.';
            $khuyenNghi[] = 'Kiểm tra camera các ca trực bị phản ánh thái độ khó chịu để xác định nhân sự vi phạm và có biện pháp chấn chỉnh kịp thời.';
        }

        // Nếu có phàn nàn chờ lâu
        if ($hasWaitComplaint) {
            $khuyenNghi[] = 'Chuẩn bị khâu sơ chế nguyên liệu trước 17h30 và bố trí quy trình bếp ra món song song để giảm thời gian khách chờ.';
        }

        // Nếu có phàn nàn bãi xe
        if ($hasParkingComplaint) {
            $khuyenNghi[] = 'Bố trí nhân sự hướng dẫn xếp xe ngăn nắp hoặc liên kết thêm điểm gửi xe phụ gần quán vào cuối tuần.';
        }

        // Nếu có phàn nàn món ăn
        if ($hasFoodComplaint) {
            $khuyenNghi[] = 'Chuẩn hóa định lượng công thức nêm nếm giữa các ca bếp và kiểm soát nhiệt độ món ăn trước khi bưng ra.';
        }

        // Đề xuất chăm sóc khách hàng (luôn có)
        $khuyenNghi[] = 'Chủ động sử dụng tính năng "AI Gợi ý trả lời" để phản hồi và xoa dịu các đánh giá chưa hài lòng, thể hiện sự cầu thị của quán.';

        // Đề xuất marketing/tăng doanh thu (luôn có)
        $khuyenNghi[] = 'Tận dụng các món được khen nhiều để tạo chương trình combo ưu đãi giờ vàng hoặc làm bài quảng bá thu hút khách mới.';

        // Lấy 3 đến 4 khuyến nghị chất lượng nhất
        $khuyenNghi = array_slice(array_values(array_unique($khuyenNghi)), 0, 4);

        // ========================================================
        // 7. TỔNG KẾT VẬN HÀNH DÀNH CHO CHỦ QUÁN
        // ========================================================
        if ($hasStaffComplaint) {
            $tongQuan = "Quán {$quan->ten_quan} đang nhận được phản ánh cần chấn chỉnh gấp về thái độ phục vụ của nhân viên (khách phản ánh nhân viên có biểu hiện khó chịu, chưa niềm nở). Chủ quán cần tập trung cải thiện khâu nhân sự ngay để bảo vệ uy tín và điểm đánh giá của quán.";
        } elseif ($hasWaitComplaint) {
            $tongQuan = "Quán {$quan->ten_quan} thu hút khách nhờ món ăn ngon nhưng thời gian phục vụ vào giờ cao điểm bị phàn nàn nhiều do phải đợi lâu. Cần tối ưu lại khâu ra món của bếp để nâng cao trải nghiệm trọn vẹn hơn.";
        } elseif ($avgStar >= 4.2) {
            $tongQuan = "Quán {$quan->ten_quan} đang giữ phong độ vận hành xuất sắc với {$avgStar}/5 sao. Đa số thực khách hài lòng về hương vị và phong cách phục vụ, đây là lợi thế lớn để đẩy mạnh quảng bá thương hiệu.";
        } elseif ($avgStar >= 3.5) {
            $tongQuan = "Quán {$quan->ten_quan} đạt mức hài lòng khá ({$avgStar}/5 sao). Món ăn được đón nhận tốt nhưng cần chú trọng khắc phục khâu phục vụ và tiện ích đi kèm để mang lại trải nghiệm hoàn hảo cho khách.";
        } else {
            $tongQuan = "Quán {$quan->ten_quan} hiện đạt {$avgStar}/5 sao với nhiều góp ý từ khách hàng. Chủ quán cần rà soát lại ngay quy trình phục vụ và chất lượng món ăn để nhanh chóng cải thiện điểm đánh giá.";
        }

        return [
            'tong_quan' => $tongQuan,
            'ti_le_hai_long' => [
                'tich_cuc' => $positivePercent,
                'trung_tinh' => $neutralPercent,
                'can_cai_thien' => $negativePercent,
            ],
            'diem_khen' => $diemKhen,
            'diem_can_khac_phuc' => $diemCanKhacPhuc,
            'top_mon_an' => $topMonAn,
            'khuyen_nghi_chuyen_gia' => $khuyenNghi,
        ];
    }

    /**
     * Tạo 6 phương án phản hồi dự phòng thông minh theo 6 phong cách F&B (Tránh trùng lặp)
     */
    public function generateSmartFallbackSuggestions(Quan $quan, QuanDanhGia $danhGia, ?int $seed = null): array
    {
        $tenKhach = $danhGia->nguoiDung?->ho_ten ?? 'quý khách';
        $soSao = (int) $danhGia->so_sao;
        $binhLuan = trim((string) $danhGia->binh_luan);
        $tenQuan = $quan->ten_quan;
        $offset = abs((int) ($danhGia->id ?? 1) + (int) ($seed ?? 0));

        if ($soSao >= 4) {
            $lichSuPool = [
                "Dạ {$tenQuan} chân thành cảm ơn {$tenKhach} đã dành thời gian gửi gắm đánh giá quý báu và ủng hộ quán ạ! Sự hài lòng của bạn là niềm vinh hạnh to lớn cho toàn thể đội ngũ. Kính chúc bạn luôn nhiều niềm vui và rất mong sớm được tiếp tục đón tiếp bạn ạ!",
                "Dạ kính chào {$tenKhach}, thay mặt tập thể {$tenQuan}, quán xin gửi lời cảm ơn và tri ân chân thành nhất tới bạn vì trải nghiệm tuyệt vời vừa qua. Những nhận xét tích cực từ bạn chính là động lực to lớn để quán nâng cao chất lượng mỗi ngày. Kính chúc bạn vạn sự như ý!",
                "Dạ {$tenQuan} rất vinh hạnh được đón tiếp và phục vụ {$tenKhach}! Cảm ơn bạn đã luôn tin tưởng trao gửi bữa ăn của mình cho quán. Chúng mình sẽ tiếp tục giữ vững phong độ để mang đến cho bạn những trải nghiệm trọn vẹn nhất trong những lần ghé tới ạ!",
            ];

            $thanThienPool = [
                "Dạ chào {$tenKhach} iu quý! Cả quán ai đọc được nhận xét này cũng vui cả ngày luôn ạ 🥰. Cảm ơn bạn đã yêu thích món ngon của quán. Lần tới ghé nhớ bảo các bạn nhân viên để tụi mình chăm sóc bạn thật chu đáo nha!",
                "Dạ nhận được 5 sao từ {$tenKhach} làm cả bếp lẫn bàn như được tiếp thêm 200% năng lượng vậy ạ! Tụi mình cảm ơn bạn nhiều thiệt nhiều. Lần sau ghé quán cứ tự nhiên như ở nhà bạn nhé ❤️",
                "Hế lô {$tenKhach} thân thương! Cảm ơn bạn đã ghé quán và dành trọn tình cảm cho {$tenQuan} nha. Tụi mình đã lưu lại bàn quen của bạn rồi nè, nhớ ghé thăm quán thường xuyên nha!",
            ];

            $triAnPool = [
                "Dạ cảm ơn {$tenKhach} rất nhiều vì đã ghé và để lại đánh giá tích cực! Quán luôn có những ưu đãi và món mới dành riêng cho khách quen, lần tới ghé trải nghiệm bạn nhớ nhắn quán để nhận thêm quà bất ngờ nhé! ✨",
                "Dạ {$tenQuan} xin gửi tặng bạn lời tri ân sâu sắc! Để đáp lại sự yêu mến của {$tenKhach}, quán xin phép được dành tặng ưu đãi đặc biệt trong lần ghé tiếp theo. Hẹn sớm gặp lại bạn tại quán nhé!",
                "Cảm ơn {$tenKhach} đã trở thành một phần đặc biệt của {$tenQuan}! Lần tới ghé dùng bữa, bạn nhớ nhắc các bạn phục vụ để quán mời bạn một phần tráng miệng tri ân nhé 🎁",
            ];

            $nganGonPool = [
                "Dạ {$tenQuan} chân thành cảm ơn {$tenKhach} đã ủng hộ và khen ngợi quán ạ! Chúc bạn luôn ngon miệng và hẹn sớm gặp lại nhé!",
                "Dạ quán xin cảm ơn {$tenKhach} rất nhiều ạ! Chúc bạn ngày mới tràn đầy niềm vui và mong sớm đón tiếp bạn quay lại!",
                "Dạ {$tenQuan} cảm ơn bạn {$tenKhach} iu quý! Chúc bạn có những bữa ăn thật trọn vẹn và ngon miệng mỗi ngày ạ ✨",
            ];

            $chuyenSauPool = [
                ! empty($binhLuan)
                    ? "Dạ {$tenQuan} rất hạnh phúc khi biết bạn {$tenKhach} hài lòng với trải nghiệm vừa rồi: \"{$binhLuan}\". Quán sẽ luôn giữ gìn hương vị và phong độ phục vụ này để đón bạn lần tới ạ!"
                    : "Dạ {$tenQuan} chân thành cảm ơn trải nghiệm trọn vẹn 5 sao từ {$tenKhach}. Đội ngũ bếp và phục vụ cam kết luôn duy trì chất lượng chuẩn mực nhất cho bạn!",
                "Dạ từng lời khen của {$tenKhach} đều là kim chỉ nam để quán hoàn thiện hơn. Bếp trưởng và đội ngũ phục vụ đã gửi lời cảm ơn riêng tới bạn, hẹn gặp lại bạn trong chuyến ẩm thực tiếp theo!",
                "Dạ {$tenQuan} rất vui vì đã đem đến cho {$tenKhach} một bữa ăn vừa miệng và thoải mái. Quán đang cập nhật thêm nhiều món mới hấp dẫn, mong bạn sẽ sớm quay lại trải nghiệm!",
            ];

            $vuiVePool = [
                "Team {$tenQuan} đọc xong review của {$tenKhach} là mỉm cười cả ngày luôn á! Cảm ơn bạn nhiều nha, hẹn lần tới chúng mình lại 'quẩy' món ngon cùng nhau nhé! 🎉",
                "Bí quyết để một ngày làm việc của quán ngập tràn tiếng cười chính là những review dễ thương như của {$tenKhach} đó ạ! Quán cảm ơn bạn x1000 lần luôn nè 🥰",
                "Dạ khách khen ngon 1 thì quán vui 10, mà khách khen 5 sao thì quán vui cả tháng luôn ạ! Cảm ơn bạn {$tenKhach} siêu đáng yêu, hẹn sớm gặp lại bạn nha!",
            ];

            return [
                [
                    'tone' => 'lich_su',
                    'label' => 'Lịch sự & Chuẩn mực',
                    'icon' => 'workspace_premium',
                    'reply' => $lichSuPool[$offset % count($lichSuPool)],
                ],
                [
                    'tone' => 'than_thien',
                    'label' => 'Thân thiện & Ấm áp',
                    'icon' => 'favorite',
                    'reply' => $thanThienPool[($offset + 1) % count($thanThienPool)],
                ],
                [
                    'tone' => 'tri_an',
                    'label' => 'Tri ân & Ưu đãi',
                    'icon' => 'card_giftcard',
                    'reply' => $triAnPool[($offset + 2) % count($triAnPool)],
                ],
                [
                    'tone' => 'ngan_gon',
                    'label' => 'Ngắn gọn & Tinh tế',
                    'icon' => 'bolt',
                    'reply' => $nganGonPool[($offset + 3) % count($nganGonPool)],
                ],
                [
                    'tone' => 'chuyen_sau',
                    'label' => 'Chi tiết & Điểm nhấn',
                    'icon' => 'psychology',
                    'reply' => $chuyenSauPool[($offset + 4) % count($chuyenSauPool)],
                ],
                [
                    'tone' => 'vui_ve',
                    'label' => 'Vui tươi & Duyên dáng',
                    'icon' => 'sentiment_satisfied',
                    'reply' => $vuiVePool[($offset + 5) % count($vuiVePool)],
                ],
            ];
        }

        if ($soSao === 3) {
            $lichSuPool = [
                "Dạ chào {$tenKhach}, {$tenQuan} rất cảm ơn bạn đã ghé thăm và gửi lại những chia sẻ thực tế. Quán xin ghi nhận nghiêm túc các điểm chưa làm bạn thật sự hài lòng để cải thiện quy trình phục vụ và chất lượng món ăn. Rất mong sẽ có cơ hội được đón tiếp bạn chu đáo hơn trong lần tới ạ!",
                "Dạ kính chào {$tenKhach}, {$tenQuan} xin chân thành cảm ơn những nhận xét khách quan của bạn. Đây là cơ sở quý giá giúp ban quản lý quán rà soát lại tiêu chuẩn phục vụ. Kính chúc bạn nhiều sức khỏe và mong sớm có dịp đón bạn ghé lại!",
                "Dạ {$tenQuan} rất trân trọng đóng góp từ {$tenKhach}. Quán hiểu rằng trải nghiệm lần này vẫn còn những thiếu sót cần khắc phục. Đội ngũ quán xin tiếp thu để nâng tầm dịch vụ tốt hơn nữa trong thời gian tới ạ.",
            ];

            $thanThienPool = [
                "Dạ chào bạn {$tenKhach}, tụi mình rất tiếc khi trải nghiệm lần này của bạn chưa được trọn vẹn 100%. Quán đã ngồi lại cùng nhau để rút kinh nghiệm ngay. Mong bạn tiếp tục ủng hộ để lần tới tụi mình phục vụ bạn chu đáo hơn nha!",
                "Dạ chào {$tenKhach} nè, cảm ơn bạn đã chân thành chia sẻ với quán nha. Tụi mình biết quán vẫn còn điểm cần cải thiện, và tụi mình đang nỗ lực từng ngày để lần sau bạn ghé sẽ thấy quán hoàn thiện hơn nhiều đó ạ!",
                "Dạ tụi mình đã đọc kỹ từng dòng nhận xét của {$tenKhach} rồi ạ. Rất mong bạn thông cảm cho những điểm chưa vừa ý lần này. Cho tụi mình thêm một cơ hội để chuộc lỗi với bạn trong lần tới nhé ❤️",
            ];

            $triAnPool = [
                "Dạ {$tenQuan} chân thành cảm ơn đóng góp quý báu từ {$tenKhach}! Quán đang nâng cấp khâu phục vụ từng ngày. Rất hy vọng bạn sẽ cho quán cơ hội được phục vụ bạn một bữa ăn thật trọn vẹn và vừa ý trong lần tới ạ!",
                "Dạ cảm ơn {$tenKhach} đã thẳng thắn góp ý. Quán xin phép được gửi lời mời bạn quay lại quán trong thời gian tới để trải nghiệm những cải tiến mới nhất cùng ưu đãi tri ân từ quán ạ!",
                "Dạ đóng góp của {$tenKhach} giúp quán nhìn nhận lại khâu vận hành thực tế. Lần tới ghé dùng bữa, bạn nhớ báo quán để tụi mình sắp xếp bàn và chuẩn bị món thật chỉn chu cho bạn nhé!",
            ];

            $nganGonPool = [
                "Dạ {$tenQuan} cảm ơn góp ý của {$tenKhach} ạ! Quán xin ghi nhận và khắc phục ngay để phục vụ bạn tốt hơn trong lần tới.",
                "Dạ quán xin chân thành tiếp thu chia sẻ của {$tenKhach} để nâng cao chất lượng phục vụ và món ăn mỗi ngày ạ!",
                "Dạ {$tenQuan} cảm ơn bạn {$tenKhach} đã ghé thăm và góp ý chân thành. Chúc bạn một ngày tốt lành và hẹn gặp lại bạn!",
            ];

            $chuyenSauPool = [
                ! empty($binhLuan)
                    ? "Dạ quán đã chuyển trực tiếp phản hồi: \"{$binhLuan}\" của {$tenKhach} tới bộ phận phụ trách để chấn chỉnh ngay. Cảm ơn bạn đã giúp quán hoàn thiện hơn mỗi ngày!"
                    : "Dạ ban quản lý quán đã ghi nhận chi tiết để kiểm tra lại quy trình làm việc giữa các ca trực. Mong sẽ sớm được đón bạn quay lại với diện mạo mới mẻ hơn!",
                "Dạ từng ý kiến đóng góp của bạn đều được ban quản lý ghi nhận vào sổ nhật trình cải tiến chất lượng. Rất mong được đón tiếp bạn trong một dịp gần nhất!",
                "Dạ {$tenQuan} cam kết kiểm soát chặt chẽ hơn nữa từ khâu nêm nếm đến tốc độ ra món để khách hàng như {$tenKhach} luôn cảm thấy hài lòng nhất khi ghé quán.",
            ];

            $vuiVePool = [
                "Dạ dù lần này mới đạt được 3 sao nhưng team {$tenQuan} sẽ quyết tâm nâng cấp lên 5 sao tròn trĩnh trong lòng {$tenKhach} ở lần gặp tới nha! Cảm ơn bạn nhiều ạ!",
                "Dạ cảm ơn bạn {$tenKhach} đã chỉ ra những điểm quán cần tiến bộ. Quán sẽ 'nâng cấp' ngay và hẹn bạn một ngày không xa để chứng minh sự thay đổi nhé ✨",
                "Dạ 3 sao là lời nhắc nhở quý giá cho tụi mình. Cảm ơn {$tenKhach} đã đồng hành cùng quán, hẹn sớm gặp lại bạn với phiên bản quán xịn xò hơn nhé!",
            ];

            return [
                [
                    'tone' => 'lich_su',
                    'label' => 'Cầu thị & Tiếp thu',
                    'icon' => 'workspace_premium',
                    'reply' => $lichSuPool[$offset % count($lichSuPool)],
                ],
                [
                    'tone' => 'than_thien',
                    'label' => 'Thân thiện & Lắng nghe',
                    'icon' => 'favorite',
                    'reply' => $thanThienPool[($offset + 1) % count($thanThienPool)],
                ],
                [
                    'tone' => 'tri_an',
                    'label' => 'Khắc phục & Mời trải nghiệm',
                    'icon' => 'card_giftcard',
                    'reply' => $triAnPool[($offset + 2) % count($triAnPool)],
                ],
                [
                    'tone' => 'ngan_gon',
                    'label' => 'Ngắn gọn & Tiếp thu',
                    'icon' => 'bolt',
                    'reply' => $nganGonPool[($offset + 3) % count($nganGonPool)],
                ],
                [
                    'tone' => 'chuyen_sau',
                    'label' => 'Chi tiết & Hành động',
                    'icon' => 'psychology',
                    'reply' => $chuyenSauPool[($offset + 4) % count($chuyenSauPool)],
                ],
                [
                    'tone' => 'vui_ve',
                    'label' => 'Tích cực & Cầu thị',
                    'icon' => 'sentiment_satisfied',
                    'reply' => $vuiVePool[($offset + 5) % count($vuiVePool)],
                ],
            ];
        }

        // 1 - 2 sao
        $lichSuPool = [
            "Dạ kính chào {$tenKhach}, {$tenQuan} xin chân thành gửi lời xin lỗi sâu sắc tới bạn vì trải nghiệm chưa tốt trong lần ghé quán vừa qua ạ. Quán hoàn toàn nhận trách nhiệm và đã làm việc khẩn cấp với các bộ phận liên quan để chấn chỉnh, kiên quyết không để sự việc lặp lại. Rất mong nhận được sự lượng thứ từ bạn ạ.",
            "Dạ kính chào {$tenKhach}, thay mặt ban quản lý {$tenQuan}, quán xin cúi đầu nhận lỗi và thành thật xin lỗi vì đã không thể đem lại cho bạn một bữa ăn như kỳ vọng. Chúng mình đã tiến hành họp chấn chỉnh toàn bộ ca trực và rà soát lại quy trình. Mong bạn rộng lòng bỏ qua cho thiếu sót lần này của quán ạ.",
            "Dạ chào {$tenKhach}, {$tenQuan} xin chân thành xin lỗi vì sự bất tiện và cảm xúc không vui mà bạn đã gặp phải. Chúng mình trân trọng tiếp thu mọi ý kiến phàn nàn và cam kết khắc phục dứt điểm ngay lập tức ạ.",
        ];

        $thanThienPool = [
            "Dạ chào bạn {$tenKhach}, tụi mình thật sự rất buồn và áy náy khi biết bạn chưa hài lòng. Ban quản lý quán đã trực tiếp kiểm tra lại toàn bộ ca trực hôm đó để xử lý dứt điểm. Quán chân thành tiếp thu và xin lỗi bạn rất nhiều ạ!",
            "Dạ nghe bạn {$tenKhach} chia sẻ mà tụi mình thấy có lỗi vô cùng ạ. Tụi mình không muốn bao biện bất cứ điều gì, chỉ mong được gửi lời xin lỗi chân thành nhất đến bạn. Mong bạn cho tụi mình một cơ hội được phục vụ bạn chu đáo hơn nha!",
            "Dạ {$tenKhach} ơi, tụi mình xin lỗi bạn nhiều lắm ạ. Sự việc vừa rồi là sơ sót rất đáng tiếc của quán. Tụi mình đã chấn chỉnh lại nhân viên và quy trình rồi, mong bạn đừng giận quán nữa nha!",
        ];

        $triAnPool = [
            "Dạ chào {$tenKhach}, {$tenQuan} vô cùng lấy làm tiếc về sơ sót lần này. Quán rất mong có thể liên hệ trực tiếp với bạn để xin lỗi và gửi tặng một ưu đãi/voucher chuộc lỗi. Rất mong bạn rộng lòng cho quán thêm cơ hội để chứng minh sự thay đổi tích cực ạ!",
            "Dạ {$tenQuan} xin phép được đền bù thỏa đáng cho trải nghiệm chưa vui lần này của {$tenKhach}. Quán rất mong bạn có thể nhắn tin riêng hoặc gọi cho quán để tụi mình gửi tặng bữa ăn chuộc lỗi thay lời xin lỗi chân thành nhất ạ!",
            "Dạ để chuộc lại lỗi lầm với {$tenKhach}, quán xin phép được mời bạn và người thân ghé lại dùng bữa miễn phí hoặc nhận ưu đãi đặc biệt trong lần tới. Rất mong bạn cho quán cơ hội phục vụ bạn chu đáo hơn!",
        ];

        $nganGonPool = [
            "Dạ {$tenQuan} thành thật xin lỗi {$tenKhach} về trải nghiệm không hài lòng vừa rồi ạ. Quán đã tiếp thu và chấn chỉnh ngay khâu phục vụ/chế biến.",
            "Dạ quán xin chân thành nhận lỗi và xin lỗi {$tenKhach}. Chúng mình cam kết khắc phục ngay và không để sự việc tái diễn.",
            "Dạ {$tenQuan} xin cúi đầu nhận lỗi cùng bạn {$tenKhach}. Chúc bạn luôn bình an và mong bạn lượng thứ cho sơ suất của quán ạ.",
        ];

        $chuyenSauPool = [
            ! empty($binhLuan)
                ? "Dạ ban quản lý quán đã làm việc trực tiếp với ca trực liên quan về vấn đề: \"{$binhLuan}\" mà {$tenKhach} phản ánh. Quán đã có biện pháp xử lý và đào tạo lại nhân sự ngay hôm nay ạ."
                : "Dạ ban quản lý quán đã lập tức trích xuất dữ liệu và kiểm tra lại toàn bộ quy trình chế biến, phục vụ để đảm bảo tiêu chuẩn an toàn và thái độ tốt nhất.",
            "Dạ quán xin cam kết xử lý dứt điểm vấn đề này. Từng khâu từ bảo quản nguyên liệu đến thái độ giao tiếp của nhân viên đều đã được chấn chỉnh nghiêm túc.",
            "Dạ {$tenQuan} xin ghi nhận phản ánh của {$tenKhach} như một bài học lớn trong khâu quản lý vận hành. Quán sẽ giám sát chặt chẽ hơn để mang lại dịch vụ hoàn hảo nhất.",
        ];

        $vuiVePool = [
            "Dạ chào {$tenKhach}, quán rất mong có thể trao đổi trực tiếp qua điện thoại hoặc Zalo để lắng nghe chi tiết và giải quyết thỏa đáng nhất cho bạn. Bạn có thể để lại tin nhắn hoặc gọi cho hotline quán bất cứ lúc nào ạ!",
            "Dạ quản lý quán rất mong được kết nối trực tiếp với {$tenKhach} qua số điện thoại của quán để lắng nghe và chăm sóc bạn chu đáo hơn ạ. Xin chân thành cảm ơn bạn!",
            "Dạ {$tenQuan} luôn sẵn sàng lắng nghe mọi góp ý từ bạn qua tin nhắn riêng để có cơ hội được hỗ trợ và chuộc lỗi trọn vẹn nhất với bạn ạ!",
        ];

        return [
            [
                'tone' => 'lich_su',
                'label' => 'Xin lỗi & Cầu thị',
                'icon' => 'workspace_premium',
                'reply' => $lichSuPool[$offset % count($lichSuPool)],
            ],
            [
                'tone' => 'than_thien',
                'label' => 'Chân thành & Nhận lỗi',
                'icon' => 'favorite',
                'reply' => $thanThienPool[($offset + 1) % count($thanThienPool)],
            ],
            [
                'tone' => 'tri_an',
                'label' => 'Chuộc lỗi & Đền bù',
                'icon' => 'card_giftcard',
                'reply' => $triAnPool[($offset + 2) % count($triAnPool)],
            ],
            [
                'tone' => 'ngan_gon',
                'label' => 'Ngắn gọn & Nghiêm túc',
                'icon' => 'bolt',
                'reply' => $nganGonPool[($offset + 3) % count($nganGonPool)],
            ],
            [
                'tone' => 'chuyen_sau',
                'label' => 'Cam kết xử lý',
                'icon' => 'psychology',
                'reply' => $chuyenSauPool[($offset + 4) % count($chuyenSauPool)],
            ],
            [
                'tone' => 'vui_ve',
                'label' => 'Kết nối hỗ trợ',
                'icon' => 'support_agent',
                'reply' => $vuiVePool[($offset + 5) % count($vuiVePool)],
            ],
        ];
    }

    /**
     * Tạo câu phản hồi dự phòng thông minh cho từng đánh giá
     */
    protected function generateSmartFallbackReply(Quan $quan, QuanDanhGia $danhGia): string
    {
        $suggestions = $this->generateSmartFallbackSuggestions($quan, $danhGia);
        return $suggestions[0]['reply'] ?? "Dạ {$quan->ten_quan} chân thành cảm ơn bạn đã đánh giá ạ!";
    }
}
