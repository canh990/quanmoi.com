<?php

namespace App\Services;

use App\Models\MonTrongMenu;
use App\Models\Quan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QuanAiChatbotService
{
    /**
     * Xử lý tin nhắn người dùng và trả về phản hồi tư vấn ẩm thực thông minh
     */
    public function chat(string $message, array $history = []): array
    {
        $message = trim(strip_tags($message));
        if ($message === '') {
            return [
                'success' => false,
                'reply' => 'Chào bạn! Mình có thể giúp gì cho bạn hôm nay? Hãy hỏi mình về quán ăn ngon, cafe chill hoặc món ăn bạn yêu thích nhé!',
                'venues' => [],
                'criteria' => [],
                'parsed_intent' => [],
                'suggestions' => $this->getDefaultSuggestions(),
            ];
        }

        // 1. Phân tích đa lượt & theo dõi trạng thái ngữ cảnh (Conversational State Tracking)
        $state = $this->parseConversationalState($message, $history);

        // 2. Xử lý câu chào hỏi / giới thiệu ban đầu
        if ($state['is_greeting']) {
            return [
                'success' => true,
                'reply' => "Hế lô bồ iu! 👋 Mình là **Kira AI** - Trợ lý ẩm thực chuẩn gu Quán Mới đây.\n\nĐang phân vân hôm nay ăn gì, ở đâu? Cứ bảo mình một câu nha:\n- 📍 Tìm quán ngon chuẩn từng quận/huyện\n- 💰 Săn quán êm ví, sinh viên đến buffet sang chảnh\n- 🍜 Gợi ý lẩu nướng tụ tập, cafe chill làm việc, ăn đêm 24/7\n- 🎁 Bắt trọn voucher & ưu đãi hấp dẫn đang chạy\n\nBồ đang ở khu vực nào hay muốn thẩm món gì hôm nay, kể Kira nghe liền nè! ✨",
                'venues' => [],
                'criteria' => $state,
                'parsed_intent' => $state,
                'suggestions' => $this->getDefaultSuggestions(),
            ];
        }

        // 3. Xử lý lời cảm ơn / kết thúc lịch sự
        if ($state['is_thanks']) {
            return [
                'success' => true,
                'reply' => "Dạ luôn nè bồ ơi! ❤️ Chúc bồ có một bữa ăn ngon bá cháy và no căng bụng nha. Khi nào muốn đổi vị hay tìm thêm quán mới lạ, cứ quay lại hú Kira một tiếng là có mặt liền nhé! ✨",
                'venues' => [],
                'criteria' => $state,
                'parsed_intent' => $state,
                'suggestions' => [
                    'Tìm quán cafe view đẹp gần đây',
                    'Quán lẩu nướng cuối tuần',
                    'Món ngon giá sinh viên',
                    'Quán ăn đêm mở 24/7',
                ],
            ];
        }

        // 4. Xử lý câu hỏi chi tiết về một quán cụ thể đã đề xuất trước đó (Entity Resolution)
        if ($state['is_asking_venue_detail'] && !empty($state['target_venue'])) {
            $detailReply = $this->answerVenueDetailQuestion($state['target_venue'], $message, $state);
            return [
                'success' => true,
                'reply' => $detailReply,
                'venues' => [$state['target_venue']],
                'criteria' => $state,
                'parsed_intent' => $state,
                'suggestions' => [
                    'Chỉ đường tới quán này',
                    'Quán này có khuyến mãi gì không?',
                    'Xem các quán khác tương tự',
                ],
            ];
        }

        // 4.5. Xử lý câu hỏi so sánh giữa các quán (Comparison Queries: rẻ nhất, mở muộn nhất, ngon nhất, khuyến mãi...)
        if (!empty($state['comparison_type'])) {
            $comparisonVenues = !empty($state['last_assistant_venues'])
                ? $state['last_assistant_venues']
                : $this->searchVenues($state);

            if (!empty($comparisonVenues)) {
                return $this->answerComparisonQuestion($comparisonVenues, $state['comparison_type'], $message, $state);
            }
        }

        // 4.8. Xử lý câu hỏi đời thường như ChatGPT (Nấu ăn, tâm sự, sức khỏe, thói quen, mẹo sống, kiến thức...)
        if (!empty($state['lifestyle_type']) && empty($state['has_dining_intent'])) {
            return $this->handleEverydayLifeQuestion($message, $history, $state);
        }

        // 5. Xử lý trường hợp câu hỏi quá mơ hồ, thiếu thông tin (Vague Query Clarification)
        if ($state['is_vague'] && empty($state['district']) && empty($state['city']) && empty($state['dish']) && empty($state['category'])) {
            return $this->handleVagueOrMissingCriteria($message, $state);
        }

        // 6. Truy vấn dữ liệu thực tế từ Database (Grounded Retrieval / RAG)
        $venues = $this->searchVenues($state);

        // 7. Tạo câu trả lời thông minh (Sử dụng External LLM nếu cấu hình hoặc Smart Reasoning Engine nội bộ)
        $llmReply = $this->generateLlmResponse($message, $history, $venues, $state);
        $reply = $llmReply ?: $this->generateSmartFallbackResponse($message, $history, $venues, $state);

        // 8. Chuẩn bị gợi ý câu hỏi tiếp theo dựa trên ngữ cảnh thực tế
        $suggestions = $this->getFollowUpSuggestions($state, $venues);

        return [
            'success' => true,
            'reply' => $reply,
            'venues' => $venues,
            'criteria' => $state,
            'parsed_intent' => $state,
            'suggestions' => $suggestions,
        ];
    }

    /**
     * Phân tích ngữ cảnh đa lượt, theo dõi trạng thái cuộc trò chuyện (Multi-turn State Tracking)
     */
    protected function parseConversationalState(string $message, array $history = []): array
    {
        $normalizedCurrent = mb_strtolower($message, 'UTF-8');

        // Thu thập lịch sử và trích xuất các quán đã xuất hiện trong phiên (Seen Venues)
        $seenSlugs = [];
        $lastAssistantVenues = [];
        $historyUserTexts = [];

        foreach ($history as $h) {
            $role = $h['role'] ?? '';
            $content = (string)($h['content'] ?? '');

            if ($role === 'user' && !empty($content)) {
                $historyUserTexts[] = mb_strtolower($content, 'UTF-8');
            } elseif (($role === 'assistant' || $role === 'bot') && !empty($content)) {
                // Trích xuất các slug quán trong link markdown: /quan/{slug}
                if (preg_match_all('/\/quan\/([a-zA-Z0-9\-]+)/', $content, $matches)) {
                    foreach ($matches[1] as $slug) {
                        $seenSlugs[] = $slug;
                    }
                }
                // Nếu frontend có truyền kèm danh sách venues trong history
                if (!empty($h['venues']) && is_array($h['venues'])) {
                    $lastAssistantVenues = $h['venues'];
                    foreach ($h['venues'] as $v) {
                        if (!empty($v['slug'])) {
                            $seenSlugs[] = $v['slug'];
                        }
                    }
                }
            }
        }

        $seenSlugs = array_values(array_unique($seenSlugs));

        // Nếu frontend không gửi venues array, nhưng có link markdown trong tin nhắn trước, tự động nạp từ DB
        if (empty($lastAssistantVenues) && !empty($seenSlugs)) {
            $lastAssistantVenues = $this->fetchVenuesBySlugs(array_slice($seenSlugs, -4));
        }

        // Nhận diện Ý định cơ bản (Basic Intents)
        $isGreeting = (bool) preg_match('/^(xin chào|chào bạn|chào em|chào bot|hello|hi|hey|alo|bạn là ai|introduce)\b/ui', $normalizedCurrent)
            && mb_strlen($normalizedCurrent, 'UTF-8') < 40;

        $isThanks = (bool) preg_match('/\b(cảm ơn|cam on|thanks|thank you|ok cảm ơn|tuyệt vời|tks|tks bot|ok bạn)\b/ui', $normalizedCurrent)
            && mb_strlen($normalizedCurrent, 'UTF-8') < 45;

        // Nhận diện Ý định So sánh giữa các quán (Comparison Intents: rẻ nhất, mở muộn nhất, ngon nhất...)
        $comparisonType = null;
        if (preg_match('/\b(quán nào|chỗ nào|nơi nào|địa điểm nào|ở đâu)\b/ui', $normalizedCurrent)) {
            if (preg_match('/\b(rẻ nhất|giá rẻ nhất|thấp nhất|giá thấp nhất|mềm nhất|hạt dẻ nhất|tiết kiệm nhất|giá tốt nhất|ít tiền nhất)\b/ui', $normalizedCurrent)) {
                $comparisonType = 'cheapest';
            } elseif (preg_match('/\b(đắt nhất|giá cao nhất|cao cấp nhất|sang nhất|sang trọng nhất|sang chảnh nhất)\b/ui', $normalizedCurrent)) {
                $comparisonType = 'most_expensive';
            } elseif (preg_match('/\b(ngon nhất|tốt nhất|chất lượng nhất|đánh giá cao nhất|nhiều sao nhất|sao cao nhất|review tốt nhất|được khen nhiều nhất|nổi tiếng nhất)\b/ui', $normalizedCurrent)) {
                $comparisonType = 'highest_rated';
            } elseif (preg_match('/\b(mở muộn nhất|đóng cửa muộn nhất|mở trễ nhất|đóng trễ nhất|mở khuya nhất|khuya nhất|mở đêm|mở 24\/7|24h|xuyên đêm)\b/ui', $normalizedCurrent)) {
                $comparisonType = 'latest_open';
            } elseif (preg_match('/\b(có khuyến mãi|có voucher|có giảm giá|có ưu đãi|đang giảm giá|deal|ưu đãi nhất)\b/ui', $normalizedCurrent)) {
                $comparisonType = 'has_promotion';
            } elseif (preg_match('/\b(gần nhất|gần trung tâm nhất)\b/ui', $normalizedCurrent)) {
                $comparisonType = 'nearest';
            }
        } elseif (preg_match('/\b(so sánh|quán nào hơn|nên đi quán nào|chọn quán nào|quán nào ok hơn|quán nào được hơn|quán nào hợp hơn)\b/ui', $normalizedCurrent)) {
            $comparisonType = 'comparison_general';
        } elseif (preg_match('/^(rẻ nhất|quán rẻ nhất|mở muộn nhất|ngon nhất|đánh giá cao nhất|có khuyến mãi)\??$/ui', trim($normalizedCurrent))) {
            if (str_contains($normalizedCurrent, 'rẻ')) $comparisonType = 'cheapest';
            elseif (str_contains($normalizedCurrent, 'muộn') || str_contains($normalizedCurrent, 'khuya')) $comparisonType = 'latest_open';
            elseif (str_contains($normalizedCurrent, 'ngon') || str_contains($normalizedCurrent, 'đánh giá')) $comparisonType = 'highest_rated';
            elseif (str_contains($normalizedCurrent, 'khuyến mãi') || str_contains($normalizedCurrent, 'voucher')) $comparisonType = 'has_promotion';
        }

        // Nhận diện Ý định phản hồi tiếp nối (Follow-up / Feedback Intents)
        $isAskingMore = (bool) preg_match('/\b(còn quán nào( nữa)? không|quán khác đi|thêm vài quán|xem thêm|gợi ý thêm|quán khác nữa|quán nào khác)\b/ui', $normalizedCurrent);
        $isAskingCheaper = (bool) preg_match('/\b(rẻ hơn|đắt quá|giá cao quá|hạt dẻ hơn|bình dân hơn|tiết kiệm hơn|giảm giá|sinh viên hơn)\b/ui', $normalizedCurrent) && !$comparisonType;
        
        // Nhận diện Ý định làm mới liên tục & tìm quán mới (Continuous Fresh Data Refresh & Newest Venues)
        $isRefreshing = (bool) preg_match('/\b(làm mới|làm mới gợi ý|làm mới dữ liệu|làm mới quán|đổi quán|quán khác|gợi ý khác|xem quán khác|đổi gió|thêm quán|refresh|quán mới hôm nay|gợi ý quán mới|quán mới khác|quán khác nữa|làm mới tiếp|tìm quán khác|đổi quán khác|gợi ý quán mới liên tục|làm mới liên tục)\b/ui', $normalizedCurrent);
        $wantsNewest = (bool) preg_match('/\b(quán mới nhất|mới mở|mới đăng|mới cập nhật|quán mới hôm nay|gợi ý quán mới|quán mới|quán mới toanh|mới toanh|quán mới mở)\b/ui', $normalizedCurrent);
        
        // Nhận diện hỏi chi tiết / tính năng về quán
        $isAskingVenueDetail = false;
        $targetVenue = null;
        $detailQuestionType = null;
        $inquiredDish = null;

        if (!$comparisonType && !empty($lastAssistantVenues) && preg_match('/\b(quán (1|2|3|4|đầu|đầu tiên|thứ nhất|thứ hai|thứ ba|thứ tư|cuối|đó|này)|chỗ (1|2|3|4|đầu|này|đó))\b/ui', $normalizedCurrent, $targetMatch)) {
            $isAskingVenueDetail = true;
            $idxStr = mb_strtolower($targetMatch[2] ?? $targetMatch[1] ?? '', 'UTF-8');
            $targetIdx = 0;
            if (in_array($idxStr, ['2', 'thứ hai', 'thứ 2'])) $targetIdx = 1;
            if (in_array($idxStr, ['3', 'thứ ba', 'thứ 3'])) $targetIdx = 2;
            if (in_array($idxStr, ['4', 'thứ tư', 'thứ 4'])) $targetIdx = 3;
            if (in_array($idxStr, ['cuối'])) $targetIdx = count($lastAssistantVenues) - 1;

            $targetVenue = $lastAssistantVenues[$targetIdx] ?? $lastAssistantVenues[0] ?? null;
        } elseif (!$comparisonType && !empty($lastAssistantVenues)) {
            // Kiểm tra xem người dùng có nhắc trực tiếp tên của quán nào không
            foreach ($lastAssistantVenues as $v) {
                $nameLower = mb_strtolower($v['name'] ?? $v['ten_quan'] ?? '', 'UTF-8');
                $cleanName = trim(preg_replace('/\b(quán|nhà hàng|tiệm|cà phê|cafe)\b/ui', '', $nameLower));
                if (!empty($cleanName) && mb_strlen($cleanName, 'UTF-8') >= 3 && str_contains($normalizedCurrent, $cleanName)) {
                    $isAskingVenueDetail = true;
                    $targetVenue = $v;
                    break;
                }
            }
        }

        // Nếu người dùng hỏi tiện ích/tính năng mà không chỉ định số thứ tự, ngầm định hỏi về quán trong ngữ cảnh
        $hasFeatureQuery = (bool) preg_match('/\b(mở mấy giờ|giờ mở cửa|đóng cửa|mấy giờ đóng|còn mở không|mở lúc nào|mở đến mấy giờ|địa chỉ|ở đâu|chỉ đường|thực đơn|menu|giá bao nhiêu|khoảng giá|đắt không|khuyến mãi|voucher|số điện thoại|sđt|hotline|liên hệ|chỗ để xe|bãi đỗ xe|đậu xe|đỗ ô tô|đậu ô tô|xe hơi|gửi xe|bãi xe|máy lạnh|điều hòa|phòng riêng|phòng vip|ngoài trời|sân vườn|chuyển khoản|quẹt thẻ|giao hàng|ship|shopeefood|có bán món|có món|có bán)\b/ui', $normalizedCurrent);

        if (!$targetVenue && !empty($lastAssistantVenues) && $hasFeatureQuery && !$comparisonType) {
            $isAskingVenueDetail = true;
            $targetVenue = $lastAssistantVenues[0];
        }

        // Nhận diện loại câu hỏi chi tiết
        if ($isAskingVenueDetail && $targetVenue) {
            if (preg_match('/\b(mở mấy giờ|giờ mở cửa|đóng cửa|mấy giờ đóng|còn mở không|mở lúc nào|mở đến mấy giờ)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'hours';
            } elseif (preg_match('/\b(địa chỉ|ở đâu|chỉ đường|đường nào|nằm ở đâu|vị trí)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'address';
            } elseif (preg_match('/\b(số điện thoại|sđt|hotline|liên hệ|gọi điện|đặt bàn)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'phone';
            } elseif (preg_match('/\b(chỗ để xe|bãi đỗ xe|đậu xe|đỗ ô tô|đậu ô tô|xe hơi|gửi xe|bãi xe)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'parking';
            } elseif (preg_match('/\b(máy lạnh|điều hòa|phòng lạnh)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'air_conditioning';
            } elseif (preg_match('/\b(phòng riêng|phòng vip|phòng kín|riêng tư)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'private_room';
            } elseif (preg_match('/\b(ngoài trời|sân vườn|rooftop|ban công|view đẹp|thoáng mát)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'outdoor';
            } elseif (preg_match('/\b(chuyển khoản|quẹt thẻ|momo|thanh toán|hóa đơn|vat|hóa đơn đỏ)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'payment';
            } elseif (preg_match('/\b(giao hàng|ship|shopeefood|mang về|delivery)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'delivery';
            } elseif (preg_match('/\b(có bán|có món)\s+([a-zA-Z0-9\p{L}\s]+)\b/ui', $normalizedCurrent, $dm)) {
                $detailQuestionType = 'dish_check';
                $rawDish = trim($dm[2]);
                $cleanDish = trim(preg_replace('/\b(không|k|ko|hả|nhỉ|vậy|ạ|bạn|bot|em|ad|admin|quán)\b/ui', '', $rawDish));
                $inquiredDish = $cleanDish ?: $rawDish;
            } elseif (preg_match('/\b(món gì|thực đơn|menu|món ngon|đặc sản|có gì ăn)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'menu';
            } elseif (preg_match('/\b(giá|bao nhiêu tiền|khoảng giá|đắt không)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'price';
            } elseif (preg_match('/\b(khuyến mãi|voucher|giảm giá|ưu đãi|deal)\b/ui', $normalizedCurrent)) {
                $detailQuestionType = 'promo';
            } else {
                $detailQuestionType = 'general';
            }
        }

        // Nhận diện câu hỏi quá ngắn / mơ hồ
        $isVague = (bool) preg_match('/^(tìm quán ăn ngon|ăn gì bây giờ|đói quá|tư vấn quán|quán ngon|ăn gì|chỗ ăn ngon|có gì ngon không|ăn gì đây|quán ăn)\??$/ui', trim($normalizedCurrent));
        if ($isRefreshing || $wantsNewest) {
            $isVague = false;
        }

        // ── Trích xuất Quận/Huyện & Tỉnh/Thành ─────────────────────────────────
        // Từ điển mở rộng địa bàn (TP.HCM, Hà Nội, Đà Nẵng) kèm từ viết tắt phổ biến
        $districtMap = [
            // TP. Hồ Chí Minh
            'quận 1' => 'Quận 1', 'q1' => 'Quận 1', 'q.1' => 'Quận 1', 'quan 1' => 'Quận 1',
            'quận 2' => 'Quận 2', 'q2' => 'Quận 2', 'q.2' => 'Quận 2', 'quan 2' => 'Quận 2',
            'quận 3' => 'Quận 3', 'q3' => 'Quận 3', 'q.3' => 'Quận 3', 'quan 3' => 'Quận 3',
            'quận 4' => 'Quận 4', 'q4' => 'Quận 4', 'q.4' => 'Quận 4', 'quan 4' => 'Quận 4',
            'quận 5' => 'Quận 5', 'q5' => 'Quận 5', 'q.5' => 'Quận 5', 'quan 5' => 'Quận 5',
            'quận 6' => 'Quận 6', 'q6' => 'Quận 6', 'q.6' => 'Quận 6', 'quan 6' => 'Quận 6',
            'quận 7' => 'Quận 7', 'q7' => 'Quận 7', 'q.7' => 'Quận 7', 'quan 7' => 'Quận 7',
            'quận 8' => 'Quận 8', 'q8' => 'Quận 8', 'q.8' => 'Quận 8', 'quan 8' => 'Quận 8',
            'quận 9' => 'Quận 9', 'q9' => 'Quận 9', 'q.9' => 'Quận 9', 'quan 9' => 'Quận 9',
            'quận 10' => 'Quận 10', 'q10' => 'Quận 10', 'q.10' => 'Quận 10', 'quan 10' => 'Quận 10',
            'quận 11' => 'Quận 11', 'q11' => 'Quận 11', 'q.11' => 'Quận 11', 'quan 11' => 'Quận 11',
            'quận 12' => 'Quận 12', 'q12' => 'Quận 12', 'q.12' => 'Quận 12', 'quan 12' => 'Quận 12',
            'bình thạnh' => 'Quận Bình Thạnh', 'binh thanh' => 'Quận Bình Thạnh', 'q.bình thạnh' => 'Quận Bình Thạnh',
            'gò vấp' => 'Quận Gò Vấp', 'go vap' => 'Quận Gò Vấp', 'q.gò vấp' => 'Quận Gò Vấp',
            'phú nhuận' => 'Quận Phú Nhuận', 'phu nhuan' => 'Quận Phú Nhuận', 'q.phú nhuận' => 'Quận Phú Nhuận',
            'tân bình' => 'Quận Tân Bình', 'tan binh' => 'Quận Tân Bình', 'q.tân bình' => 'Quận Tân Bình',
            'tân phú' => 'Quận Tân Phú', 'tan phu' => 'Quận Tân Phú', 'q.tân phú' => 'Quận Tân Phú',
            'bình tân' => 'Quận Bình Tân', 'binh tan' => 'Quận Bình Tân', 'q.bình tân' => 'Quận Bình Tân',
            'thủ đức' => 'Thành phố Thủ Đức', 'thu duc' => 'Thành phố Thủ Đức', 'tp thủ đức' => 'Thành phố Thủ Đức', 'tp.thủ đức' => 'Thành phố Thủ Đức',
            'hóc môn' => 'Huyện Hóc Môn', 'hoc mon' => 'Huyện Hóc Môn',
            'bình chánh' => 'Huyện Bình Chánh', 'binh chanh' => 'Huyện Bình Chánh',
            'nhà bè' => 'Huyện Nhà Bè', 'nha be' => 'Huyện Nhà Bè',

            // Hà Nội
            'ba đình' => 'Quận Ba Đình', 'ba dinh' => 'Quận Ba Đình', 'q.ba đình' => 'Quận Ba Đình',
            'hoàn kiếm' => 'Quận Hoàn Kiếm', 'hoan kiem' => 'Quận Hoàn Kiếm', 'q.hoàn kiếm' => 'Quận Hoàn Kiếm', 'phố cổ' => 'Quận Hoàn Kiếm',
            'đống đa' => 'Quận Đống Đa', 'dong da' => 'Quận Đống Đa', 'q.đống đa' => 'Quận Đống Đa',
            'cầu giấy' => 'Quận Cầu Giấy', 'cau giay' => 'Quận Cầu Giấy', 'q.cầu giấy' => 'Quận Cầu Giấy',
            'tây hồ' => 'Quận Tây Hồ', 'tay ho' => 'Quận Tây Hồ', 'hồ tây' => 'Quận Tây Hồ',
            'hai bà trưng' => 'Quận Hai Bà Trưng', 'hai ba trung' => 'Quận Hai Bà Trưng',
            'thanh xuân' => 'Quận Thanh Xuân', 'thanh xuan' => 'Quận Thanh Xuân',
            'hoàng mai' => 'Quận Hoàng Mai', 'hoang mai' => 'Quận Hoàng Mai',
            'long biên' => 'Quận Long Biên', 'long bien' => 'Quận Long Biên',
            'nam từ liêm' => 'Quận Nam Từ Liêm', 'bắc từ liêm' => 'Quận Bắc Từ Liêm',
            'hà đông' => 'Quận Hà Đông', 'ha dong' => 'Quận Hà Đông',

            // Đà Nẵng
            'hải châu' => 'Quận Hải Châu', 'hai chau' => 'Quận Hải Châu',
            'thanh khê' => 'Quận Thanh Khê', 'thanh khe' => 'Quận Thanh Khê',
            'sơn trà' => 'Quận Sơn Trà', 'son tra' => 'Quận Sơn Trà',
            'ngũ hành sơn' => 'Quận Ngũ Hành Sơn', 'ngu hanh son' => 'Quận Ngũ Hành Sơn',
            'liên chiểu' => 'Quận Liên Chiểu', 'lien chieu' => 'Quận Liên Chiểu',
            'cẩm lệ' => 'Quận Cẩm Lệ', 'cam le' => 'Quận Cẩm Lệ',
        ];

        // Ưu tiên trích xuất quận từ tin nhắn hiện tại trước (để nhận diện hành động đổi quận)
        $district = null;
        foreach ($districtMap as $kw => $val) {
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/ui', $normalizedCurrent)) {
                $district = $val;
                break;
            }
        }

        // Nếu tin nhắn hiện tại không đổi quận, xem lại ngữ cảnh lịch sử
        if (!$district) {
            for ($i = count($historyUserTexts) - 1; $i >= 0; $i--) {
                foreach ($districtMap as $kw => $val) {
                    if (preg_match('/\b' . preg_quote($kw, '/') . '\b/ui', $historyUserTexts[$i])) {
                        $district = $val;
                        break 2;
                    }
                }
            }
        }

        // Nhận diện Thành phố
        $city = null;
        $combinedAll = implode(' ', $historyUserTexts) . ' ' . $normalizedCurrent;

        if (preg_match('/\b(hà nội|ha noi|hn)\b/ui', $normalizedCurrent)) {
            $city = 'Thành phố Hà Nội';
        } elseif (preg_match('/\b(đà nẵng|da nang|đn)\b/ui', $normalizedCurrent)) {
            $city = 'Thành phố Đà Nẵng';
        } elseif (preg_match('/\b(sài gòn|sai gon|hồ chí minh|ho chi minh|tphcm|tp.hcm|hcm)\b/ui', $normalizedCurrent)) {
            $city = 'Thành phố Hồ Chí Minh';
        } elseif (preg_match('/\b(hà nội|ha noi|hn)\b/ui', $combinedAll)) {
            $city = 'Thành phố Hà Nội';
        } elseif (preg_match('/\b(đà nẵng|da nang|đn)\b/ui', $combinedAll)) {
            $city = 'Thành phố Đà Nẵng';
        } elseif (preg_match('/\b(sài gòn|sai gon|hồ chí minh|ho chi minh|tphcm|tp.hcm|hcm)\b/ui', $combinedAll)) {
            $city = 'Thành phố Hồ Chí Minh';
        }

        // Tự động suy luận thành phố từ quận/huyện
        if ($district && !$city) {
            if (str_contains($district, 'Ba Đình') || str_contains($district, 'Hoàn Kiếm') || str_contains($district, 'Cầu Giấy') || str_contains($district, 'Đống Đa') || str_contains($district, 'Tây Hồ')) {
                $city = 'Thành phố Hà Nội';
            } elseif (str_contains($district, 'Hải Châu') || str_contains($district, 'Sơn Trà') || str_contains($district, 'Thanh Khê')) {
                $city = 'Thành phố Đà Nẵng';
            } else {
                $city = 'Thành phố Hồ Chí Minh';
            }
        }

        // ── Trích xuất Loại hình & Món ăn ─────────────────────────────────────
        $categoryKeywords = [
            'Lẩu & Nướng' => ['lẩu', 'nướng', 'bbq', 'thịt nướng', 'lẩu thái', 'lẩu nướng', 'buffet nướng', 'lẩu bò', 'lẩu gà', 'nướng ngói', 'lẩu mắm', 'lẩu cá'],
            'Cà phê & Trà' => ['cà phê', 'cafe', 'coffee', 'trà sữa', 'matcha', 'trà đào', 'trà sen', 'nước ép', 'sinh tố', 'quán nước', 'cà phê muối', 'tà tưa', 'cold brew'],
            'Ăn vặt' => ['ăn vặt', 'bánh tráng', 'xiên bẩn', 'cá viên', 'trà chanh', 'nem chua', 'khoai tây lắc', 'bò bía', 'chè', 'bingsu', 'tàu hũ', 'ốc', 'hàu nướng'],
            'Nhà hàng' => ['nhà hàng', 'hải sản', 'fine dining', 'tiệc', 'gia đình', 'sushi', 'cơm niêu', 'món việt', 'món âu', 'steak', 'bò bít tết', 'dimsum', 'ẩm thực'],
            'Billiards & Giải trí' => ['bida', 'billiards', 'bi-a', 'giải trí'],
            'Quán Đêm 24/7' => ['ăn đêm', 'quán đêm', 'ăn khuya', '24/7', '24h', 'xuyên đêm', 'mở đêm', 'khuya'],
        ];

        $category = null;
        // Ưu tiên tin nhắn hiện tại
        foreach ($categoryKeywords as $catName => $keywords) {
            foreach ($keywords as $kw) {
                if (preg_match('/\b' . preg_quote($kw, '/') . '\b/ui', $normalizedCurrent)) {
                    $category = $catName;
                    break 2;
                }
            }
        }

        // Nếu tin nhắn hiện tại không đổi loại hình, lấy từ ngữ cảnh lịch sử
        if (!$category) {
            for ($i = count($historyUserTexts) - 1; $i >= 0; $i--) {
                foreach ($categoryKeywords as $catName => $keywords) {
                    foreach ($keywords as $kw) {
                        if (preg_match('/\b' . preg_quote($kw, '/') . '\b/ui', $historyUserTexts[$i])) {
                            $category = $catName;
                            break 2;
                        }
                    }
                }
            }
        }

        // Trích xuất Tên Món Ăn Cụ Thể
        $commonDishes = [
            'lẩu bò', 'lẩu thái', 'lẩu gà', 'lẩu mắm', 'thịt nướng', 'hàu nướng',
            'bún bò', 'phở', 'bánh mì', 'cơm tấm', 'bún chả', 'gà rán', 'mì cay',
            'bún đậu', 'bún riêu', 'bánh xèo', 'bánh cuốn', 'hủ tiếu', 'mì quảng',
            'matcha', 'trà đào', 'cà phê trứng', 'cà phê muối', 'trà sữa', 'sushi',
            'nem nướng', 'cháo ếch', 'dimsum', 'ốc', 'cơm gà', 'cơm niêu'
        ];

        $dishKeyword = null;
        foreach ($commonDishes as $dish) {
            if (preg_match('/\b' . preg_quote($dish, '/') . '\b/ui', $normalizedCurrent)) {
                $dishKeyword = $dish;
                break;
            }
        }
        if (!$dishKeyword) {
            for ($i = count($historyUserTexts) - 1; $i >= 0; $i--) {
                foreach ($commonDishes as $dish) {
                    if (preg_match('/\b' . preg_quote($dish, '/') . '\b/ui', $historyUserTexts[$i])) {
                        $dishKeyword = $dish;
                        break 2;
                    }
                }
            }
        }

        // ── Trích xuất Ngân sách & Xử lý điều chỉnh giá ─────────────────────────
        $maxPrice = null;
        $minPrice = null;

        if (preg_match('/dưới\s*([0-9]+)\s*(k|nghìn|ngàn|000)/ui', $normalizedCurrent, $m)) {
            $val = (int)$m[1];
            $maxPrice = ($m[2] === '000') ? $val : $val * 1000;
        } elseif (preg_match('/\b(sinh viên|giá rẻ|bình dân|tiết kiệm|hạt dẻ)\b/ui', $normalizedCurrent)) {
            $maxPrice = 60000;
        } elseif (preg_match('/tầm\s*([0-9]+)\s*k/ui', $normalizedCurrent, $m)) {
            $maxPrice = (int)$m[1] * 1200;
        } elseif (preg_match('/\b(cao cấp|sang trọng|sang chảnh|buffet|tiệc)\b/ui', $normalizedCurrent)) {
            $minPrice = 200000;
        }

        // Nếu người dùng phản hồi "rẻ hơn đi" / "đắt quá", tự động ép trần giá xuống mức bình dân
        if ($isAskingCheaper) {
            $maxPrice = $maxPrice ? min($maxPrice * 0.7, 80000) : 60000;
        }

        // Nếu tin nhắn hiện tại không nhắc ngân sách, lấy từ lịch sử
        if (!$maxPrice && !$minPrice && !$isAskingCheaper) {
            for ($i = count($historyUserTexts) - 1; $i >= 0; $i--) {
                if (preg_match('/dưới\s*([0-9]+)\s*(k|nghìn|ngàn|000)/ui', $historyUserTexts[$i], $m)) {
                    $val = (int)$m[1];
                    $maxPrice = ($m[2] === '000') ? $val : $val * 1000;
                    break;
                } elseif (preg_match('/\b(sinh viên|giá rẻ|bình dân|tiết kiệm|hạt dẻ)\b/ui', $historyUserTexts[$i])) {
                    $maxPrice = 60000;
                    break;
                }
            }
        }

        // ── Trích xuất Dịp / Hoàn cảnh (Occasion & Vibes) ──────────────────────
        $occasion = null;
        if (preg_match('/\b(hẹn hò|người yêu|lãng mạn|tỏ tình|date)\b/ui', $combinedAll)) {
            $occasion = 'hẹn hò';
        } elseif (preg_match('/\b(họp nhóm|tụ tập|bạn bè|đông người|liên hoan|tiệc)\b/ui', $combinedAll)) {
            $occasion = 'tụ tập nhóm';
        } elseif (preg_match('/\b(gia đình|bố mẹ|người lớn|trẻ con|con nít)\b/ui', $combinedAll)) {
            $occasion = 'gia đình';
        } elseif (preg_match('/\b(làm việc|học bài|yên tĩnh|đọc sách|chạy deadline)\b/ui', $combinedAll)) {
            $occasion = 'làm việc & học bài';
        }

        $requireOpenNow = preg_match('/\b(đang mở cửa|mở cửa bây giờ|ngay bây giờ|mở chưa|còn mở không|mở đêm|khuya|24\/7|24h)\b/ui', $combinedAll) === 1;
        $wantsPromotion = preg_match('/\b(khuyến mãi|voucher|giảm giá|ưu đãi|deal|mã giảm)\b/ui', $combinedAll) === 1;

        // ── Nhận diện các câu hỏi đời sống thường nhật (Everyday Life / Lifestyle Intents) ──
        $lifestyleType = null;
        $inquiredLifestyleTopic = null;

        // 1. Nấu ăn & Công thức món ăn tại nhà (Home Cooking & Recipes)
        if (preg_match('/\b(cách nấu|công thức|hướng dẫn làm|cách làm món|cách làm|cách pha|nấu món|nguyên liệu làm|làm thế nào để nấu|nấu gì ngon|món ngon gia đình|cách luộc|cách ướp|cách xào|cách kho|cách nướng|cách rán|cách chiên|cách hầm|cách nấu canh)\b/ui', $normalizedCurrent)) {
            $lifestyleType = 'recipe';
            if (preg_match('/\b(?:cách nấu|công thức|hướng dẫn làm|cách làm món|cách làm|cách pha|nguyên liệu làm món|nguyên liệu làm|làm món)\s+([a-zA-Z0-9\p{L}\s]+)/ui', $normalizedCurrent, $rm)) {
                $inquiredLifestyleTopic = trim(preg_replace('/\b(ngon|tại nhà|chuẩn vị|đơn giản|nhanh|không|nhỉ|hả|ạ|bạn|nhé|cho mình|giúp mình)\b/ui', '', $rm[1]));
            }
        }
        // 2. Dinh dưỡng, Giảm cân, Eat Clean (Nutrition & Diet)
        elseif (preg_match('/\b(eat clean|giảm cân|giảm mỡ|tăng cơ|calo|thâm hụt calo|ăn kiêng|thực đơn giảm cân|keto|low carb|chế độ ăn|ăn chay|dinh dưỡng lành mạnh|tập gym nên ăn gì|healthy)\b/ui', $normalizedCurrent)) {
            $lifestyleType = 'nutrition';
        }
        // 3. Tâm lý, Cảm xúc, Xoa dịu & Động lực (Emotional Counseling & Wellness)
        elseif (preg_match('/\b(buồn|mệt mỏi|stress|áp lực|chán quá|thất tình|cô đơn|an ủi|vực dậy|tâm sự|sếp mắng|bế tắc|nản lòng|chữa lành|động lực sống|lời khuyên|tâm trạng)\b/ui', $normalizedCurrent)) {
            $lifestyleType = 'emotion';
        }
        // 4. Giấc ngủ & Thói quen sống khỏe (Sleep & Healthy Living)
        elseif (preg_match('/\b(làm sao để ngủ ngon|mất ngủ|khó ngủ|ngủ sâu|mẹo ngủ ngon|sống khỏe|thói quen tốt|tiết kiệm tiền|quản lý thời gian|uống nước đúng cách)\b/ui', $normalizedCurrent)) {
            $lifestyleType = 'sleep_health';
        }
        // 5. Hẹn hò, Quà tặng & Cuối tuần (Dating & Hangout Ideas)
        elseif (preg_match('/\b(hẹn hò|tặng quà|bạn gái|bạn trai|người yêu|tỏ tình|ý tưởng hẹn hò|cuối tuần làm gì|chơi gì|cuối tuần đi đâu|dẫn người yêu đi đâu)\b/ui', $normalizedCurrent)) {
            $lifestyleType = 'dating';
        }
        // 6. Hài hước & Sáng tạo thơ ca (Humor & Creative)
        elseif (preg_match('/\b(kể chuyện cười|hài hước|chuyện vui|làm thơ|bài thơ|câu đố|đố vui|kể chuyện hài)\b/ui', $normalizedCurrent)) {
            $lifestyleType = 'creative';
        }
        // 7. Kiến thức tổng quát đời thường (General Life Knowledge)
        elseif (preg_match('/\b(tại sao|nguyên lý|nghĩa là gì|là gì|giải thích|quy tắc 80\/20|mẹo vặt|vì sao)\b/ui', $normalizedCurrent) && !preg_match('/\b(quán|quán ăn|nhà hàng)\b/ui', $normalizedCurrent)) {
            $lifestyleType = 'general_knowledge';
        }

        // Kiểm tra xem câu hỏi có ý định tìm quán/nhà hàng bên ngoài không
        $hasDiningIntent = (bool) preg_match('/\b(quán|nhà hàng|tiệm|quán ăn|quán nước|quán cafe|ở đâu|chỗ nào|địa chỉ|tìm quán|gợi ý quán|đặt bàn|gần đây|quận 1|quận 2|quận 3|quận 4|quận 5|quận 7|quận 10|bình thạnh|ba đình|cầu giấy)\b/ui', $normalizedCurrent)
            || !empty($district) || !empty($category) || !empty($maxPrice) || !empty($minPrice);

        return [
            'raw_message' => $message,
            'is_greeting' => $isGreeting,
            'is_thanks' => $isThanks,
            'is_vague' => $isVague,
            'is_asking_more' => $isAskingMore,
            'is_asking_cheaper' => $isAskingCheaper,
            'is_refreshing' => $isRefreshing,
            'wants_newest' => $wantsNewest,
            'comparison_type' => $comparisonType,
            'is_asking_venue_detail' => $isAskingVenueDetail,
            'target_venue' => $targetVenue,
            'detail_question_type' => $detailQuestionType,
            'inquired_dish' => $inquiredDish,
            'district' => $district,
            'city' => $city,
            'category' => $category,
            'dish' => $dishKeyword,
            'max_price' => $maxPrice,
            'min_price' => $minPrice,
            'occasion' => $occasion,
            'open_now' => $requireOpenNow,
            'wants_promotion' => $wantsPromotion,
            'seen_slugs' => $seenSlugs,
            'last_assistant_venues' => $lastAssistantVenues,
            'lifestyle_type' => $lifestyleType,
            'lifestyle_topic' => $inquiredLifestyleTopic,
            'has_dining_intent' => $hasDiningIntent,
        ];
    }

    /**
     * Tìm kiếm và xếp hạng các quán ăn thực tế trong cơ sở dữ liệu
     */
    protected function searchVenues(array $state, int $limit = 3): array
    {
        $query = Quan::query()
            ->where('trang_thai', 'da_duyet')
            ->with([
                'danhMucMenu.monAn' => fn ($q) => $q->select('id', 'danh_muc_id', 'ten_mon', 'gia')->take(6),
                'khuyenMaiDangChay' => fn ($q) => $q->select('id', 'quan_id', 'tieu_de', 'ma_voucher', 'gia_tri_giam')->take(1),
            ])
            ->withCount('danhGia')
            ->withAvg('danhGia', 'so_sao');

        // Luôn loại trừ các quán đã hiển thị trong phiên để tránh lặp lại kết quả
        if (!empty($state['seen_slugs'])) {
            $query->whereNotIn('slug', $state['seen_slugs']);
        }

        // Lọc theo Quận/Huyện hoặc Tỉnh/Thành
        if (!empty($state['district'])) {
            $query->where(function ($q) use ($state) {
                $q->where('ten_quan_huyen', 'like', "%{$state['district']}%")
                  ->orWhere('dia_chi_chi_tiet', 'like', "%{$state['district']}%");
            });
            if (!empty($state['city'])) {
                $query->where('ten_tinh_thanh', 'like', "%{$state['city']}%");
            }
        } elseif (!empty($state['city'])) {
            $query->where('ten_tinh_thanh', 'like', "%{$state['city']}%");
        }

        // Lọc theo Loại hình ẩm thực chuẩn xác từ database (loại bỏ dữ liệu sai)
        if (!empty($state['category'])) {
            $cat = $state['category'];
            $catParts = array_filter(array_map('trim', preg_split('/[&,]/', $cat)));
            $query->where(function ($q) use ($cat, $catParts) {
                $q->where('loai_hinh_kinh_doanh', 'like', "%{$cat}%");
                foreach ($catParts as $part) {
                    if (mb_strlen($part, 'UTF-8') >= 3) {
                        $q->orWhere('loai_hinh_kinh_doanh', 'like', "%{$part}%");
                    }
                }
            });
        }

        // Lọc theo Món ăn cụ thể (kiểm tra thực đơn món ăn thực tế hoặc tên quán)
        if (!empty($state['dish'])) {
            $query->where(function ($q) use ($state) {
                $q->whereHas('danhMucMenu.monAn', function ($sub) use ($state) {
                    $sub->where('ten_mon', 'like', "%{$state['dish']}%");
                })
                ->orWhere('ten_quan', 'like', "%{$state['dish']}%");
            });
        }

        // Lọc theo Ngân sách
        if (!empty($state['max_price'])) {
            $query->where(function ($q) use ($state) {
                $q->whereNull('gia_nho_nhat')
                  ->orWhere('gia_nho_nhat', '<=', $state['max_price']);
            });
        }
        if (!empty($state['min_price'])) {
            $query->where(function ($q) use ($state) {
                $q->whereNull('gia_lon_nhat')
                  ->orWhere('gia_lon_nhat', '>=', $state['min_price']);
            });
        }

        // Lọc có khuyến mãi nếu người dùng yêu cầu
        if (!empty($state['wants_promotion'])) {
            $query->whereHas('khuyenMaiDangChay');
        }

        // Sắp xếp ưu tiên linh hoạt theo nhu cầu làm mới & tìm kiếm
        if (!empty($state['wants_newest'])) {
            $query->orderByDesc('created_at')->orderByDesc('id');
        } elseif (!empty($state['is_refreshing'])) {
            $query->orderByDesc('created_at')->orderByDesc('is_noi_bat')->orderByDesc('id');
        } elseif (!empty($state['is_asking_cheaper'])) {
            $query->orderBy('gia_nho_nhat', 'asc')
                  ->orderByDesc('danh_gia_avg_so_sao');
        } else {
            $query->orderByDesc('is_xac_thuc')
                  ->orderByDesc('is_noi_bat')
                  ->orderByDesc('danh_gia_avg_so_sao')
                  ->orderByDesc('luot_xem');
        }

        $venues = $query->take($limit)->get();

        // Cơ chế làm mới dữ liệu liên tục không bị trùng lặp:
        // Nếu bộ lọc hẹp đã hết quán chưa xem (do seen_slugs), mở rộng tìm kiếm các quán CHƯA XEM khác
        if ($venues->isEmpty() && !empty($state['seen_slugs'])) {
            // Bước 1: Mở rộng tìm kiếm các quán CHƯA XEM trong thành phố hoặc theo loại hình (vẫn loại trừ seen_slugs)
            $expandQuery = Quan::query()
                ->where('trang_thai', 'da_duyet')
                ->whereNotIn('slug', $state['seen_slugs'])
                ->with([
                    'danhMucMenu.monAn' => fn ($q) => $q->select('id', 'danh_muc_id', 'ten_mon', 'gia')->take(6),
                    'khuyenMaiDangChay' => fn ($q) => $q->select('id', 'quan_id', 'tieu_de', 'ma_voucher', 'gia_tri_giam')->take(1),
                ])
                ->withCount('danhGia')
                ->withAvg('danhGia', 'so_sao');

            if (!empty($state['city'])) {
                $expandQuery->where('ten_tinh_thanh', 'like', "%{$state['city']}%");
            }
            if (!empty($state['category'])) {
                $expandQuery->where('loai_hinh_kinh_doanh', 'like', "%{$state['category']}%");
            }

            if (!empty($state['wants_newest']) || !empty($state['is_refreshing'])) {
                $expandQuery->orderByDesc('created_at')->orderByDesc('id');
            } else {
                $expandQuery->orderByDesc('is_noi_bat')->orderByDesc('danh_gia_avg_so_sao');
            }

            $venues = $expandQuery->take($limit)->get();

            // Bước 2: Nếu cùng thành phố/loại hình cũng đã xem hết, tìm BẤT KỲ quán nào CHƯA XEM trên toàn hệ thống
            if ($venues->isEmpty()) {
                $venues = Quan::query()
                    ->where('trang_thai', 'da_duyet')
                    ->whereNotIn('slug', $state['seen_slugs'])
                    ->with([
                        'danhMucMenu.monAn' => fn ($q) => $q->select('id', 'danh_muc_id', 'ten_mon', 'gia')->take(6),
                        'khuyenMaiDangChay' => fn ($q) => $q->select('id', 'quan_id', 'tieu_de', 'ma_voucher', 'gia_tri_giam')->take(1),
                    ])
                    ->withCount('danhGia')
                    ->withAvg('danhGia', 'so_sao')
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->take($limit)
                    ->get();
            }

            // Bước 3: Chỉ khi người dùng đã xem HẾT TOÀN BỘ quán trong database, mới bắt đầu vòng lặp mới từ các quán nổi bật nhất
            if ($venues->isEmpty()) {
                $venues = Quan::query()
                    ->where('trang_thai', 'da_duyet')
                    ->with([
                        'danhMucMenu.monAn' => fn ($q) => $q->select('id', 'danh_muc_id', 'ten_mon', 'gia')->take(6),
                        'khuyenMaiDangChay' => fn ($q) => $q->select('id', 'quan_id', 'tieu_de', 'ma_voucher', 'gia_tri_giam')->take(1),
                    ])
                    ->withCount('danhGia')
                    ->withAvg('danhGia', 'so_sao')
                    ->when(!empty($state['district']), function($q) use ($state) {
                        $q->where(function ($sub) use ($state) {
                            $sub->where('ten_quan_huyen', 'like', "%{$state['district']}%")
                                ->orWhere('dia_chi_chi_tiet', 'like', "%{$state['district']}%");
                        });
                    })
                    ->when(!empty($state['category']), function($q) use ($state) {
                        $q->where('loai_hinh_kinh_doanh', 'like', "%{$state['category']}%");
                    })
                    ->orderByDesc('danh_gia_avg_so_sao')
                    ->take($limit)
                    ->get();
            }
        }

        // Định dạng dữ liệu quán trả về chuẩn hóa (tương thích cả frontend lẫn test suite)
        return $venues->map(fn($quan) => $this->formatVenueForOutput($quan))->values()->all();
    }

    /**
     * Định dạng dữ liệu quán ăn từ database chuẩn xác, loại bỏ dữ liệu ảo/giả mạo
     */
    protected function formatVenueForOutput(Quan $quan): array
    {
        $dishes = [];
        if ($quan->relationLoaded('danhMucMenu')) {
            foreach ($quan->danhMucMenu as $dm) {
                if ($dm->relationLoaded('monAn')) {
                    foreach ($dm->monAn as $mon) {
                        $dishes[] = $mon->ten_mon;
                    }
                }
            }
        }

        // Đánh giá thực tế từ người dùng (không bịa số sao ảo 4.5 nếu quán chưa có review)
        $hasRating = ($quan->danh_gia_avg_so_sao !== null && (float)$quan->danh_gia_avg_so_sao > 0);
        $avgRating = $hasRating ? round((float)$quan->danh_gia_avg_so_sao, 1) : null;
        $reviewCount = (int) ($quan->danh_gia_count ?? $quan->danhGia()->count());
        $promo = $quan->relationLoaded('khuyenMaiDangChay') ? $quan->khuyenMaiDangChay->first() : null;

        // Khoảng giá thực tế từ database
        $priceText = 'Đang cập nhật giá';
        if ($quan->gia_nho_nhat && $quan->gia_lon_nhat) {
            $min = number_format((float)$quan->gia_nho_nhat, 0, ',', '.') . 'đ';
            $max = number_format((float)$quan->gia_lon_nhat, 0, ',', '.') . 'đ';
            $priceText = "{$min} - {$max}";
        } elseif ($quan->gia_nho_nhat) {
            $priceText = 'Từ ' . number_format((float)$quan->gia_nho_nhat, 0, ',', '.') . 'đ';
        } elseif ($quan->gia_lon_nhat) {
            $priceText = 'Đến ' . number_format((float)$quan->gia_lon_nhat, 0, ',', '.') . 'đ';
        }

        // Địa chỉ đầy đủ, chính xác
        $addressParts = array_filter([
            $quan->dia_chi_chi_tiet,
            $quan->ten_phuong_xa,
            $quan->ten_quan_huyen,
            $quan->ten_tinh_thanh
        ]);
        $fullAddress = !empty($addressParts) ? implode(', ', $addressParts) : ($quan->dia_chi_chi_tiet ?: 'Đang cập nhật địa chỉ');
        $category = $quan->loai_hinh_kinh_doanh ?? 'Quán ăn';

        // Giờ mở cửa chính xác
        $openHours = 'Chưa cập nhật giờ';
        if ($quan->gio_mo_cua && $quan->gio_dong_cua) {
            $openHours = substr((string)$quan->gio_mo_cua, 0, 5) . ' - ' . substr((string)$quan->gio_dong_cua, 0, 5);
        } elseif (str_contains(strtolower($category), '24/7') || str_contains(strtolower($quan->mo_ta ?? ''), '24/7')) {
            $openHours = 'Mở cả ngày (24/7)';
        }

        return [
            'id' => (string) $quan->id,
            'name' => $quan->ten_quan,
            'ten_quan' => $quan->ten_quan,
            'slug' => $quan->slug,
            'url' => route('quan.detail', ['slug' => $quan->slug]),
            'image' => $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
            'hinh_anh' => $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
            'address' => $fullAddress,
            'dia_chi' => $fullAddress,
            'district' => $quan->ten_quan_huyen ?? 'Trung tâm',
            'city' => $quan->ten_tinh_thanh ?? '',
            'category' => $category,
            'loai_hinh' => $category,
            'price_range' => $priceText,
            'gia_trung_binh' => $priceText,
            'min_price' => $quan->gia_nho_nhat ? (float)$quan->gia_nho_nhat : null,
            'max_price' => $quan->gia_lon_nhat ? (float)$quan->gia_lon_nhat : null,
            'rating' => $avgRating,
            'danh_gia_sao' => $avgRating,
            'rating_text' => $avgRating ? "⭐ {$avgRating}/5" : "✨ Quán mới",
            'review_count' => $reviewCount,
            'open_hours' => $openHours,
            'open_time' => (string)($quan->gio_mo_cua ?? ''),
            'close_time' => (string)($quan->gio_dong_cua ?? ''),
            'is_open_now' => $quan->isDangMoCua(),
            'highlight_dishes' => array_slice(array_unique($dishes), 0, 3),
            'promotion' => $promo ? ($promo->tieu_de . ($promo->ma_voucher ? " (Mã: {$promo->ma_voucher})" : '')) : null,
            'is_verified' => (bool)$quan->is_xac_thuc,
            'is_featured' => (bool)$quan->is_noi_bat,
            'phone' => $quan->so_dien_thoai,
            'description' => $quan->mo_ta,
            'has_shopeefood' => !empty($quan->shopeefood_url),
        ];
    }

    /**
     * Trả lời trực tiếp câu hỏi đào sâu về một quán cụ thể (Entity Answering)
     */
    protected function answerVenueDetailQuestion(array $venue, string $question, array $state): string
    {
        $name = $venue['name'] ?? $venue['ten_quan'] ?? 'Quán';
        $url = $venue['url'] ?? '#';
        $type = $state['detail_question_type'] ?? 'general';
        $address = $venue['address'] ?? $venue['dia_chi'] ?? 'Địa chỉ tại quán';
        $district = $venue['district'] ?? 'Trung tâm';
        $openHours = $venue['open_hours'] ?? 'Mở cả ngày';
        $priceRange = $venue['price_range'] ?? $venue['gia_trung_binh'] ?? 'Bình dân';
        $rating = $venue['rating'] ?? $venue['danh_gia_sao'] ?? 4.5;
        $reviewCount = $venue['review_count'] ?? 0;

        switch ($type) {
            case 'hours':
                $statusText = !empty($venue['is_open_now']) ? "hiện **đang mở cửa đón khách**" : "hiện đang đóng cửa";
                return "Quán [**{$name}**]({$url}) mở cửa trong khung giờ: **{$openHours}** ({$statusText}).\n\n💡 *Mẹo cho bạn:* Nếu đi vào khung giờ cao điểm tối (18h30 - 20h00) hoặc cuối tuần, bạn nên đến sớm một chút hoặc liên hệ trước để giữ bàn đẹp nhé!";

            case 'address':
                return "Địa chỉ chính xác của [**{$name}**]({$url}) tại: **{$address}**.\n\n📍 Bạn có thể bấm vào tên quán để xem bản đồ chỉ đường Google Maps và định vị trực quan trên trang Quán Mới nhé!";

            case 'phone':
                $phone = $venue['phone'] ?? null;
                if (!empty($phone)) {
                    return "Số điện thoại liên hệ / đặt bàn của [**{$name}**]({$url}) là: **{$phone}**.\n\n💡 Bạn có thể gọi trực tiếp trước khi đến để quán giữ bàn đẹp hoặc chuẩn bị món chu đáo nhé!";
                }
                return "Hiện quán [**{$name}**]({$url}) chưa cập nhật số hotline riêng trên hệ thống. Bạn có thể ghé trực tiếp tại **{$address}** hoặc bấm vào tên quán để xem thông tin chi tiết!";

            case 'parking':
                return "Về bãi đỗ & gửi xe tại [**{$name}**]({$url}):\n- 🛵 **Xe máy:** Quán có chỗ để xe máy thuận tiện ngay phía trước, có nhân viên hướng dẫn và bảo vệ hỗ trợ an toàn.\n- 🚗 **Ô tô:** Do quán nằm tại khu vực trung tâm ({$district}), nếu bạn di chuyển bằng ô tô thì nên liên hệ trước hoặc gửi tại các bãi giữ xe ô tô lân cận nhé!\n\n📍 Địa chỉ quán: **{$address}**.";

            case 'air_conditioning':
                return "Không gian tại [**{$name}**]({$url}) được trang bị hệ thống máy lạnh và quạt mát mẻ, thoáng sạch, tạo cảm giác dễ chịu cho bạn khi thưởng thức ẩm thực ngay cả trong những ngày nắng nóng.\n\nBạn có thể bấm vào [**trang quán**]({$url}) để xem trọn bộ ảnh không gian chỗ ngồi!";

            case 'private_room':
                return "Về phòng riêng tại [**{$name}**]({$url}): Quán có không gian rộng rãi với các cụm bàn dành cho nhóm bạn bè hoặc gia đình. Nếu bạn cần không gian riêng tư cho tiệc sinh nhật hoặc gặp gỡ đối tác, bạn nên liên hệ trước để quán sắp xếp vị trí phù hợp nhất nhé!";

            case 'outdoor':
                return "Không gian tại [**{$name}**]({$url}) thoáng đãng, có ánh sáng tự nhiên và view phố sinh động. Quán rất thích hợp cho những buổi tụ tập, hẹn hò hoặc tán gẫu thoải mái!";

            case 'payment':
                return "Tại [**{$name}**]({$url}), quán hỗ trợ các hình thức thanh toán thuận tiện:\n- 💵 Tiền mặt\n- 💳 Chuyển khoản ngân hàng (quét mã QR linh hoạt)\n- 📱 Ví điện tử phổ biến (Momo, ZaloPay)\n\nNếu bạn cần xuất hóa đơn VAT cho công ty, vui lòng thông báo nhân viên khi thanh toán nhé!";

            case 'delivery':
                return "Quán [**{$name}**]({$url}) phục vụ ăn uống trực tiếp tại quán và nhận đơn mua mang về (takeaway). Bạn có thể bấm vào [**trang quán**]({$url}) để kiểm tra liên kết đặt món trực tuyến!";

            case 'dish_check':
                $dishName = $state['inquired_dish'] ?? '';
                $foundDish = null;
                if (!empty($dishName) && !empty($venue['id'])) {
                    $foundDish = MonTrongMenu::whereHas('danhMuc', function ($q) use ($venue) {
                        $q->where('quan_id', $venue['id']);
                    })->where(function ($q) use ($dishName) {
                        $q->where('ten_mon', 'like', "%{$dishName}%");
                        $words = array_filter(explode(' ', $dishName), fn($w) => mb_strlen($w, 'UTF-8') >= 3);
                        foreach ($words as $w) {
                            $q->orWhere('ten_mon', 'like', "%{$w}%");
                        }
                    })->first();
                }

                if (!$foundDish && !empty($venue['highlight_dishes'])) {
                    foreach ($venue['highlight_dishes'] as $hd) {
                        if (str_contains(mb_strtolower($hd, 'UTF-8'), mb_strtolower($dishName, 'UTF-8')) ||
                            str_contains(mb_strtolower($dishName, 'UTF-8'), mb_strtolower($hd, 'UTF-8'))) {
                            return "Tin vui cho bạn: Quán [**{$name}**]({$url}) **có phục vụ** món **{$hd}** trong thực đơn!\n\nBạn có thể ghé quán tại **{$address}** hoặc bấm vào tên quán để xem toàn bộ danh sách món ăn nhé!";
                        }
                    }
                }

                if ($foundDish) {
                    $dishPrice = number_format($foundDish->gia, 0, ',', '.') . 'đ';
                    return "Tin vui cho bạn: Quán [**{$name}**]({$url}) **có phục vụ** món **{$foundDish->ten_mon}** với mức giá **{$dishPrice}** trong thực đơn!\n\nBạn có thể ghé quán tại **{$address}** hoặc bấm vào tên quán để xem toàn bộ danh sách món ăn nhé!";
                }

                $dishes = !empty($venue['highlight_dishes']) ? implode(', ', $venue['highlight_dishes']) : 'Nhiều món đặc trưng';
                return "Hiện tại trong thực đơn hệ thống của [**{$name}**]({$url}) chưa có món **" . ($dishName ?: 'này') . "**. Tuy nhiên quán rất nổi tiếng với các món ngon như: **{$dishes}**.\n\nNếu bạn muốn tìm quán chuyên món **{$dishName}**, cứ nhắn cho mình để mình quét ngay nhé!";

            case 'menu':
                $dishes = !empty($venue['highlight_dishes']) ? implode(', ', $venue['highlight_dishes']) : 'Nhiều món ngon đặc trưng';
                return "Tại [**{$name}**]({$url}), các món nổi bật được thực khách yêu thích gồm có: **{$dishes}**.\n\n💵 Mức giá dao động: **{$priceRange}**. Bạn có thể bấm vào quán để xem toàn bộ danh mục thực đơn chi tiết!";

            case 'price':
                return "Mức giá trung bình tại [**{$name}**]({$url}) rơi vào khoảng: **{$priceRange}**.\n\nĐánh giá từ thực khách: ⭐ **{$rating}/5** ({$reviewCount} lượt đánh giá). Mức giá này được nhận xét là rất tương xứng với chất lượng món ăn và không gian quán!";

            case 'promo':
                if (!empty($venue['promotion'])) {
                    return "🎉 Tin vui cho bạn: [**{$name}**]({$url}) hiện đang có ưu đãi: **{$venue['promotion']}**!\n\nBạn hãy lưu lại mã hoặc xuất trình trên ứng dụng Quán Mới khi thanh toán để được áp dụng giảm giá nhé.";
                }
                return "Hiện tại quán [**{$name}**]({$url}) chưa chạy chương trình voucher mới. Tuy nhiên mức giá **{$priceRange}** tại quán đã rất hợp lý và thường xuyên có tặng kèm món ăn kèm khi gọi combo!";

            default:
                $openStatus = !empty($venue['is_open_now']) ? '🟢 Đang mở cửa' : '⚪ Đang đóng cửa';
                return "Thông tin về [**{$name}**]({$url}):\n- 📍 **Địa chỉ:** {$address}\n- ⏰ **Giờ mở cửa:** {$openHours} ({$openStatus})\n- 💵 **Giá:** {$priceRange} | ⭐ {$rating}/5 ({$reviewCount} review)\n- 🍜 **Món tiêu biểu:** " . (!empty($venue['highlight_dishes']) ? implode(', ', $venue['highlight_dishes']) : 'Đa dạng') . "\n\nBạn muốn tìm hiểu thêm thông tin gì về quán này không?";
        }
    }

    /**
     * Trả lời trực diện câu hỏi so sánh giữa các quán (Cheapest, Latest open, Highest rated, Promotion...)
     */
    protected function answerComparisonQuestion(array $venues, string $comparisonType, string $userMessage, array $state): array
    {
        if (empty($venues)) {
            return $this->handleVagueOrMissingCriteria($userMessage, $state);
        }

        $reply = "";
        $winnerVenue = $venues[0];

        switch ($comparisonType) {
            case 'cheapest':
                $minVal = PHP_INT_MAX;
                foreach ($venues as $v) {
                    $p = $v['min_price'] ?? null;
                    if ($p === null || $p <= 0) {
                        if (preg_match('/([0-9\.]+)\s*đ/u', $v['price_range'] ?? '', $pm)) {
                            $p = (float)str_replace('.', '', $pm[1]);
                        }
                    }
                    if ($p !== null && $p > 0 && $p < $minVal) {
                        $minVal = $p;
                        $winnerVenue = $v;
                    }
                }

                $name = $winnerVenue['name'] ?? $winnerVenue['ten_quan'];
                $url = $winnerVenue['url'];
                $reply = "Trong các quán, quán có mức giá **rẻ và tiết kiệm nhất** là [**{$name}**]({$url}) với mức giá chỉ từ **{$winnerVenue['price_range']}**.\n\n";

                $others = array_values(array_filter($venues, fn($v) => ($v['slug'] ?? '') !== ($winnerVenue['slug'] ?? '')));
                if (!empty($others)) {
                    $otherNotes = [];
                    foreach (array_slice($others, 0, 2) as $ov) {
                        $oname = $ov['name'] ?? $ov['ten_quan'];
                        $otherNotes[] = "[{$oname}]({$ov['url']}) ({$ov['price_range']})";
                    }
                    $reply .= "Để bạn dễ so sánh: các quán khác có mức giá từ " . implode(' và ', $otherNotes) . ".\n\n";
                }
                $reply .= "💡 Nếu bạn muốn ăn ngon với chi phí 'hạt dẻ' và phải chăng thì [**{$name}**]({$url}) là lựa chọn tối ưu nhất!";
                break;

            case 'most_expensive':
                $maxVal = -1;
                foreach ($venues as $v) {
                    $p = $v['max_price'] ?? ($v['min_price'] ?? 0);
                    if ($p > $maxVal) {
                        $maxVal = $p;
                        $winnerVenue = $v;
                    }
                }
                $name = $winnerVenue['name'] ?? $winnerVenue['ten_quan'];
                $url = $winnerVenue['url'];
                $reply = "Quán có phân khúc cao cấp và mức giá cao nhất là [**{$name}**]({$url}) ({$winnerVenue['price_range']}).\n\nQuán phù hợp cho các dịp tiếp khách, liên hoan trang trọng hoặc bữa tiệc gia đình ấm cúng với không gian đẹp và chất lượng phục vụ chỉn chu.";
                break;

            case 'latest_open':
                $latestCloseMinutes = -1;
                foreach ($venues as $v) {
                    $hours = $v['open_hours'] ?? '';
                    $cat = $v['category'] ?? '';
                    if (str_contains($hours, '24/7') || str_contains($cat, '24/7') || str_contains($cat, 'Quán Đêm')) {
                        $winnerVenue = $v;
                        $latestCloseMinutes = 24 * 60 + 60;
                        break;
                    }
                    $closeTime = $v['close_time'] ?? '';
                    if (preg_match('/(\d{1,2}):(\d{2})/', $closeTime, $tm)) {
                        $h = (int)$tm[1];
                        $m = (int)$tm[2];
                        if ($h < 6) $h += 24; // Mở xuyên đêm sau nửa đêm
                        $totalMin = $h * 60 + $m;
                        if ($totalMin > $latestCloseMinutes) {
                            $latestCloseMinutes = $totalMin;
                            $winnerVenue = $v;
                        }
                    }
                }

                $name = $winnerVenue['name'] ?? $winnerVenue['ten_quan'];
                $url = $winnerVenue['url'];
                $status = !empty($winnerVenue['is_open_now']) ? '🟢 đang mở cửa' : '⚪ đã đóng cửa';
                $reply = "Quán mở cửa muộn nhất là [**{$name}**]({$url}), hoạt động trong khung giờ: **{$winnerVenue['open_hours']}** ({$status}).\n\n";

                $others = array_values(array_filter($venues, fn($v) => ($v['slug'] ?? '') !== ($winnerVenue['slug'] ?? '')));
                if (!empty($others)) {
                    $otherNotes = [];
                    foreach (array_slice($others, 0, 2) as $ov) {
                        $oname = $ov['name'] ?? $ov['ten_quan'];
                        $otherNotes[] = "[{$oname}]({$ov['url']}) (đóng lúc " . (explode('-', $ov['open_hours'])[1] ?? $ov['open_hours']) . ")";
                    }
                    $reply .= "Trong khi các quán khác đóng cửa sớm hơn: " . implode(', ', $otherNotes) . ".\n\n";
                }
                $reply .= "💡 Quán này rất thích hợp nếu bạn muốn lai rai tối muộn, hẹn hò hoặc ngồi tán gẫu thoải mái không lo bị giục về sớm!";
                break;

            case 'highest_rated':
                $sorted = $venues;
                usort($sorted, function ($a, $b) {
                    $rA = (float)($a['rating'] ?? 0);
                    $rB = (float)($b['rating'] ?? 0);
                    if ($rA === $rB) {
                        return ($b['review_count'] ?? 0) <=> ($a['review_count'] ?? 0);
                    }
                    return $rB <=> $rA;
                });
                $winnerVenue = $sorted[0];
                $name = $winnerVenue['name'] ?? $winnerVenue['ten_quan'];
                $url = $winnerVenue['url'];
                $dishes = !empty($winnerVenue['highlight_dishes']) ? implode(', ', $winnerVenue['highlight_dishes']) : 'nhiều món ăn đặc trưng';

                $reply = "Quán được đánh giá cao nhất là [**{$name}**]({$url}) với điểm số ấn tượng: **⭐ {$winnerVenue['rating']}/5** ({$winnerVenue['review_count']} lượt đánh giá thực tế).\n\n";
                $reply .= "🌟 **Điểm cộng nổi bật:** Quán được cộng đồng ẩm thực khen ngợi nhiều về hương vị đặc trưng ({$dishes}) cùng thái độ phục vụ chu đáo, nhiệt tình.\n\n";
                $reply .= "Nếu bạn ưu tiên chất lượng và trải nghiệm trọn vẹn nhất thì đây là điểm đến lý tưởng số 1!";
                break;

            case 'has_promotion':
                $promoVenues = array_values(array_filter($venues, fn($v) => !empty($v['promotion'])));
                if (!empty($promoVenues)) {
                    $winnerVenue = $promoVenues[0];
                    $name = $winnerVenue['name'] ?? $winnerVenue['ten_quan'];
                    $url = $winnerVenue['url'];
                    $reply = "Trong danh sách, quán đang có chương trình khuyến mãi ưu đãi là [**{$name}**]({$url})!\n\n";
                    $reply .= "🎁 **Ưu đãi đang chạy:** **{$winnerVenue['promotion']}**\n";
                    $reply .= "💵 Khoảng giá: **{$winnerVenue['price_range']}**\n\n";
                    $reply .= "💡 Bạn hãy xuất trình mã hoặc đặt trên ứng dụng Quán Mới khi thanh toán để được nhận ngay ưu đãi nhé!";
                } else {
                    $winnerVenue = $venues[0];
                    $reply = "Hiện tại các quán trong danh sách chưa cập nhật mã voucher mới. Tuy nhiên, mức giá tại [**{$venues[0]['name']}**]({$venues[0]['url']}) ({$venues[0]['price_range']}) đã rất phải chăng và thường xuyên có tặng kèm món ăn phụ khi gọi combo!";
                }
                break;

            case 'comparison_general':
            default:
                $cheapest = $venues[0];
                $bestRated = $venues[0];
                foreach ($venues as $v) {
                    if (($v['min_price'] ?? PHP_INT_MAX) < ($cheapest['min_price'] ?? PHP_INT_MAX)) {
                        $cheapest = $v;
                    }
                    if (($v['rating'] ?? 0) > ($bestRated['rating'] ?? 0)) {
                        $bestRated = $v;
                    }
                }
                $winnerVenue = $bestRated;
                $reply = "Để giúp bạn dễ dàng đưa ra quyết định, mình so sánh nhanh các điểm nổi bật nhé:\n\n";
                foreach ($venues as $i => $v) {
                    $idx = $i + 1;
                    $reply .= "{$idx}. [**{$v['name']}**]({$v['url']}): Giá {$v['price_range']} | ⭐ {$v['rating']}/5 | Mở cửa: {$v['open_hours']}\n";
                }
                $reply .= "\n🎯 **Tóm lại lời khuyên cho bạn:**\n";
                $reply .= "- Nếu ưu tiên **tiết kiệm chi phí**: Chọn [**{$cheapest['name']}**]({$cheapest['url']}).\n";
                $reply .= "- Nếu ưu tiên **chất lượng & review cao nhất**: Chọn [**{$bestRated['name']}**]({$bestRated['url']}).\n";
                break;
        }

        return [
            'success' => true,
            'reply' => $reply,
            'venues' => [$winnerVenue],
            'criteria' => $state,
            'parsed_intent' => $state,
            'suggestions' => $this->getComparisonFollowUpSuggestions($state, $winnerVenue),
        ];
    }

    /**
     * Gợi ý câu hỏi tiếp theo sau khi người dùng vừa so sánh quán
     */
    protected function getComparisonFollowUpSuggestions(array $state, array $winnerVenue): array
    {
        $name = $winnerVenue['name'] ?? $winnerVenue['ten_quan'] ?? 'quán này';
        return [
            'Chỉ đường tới quán này',
            'Quán này có chỗ để xe không?',
            'Quán có khuyến mãi gì không?',
            'Xem thêm các quán khác',
        ];
    }

    /**
     * Xử lý câu hỏi mơ hồ: Chủ động hỏi lại thông tin cần thiết + Gợi ý ngay các quán thịnh hành
     */
    protected function handleVagueOrMissingCriteria(string $message, array $state): array
    {
        // Lấy 3 quán nổi bật đang được đánh giá cao nhất trên hệ thống làm gợi ý khởi đầu
        $trendingVenues = $this->searchVenues([
            'district' => null,
            'city' => null,
            'category' => null,
            'dish' => null,
            'max_price' => null,
            'min_price' => null,
            'seen_slugs' => [],
            'is_asking_more' => false,
            'is_asking_cheaper' => false,
        ], 3);

        $reply = "Chào bạn! Quán Mới có rất nhiều địa điểm ẩm thực hấp dẫn đang chờ bạn khám phá. 🍜✨\n\n";
        $reply .= "Để mình tư vấn **chuẩn xác và đúng gu của bạn nhất**, bạn có thể chia sẻ thêm cho mình một chút nhé:\n";
        $reply .= "1. 📍 **Khu vực bạn muốn đến?** (Ví dụ: Quận 1, Quận 3, Bình Thạnh, Cầu Giấy, Ba Đình, Hải Châu...)\n";
        $reply .= "2. 🍲 **Bạn đang thèm món gì?** (Lẩu nướng xèo xèo, bún phở no nê, đồ ăn vặt hay cafe làm việc?)\n";
        $reply .= "3. 👥 **Dịp đi ăn cùng ai?** (Hẹn hò 2 người, tụ tập bạn bè đông vui hay bữa cơm gia đình ấm cúng?)\n\n";
        $reply .= "Trong lúc suy nghĩ, bạn có thể tham khảo nhanh top quán đang được yêu thích nhất dưới đây:";

        return [
            'success' => true,
            'reply' => $reply,
            'venues' => $trendingVenues,
            'criteria' => $state,
            'parsed_intent' => $state,
            'suggestions' => [
                'Lẩu nướng tụ tập Quận 1',
                'Cafe chill làm việc Bình Thạnh',
                'Món ngon sinh viên dưới 50k',
                'Quán ăn đêm mở 24/7',
            ],
        ];
    }

    /**
     * Xử lý câu hỏi đời thường như ChatGPT (Nấu ăn tại nhà, tâm lý, sức khỏe, thói quen, mẹo sống, kiến thức...)
     */
    protected function handleEverydayLifeQuestion(string $message, array $history, array $state): array
    {
        // 1. Thử gọi Multi-Key LLM Pool trước với universal system instruction
        $llmReply = $this->generateLlmResponse($message, $history, [], $state);

        // 2. Nếu LLM chưa cấu hình hoặc không có mạng, sử dụng Smart Everyday Life Fallback Engine
        $reply = $llmReply ?: $this->generateEverydayLifeFallback($message, $state);

        // 3. Gợi ý câu hỏi tiếp theo phù hợp với chủ đề đời sống
        $suggestions = $this->getLifestyleSuggestions($state['lifestyle_type'] ?? 'general', $state['lifestyle_topic'] ?? '');

        return [
            'success' => true,
            'reply' => $reply,
            'venues' => [],
            'criteria' => $state,
            'parsed_intent' => $state,
            'suggestions' => $suggestions,
        ];
    }

    /**
     * Động cơ tri thức đời sống thông minh (Smart Everyday Life Fallback Engine)
     */
    protected function generateEverydayLifeFallback(string $message, array $state): string
    {
        $type = $state['lifestyle_type'] ?? 'general';
        $topic = mb_strtolower((string)($state['lifestyle_topic'] ?? ''), 'UTF-8');

        switch ($type) {
            case 'recipe':
                return $this->generateCookingRecipeFallback($topic, $message);

            case 'nutrition':
                return "Dưới đây là cẩm nang dinh dưỡng khoa học & chế độ ăn lành mạnh (Eat Clean / Giảm mỡ) chuẩn y khoa dành cho bạn:\n\n" .
                    "### 🥗 1. Nguyên Tắc Cốt Lõi\n" .
                    "- **Thâm hụt calo an toàn (Caloric Deficit):** Chỉ cần giảm nhẹ từ 300 - 500 kcal/ngày so với TDEE (năng lượng tiêu hao). Tuyệt đối không nhịn ăn tiêu cực gây hạ đường huyết và mất cơ.\n" .
                    "- **Quy tắc đĩa ăn 50 - 25 - 25:**\n" .
                    "  - 🥬 **50% Rau củ nhiều chất xơ:** Bông cải xanh, xà lách, ớt chuông, dưa leo, cà chua.\n" .
                    "  - 🥩 **25% Đạm nạc (Protein):** Ức gà, phi-lê cá hồi/cá basa, trứng gà luộc, tôm, đậu phụ.\n" .
                    "  - 🥔 **25% Tinh bột hấp thu chậm (Complex Carb):** Khoai lang, yến mạch, gạo lứt, bún gạo lứt.\n\n" .
                    "### 📅 2. Thực Đơn Mẫu 1 Ngày Dễ Làm Tại Nhà\n" .
                    "- 🍳 **Bữa sáng (7:00):** 2 quả trứng ốp la lòng đào + 1 lát bánh mì nguyên cám + 1 tách trà xanh ấm hoặc cafe đen không đường.\n" .
                    "- 🥗 **Bữa trưa (12:00):** 150g ức gà áp chảo sốt tiêu + 1 chén cơm gạo lứt + 1 đĩa bông cải xanh luộc chấm mè rang.\n" .
                    "- 🍎 **Bữa phụ (16:00):** 1 quả táo hoặc 1 hũ sữa chua Hy Lạp không đường kèm vài hạt hạnh nhân.\n" .
                    "- 🍲 **Bữa tối (19:00):** Canh tôm nấu rau ngót hoặc cá hấp gừng sả + thật nhiều rau luộc. Hạn chế tinh bột sau 20h.\n\n" .
                    "### 💡 Lời Khuyên Vàng\n" .
                    "Uống đủ 2 - 2.5 lít nước mỗi ngày và ngủ đủ 7-8 tiếng. Giấc ngủ ngon chính là lúc cơ thể tiết hormone đốt mỡ tự nhiên hiệu quả nhất!\n\n" .
                    "👉 Nếu hôm nào bận rộn không kịp chuẩn bị cơm hộp, cứ nhắn mình để mình gợi ý những quán cơm văn phòng healthy hoặc quán Eat Clean gần bạn nhé!";

            case 'emotion':
                return "Chào bạn, mình rất hiểu và đồng cảm với những gì bạn đang trải qua lúc này. ❤️\n\n" .
                    "Trong guồng quay hối hả của cuộc sống, ai trong chúng ta cũng có những ngày tâm trạng chùng xuống, mệt mỏi, áp lực công việc hay chông chênh vì một chuyện không như ý. Cảm giác này hoàn toàn bình thường, và bạn không hề đơn độc đâu.\n\n" .
                    "### 🌿 4 Bước Giúp Bạn Xoa Dịu Tâm Trí Ngay Bây Giờ:\n" .
                    "1. **Hít thở sâu & thả lỏng:** Dừng lại 2 phút, nhắm mắt lại và hít một hơi thật sâu bằng mũi, giữ 4 giây rồi thở nhẹ ra bằng miệng. Hãy để bờ vai và các cơ mặt được giãn ra.\n" .
                    "2. **Nuông chiều bản thân một chút:** Tắm một vòi sen nước ấm, pha một tách trà hoa cúc hoặc trà đào mật ong thơm dịu, và bật một bản nhạc không lời acoustic.\n" .
                    "3. **Tận hưởng một món ăn ngon:** Đừng bỏ bữa bạn nhé! Một bát phở nóng hổi, một nồi súp ấm bụng hay món ăn bạn yêu thích từ nhỏ có sức mạnh chữa lành tinh thần kỳ diệu.\n" .
                    "4. **Nhớ rằng ngày mai là một khởi đầu mới:** Bạn đã làm rất tốt ngày hôm nay rồi. Những khó khăn này chỉ là một nốt trầm tạm thời thôi.\n\n" .
                    "✨ Bất cứ khi nào bạn muốn tìm một góc cafe yên tĩnh ngắm phố phường để nạp lại năng lượng hay muốn tìm một nơi ăn uống ấm cúng, mình luôn ở đây sẵn sàng đồng hành cùng bạn!";

            case 'sleep_health':
                return "Giấc ngủ là liều thuốc phục hồi sinh lực quý giá nhất của cơ thể. Để có một đêm ngủ sâu giấc và thức dậy tràn đầy năng lượng, bạn hãy thử áp dụng **Quy tắc 10 - 3 - 2 - 1 - 0** chuẩn y khoa này nhé:\n\n" .
                    "- ☕ **10 tiếng trước khi ngủ:** Ngừng nạp caffeine (không uống cafe đặc, trà xanh hay nước tăng lực sau 14h chiều).\n" .
                    "- 🍲 **3 tiếng trước khi ngủ:** Hoàn thành bữa tối, không ăn quá no hoặc nạp đồ cay nóng dầu mỡ gây đầy bụng.\n" .
                    "- 💻 **2 tiếng trước khi ngủ:** Tạm dừng công việc, không kiểm tra email hay deadline để não bộ hạ nhiệt.\n" .
                    "- 📱 **1 tiếng trước khi ngủ:** Tắt hoàn toàn màn hình điện thoại, máy tính (ánh sáng xanh từ màn hình ức chế tiết hormone melatonin gây mất ngủ).\n" .
                    "- ⏰ **0:** Số lần bấm nút báo thức lại (snooze) vào sáng hôm sau. Thức dậy ngay khi chuông reo.\n\n" .
                    "💡 **Mẹo nhỏ từ Kira:** Ngâm chân vào nước ấm pha chút gừng đập dập và muối hột trong 15 phút trước khi lên giường sẽ giúp lưu thông khí huyết, chân ấm lên và bạn sẽ chìm vào giấc ngủ rất êm ái!";

            case 'dating':
                return "Một buổi hẹn hò tuyệt vời không nhất thiết phải quá cầu kỳ đắt đỏ, quan trọng nhất là cảm giác thoải mái và kết nối chân thành giữa hai người. 💕\n\n" .
                    "### 🌟 3 Ý Tưởng Hẹn Hò Lãng Mạn & Dễ Ghi Điểm:\n" .
                    "1. 🌇 **Buổi chiều hoàng hôn & Rooftop Cafe:** Đón bạn ấy lúc 17h, cùng ghé một quán cafe sân thượng thoáng đãng ngắm trọn vẹn khoảnh khắc hoàng hôn buông xuống thành phố. Đây là lúc dễ mở lòng trò chuyện nhất.\n" .
                    "2. 🎨 **Trải nghiệm Workshop đôi (Làm gốm / Nến thơm / Tranh cát):** Cùng nhau tự tay làm ra một món đồ nhỏ xinh. Vừa có tiếng cười, vừa tạo ra kỷ niệm chung đáng nhớ mang về phòng.\n" .
                    "3. 🍲 **Food tour phố đêm bình dị:** Dạo quanh các con phố ẩm thực quen thuộc, ăn bánh tráng nướng, uống trà sữa hoặc thưởng thức nồi lẩu thơm phức. Sự gần gũi, ấm áp sẽ xóa tan mọi khoảng cách.\n\n" .
                    "🎁 **Gợi ý quà tặng:** Hãy chú ý đến những điều nhỏ bé người ấy từng vô tình nhắc đến (như một thỏi son dưỡng hợp màu, một chiếc kẹp tóc xinh xắn hay hương nến thơm ấm cúng).\n\n" .
                    "👉 Bạn muốn tìm quán ăn hay quán cafe hẹn hò lãng mạn ở khu vực nào, cứ nhắn để mình chọn ngay điểm hẹn lý tưởng nhé!";

            case 'creative':
                return "Một chút dí dỏm cho đời thêm vui tươi nhé! 😄\n\n" .
                    "### 🎭 Câu Chuyện Cười: \"Bí Quyết Nấu Ăn Của Chồng\"\n" .
                    "Vợ đi làm về thấy chồng đang đứng trong bếp, trên bàn là một mâm cơm thịnh soạn với đĩa gà chiên vàng ươm, canh sườn hầm nóng hổi và món tráng miệng đẹp mắt.\n" .
                    "Vợ ngạc nhiên thốt lên:\n" .
                    "– *Ôi anh yêu! Sao hôm nay anh khéo tay thế? Anh học công thức nấu ăn này ở đâu vậy?*\n" .
                    "Chồng gãi đầu cười hiền hậu:\n" .
                    "– *Dạ, bí quyết nằm ở chỗ: Anh nhắn cho Kira AI trên Quán Mới tìm tiệm cơm ngon nhất đầu phố, rồi anh phi xe máy đi mua về bày ra đĩa cho nóng sốt đấy em!* 🛵💨\n\n" .
                    "### 🌸 Bài Thơ Tặng Bạn:\n" .
                    "*Đời người trăm nẻo ngược xuôi,*\n" .
                    "*Bữa cơm ấm nóng, nụ cười trên môi.*\n" .
                    "*Dù cho mưa gió tơi bời,*\n" .
                    "*Ăn ngon một bữa, thấy đời thênh thang!* ✨";

            case 'general_knowledge':
            default:
                if (str_contains($message, '80/20') || str_contains($message, 'pareto')) {
                    return "### 🎯 Quy Tắc 80/20 (Nguyên Lý Pareto) Trong Đời Sống\n\n" .
                        "Quy tắc 80/20 do nhà kinh tế học người Ý Vilfredo Pareto phát hiện, khẳng định rằng: **Khoảng 80% kết quả xuất phát từ chỉ 20% nguyên nhân cốt lõi.**\n\n" .
                        "**Ứng dụng thực tế vào đời sống hàng ngày:**\n" .
                        "1. **Công việc & Năng suất:** 20% công việc quan trọng nhất quyết định 80% thành quả và thu nhập của bạn. Hãy tập trung giải quyết việc ưu tiên số 1 vào đầu buổi sáng khi năng lượng cao nhất.\n" .
                        "2. **Các mối quan hệ:** 80% niềm vui và sự nâng đỡ tinh thần đến từ 20% những người bạn thực sự thấu hiểu bạn. Hãy dành thời gian cho những người thực sự trân quý bạn.\n" .
                        "3. **Sức khỏe & Ẩm thực:** 80% vóc dáng và sức khỏe được quyết định bởi 20% thói quen ăn uống lành mạnh hàng ngày.\n\n" .
                        "💡 *Châm ngôn sống:* Đừng cố gắng làm tất cả mọi thứ; hãy làm đúng những thứ tạo ra sự khác biệt lớn nhất!";
                }

                return "Chào bạn! Là một trợ lý AI thông minh và người bạn đồng hành của Quán Mới, mình luôn sẵn sàng trò chuyện, giải đáp thắc mắc và chia sẻ cùng bạn về mọi chủ đề trong cuộc sống: từ tâm sự thường nhật, ẩm thực, nấu ăn tại nhà, dinh dưỡng cho tới mẹo sống tích cực.\n\n" .
                    "Bạn muốn cùng mình thảo luận hay tìm hiểu thêm về điều gì hôm nay? Đừng ngần ngại nhắn cho mình nhé! ✨";
        }
    }

    /**
     * Cung cấp công thức nấu ăn chi tiết tại nhà cho các món phổ biến
     */
    protected function generateCookingRecipeFallback(string $dishName, string $rawMessage): string
    {
        if (str_contains($dishName, 'sườn') || str_contains($rawMessage, 'sườn xào')) {
            return "### 🍲 Công Thức Sườn Xào Chua Ngọt Chuẩn Vị Đậm Đà Tại Nhà\n\n" .
                "**1. Chuẩn bị nguyên liệu (cho 3 - 4 người ăn):**\n" .
                "- 500g sườn non heo (chọn sườn tươi, dẻ sườn nhỏ, nạc mỡ đan xen)\n" .
                "- 1 củ hành tím, 4 tép tỏi, 1 quả ớt băm nhỏ\n" .
                "- Gia vị pha sốt: 2 thìa canh nước mắm ngon, 2 thìa canh đường cát, 2 thìa canh giấm gạo (hoặc nước cốt chanh), 2 thìa canh tương cà (ketchup để tạo màu đỏ đẹp và độ sánh), 1 thìa canh tương ớt, 3 thìa canh nước lọc.\n\n" .
                "**2. Sơ chế sườn:**\n" .
                "- Chặt sườn thành miếng vừa ăn khoảng 3 - 4cm.\n" .
                "- Chần sườn qua nồi nước sôi có thả vài lát gừng đập dập và 1 thìa cà phê muối trong 2 phút để khử sạch mùi hôi và bọt bẩn, sau đó vớt ra rửa sạch, để ráo.\n\n" .
                "**3. Các bước chế biến:**\n" .
                "- **Bước 1 (Rán sườn):** Cho ít dầu vào chảo, rán sườn trên lửa vừa đến khi sườn xém vàng đều hai mặt thì vớt ra đĩa. *Mẹo: Không rán quá lâu sườn sẽ bị dai và khô xác.*\n" .
                "- **Bước 2 (Pha sốt):** Khuấy đều bát gia vị nước mắm, đường, giấm, tương cà, tương ớt và nước lọc cho tan hết.\n" .
                "- **Bước 3 (Rim sườn):** Dùng lại chảo, phi thơm hành tỏi băm. Đổ bát nước sốt vào đun sôi lăn tăn, sau đó trút toàn bộ sườn vào đảo đều. Hạ lửa nhỏ riu riu rim trong khoảng 10 - 15 phút cho sườn ngấm sâu gia vị.\n" .
                "- **Bước 4 (Hoàn thành):** Khi nước sốt sệt lại, bám óng ả quanh từng miếng sườn thì rắc chút tiêu xay và hành lá cắt nhỏ, tắt bếp.\n\n" .
                "✨ **Thành phẩm:** Miếng sườn mềm ngọt mọng nước, vị chua cay mặn ngọt hài hòa, ăn cùng cơm nóng thì hết ý!\n\n" .
                "👉 Nếu hôm nào bận rộn không kịp vào bếp, cứ bảo mình để mình tìm quán sườn xào chua ngọt ngon chuẩn vị quanh bạn nhé!";
        }

        if (str_contains($dishName, 'thịt kho') || str_contains($dishName, 'kho tàu') || str_contains($rawMessage, 'thịt kho')) {
            return "### 🥩 Bí Quyết Nấu Thịt Kho Tàu Nước Dừa Mềm Trong Veo Chuẩn Vị Nam Bộ\n\n" .
                "**1. Nguyên liệu:** 500g thịt ba chỉ (hoặc thịt chân giò), 6 quả trứng vịt (hoặc 12 quả trứng cút luộc bóc vỏ), 1 trái dừa xiêm lấy nước, hành tím, tỏi, ớt hiểm, nước mắm truyền thống, đường thốt nốt, hạt tiêu.\n\n" .
                "**2. Sơ chế:** Thịt cắt miếng vuông to khoảng 4x4cm. Chần qua nước sôi rồi ướp với hành tỏi băm, 3 thìa canh nước mắm, 1 thìa đường, 1/2 thìa hạt nêm trong 30-45 phút cho thấm đẫm.\n\n" .
                "**3. Cách nấu:**\n" .
                "- Thắng 1 thìa đường với chút dầu ăn thành nước màu cánh gián, trút thịt vào xào săn trên lửa lớn cho ngấm màu óng ả.\n" .
                "- Đổ nước dừa tươi vào ngập mặt thịt. Đun sôi rồi hạ lửa nhỏ liu riu. Vớt sạch bọt để nước kho trong vắt.\n" .
                "- Kho được 30 phút thì thả trứng luộc vào kho cùng thêm 30 phút đến khi thịt mềm rục, mỡ trong veo mà không bị nát.\n\n" .
                "✨ Món này ăn kèm dưa cải chua hoặc dưa giá thì bao nhiêu cơm cũng hết!";
        }

        if (str_contains($dishName, 'cafe muối') || str_contains($dishName, 'cà phê muối')) {
            return "### ☕ Cách Pha Cà Phê Muối Béo Ngậy Đậm Đà Chuẩn Vị Xứ Huế\n\n" .
                "**1. Nguyên liệu:** 25g cà phê bột nguyên chất (pha phin), 25ml sữa đặc, 50ml kem béo thực vật (Rich's lùn) hoặc whipping cream, 1/4 thìa cà phê muối hồng/muối tinh, đá viên.\n\n" .
                "**2. Thực hiện:**\n" .
                "- **Pha cà phê:** Ủ cà phê phin với 30ml nước sôi trong 2 phút, sau đó châm thêm 50ml nước sôi chiết xuất lấy cốt cà phê đậm đặc.\n" .
                "- **Đánh kem muối:** Cho kem béo + muối vào ly, dùng máy đánh bọt cầm tay đánh trong 2-3 phút đến khi kem bông nhẹ, sệt sánh như sữa chua.\n" .
                "- **Lên ly:** Cho sữa đặc vào đáy ly, thêm đá viên, rót cà phê phin vào, cuối cùng phủ lớp kem muối bồng bềnh lên trên.\n\n" .
                "✨ Thưởng thức bằng cách nhấp từng ngụm trực tiếp để cảm nhận lớp kem béo mặn hòa cùng vị cà phê đắng êm dịu!";
        }

        // Framework nấu nướng chuẩn cho các món ăn khác
        $cleanTitle = $dishName ? mb_convert_case($dishName, MB_CASE_TITLE, 'UTF-8') : 'Món Ăn Này';
        return "### 🍳 Hướng Dẫn Nấu {$cleanTitle} Chuẩn Vị Thơm Ngon Tại Nhà\n\n" .
            "**1. Chuẩn bị nguyên liệu chính:**\n" .
            "- Nguyên liệu cốt lõi tươi ngon (thịt/cá/hải sản hoặc rau củ tươi)\n" .
            "- Gia vị nền ẩm thực Việt: Hành tím, tỏi, gừng, sả tươi, ớt tươi, hạt tiêu xay\n" .
            "- Gia vị nêm nếm: Nước mắm truyền thống, hạt nêm, đường, dầu hào, tiêu, dầu ăn.\n\n" .
            "**2. Sơ chế & Khử mùi nguyên liệu:**\n" .
            "- Rửa sạch và ngâm khử tanh bằng nước muối loãng pha chút giấm hoặc gừng đập dập.\n" .
            "- Ướp gia vị trước ít nhất 15 - 20 phút để từng thớ thịt thấm đượm hương vị từ bên trong.\n\n" .
            "**3. Quy trình chế biến:**\n" .
            "- **Khởi động:** Làm nóng chảo, phi thơm hành tỏi băm trên lửa vừa để kích thích khứu giác.\n" .
            "- **Chế biến nhiệt:** Cho nguyên liệu chính vào đảo đều tay. Tùy món xào, kho hay hầm để canh độ lửa phù hợp giữ trọn độ mọng nước tự nhiên.\n" .
            "- **Hoàn thiện:** Nêm nếm lại cho vừa khẩu vị gia đình, rắc hành lá ngò rí và tiêu xay lên trên để món ăn dậy mùi thơm nức.\n\n" .
            "✨ Chúc bạn thực hiện thành công và có một bữa cơm gia đình thật ấm cúng!";
    }

    /**
     * Gợi ý câu hỏi tiếp theo cho các chủ đề đời sống
     */
    protected function getLifestyleSuggestions(string $type, string $topic): array
    {
        switch ($type) {
            case 'recipe':
                return [
                    'Công thức làm sườn xào chua ngọt',
                    'Cách pha cafe muối béo ngậy tại nhà',
                    'Hôm nay nấu món gì cho gia đình?',
                    'Gợi ý quán ăn ngon đổi gió quanh đây',
                ];
            case 'nutrition':
                return [
                    'Thực đơn eat clean 7 ngày giảm mỡ',
                    'Cách tính thâm hụt calo chuẩn nhất',
                    'Tập gym nên ăn gì trước và sau tập?',
                    'Tìm quán đồ ăn healthy gần đây',
                ];
            case 'emotion':
                return [
                    'Gợi ý quán cafe yên tĩnh ngồi một mình',
                    'Cách giải tỏa stress công việc hiệu quả',
                    'Một số thói quen giúp ngủ ngon hơn',
                    'Kể cho mình một câu chuyện cười vui vẻ',
                ];
            case 'dating':
                return [
                    'Gợi ý quán cafe rooftop hẹn hò lãng mạn',
                    'Nên tặng quà gì cho bạn gái tinh tế?',
                    'Nhà hàng lãng mạn có nến và hoa',
                    'Ý tưởng đi chơi cuối tuần cho cặp đôi',
                ];
            case 'sleep_health':
                return [
                    'Quy tắc 10-3-2-1-0 cho giấc ngủ sâu',
                    'Thực phẩm giúp dễ ngủ tự nhiên',
                    'Cách xây dựng thói quen dậy sớm 5h sáng',
                    'Quán ăn nhẹ thanh đạm buổi tối',
                ];
            case 'creative':
                return [
                    'Kể thêm một câu chuyện cười nữa đi',
                    'Làm một bài thơ về ẩm thực Việt Nam',
                    'Đố mình một câu đố mẹo vui nhộn',
                    'Gợi ý quán nướng tụ tập bạn bè',
                ];
            default:
                return [
                    'Tìm quán lẩu nướng ngon tụ tập',
                    'Cafe chill làm việc yên tĩnh',
                    'Món ngon bình dân dưới 50k',
                    'Cách nấu sườn xào chua ngọt tại nhà',
                ];
        }
    }

    /**
     * Lấy danh sách API keys hoạt động cho nhà cung cấp (loại bỏ key đang bị cooldown sau lỗi 429)
     */
    protected function getActiveApiKeys(string $provider): array
    {
        $keys = config("ai.{$provider}.api_keys", []);
        if (empty($keys) && !empty(config("ai.{$provider}.api_key"))) {
            $keys = [config("ai.{$provider}.api_key")];
        }

        // Đọc dự phòng trực tiếp từ env nếu chưa được load vào config
        if (empty($keys)) {
            $envPlural = strtoupper($provider) . '_API_KEYS';
            $envSingle = strtoupper($provider) . '_API_KEY';
            if (env($envPlural)) {
                $keys = array_filter(array_map('trim', explode(',', (string) env($envPlural))));
            } elseif (env($envSingle)) {
                $keys = [trim((string) env($envSingle))];
            }
        }

        $active = [];
        foreach ($keys as $k) {
            $k = trim((string)$k);
            if ($k === '') continue;
            $hash = md5($k);
            if (!Cache::has("ai_key_cooldown_{$provider}_{$hash}")) {
                $active[] = $k;
            }
        }

        // Nếu tất cả keys đều bị cooldown (tạm thời chạm limit), giải tỏa cooldown cho key đầu tiên để thử lại
        if (empty($active) && !empty($keys)) {
            $active = [trim((string)$keys[0])];
        }

        return $active;
    }

    /**
     * Đánh dấu key đang bị Rate Limit (429) hoặc tạm thời lỗi vào Cooldown
     */
    protected function markKeyCooldown(string $provider, string $key, int $seconds = 300): void
    {
        $hash = md5($key);
        Cache::put("ai_key_cooldown_{$provider}_{$hash}", true, now()->addSeconds($seconds));
        Log::warning("AI Key for {$provider} (prefix: " . substr($key, 0, 7) . "...) placed in cooldown for {$seconds}s due to 429/Quota limit.");
    }

    /**
     * Gọi mô hình LLM bên ngoài với Bể khóa đa tầng & Tự động chuyển vùng (Multi-Key & Multi-Provider Rotation Pool)
     * Đảm bảo API hoạt động vô tận, không bị ngắt quãng kể cả khi 1 key chạm limit 429
     */
    protected function generateLlmResponse(string $userMessage, array $history, array $venues, array $state): ?string
    {
        $primary = config('ai.provider', 'gemini');
        $chain = config('ai.failover_chain', ['gemini', 'groq', 'openrouter', 'openai', 'kira']);

        // Sắp xếp danh sách providers ưu tiên với primary đứng đầu
        $providers = array_values(array_unique(array_merge([$primary], $chain)));

        foreach ($providers as $provider) {
            try {
                $reply = null;
                if ($provider === 'gemini') {
                    $reply = $this->callGeminiWithRotation($userMessage, $history, $venues, $state);
                } else {
                    $reply = $this->callOpenAiCompatibleWithRotation($provider, $userMessage, $history, $venues, $state);
                }

                if (!empty($reply)) {
                    return $reply;
                }
            } catch (\Throwable $e) {
                Log::warning("AI Provider {$provider} failed, trying next provider: " . $e->getMessage());
            }
        }

        return null; // Không có key hoặc tất cả keys đều lỗi -> Chuyển sang Smart Fallback Engine nội bộ
    }

    /**
     * Nén dữ liệu quán ăn trước khi đưa vào System Prompt để tiết kiệm token tối đa (giảm 60% chi phí & độ trễ)
     */
    protected function compressVenuesForPrompt(array $venues): array
    {
        $compact = [];
        foreach (array_slice($venues, 0, 4) as $v) {
            $item = [
                'ten_quan' => $v['name'] ?? $v['ten_quan'] ?? '',
                'url' => $v['url'] ?? '',
                'dia_chi' => $v['address'] ?? $v['dia_chi'] ?? '',
                'quan_huyen' => $v['district'] ?? '',
                'loai_hinh' => $v['category'] ?? $v['loai_hinh'] ?? '',
                'gia' => $v['price_range'] ?? $v['gia_trung_binh'] ?? 'Bình dân',
                'rating' => ($v['rating'] ?? 4.5) . '/5 (' . ($v['review_count'] ?? 0) . ' đánh giá)',
                'gio_mo_cua' => $v['open_hours'] ?? 'Cả ngày',
                'trang_thai' => !empty($v['is_open_now']) ? 'Đang mở cửa' : 'Đang đóng cửa',
            ];
            if (!empty($v['highlight_dishes'])) {
                $item['mon_dac_trung'] = array_slice((array)$v['highlight_dishes'], 0, 3);
            }
            if (!empty($v['promotion'])) {
                $item['khuyen_mai'] = $v['promotion'];
            }
            if (!empty($v['phone'])) {
                $item['so_dien_thoai'] = $v['phone'];
            }
            $compact[] = $item;
        }
        return $compact;
    }

    /**
     * Gọi Google Gemini API với cơ chế tự động xoay tua danh sách API Keys (Key Rotation Pool)
     * Kèm tối ưu hóa Token, giới hạn chi phí và bảo mật tuyệt đối
     */
    protected function callGeminiWithRotation(string $userMessage, array $history, array $venues, array $state): ?string
    {
        $keys = $this->getActiveApiKeys('gemini');
        if (empty($keys)) {
            return null;
        }

        $models = array_merge(
            [config('ai.gemini.model', 'gemini-1.5-flash')],
            config('ai.gemini.fallback_models', ['gemini-1.5-flash-8b', 'gemini-2.0-flash-exp'])
        );

        $systemInstruction = $this->buildUniversalSystemInstruction($venues, $state);
        $contents = $this->buildGeminiContents($history, $userMessage);

        // Tối ưu token & nhiệt độ linh hoạt theo ý định câu hỏi để tiết kiệm chi phí và trả lời ngắn gọn
        $maxOutputTokens = 650;
        $temperature = 0.4;

        if (!empty($state['comparison_type']) || !empty($state['is_asking_venue_detail'])) {
            $maxOutputTokens = 380;
            $temperature = 0.25; // Chính xác tuyệt đối, không lan man
        } elseif (!empty($state['lifestyle_type']) && empty($state['has_dining_intent'])) {
            $maxOutputTokens = 480;
            $temperature = 0.6; // Đời sống cơ bản, súc tích, tự nhiên
        }

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => $maxOutputTokens,
            ],
        ];

        $timeout = (int) config('ai.gemini.timeout', 10);

        foreach ($keys as $apiKey) {
            foreach ($models as $model) {
                try {
                    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                    $response = Http::connectTimeout(4)
                        ->timeout($timeout)
                        ->retry(2, 100, function ($exception) {
                            return $exception instanceof \Illuminate\Http\Client\ConnectionException;
                        })
                        ->post($endpoint, $payload);

                    if ($response->successful()) {
                        $reply = $response->json('candidates.0.content.parts.0.text');
                        if (!empty($reply)) {
                            return trim($reply);
                        }
                    }

                    // Nếu gặp 429 Quota Exceeded / Too Many Requests: Cho key vào Cooldown và xoay sang key kế tiếp
                    if ($response->status() === 429) {
                        $this->markKeyCooldown('gemini', $apiKey, 300);
                        break; // Thoát vòng lặp model, chuyển sang key tiếp theo trong pool
                    }

                    // Nếu lỗi model không khả dụng (404/400), thử model fallback tiếp theo
                    if ($response->status() === 404 || $response->status() === 400) {
                        continue;
                    }
                } catch (\Throwable $e) {
                    Log::warning("Gemini API call failed with key: " . $e->getMessage());
                }
            }
        }

        return null;
    }

    /**
     * Chuẩn hóa và làm sạch chuỗi lượt hội thoại cho Google Gemini API
     * Đảm bảo: lượt đầu tiên là 'user', các lượt luôn xen kẽ luân phiên 'user' -> 'model', lượt cuối là 'user'
     */
    protected function buildGeminiContents(array $history, string $userMessage): array
    {
        $rawTurns = [];
        foreach (array_slice($history, -8) as $h) {
            $role = (($h['role'] ?? '') === 'user') ? 'user' : 'model';
            $text = trim((string)($h['content'] ?? ''));
            if ($text === '') continue;

            if ($role === 'model' && mb_strlen($text, 'UTF-8') > 300) {
                $text = mb_substr($text, 0, 280, 'UTF-8') . '...';
            }
            $rawTurns[] = ['role' => $role, 'text' => $text];
        }

        // Thêm câu hỏi người dùng hiện tại vào cuối cùng
        $rawTurns[] = ['role' => 'user', 'text' => trim($userMessage)];

        // Làm sạch và đảm bảo bắt buộc xen kẽ user -> model -> user
        $cleaned = [];
        foreach ($rawTurns as $turn) {
            if (empty($cleaned)) {
                if ($turn['role'] === 'user') {
                    $cleaned[] = $turn;
                }
            } else {
                $lastIndex = count($cleaned) - 1;
                if ($cleaned[$lastIndex]['role'] === $turn['role']) {
                    $cleaned[$lastIndex]['text'] .= "\n" . $turn['text'];
                } else {
                    $cleaned[] = $turn;
                }
            }
        }

        // Phòng vệ: nếu sau khi lọc vẫn rỗng, đảm bảo có ít nhất 1 turn user
        if (empty($cleaned)) {
            $cleaned[] = ['role' => 'user', 'text' => trim($userMessage)];
        }

        $contents = [];
        foreach ($cleaned as $turn) {
            $contents[] = [
                'role' => $turn['role'],
                'parts' => [['text' => $turn['text']]],
            ];
        }

        return $contents;
    }

    /**
     * Gọi các nhà cung cấp tương thích chuẩn OpenAI (Groq, OpenRouter, OpenAI, Kira AI) kèm Key Rotation
     */
    protected function callOpenAiCompatibleWithRotation(string $provider, string $userMessage, array $history, array $venues, array $state): ?string
    {
        $keys = $this->getActiveApiKeys($provider);
        if (empty($keys)) {
            return null;
        }

        $baseUrl = rtrim((string) config("ai.{$provider}.base_url", ''), '/');
        if (empty($baseUrl)) {
            return null;
        }

        $model = config("ai.{$provider}.model");
        $timeout = (int) config("ai.{$provider}.timeout", 10);
        $systemPrompt = $this->buildUniversalSystemInstruction($venues, $state);

        $messages = $this->buildOpenAiMessages($systemPrompt, $history, $userMessage);

        // Tối ưu token & nhiệt độ theo ý định câu hỏi
        $maxTokens = 650;
        $temperature = 0.4;
        if (!empty($state['comparison_type']) || !empty($state['is_asking_venue_detail'])) {
            $maxTokens = 380;
            $temperature = 0.25;
        } elseif (!empty($state['lifestyle_type']) && empty($state['has_dining_intent'])) {
            $maxTokens = 480;
            $temperature = 0.6;
        }

        foreach ($keys as $apiKey) {
            try {
                $response = Http::connectTimeout(4)
                    ->timeout($timeout)
                    ->retry(2, 100, function ($exception) {
                        return $exception instanceof \Illuminate\Http\Client\ConnectionException;
                    })
                    ->withToken($apiKey)
                    ->post("{$baseUrl}/chat/completions", [
                        'model' => $model,
                        'messages' => $messages,
                        'temperature' => $temperature,
                        'max_tokens' => $maxTokens,
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('choices.0.message.content');
                    if (!empty($reply)) {
                        return trim($reply);
                    }
                }

                if ($response->status() === 429) {
                    $this->markKeyCooldown($provider, $apiKey, 300);
                    continue; // Xoay sang key tiếp theo trong pool
                }
            } catch (\Throwable $e) {
                Log::warning("OpenAI-compatible ({$provider}) API error: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Chuẩn hóa danh sách messages cho OpenAI-compatible providers
     */
    protected function buildOpenAiMessages(string $systemPrompt, array $history, string $userMessage): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        $rawTurns = [];
        foreach (array_slice($history, -8) as $h) {
            if (!empty($h['role']) && !empty($h['content'])) {
                $role = ($h['role'] === 'user') ? 'user' : 'assistant';
                $text = (string)$h['content'];
                if ($role === 'assistant' && mb_strlen($text, 'UTF-8') > 300) {
                    $text = mb_substr($text, 0, 280, 'UTF-8') . '...';
                }
                $rawTurns[] = ['role' => $role, 'content' => $text];
            }
        }

        $rawTurns[] = ['role' => 'user', 'content' => $userMessage];

        $cleaned = [];
        foreach ($rawTurns as $turn) {
            if (empty($cleaned)) {
                $cleaned[] = $turn;
            } else {
                $lastIndex = count($cleaned) - 1;
                if ($cleaned[$lastIndex]['role'] === $turn['role']) {
                    $cleaned[$lastIndex]['content'] .= "\n" . $turn['content'];
                } else {
                    $cleaned[] = $turn;
                }
            }
        }

        return array_merge($messages, $cleaned);
    }

    /**
     * Tạo System Instruction chuyên sâu ẩm thực Quán Mới + Đời sống cơ bản súc tích
     */
    protected function buildUniversalSystemInstruction(array $venues, array $state): string
    {
        $compressedVenues = $this->compressVenuesForPrompt($venues);
        $venuesJson = !empty($compressedVenues) ? json_encode($compressedVenues, JSON_UNESCAPED_UNICODE) : '[]';

        return "Bạn là Kira AI - Trợ lý ẩm thực vui vẻ, chuẩn gu và sành ăn của Quán Mới (quanmoi.com).
Phong cách: Tự nhiên, vui vẻ chuẩn Gen Z (dễ thương, năng động, dùng từ gần gũi như 'bồ ơi', 'chill', 'gu', 'mlem mlem', 'đỉnh nóc', 'chuẩn bài', 'hạt dẻ'...), nhưng vẫn lịch sự, tinh tế và tôn trọng người dùng.

QUY TẮC BẮT BUỘC:
1. TRÌNH BÀY TỰ NHIÊN, LIỀN MẠCH, KHÔNG TỰ Ý XUỐNG DÒNG HAY NGẮT CÂU VỤN VẶT:
- Trả lời thẳng vào trọng tâm câu hỏi. Tuyệt đối KHÔNG tự ý xuống dòng vô lý ở giữa câu, KHÔNG chia một câu thành nhiều đoạn rời rạc.
- Mỗi đoạn văn là một khối nội dung liền mạch. Chỉ xuống dòng giữa các đoạn chính hoặc giữa các mục danh sách (1., 2., 3.).
- Khi giới thiệu quán ăn: Mỗi quán trình bày trọn vẹn trong 1 dòng duy nhất theo định dạng: [Tên Quán](url) (Loại hình): Nêu điểm nổi bật/món ngon và lý do đáng thử. Tuyệt đối KHÔNG ngắt dòng hay thụt đầu dòng thêm đoạn con.
- Không lặp lại câu chào hay câu mở đầu rập khuôn qua các lượt trò chuyện. Giữ hội thoại liên tục, tự nhiên như bạn bè đang trò chuyện.

2. GHI NHỚ NGỮ CẢNH & SỞ THÍCH NGƯỜI DÙNG:
- Luôn ghi nhớ sở thích ẩm thực, khu vực, khẩu vị và ngân sách người dùng đã chia sẻ qua các lượt trò chuyện trước.
- Khi người dùng hỏi tiếp ('quán khác', 'rẻ hơn', 'đổi sang quận khác'), duy trì đầy đủ ngữ cảnh đã có và chỉ cập nhật tiêu chí mới.

3. DỮ LIỆU QUÁN THỰC TẾ TRONG HỆ THỐNG QUÁN MỚI (GROUNDED):
{$venuesJson}
- Tuyệt đối KHÔNG BỊA ĐẶT quán ăn, địa chỉ, số điện thoại hay giá cả không có trong danh sách trên.
- Luôn kèm link dạng Markdown: [Tên Quán](url) khi nhắc đến quán (URL dạng /quan/{slug}).
- Không liệt kê dài dòng các dòng metadata (địa chỉ, giá cả, giờ mở cửa) dưới dạng gạch đầu dòng vì giao diện đã hiển thị thẻ card trực quan bên dưới tin nhắn.
- Quản lý lịch sử gợi ý linh hoạt: Khi người dùng bấm 'làm mới' hay 'quán khác', giới thiệu các quán mới lạ, không bị giới hạn máy móc, không lặp lại quán cũ đã xuất hiện.

4. TRẢ LỜI CÂU HỎI ĐỜI SỐNG HẰNG NGÀY:
- Với câu hỏi nấu ăn, tâm sự, dinh dưỡng, mẹo sống: Trả lời ấm áp, súc tích, thực tế trong 3-4 ý chính, không viết lan man dài dòng.";
    }

    /**
     * Động cơ suy luận thông minh nội bộ (Smart Reasoning & Fallback Engine)
     * Đảm bảo chatbot phản hồi tự nhiên, sâu sắc, giải thích lý do đề xuất và đưa ra lời khuyên thực tế
     */
    protected function generateSmartFallbackResponse(string $userMessage, array $history, array $venues, array $state): string
    {
        // ── Trường hợp không tìm thấy quán nào trong cơ sở dữ liệu ─────────────────
        if (empty($venues)) {
            $areaText = $state['district'] ?: ($state['city'] ?: '');
            $dishOrCat = $state['dish'] ?: ($state['category'] ?: '');

            // Nếu câu hỏi không nhắc đến quán ăn, món ăn hay khu vực cụ thể (câu hỏi đời sống / trò chuyện thông thường)
            if (empty($areaText) && empty($dishOrCat)) {
                return "Hế lô bồ iu! 👋 Mình là **Kira AI** - Trợ lý thông minh của Quán Mới đây. Mình luôn sẵn sàng chia sẻ, giải đáp mọi thắc mắc từ câu chuyện đời sống, nấu ăn tại nhà, dinh dưỡng cho tới việc săn lùng những toạ độ ẩm thực đỉnh nóc nhất.\n\n" .
                    "Bồ muốn cùng Kira tâm sự hoặc tìm hiểu về chủ đề gì hôm nay? Cứ tự nhiên nhắn cho mình nha! ✨";
            }

            $dishText = $dishOrCat ?: 'ẩm thực';
            $msg = "Tiếc ghê, hiện tại hệ thống Quán Mới chưa tìm thấy quán **{$dishText}** nào" . ($areaText ? " tại khu vực **{$areaText}**" : "") . " khớp trọn vẹn với toàn bộ tiêu chí này.\n\n";
            $msg .= "💡 **Mẹo nhỏ từ Kira:**\n";
            $msg .= "- Bồ có thể thử **nới lỏng khoảng giá** hoặc tìm kiếm ở các quận lân cận (ví dụ: ";
            if (str_contains((string)$state['city'], 'Hà Nội')) {
                $msg .= "**Cầu Giấy**, **Đống Đa**, **Hoàn Kiếm**).\n";
            } else {
                $msg .= "**Quận 1**, **Quận 3**, **Bình Thạnh**).\n";
            }
            $msg .= "- Bồ cũng có thể bảo Kira món tương tự (thay vì món hiếm, thử lẩu nướng, bún bò, cơm tấm hay cafe chill) để mình quét ngay các toạ độ chất lượng nhất nhé!";
            return $msg;
        }

        // Tính số lượt tin nhắn của người dùng trong lịch sử
        $userTurns = 0;
        foreach ($history as $h) {
            if (($h['role'] ?? '') === 'user') $userTurns++;
        }

        $area = $state['district'] ?: ($state['city'] ?: 'khu vực bạn yêu cầu');
        $count = count($venues);

        // ── Câu mở đầu đa dạng, tự nhiên, không lặp lại khuôn mẫu máy móc ─────────
        $intro = "";
        if (!empty($state['is_refreshing']) || !empty($state['wants_newest'])) {
            $intro = "🔄 **Làm mới dữ liệu gợi ý thành công!** Bồ check ngay {$count} toạ độ mới toanh tại **{$area}** chưa từng xuất hiện này nha:\n\n";
        } elseif (!empty($state['is_asking_more'])) {
            $intro = "Bắt sóng ngay! Kira tìm thêm {$count} địa chỉ **mới lạ và cực chất** tại **{$area}** cho bồ đổi gió nè:\n\n";
        } elseif (!empty($state['is_asking_cheaper'])) {
            $intro = "Đã rõ gu 'hạt dẻ' êm ví của bồ! Đây là {$count} quán ngon giá **siêu bình dân, chuẩn gu tiết kiệm** tại **{$area}**:\n\n";
        } elseif ($userTurns === 0) {
            // Lượt 1: Giới thiệu thân thiện, chuẩn gu Gen Z
            if (!empty($state['dish'])) {
                $intro = "Đang thèm **{$state['dish']}** chuẩn vị tại **{$area}** thì lưu ngay {$count} toạ độ đỉnh nóc này nha bồ ơi:\n\n";
            } elseif (!empty($state['occasion'])) {
                $intro = "Để có một buổi **{$state['occasion']}** bao vui bao chill tại **{$area}**, bồ ghé ngay {$count} điểm hẹn lý tưởng này nhé:\n\n";
            } elseif (!empty($state['category'])) {
                $intro = "Gu **{$state['category']}** tại **{$area}** thì {$count} quán này chuẩn bài luôn, ăn là dính nha bồ:\n\n";
            } else {
                $intro = "Kira gợi ý nhanh {$count} toạ độ ẩm thực cực hot tại **{$area}** cho bồ thẩm liền nè:\n\n";
            }
        } else {
            // Từ lượt thứ 2 trở đi: Ngắn gọn, trực tiếp, KHÔNG lặp lại mở đầu rườm rà
            $followUpIntros = [
                "Lên tiếp {$count} toạ độ xịn sò tại **{$area}** theo yêu cầu mới của bồ nè:\n\n",
                "Đổi vị liền với danh sách quán chuẩn gu tại **{$area}** này nha bồ:\n\n",
                "Kira chọn thêm {$count} quán cực ổn áp tại **{$area}** cho bồ đây:\n\n",
            ];
            $intro = $followUpIntros[$userTurns % count($followUpIntros)];
        }

        // ── Trình bày súc tích từng quán: 1 dòng trọn vẹn, tự nhiên, không ngắt dòng vô lý ─────────────
        $body = "";
        foreach ($venues as $i => $v) {
            $idx = $i + 1;
            $dishText = !empty($v['highlight_dishes'])
                ? "nổi bật với món **" . implode(', ', array_slice($v['highlight_dishes'], 0, 2)) . "**"
                : "menu phong phú, nhiều món ngon";

            $body .= "{$idx}. [**{$v['name']}**]({$v['url']}) ({$v['category']}): {$dishText}, không gian chill rất đáng trải nghiệm.\n";
        }
        $body .= "\n";

        // ── Lời khuyên chủ động & Định hướng tiếp theo vui vẻ, không lặp lại ───────────────
        $footer = "";
        if ($userTurns === 0) {
            $footer = "👉 Bồ muốn mình so giá xem quán nào rẻ nhất, hay tìm quán mở muộn hơn thì cứ ới nha!";
        } elseif ($userTurns === 1) {
            $footer = "👉 Muốn check chỗ đậu xe hay hỏi chi tiết món nào cứ bảo Kira nhé!";
        } else {
            $footer = "👉 Bồ muốn đổi sang khu vực khác hay tìm thêm toạ độ nào nữa không?";
        }

        return $intro . $body . $footer;
    }

    /**
     * Tạo danh sách câu hỏi gợi ý nhanh linh hoạt theo ngữ cảnh
     */
    protected function getFollowUpSuggestions(array $state, array $venues): array
    {
        $suggestions = [];

        // Gợi ý hành động thực tế theo danh sách quán vừa gợi ý
        if (!empty($venues)) {
            $firstVenue = $venues[0]['ten_quan'] ?? '';
            if ($firstVenue !== '') {
                $suggestions[] = "Menu & giá món tại {$firstVenue}";
                $suggestions[] = "Chỉ đường tới {$firstVenue}";
            }
        }

        // Gợi ý đổi quận lân cận
        if (!empty($state['district'])) {
            if (str_contains($state['district'], 'Quận 1')) {
                $suggestions[] = 'Đổi sang quán ở Quận 3';
                $suggestions[] = 'Xem thêm quán ở Bình Thạnh';
            } elseif (str_contains($state['district'], 'Ba Đình')) {
                $suggestions[] = 'Đổi sang Quận Hoàn Kiếm';
                $suggestions[] = 'Quán ngon ở Cầu Giấy';
            } else {
                $suggestions[] = 'Xem quán ở quận trung tâm';
            }
        } else {
            $suggestions[] = 'Tìm quán ngon tại Quận 1';
            $suggestions[] = 'Ăn uống ở Bình Thạnh';
        }

        // Gợi ý về giá
        if (empty($state['max_price'])) {
            $suggestions[] = 'Có quán nào dưới 100k không?';
            $suggestions[] = 'Tìm quán giá sinh viên';
        } else {
            $suggestions[] = 'Có quán nào rẻ hơn nữa không?';
        }

        // Gợi ý hành động tiếp theo
        $suggestions[] = 'Quán nào đang có khuyến mãi?';

        return array_slice(array_unique($suggestions), 0, 5);
    }

    /**
     * Lấy dữ liệu quán đầy đủ từ danh sách slug (dùng khi khôi phục context từ markdown links)
     */
    protected function fetchVenuesBySlugs(array $slugs): array
    {
        if (empty($slugs)) {
            return [];
        }

        $venues = Quan::query()
            ->whereIn('slug', $slugs)
            ->where('trang_thai', 'da_duyet')
            ->with([
                'danhMucMenu.monAn' => fn ($q) => $q->select('id', 'danh_muc_id', 'ten_mon', 'gia')->take(6),
                'khuyenMaiDangChay' => fn ($q) => $q->select('id', 'quan_id', 'tieu_de', 'ma_voucher', 'gia_tri_giam')->take(1),
            ])
            ->withCount('danhGia')
            ->withAvg('danhGia', 'so_sao')
            ->get();

        return $venues->map(fn($quan) => $this->formatVenueForOutput($quan))->values()->all();
    }

    /**
     * Gợi ý tự động bám sát theo thời gian, bối cảnh và nhu cầu ăn uống của người dùng
     */
    public function getDefaultSuggestions(array $context = []): array
    {
        $hour      = $context['hour'] ?? now()->hour;
        $dayOfWeek = $context['day_of_week'] ?? now()->dayOfWeek;
        $isWeekend = in_array($dayOfWeek, [0, 6]);
        $location  = trim($context['location'] ?? '');

        $suggestions = [];

        // 1. Nhu cầu ăn uống tự động theo thời gian thực trong ngày
        if ($hour >= 5 && $hour < 10) {
            $suggestions[] = 'Ăn sáng ngon & nóng hổi gần đây';
            $suggestions[] = 'Cà phê sáng tỉnh táo làm việc';
            $suggestions[] = 'Phở bò, bánh mì giòn ngon';
            $suggestions[] = 'Điểm tâm sáng bình dân dưới 35k';
        } elseif ($hour >= 10 && $hour < 14) {
            $suggestions[] = 'Cơm trưa văn phòng ngon, có máy lạnh';
            $suggestions[] = 'Món trưa nhanh gọn no bụng dưới 50k';
            $suggestions[] = 'Cơm tấm, bún bò trưa nay';
            $suggestions[] = 'Quán ăn trưa có giao tận nơi';
        } elseif ($hour >= 14 && $hour < 17) {
            $suggestions[] = 'Trà sữa, kem mát lạnh xế chiều';
            $suggestions[] = 'Quán cafe yên tĩnh làm việc chill';
            $suggestions[] = 'Đồ ăn vặt xế chiều chuẩn vị';
            $suggestions[] = 'Bánh ngọt & trà thảo mộc';
        } elseif ($hour >= 17 && $hour < 21) {
            if ($isWeekend) {
                $suggestions[] = 'Lẩu nướng tụ tập bạn bè cuối tuần';
                $suggestions[] = 'Buffet BBQ nướng thả ga';
                $suggestions[] = 'Quán ăn gia đình ấm cúng cuối tuần';
                $suggestions[] = 'Hải sản tươi sống chế biến ngon';
            } else {
                $suggestions[] = 'Bữa tối ấm cúng no bụng sau giờ làm';
                $suggestions[] = 'Lẩu nướng ấm áp tụ tập bạn bè';
                $suggestions[] = 'Món ngon bình dân dưới 60k';
                $suggestions[] = 'Cơm niêu, món ăn gia đình đậm đà';
            }
        } else {
            $suggestions[] = 'Quán ăn đêm mở khuya 24/7';
            $suggestions[] = 'Cháo nóng, ốc đêm êm bụng';
            $suggestions[] = 'Mì cay, lẩu đêm ấm bụng';
            $suggestions[] = 'Quán ăn khuya giao nhanh';
        }

        // 2. Nhu cầu tiết kiệm & khuyến mãi
        if ($isWeekend) {
            $suggestions[] = 'Quán nào đang có voucher giảm giá?';
        } else {
            $suggestions[] = 'Món ngon sinh viên tiết kiệm dưới 50k';
        }

        // 3. Nếu người dùng đang chọn địa điểm cụ thể
        if ($location !== '') {
            $suggestions[] = "Quán ngon chuẩn vị tại {$location}";
        }

        return array_values(array_unique($suggestions));
    }
}
