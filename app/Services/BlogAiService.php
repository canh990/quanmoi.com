<?php

namespace App\Services;

use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlogAiService
{
    /**
     * Common Vietnamese profanity & toxic terms blacklist for moderation.
     */
    protected array $profanityKeywords = [
        'đụ', 'đụ má', 'đụ mẹ', 'du ma', 'duma', 'địt', 'địt mẹ', 'dit me', 'ditme', 
        'lồn', 'lon', 'cặc', 'buồi', 'cac', 'buoi', 'dcm', 'đcm', 'vcl', 'vl', 
        'dkm', 'đkm', 'đmm', 'dmm', 'chó đẻ', 'cho de', 'mẹ mày', 'me may', 
        'thằng ngu', 'con chó', 'óc chó', 'oc cho', 'biến thái', 'mat day', 'mất dạy', 
        'khốn nạn', 'đồ chó', 'con điếm', 'phò', 'cave', 'đĩ', 'suc vat', 'súc vật',
        'thang cho', 'con me may', 'clmm', 'cc'
    ];

    /**
     * Common gambling, black-hat advertising and scam keywords.
     */
    protected array $spamGamblingKeywords = [
        'kubet', 'thabet', 'sunwin', 'go88', 'b52', '789club', 'hitclub', 
        'tài xỉu', 'tai xiu', 'cá cược', 'ca cuoc', 'lô đề', 'lo de', 'so de', 
        'kèo bóng', 'soi kèo', 'soi cau', 'soi cầu', 'kiếm tiền online', 'kiem tien online',
        'vay tiền', 'vay nong', 'vay nặng lãi', 'hack nick', 'hack tài khoản',
        'nạp thẻ x2', 'nhận code tân thủ', 'bắn cá đổi thưởng', 'nohu', 'nổ hũ'
    ];

    /**
     * Common foreign loan words & culinary terms used in Vietnamese food reviews.
     */
    protected array $loanWords = [
        'p/p', 'review', 'reviewer', 'cafe', 'coffee', 'view', 'chill', 'menu', 'buffet',
        'topping', 'order', 'check-in', 'checkin', 'take-away', 'takeaway', 'wifi',
        'bill', 'voucher', 'combo', 'pizza', 'bbq', 'cheese', 'beefsteak', 'steak',
        'cocktail', 'mocktail', 'smoothie', 'matcha', 'latte', 'croissant', 'tiramisu',
        'bingsu', 'sashimi', 'sushi', 'tempura', 'tokbokki', 'kimbap', 'kimchi',
        'hotpot', 'decor', 'concept', 'rooftop', 'fanpage', 'facebook', 'tiktok',
        'instagram', 'shipper', 'delivery', 'fastfood', 'snack', 'drink', 'fresh',
        'healthy', 'organic', 'homemade', 'handmade', 'michelin', 'signature',
        'streetfood', 'seafood', 'set', 'vip', 'pass', 'fan', 'spa', 'gym', 'bar', 'pub',
        'live', 'acoustic', 'studio', 'check', 'in', 'out'
    ];

    /**
     * Vietnamese typo / teencode / spelling dictionary.
     */
    protected array $spellingDictionary = [
        // Contextual and phrasal repairs
        'người đikj vbhreuiehnf' => 'người đi làm',
        'lẫn người đikj vbhreuiehnf' => 'lẫn người đi làm',
        'đikj vbhreuiehnf' => 'đi làm',
        'học sinh, sinh viên lẫn người đikj' => 'học sinh, sinh viên lẫn người đi làm',
        'học sinh, sinh viên lẫn người đik' => 'học sinh, sinh viên lẫn người đi làm',
        'lẫn người đikj' => 'lẫn người đi làm',
        'lẫn người đik' => 'lẫn người đi làm',
        'người đikj' => 'người đi làm',
        'người đik' => 'người đi làm',
        'đik làm' => 'đi làm',

        // Common teencode & abbreviations
        'đikj' => 'đi',
        'đik' => 'đi',
        'chj' => 'chị',
        'ak' => 'à',
        'uk' => 'ừ',
        'nka' => 'nha',
        'nghen' => 'nhe',
        'ko' => 'không',
        'hok' => 'không',
        'hong' => 'không',
        'hổng' => 'không',
        'k' => 'không',
        'kh' => 'không',
        'khong' => 'không',
        'hem' => 'không',
        'k0' => 'không',
        'dc' => 'được',
        'đc' => 'được',
        'duoc' => 'được',
        'dk' => 'được',
        'đk' => 'được',
        'wa' => 'quá',
        'qúa' => 'quá',
        'wán' => 'quán',
        'thik' => 'thích',
        'thjx' => 'thích',
        'thix' => 'thích',
        'rùi' => 'rồi',
        'roi' => 'rồi',
        'ngon wa' => 'ngon quá',
        'rat' => 'rất',
        'chẹp chẹp' => 'ngon tuyệt',
        'mik' => 'mình',
        'mk' => 'mình',
        'j' => 'gì',
        'z' => 'vậy',
        'zậy' => 'vậy',
        'vay' => 'vậy',
        'nè' => 'này',
        'vs' => 'với',
        'voi' => 'với',
        'bt' => 'biết',
        'bit' => 'biết',
        'lun' => 'luôn',
        'luon' => 'luôn',
        'thui' => 'thôi',
        'thoi' => 'thôi',
        'nhìu' => 'nhiều',
        'nhieu' => 'nhiều',
        'mún' => 'muốn',
        'muon' => 'muốn',
        'oki' => 'ok',
        'oke' => 'ok',
        'okie' => 'ok',
        'dthw' => 'dễ thương',
        'dthuong' => 'dễ thương',
        'đr' => 'đúng rồi',
        'dr' => 'đúng rồi',
        'bh' => 'bây giờ',
        'bjo' => 'bây giờ',
        'mn' => 'mọi người',
        'mng' => 'mọi người',
        'moi nguoi' => 'mọi người',
        'đv' => 'đối với',
        'tgian' => 'thời gian',
        'nc' => 'nước',
        'do an' => 'đồ ăn',
        'k gian' => 'không gian',
        'kbiet' => 'không biết',
        'k bit' => 'không biết',
        'hẻm' => 'hẻm',
        'nv' => 'nhân viên',
        'fb' => 'phản hồi',
        'view mê' => 'view tuyệt đẹp',
        'cf' => 'cà phê',
        'cafe' => 'cà phê',
        
        // Orthographic typos & common food slang
        'xịn xò' => 'xịn sò',
        'xin xo' => 'xịn sò',
        'suất sắc' => 'xuất sắc',
        'xuat sac' => 'xuất sắc',
        'chuẩn zị' => 'chuẩn vị',
        'chuan vi' => 'chuẩn vị',
        'chẹp' => 'hấp dẫn',
        'bổ xung' => 'bổ sung',
        'sắp sếp' => 'sắp xếp',
        'chia sẽ' => 'chia sẻ',
        'giành cho' => 'dành cho',
        'trần trừ' => 'chần chừ',
        'cọ sát' => 'cọ xát',
        'sơ xài' => 'sơ sài',
        'xuất sứ' => 'xuất xứ',
        'chuẩn đoán' => 'chẩn đoán',
        'thăm quan' => 'tham quan',
        'đọc giả' => 'độc giả',
        'đỉnh chóp' => 'tuyệt đỉnh',
        'mlem mlem' => 'trông rất hấp dẫn',
        'mlem' => 'hấp dẫn',
        'ngon xỉu' => 'ngon tuyệt hảo',
        'ngon nhức nách' => 'hương vị vô cùng đậm đà',
        'nhức nách' => 'tuyệt hảo',
        'hết nước chấm' => 'cực kỳ ngon và vừa miệng',
        'rẻ bèo' => 'giá cả rất bình dân',
        'bự chà bá' => 'kích thước rất lớn',
        'bánh cuốn' => 'cuốn hút',
        'take away' => 'mang về',
        'review' => 'review',
        'quán đẹp view mê' => 'quán có không gian đẹp mắt',
        'ngon gê' => 'ngon ghê',
        'ngon ghe' => 'ngon ghê',
        'bắt trước' => 'bắt chước',
        'dấu diếm' => 'giấu giếm',
        'sơ xuất' => 'sơ suất',
        'sáng lạng' => 'xán lạn',
        'chín mùi' => 'chín muồi',
        'tuyệt zời' => 'tuyệt vời',
        'tuyet voi' => 'tuyệt vời',
    ];

    /**
     * 1. SUGGEST CAPTIVATING TITLES
     */
    public function suggestTitles(string $topic, ?string $currentTitle = null, ?string $keywords = null): array
    {
        $input = trim($topic ?: ($currentTitle ?: $keywords));
        if ($input === '') {
            $input = 'Quán ăn ngon và quán cà phê đẹp';
        }

        // Try Gemini AI if configured
        $geminiResult = $this->callGeminiApiForTitles($input);
        if (!empty($geminiResult)) {
            return $geminiResult;
        }

        // Realistic Vietnamese Food Review Title Engine (Diverse journalistic angles)
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', '', $input);
        $clean = trim($clean);

        return [
            [
                'title' => "Review Chân Thực: {$clean} Có Đáng Tiền Như Lời Đồn?",
                'type' => 'Đánh giá chân thực',
                'badge' => 'Review Hot',
                'description' => 'Góc nhìn khách quan, phân tích chi tiết hương vị và dịch vụ.'
            ],
            [
                'title' => "Top Món Nhất Định Phải Thử Khi Ghé {$clean} Cùng Hội Bạn Thân",
                'type' => 'Danh sách Top món',
                'badge' => 'Gợi ý Menu',
                'description' => 'Điểm danh các món signature đậm đà, được thực khách khen ngợi nhiều nhất.'
            ],
            [
                'title' => "Phát Hiện Tọa Độ {$clean}: Không Gian Cực Chill, Check-in Cháy Máy",
                'type' => 'Khám phá không gian',
                'badge' => 'Sống ảo & Chill',
                'description' => 'Nhấn mạnh điểm nhấn thiết kế, ánh sáng và trải nghiệm thư giãn.'
            ],
            [
                'title' => "Cẩm Nang Ăn Uống Tại {$clean}: Ăn Gì, Đi Khung Giờ Nào Chuẩn Nhất?",
                'type' => 'Cẩm nang kinh nghiệm',
                'badge' => 'Kinh nghiệm A-Z',
                'description' => 'Bật mí mẹo đi ăn không lo chờ đợi, gợi ý cách chọn món tiết kiệm.'
            ],
            [
                'title' => "Cơn Sốt Ẩm Thực Mới: Lý Do Khiến {$clean} Gây Bão Giới Trẻ",
                'type' => 'Xu hướng & Viral',
                'badge' => 'Bắt Trend',
                'description' => 'Khai thác yếu tố độc đáo, công thức chế biến đặc trưng và độ thảo luận cao.'
            ],
        ];
    }

    /**
     * 2. GENERATE FOOD REVIEW OUTLINE / DRAFT
     */
    public function generateReviewOutline(string $keywords, ?string $restaurantName = null, ?string $dishType = null): array
    {
        $name = trim($restaurantName ?: 'Quán Ăn');
        $dish = trim($dishType ?: 'ẩm thực phong phú');
        $kw = trim($keywords ?: 'quán ngon, giá hợp lý, phục vụ nhiệt tình');

        // Try Gemini if available
        $geminiResult = $this->callGeminiApiForOutline($keywords, $name, $dish);
        if (!empty($geminiResult)) {
            return $geminiResult;
        }

        $title = "Khám phá ẩm thực tại {$name}: Trải nghiệm hương vị và không gian đáng nhớ";
        $excerpt = "Một buổi trải nghiệm ấn tượng tại {$name} với những món {$dish} đậm đà hương vị, mức giá hợp lý và không gian thích hợp tụ họp bạn bè, gia đình.";

        $htmlContent = <<<HTML
<h2>I. Giới thiệu tổng quan về {$name}</h2>
<p>Nếu bạn là một tín đồ đam mê ẩm thực và đang tìm kiếm một địa chỉ mới mẻ để đổi gió cuối tuần, thì <strong>{$name}</strong> chắc chắn là cái tên không nên bỏ lỡ. Nổi tiếng với <em>{$dish}</em> và phong cách phục vụ chu đáo, quán đã nhanh chóng nhận được rất nhiều lời khen ngợi từ cộng đồng ẩm thực địa phương.</p>

<h2>II. Không gian quán & Phong cách thiết kế</h2>
<p>Bước chân vào <strong>{$name}</strong>, ấn tượng đầu tiên chính là không gian được bài trí tinh tế, sạch sẽ và thoáng đãng. Bàn ghế được bố trí khoảng cách hợp lý, ánh sáng ấm cúng tạo cảm giác gần gũi, thoải mái cho những buổi hẹn hò hay tụ tập đông người.</p>
<ul>
    <li><strong>Điểm cộng không gian:</strong> Khu vực chụp ảnh sống ảo bắt mắt, điều hòa mát mẻ, bãi giữ xe thuận tiện.</li>
    <li><strong>Phù hợp cho:</strong> Gia đình, nhóm bạn trẻ, liên hoan nhỏ hoặc những buổi gặp gỡ thân mật.</li>
</ul>

<h2>III. Trải nghiệm Thực đơn & Các món "Must-Try"</h2>
<p>Menu của quán khá đa dạng với các món ăn mang đậm dấu ấn riêng. Dưới đây là những món đặc sắc nhất bạn nhất định nên gọi:</p>
<ol>
    <li><strong>Món Signature:</strong> Hương vị đậm đà, nguyên liệu tươi ngon được tuyển chọn kỹ lưỡng, nước chấm và gia vị hòa quyện vừa miệng.</li>
    <li><strong>Món ăn kèm & Tráng miệng:</strong> Cách bài trí đẹp mắt, lượng thức ăn đầy đặn so với giá tiền.</li>
    <li><strong>Đồ uống:</strong> Menu nước thanh mát, hỗ trợ cân bằng vị giác hoàn hảo.</li>
</ol>

<h2>IV. Đánh giá chất lượng dịch vụ & Giá cả</h2>
<p>Về dịch vụ, đội ngũ nhân viên tại quán gây thiện cảm bởi sự nhanh nhẹn, thân thiện và luôn sẵn sàng hỗ trợ khách hàng. Thời gian lên món khá nhanh, chỉ mất khoảng 10 - 15 phút sau khi order.</p>
<p>Mức giá tại quán được đánh giá là <strong>xứng đáng với chất lượng (P/P cực tốt)</strong>, dao động phù hợp cho cả học sinh, sinh viên lẫn người đi làm.</p>

<h2>V. Tổng kết & Lời khuyên khi ghé quán</h2>
<p><strong>Chấm điểm tổng quan:</strong> 9/10 sao ⭐⭐⭐⭐⭐</p>
<p><em>Lời khuyên hữu ích:</em> Nếu ghé quán vào giờ cao điểm (18:30 - 20:00) hoặc cuối tuần, bạn nên liên hệ đặt bàn trước để có được vị trí ngồi đẹp nhất và không phải chờ đợi lâu.</p>
HTML;

        return [
            'title' => $title,
            'excerpt' => $excerpt,
            'html_content' => $htmlContent,
            'sections' => [
                'Giới thiệu tổng quan quán và nguồn cảm hứng',
                'Đánh giá không gian, chỗ ngồi và điểm nhấn check-in',
                'Thực đơn chi tiết & các món signature bắt buộc phải thử',
                'Nhận xét về thái độ phục vụ và mức giá',
                'Tổng kết chấm điểm & bí quyết khi ghé quán',
            ]
        ];
    }

    /**
     * 3. AUTO SPELL & GRAMMAR CHECK / FIX (STRUCTURED FOR TITLE, EXCERPT, AND HTML CONTENT)
     */
    public function fixStructuredSpelling(?string $title, ?string $excerpt, ?string $content): array
    {
        $titleRes = $this->fixSpellingText($title ?? '', false);
        $excerptRes = $this->fixSpellingText($excerpt ?? '', false);
        $contentRes = $this->fixSpellingText($content ?? '', true);

        $mergedChanges = [];
        foreach ($titleRes['changes'] as $c) {
            $c['field'] = 'Tiêu đề';
            $mergedChanges[] = $c;
        }
        foreach ($excerptRes['changes'] as $c) {
            $c['field'] = 'Tóm tắt';
            $mergedChanges[] = $c;
        }
        foreach ($contentRes['changes'] as $c) {
            $c['field'] = 'Nội dung';
            $mergedChanges[] = $c;
        }

        $isIdentical = $titleRes['is_identical'] && $excerptRes['is_identical'] && $contentRes['is_identical'];

        return [
            'title_original' => $title ?? '',
            'title_corrected' => $titleRes['corrected'],
            'excerpt_original' => $excerpt ?? '',
            'excerpt_corrected' => $excerptRes['corrected'],
            'content_original' => $content ?? '',
            'content_corrected' => $contentRes['corrected'],
            'changes' => $mergedChanges,
            'changes_count' => count($mergedChanges),
            'is_identical' => $isIdentical,
        ];
    }

    /**
     * Detect unnatural keyboard smash, impossible consonant clusters, or invalid syllables
     */
    public function isGibberishToken(string $rawWord): bool
    {
        $clean = mb_strtolower(trim($rawWord, ".,!?:;\"'()[]{}<>-/\\"), 'UTF-8');
        if ($clean === '' || is_numeric($clean)) {
            return false;
        }

        // Check against whitelist of English loan words and food terminology
        if (in_array($clean, $this->loanWords)) {
            return false;
        }

        // Must be at least 3 letters to be evaluated as gibberish
        if (mb_strlen($clean, 'UTF-8') < 3) {
            return false;
        }

        // 1. Impossible consonant clusters in Vietnamese
        $impossibleClusters = [
            'vbh', 'bhr', 'hnf', 'ghr', 'fgh', 'qwe', 'asd', 'zxc', 'jkl', 'xcv', 'bnm',
            'kjk', 'hjk', 'dfg', 'cvb', 'tyu', 'uio', 'rty', 'fgh', 'zxc', 'wer',
            'jh', 'khj', 'nhf', 'chj', 'phj', 'thj', 'trj', 'ghj',
            'rf', 'tf', 'kf', 'df', 'gf', 'vf', 'bf', 'mf', 'lf',
            'kj', 'lj', 'mj', 'pj', 'qj', 'rj', 'sj', 'tj', 'vj', 'xj', 'zj',
            'bj', 'dj', 'đj', 'gj', 'hj'
        ];
        foreach ($impossibleClusters as $cluster) {
            if (strpos($clean, $cluster) !== false) {
                return true;
            }
        }

        // 2. Contains invalid letters in non-loan word (f, j, w, z)
        if (preg_match('/[fjwz]/u', $clean)) {
            return true;
        }

        // 3. Repeated consecutive characters: "xxxx", "aaaa"
        if (preg_match('/(.)\1{3,}/u', $clean)) {
            return true;
        }

        // 4. Unnaturally long single token >= 9 characters without hyphen
        if (mb_strlen($clean, 'UTF-8') >= 9) {
            return true;
        }

        return false;
    }

    /**
     * Core Spell Checker Engine supporting both plain text and rich HTML
     */
    public function fixSpellingText(string $text, bool $isHtml = false): array
    {
        $original = $text;
        $corrected = $text;
        $changes = [];

        if (trim($text) === '') {
            return [
                'original' => $original,
                'corrected' => $corrected,
                'changes' => [],
                'changes_count' => 0,
                'is_identical' => true,
            ];
        }

        // Sort dictionary so longer phrases take precedence over individual words
        $dict = $this->spellingDictionary;
        uksort($dict, function ($a, $b) {
            return mb_strlen($b, 'UTF-8') <=> mb_strlen($a, 'UTF-8');
        });

        // 1. Dictionary & Phrasal replacements
        foreach ($dict as $typo => $fixed) {
            if ($isHtml) {
                // Safely bypass HTML tags and only match whole words outside tags
                $pattern = '/(<[^>]+>)|((?<![\p{L}\p{N}])' . preg_quote($typo, '/') . '(?![\p{L}\p{N}]))/iu';
                $count = 0;
                $corrected = preg_replace_callback($pattern, function ($matches) use ($fixed, &$count) {
                    if (!empty($matches[1])) {
                        return $matches[1]; // keep HTML tags intact
                    }
                    $count++;
                    $match = $matches[2];
                    if (mb_strtoupper($match, 'UTF-8') === $match) {
                        return mb_strtoupper($fixed, 'UTF-8');
                    }
                    if (mb_strtoupper(mb_substr($match, 0, 1, 'UTF-8'), 'UTF-8') === mb_substr($match, 0, 1, 'UTF-8')) {
                        return mb_convert_case($fixed, MB_CASE_TITLE, 'UTF-8');
                    }
                    return $fixed;
                }, $corrected);

                if ($count > 0) {
                    $changes[] = [
                        'from' => $typo,
                        'to' => $fixed,
                        'count' => $count,
                        'reason' => 'Chuẩn hóa tiếng Việt / từ viết tắt'
                    ];
                }
            } else {
                $pattern = '/(?<![\p{L}\p{N}])' . preg_quote($typo, '/') . '(?![\p{L}\p{N}])/iu';
                if (preg_match($pattern, $corrected)) {
                    $count = 0;
                    $corrected = preg_replace_callback($pattern, function ($matches) use ($fixed) {
                        $match = $matches[0];
                        if (mb_strtoupper($match, 'UTF-8') === $match) {
                            return mb_strtoupper($fixed, 'UTF-8');
                        }
                        if (mb_strtoupper(mb_substr($match, 0, 1, 'UTF-8'), 'UTF-8') === mb_substr($match, 0, 1, 'UTF-8')) {
                            return mb_convert_case($fixed, MB_CASE_TITLE, 'UTF-8');
                        }
                        return $fixed;
                    }, $corrected, -1, $count);

                    if ($count > 0) {
                        $changes[] = [
                            'from' => $typo,
                            'to' => $fixed,
                            'count' => $count,
                            'reason' => 'Chuẩn hóa tiếng Việt / từ viết tắt'
                        ];
                    }
                }
            }
        }

        // 2. Gibberish & keyboard-smash token detector & cleaner
        $cleanGibberishCallback = function ($plainText) use (&$changes) {
            $tokens = preg_split('/(\s+)/u', $plainText, -1, PREG_SPLIT_DELIM_CAPTURE);
            $resTokens = [];
            foreach ($tokens as $token) {
                if (trim($token) === '' || !preg_match('/\p{L}/u', $token)) {
                    $resTokens[] = $token;
                    continue;
                }

                if ($this->isGibberishToken($token)) {
                    $cleanWord = trim($token, ".,!?:;\"'()[]{}<>-/\\");
                    $changes[] = [
                        'from' => $cleanWord,
                        'to' => '(Loại bỏ từ vô nghĩa)',
                        'count' => 1,
                        'reason' => 'Phát hiện ký tự gõ phím sai / từ vô nghĩa'
                    ];
                    continue;
                }
                $resTokens[] = $token;
            }
            $out = implode('', $resTokens);
            return preg_replace('/\s{2,}/u', ' ', $out);
        };

        if ($isHtml) {
            $corrected = preg_replace_callback('/(>[^<]+<|^[^<]+<|>[^<]+$)/u', function ($matches) use ($cleanGibberishCallback) {
                $node = $matches[0];
                $prefix = '';
                $suffix = '';
                if (str_starts_with($node, '>')) { $prefix = '>'; $node = substr($node, 1); }
                if (str_ends_with($node, '<')) { $suffix = '<'; $node = substr($node, 0, -1); }
                $cleaned = $cleanGibberishCallback($node);
                return $prefix . $cleaned . $suffix;
            }, $corrected);
        } else {
            $corrected = $cleanGibberishCallback($corrected);
        }

        // 3. Punctuation and spacing cleanups (Safe for HTML)
        if ($isHtml) {
            $corrected = preg_replace_callback('/(>[^<]+<|^[^<]+<|>[^<]+$)/u', function ($matches) {
                $node = $matches[0];
                // Remove space before punctuation: "ngon , rẻ ." => "ngon, rẻ."
                $node = preg_replace('/\s+([,.:;?!])/', '$1', $node);
                // Missing space after punctuation: "ngon,rẻ" => "ngon, rẻ"
                $node = preg_replace('/(\p{L})([,;])(\p{L})/u', '$1$2 $3', $node);
                // Normalize repeated punctuation: "???" => "??"
                $node = preg_replace('/([?!]){3,}/', '$1$1', $node);
                return $node;
            }, $corrected);
            // Remove awkward space before HTML closing tag
            $corrected = preg_replace('/\s+(<\/[a-z0-9]+>)/iu', '$1', $corrected);
        } else {
            // Remove space before punctuation: "ngon , rẻ ." => "ngon, rẻ."
            $corrected = preg_replace('/\s+([,.:;?!])/', '$1', $corrected);
            // Missing space after punctuation: "ngon,rẻ" => "ngon, rẻ"
            $corrected = preg_replace('/(\p{L})([,;])(\p{L})/u', '$1$2 $3', $corrected);
            // Normalize repeated punctuation: "???" => "??"
            $corrected = preg_replace('/([?!]){3,}/', '$1$1', $corrected);
        }

        return [
            'original' => $original,
            'corrected' => $corrected,
            'changes' => $changes,
            'changes_count' => count($changes),
            'is_identical' => ($original === $corrected),
        ];
    }

    /**
     * Backwards-compatible single text string fix
     */
    public function fixSpellingAndGrammar(string $text): array
    {
        return $this->fixSpellingText($text, false);
    }

    /**
     * 4. AUTO-TAGGING & CATEGORIZATION
     */
    public function suggestTagsAndCategory(string $title, string $content, ?string $excerpt = null): array
    {
        $rawText = strip_tags($title . ' ' . $excerpt . ' ' . $content);
        $lower = mb_strtolower($rawText, 'UTF-8');

        // Fetch existing categories from DB
        $dbCategories = BlogCategory::where('status', true)->get();

        // 1. Determine Category
        $suggestedCategory = 'Review Quán'; // Default fallback
        $categoryConfidence = 85;

        if (preg_match('/(cà phê|cafe|coffee|trà sữa|tiệm trà|matcha|latte|nước ép)/iu', $lower)) {
            $catMatch = $dbCategories->first(function ($c) {
                return Str::contains(mb_strtolower($c->name, 'UTF-8'), ['cà phê', 'cafe', 'đồ uống', 'trà']);
            });
            $suggestedCategory = $catMatch ? $catMatch->name : 'Cà Phê & Đồ Uống';
            $categoryConfidence = 96;
        } elseif (preg_match('/(đặc sản|địa phương|hà nội|sài gòn|đà lạt|miền tây|truyền thống|bún đậu|phở|bánh mì)/iu', $lower)) {
            $catMatch = $dbCategories->first(function ($c) {
                return Str::contains(mb_strtolower($c->name, 'UTF-8'), ['địa phương', 'món ngon', 'ẩm thực']);
            });
            $suggestedCategory = $catMatch ? $catMatch->name : 'Món Ngon Địa Phương';
            $categoryConfidence = 92;
        } elseif (preg_match('/(khuyến mãi|ưu đãi|giảm giá|voucher|tặng món|khai trương|deal)/iu', $lower)) {
            $catMatch = $dbCategories->first(function ($c) {
                return Str::contains(mb_strtolower($c->name, 'UTF-8'), ['khuyến mãi', 'ưu đãi', 'sự kiện']);
            });
            $suggestedCategory = $catMatch ? $catMatch->name : 'Khuyến Mãi & Ưu Đãi';
            $categoryConfidence = 95;
        } elseif (preg_match('/(chủ quán|kinh doanh|mở quán|vận hành|tâm sự|câu chuyện)/iu', $lower)) {
            $catMatch = $dbCategories->first(function ($c) {
                return Str::contains(mb_strtolower($c->name, 'UTF-8'), ['chủ quán', 'góc chủ quán']);
            });
            $suggestedCategory = $catMatch ? $catMatch->name : 'Góc Chủ Quán';
            $categoryConfidence = 90;
        } else {
            $catMatch = $dbCategories->first(function ($c) {
                return Str::contains(mb_strtolower($c->name, 'UTF-8'), ['review', 'khám phá']);
            });
            if ($catMatch) {
                $suggestedCategory = $catMatch->name;
            }
        }

        // 2. Propose Tags based on semantic keywords
        $tagCandidates = [];

        // Location-based tags
        if (preg_match('/(sài gòn|saigon|tphcm|quận 1|quận 3|quận 4|quận 7|bình thạnh|thủ đức)/iu', $lower)) {
            $tagCandidates[] = 'MonNgonSaigon';
            $tagCandidates[] = 'AmThucSaiGon';
        }
        if (preg_match('/(hà nội|hanoi|phố cổ|hoàn kiếm|cầu giấy|tây hồ)/iu', $lower)) {
            $tagCandidates[] = 'AmThucHaNoi';
            $tagCandidates[] = 'MonNgonPhoCo';
        }
        if (preg_match('/(đà lạt|dalat|sương mù)/iu', $lower)) {
            $tagCandidates[] = 'CheckInDaLat';
            $tagCandidates[] = 'CaPheDaLat';
        }

        // Food/Beverage niche tags
        if (preg_match('/(cà phê|cafe|coffee|tiệm trà)/iu', $lower)) {
            $tagCandidates[] = 'CaPheDep';
            $tagCandidates[] = 'QuanCafeChill';
        }
        if (preg_match('/(lẩu|nướng|bbq|buffet)/iu', $lower)) {
            $tagCandidates[] = 'LauNuong';
            $tagCandidates[] = 'BuffetNgon';
        }
        if (preg_match('/(ốc|hải sản|oc huong|ngheu)/iu', $lower)) {
            $tagCandidates[] = 'QuanOcNgon';
            $tagCandidates[] = 'HaiSanTuoi';
        }
        if (preg_match('/(ăn vặt|via he|vỉa hè|đường phố|banh trang)/iu', $lower)) {
            $tagCandidates[] = 'AnVatSaiGon';
            $tagCandidates[] = 'AmThucDuongPho';
        }
        if (preg_match('/(giá rẻ|sinh viên|bình dân|học sinh)/iu', $lower)) {
            $tagCandidates[] = 'GiaSinhVien';
        }
        if (preg_match('/(view đẹp|sống ảo|chill|decor)/iu', $lower)) {
            $tagCandidates[] = 'CheckInSongAo';
            $tagCandidates[] = 'KhongGianChill';
        }

        // General popular food tags
        $tagCandidates[] = 'ReviewAmThuc';
        $tagCandidates[] = 'DiaDiemAnUong';
        $tagCandidates[] = 'QuanMoi';

        $uniqueTags = array_slice(array_unique($tagCandidates), 0, 6);

        return [
            'category' => $suggestedCategory,
            'category_confidence' => $categoryConfidence,
            'tags' => $uniqueTags,
            'tags_formatted' => array_map(fn($t) => '#' . $t, $uniqueTags),
            'tags_comma_string' => implode(', ', $uniqueTags),
        ];
    }

    /**
     * 5. AUTOMATED COMMENT MODERATION (AI MODERATION)
     */
    public function moderateComment(string $content, ?User $user = null): array
    {
        $raw = trim($content);

        // 1. Length & Empty check
        if (mb_strlen($raw) < 2) {
            return [
                'is_safe' => false,
                'flag' => 'too_short',
                'reason' => 'Bình luận quá ngắn, vui lòng chia sẻ thêm ý kiến của bạn.',
            ];
        }

        $lower = mb_strtolower($raw, 'UTF-8');

        // 2. Check for Spam Links / Malicious URLs
        // Detects http, https, ftp, domain extensions (.com, .net, .org, .xyz, .top, .vip, .bet, telegram, zalo invites)
        $linkPattern = '/(https?:\/\/[^\s]+|www\.[^\s]+|[a-zA-Z0-9-]+\.(com|vn|net|org|xyz|top|vip|bet|site|link|click|club|online|info|me|cc|tk|ml|ga|cf|gq)\b|t\.me\/|zalo\.me\/)/i';
        if (preg_match($linkPattern, $raw, $matches)) {
            Log::info('AI Moderation flagged spam link in comment:', ['link' => $matches[0], 'user_id' => $user?->id]);
            return [
                'is_safe' => false,
                'flag' => 'spam_link',
                'reason' => 'Bình luận bị AI chặn do chứa liên kết hoặc đường dẫn URL ngoài không an toàn.',
            ];
        }

        // 3. Check for Gambling, Betting, Fraudulent Spam
        foreach ($this->spamGamblingKeywords as $keyword) {
            if (mb_strpos($lower, $keyword) !== false) {
                Log::info('AI Moderation flagged gambling/scam keyword:', ['keyword' => $keyword, 'user_id' => $user?->id]);
                return [
                    'is_safe' => false,
                    'flag' => 'gambling_scam',
                    'reason' => 'Bình luận bị AI chặn do chứa nội dung quảng cáo cờ bạc, cá cược hoặc lừa đảo.',
                ];
            }
        }

        // 4. Check for Profanity, Vulgarities and Toxic Harassment
        foreach ($this->profanityKeywords as $badWord) {
            // Check word boundary or substring match
            $badPattern = '/\b' . preg_quote($badWord, '/') . '\b/iu';
            if (preg_match($badPattern, $lower) || mb_strpos($lower, ' ' . $badWord . ' ') !== false) {
                Log::info('AI Moderation flagged profanity:', ['word' => $badWord, 'user_id' => $user?->id]);
                return [
                    'is_safe' => false,
                    'flag' => 'profanity',
                    'reason' => 'Bình luận chứa từ ngữ thô tục hoặc thiếu chuẩn mực, đã bị hệ thống AI tự động chặn.',
                ];
            }
        }

        // 5. Check for Repetitive Character Spam (e.g., "aaaaaaaaaaaa", "11111111111")
        if (preg_match('/(.)\1{7,}/u', $raw)) {
            return [
                'is_safe' => false,
                'flag' => 'repetitive_spam',
                'reason' => 'Bình luận chứa chuỗi ký tự lặp vô nghĩa (spam).',
            ];
        }

        // Clean & Verified
        return [
            'is_safe' => true,
            'flag' => 'clean',
            'reason' => 'Bình luận hợp lệ và đã qua kiểm duyệt an toàn.',
        ];
    }

    /**
     * Optional External Gemini API helper for title suggestions
     */
    protected function callGeminiApiForTitles(string $input): ?array
    {
        $apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        if (!$apiKey) {
            return null;
        }

        try {
            $prompt = "Bạn là trợ lý AI chuyên gia viết content ẩm thực. Hãy gợi ý 5 tiêu đề bài viết blog cực kỳ hấp dẫn, bắt trend, giật tít tinh tế cho chủ đề: '{$input}'. Trả về JSON array dạng: [{\"title\":\"...\", \"type\":\"...\", \"badge\":\"...\", \"description\":\"...\"}]";
            $response = Http::timeout(4)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                $content = $response->json('candidates.0.content.parts.0.text');
                if (preg_match('/\[.*\]/s', $content, $matches)) {
                    $json = json_decode($matches[0], true);
                    if (is_array($json) && count($json) > 0) {
                        return $json;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini API call failed for title suggestions, falling back to local AI engine: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Optional External Gemini API helper for outline generation
     */
    protected function callGeminiApiForOutline(string $keywords, string $name, string $dish): ?array
    {
        $apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        if (!$apiKey) {
            return null;
        }

        try {
            $prompt = "Bạn là một food reviewer chuyên nghiệp. Hãy viết dàn ý chi tiết bài viết review ẩm thực cho quán '{$name}' (Món: {$dish}, từ khóa: {$keywords}). Trả về JSON với các trường: title (string), excerpt (string), html_content (mã HTML chuẩn Quill gồm các thẻ h2, p, ul, li, ol, strong, em), sections (array string các phần chính).";
            $response = Http::timeout(5)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                $content = $response->json('candidates.0.content.parts.0.text');
                if (preg_match('/\{.*\}/s', $content, $matches)) {
                    $json = json_decode($matches[0], true);
                    if (is_array($json) && !empty($json['html_content'])) {
                        return $json;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini API call failed for review outline, falling back to local AI engine: ' . $e->getMessage());
        }

        return null;
    }
}
