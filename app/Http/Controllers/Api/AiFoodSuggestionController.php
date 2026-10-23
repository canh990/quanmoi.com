<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonTrongMenu;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AiFoodSuggestionController extends Controller
{
    /**
     * Ngữ cảnh gợi ý món ăn theo khung giờ (dùng khi need = 'auto')
     */
    private array $contextByHour = [
        'early_morning' => [
            'key'      => 'early_morning',
            'label'    => 'Sáng sớm',
            'icon'     => '🌅',
            'dishes'   => ['Bún bò Huế', 'Phở bò', 'Bánh mì', 'Xôi', 'Cháo', 'Hủ tiếu'],
            'message'  => 'Buổi sáng ăn gì khởi động ngày mới tràn đầy năng lượng?',
            'reason'   => 'Nóng hổi, dễ tiêu hóa, nạp năng lượng nhanh',
        ],
        'morning' => [
            'key'      => 'morning',
            'label'    => 'Buổi sáng',
            'icon'     => '☀️',
            'dishes'   => ['Phở bò', 'Bún bò', 'Bánh mì', 'Cơm tấm', 'Bún đậu', 'Hủ tiếu', 'Mì Quảng'],
            'message'  => 'Gợi ý bữa sáng ngon lành & chắc dạ cho bạn',
            'reason'   => 'Bữa sáng đậm vị, no lâu suốt buổi làm việc',
        ],
        'lunch' => [
            'key'      => 'lunch',
            'label'    => 'Bữa trưa',
            'icon'     => '🍱',
            'dishes'   => ['Cơm tấm', 'Cơm gà', 'Bún bò', 'Phở', 'Bún đậu mắm tôm', 'Mì Quảng', 'Bánh canh', 'Hủ tiếu'],
            'message'  => 'Trưa nay ăn gì? AI gợi ý các món no bụng chuẩn vị',
            'reason'   => 'Cung cấp đủ năng lượng cho buổi chiều làm việc',
        ],
        'afternoon' => [
            'key'      => 'afternoon',
            'label'    => 'Xế chiều',
            'icon'     => '🧋',
            'dishes'   => ['Trà sữa', 'Cà phê', 'Đồ ăn vặt', 'Chè', 'Bánh ngọt', 'Nước ép trái cây', 'Kem'],
            'message'  => 'Chiều xế chill chill, nhâm nhi món gì nào?',
            'reason'   => 'Giải khát, tỉnh táo & bổ sung đường huyết',
        ],
        'early_evening' => [
            'key'      => 'early_evening',
            'label'    => 'Bữa tối',
            'icon'     => '🌇',
            'dishes'   => ['Lẩu nướng', 'Cơm gà', 'Bún bò', 'Cơm tấm', 'Hải sản', 'Phở', 'Bún riêu'],
            'message'  => 'Tối nay ăn gì? Bữa tối ấm cúng cùng người thân & bạn bè',
            'reason'   => 'Bữa tối sum họp, thưởng thức trọn vẹn hương vị',
        ],
        'evening' => [
            'key'      => 'evening',
            'label'    => 'Tối muộn',
            'icon'     => '🌙',
            'dishes'   => ['Lẩu nướng', 'Hải sản', 'BBQ', 'Bít tết', 'Cháo ếch', 'Ốc', 'Mực nướng'],
            'message'  => 'Cuối ngày thư giãn, ăn gì cho thật đã?',
            'reason'   => 'Tụ họp bạn bè, chill cuối ngày',
        ],
        'late_night' => [
            'key'      => 'late_night',
            'label'    => 'Ăn đêm',
            'icon'     => '🌃',
            'dishes'   => ['Cháo', 'Mì gói', 'Ốc', 'Bún bò', 'Phở', 'Bánh mì nướng', 'Cơm đêm'],
            'message'  => 'Đêm khuya đói bụng, tìm quán ăn đêm nóng hổi nào!',
            'reason'   => 'Ấm bụng đêm muộn, cứu đói nhanh',
        ],
    ];

    /**
     * Ngữ cảnh gợi ý món ăn theo từng nhu cầu cụ thể của người dùng
     */
    private array $contextByNeed = [
        'bua_chinh' => [
            'key'      => 'bua_chinh',
            'label'    => 'Bữa no chính',
            'icon'     => '🍱',
            'dishes'   => ['Cơm tấm', 'Cơm gà', 'Phở bò', 'Bún bò Huế', 'Bún chả', 'Bánh canh', 'Cơm niêu', 'Mì Quảng', 'Hủ tiếu'],
            'message'  => 'Các món bữa chính chắc bụng, giàu dinh dưỡng',
            'reason'   => 'No lâu, đậm đà, đầy đủ calo',
        ],
        'nhanh_gon' => [
            'key'      => 'nhanh_gon',
            'label'    => 'Nhanh gọn',
            'icon'     => '⚡',
            'dishes'   => ['Bánh mì', 'Xôi', 'Hủ tiếu', 'Bún riêu', 'Bánh cuốn', 'Bánh bao', 'Mì xào'],
            'message'  => 'Món ngon tiện lợi, phục vụ nhanh, dễ mang đi',
            'reason'   => 'Nhanh chóng, tiết kiệm thời gian tối đa',
        ],
        'thanh_dam' => [
            'key'      => 'thanh_dam',
            'label'    => 'Thanh đạm & Healthy',
            'icon'     => '🥗',
            'dishes'   => ['Cháo', 'Phở gà', 'Bún mọc', 'Gỏi cuốn', 'Cơm chay', 'Salad', 'Đậu hũ', 'Canh rau'],
            'message'  => 'Món nhẹ bụng, ít dầu mỡ, tươi ngon bổ dưỡng',
            'reason'   => 'Thanh nhẹ, dễ tiêu hóa, tốt cho vóc dáng',
        ],
        'an_vat' => [
            'key'      => 'an_vat',
            'label'    => 'Ăn vặt & Giải nhiệt',
            'icon'     => '🧋',
            'dishes'   => ['Trà sữa', 'Cà phê', 'Chè', 'Bánh tráng nướng', 'Nem chua rán', 'Ốc', 'Kem', 'Nước ép'],
            'message'  => 'Món nhâm nhi giải trí, xua tan mệt mỏi',
            'reason'   => 'Vui miệng, xả stress, hương vị ngọt ngào',
        ],
        'lau_nuong' => [
            'key'      => 'lau_nuong',
            'label'    => 'Lẩu & Tụ tập',
            'icon'     => '🍲',
            'dishes'   => ['Lẩu nướng', 'BBQ', 'Hải sản', 'Bò nướng', 'Lẩu Thái', 'Bít tết', 'Ốc'],
            'message'  => 'Các món lý tưởng cho buổi gặp gỡ, liên hoan',
            'reason'   => 'Không khí quây quần ấm cúng, chia sẻ cùng nhau',
        ],
        'tiet_kiem' => [
            'key'      => 'tiet_kiem',
            'label'    => 'Sinh viên / Tiết kiệm',
            'icon'     => '💰',
            'dishes'   => ['Cơm bình dân', 'Bánh mì', 'Xôi', 'Bún đậu', 'Mì trộn', 'Bún chả', 'Hủ tiếu gõ'],
            'message'  => 'Giá hạt dẻ, no căng bụng, êm ví cho mọi người',
            'reason'   => 'Giá mềm, phần ăn đầy đặn, siêu tiết kiệm',
        ],
    ];

    /**
     * API: Gợi ý món ăn thông minh theo ngữ cảnh thời gian & nhu cầu người dùng
     *
     * Query params:
     *  - need        : 'auto' (mặc định) | 'bua_chinh' | 'nhanh_gon' | 'thanh_dam' | 'an_vat' | 'lau_nuong' | 'tiet_kiem'
     *  - hour        : giờ hiện tại (0-23) từ trình duyệt
     *  - day_of_week : ngày trong tuần (0=CN, 6=T7) từ trình duyệt
     *  - history     : JSON array tên món đã tìm kiếm gần đây (max 10)
     *  - location    : tinh_thanh_id tùy chọn
     *  - refresh     : 1 nếu người dùng muốn làm mới gợi ý khác
     *  - limit       : số gợi ý trả về (mặc định 9)
     */
    public function suggest(Request $request)
    {
        $need        = trim((string) $request->input('need', 'auto'));
        $hour        = (int) $request->input('hour', now()->hour);
        $dayOfWeek   = (int) $request->input('day_of_week', now()->dayOfWeek);
        $history     = $this->parseHistory($request->input('history', '[]'));
        $tinhThanhId = trim((string) $request->input('location', ''));
        $limit       = min((int) $request->input('limit', 18), 24);

        // 1. Phân giải ngữ cảnh theo nhu cầu hoặc thời gian
        $context = $this->resolveContext($need, $hour, $dayOfWeek, $history, $request->user());

        // 2. Lấy dữ liệu gợi ý từ DB (cache tự động xoay mới mỗi 2 phút)
        $cacheWindow = floor(time() / 120);
        $cacheKey = 'ai_food_suggest_v3_' . $context['key'] . '_' . md5($tinhThanhId) . '_' . $cacheWindow;

        $dbSuggestions = Cache::remember($cacheKey, 120, function () use ($context, $tinhThanhId, $limit) {
            return $this->fetchSmartSuggestions($context, $tinhThanhId, $limit);
        });

        // 3. Cá nhân hóa theo lịch sử tìm kiếm & khẩu vị của người dùng
        $personalized = $this->personalizeSuggestions($dbSuggestions, $history, $context, $limit);

        // 4. Bổ sung món phong phú nếu database trả về ít hơn limit
        $final = $this->fillWithFallback($personalized, $context, $limit);

        // 5. Thêm badge & lý do thông minh cho từng món
        $final = $this->attachAiMetadata($final, $context, $history);

        return response()->json([
            'success'     => true,
            'need'        => $need,
            'context'     => [
                'key'     => $context['key'],
                'label'   => $context['label'],
                'icon'    => $context['icon'],
                'message' => $context['message'],
                'reason'  => $context['reason'] ?? 'Gợi ý thông minh bởi Kira AI',
            ],
            'suggestions' => $final,
        ]);
    }

    /**
     * Xác định ngữ cảnh dựa trên nhu cầu hoặc tự động theo giờ + lịch sử
     */
    private function resolveContext(string $need, int $hour, int $dayOfWeek, array $history, mixed $user = null): array
    {
        // Nếu người dùng chọn 1 nhu cầu cụ thể
        if (isset($this->contextByNeed[$need])) {
            return $this->contextByNeed[$need];
        }

        // Tự động (need = 'auto'): Nhận diện theo giờ trong ngày
        $isWeekend = in_array($dayOfWeek, [0, 6]);

        if ($hour >= 5 && $hour < 8) {
            $key = 'early_morning';
        } elseif ($hour >= 8 && $hour < 11) {
            $key = 'morning';
        } elseif ($hour >= 11 && $hour < 14) {
            $key = 'lunch';
        } elseif ($hour >= 14 && $hour < 17) {
            $key = 'afternoon';
        } elseif ($hour >= 17 && $hour < 19) {
            $key = 'early_evening';
        } elseif ($hour >= 19 && $hour < 23) {
            $key = 'evening';
        } else {
            $key = 'late_night';
        }

        $context = $this->contextByHour[$key];
        $context['is_weekend'] = $isWeekend;

        // Cuối tuần tăng cường lẩu/nướng/hải sản vào buổi tối
        if ($isWeekend && in_array($key, ['evening', 'early_evening'])) {
            $context['message'] = 'Cuối tuần tụ họp, ăn lẩu nướng & hải sản thả ga!';
            $context['reason']  = 'Thích hợp hội ngộ bạn bè & gia đình cuối tuần';
            array_unshift($context['dishes'], 'Lẩu nướng', 'Hải sản', 'BBQ');
        }

        // Nếu người dùng có lịch sử tìm kiếm nhiều món ăn vặt hoặc chay, thêm vào danh sách ưu tiên
        if (!empty($history)) {
            $historyString = implode(' ', $history);
            if (stripos($historyString, 'chay') !== false) {
                array_unshift($context['dishes'], 'Cơm chay', 'Bún chay');
            } elseif (stripos($historyString, 'trà sữa') !== false || stripos($historyString, 'cafe') !== false) {
                if ($key === 'afternoon' || $key === 'morning') {
                    array_unshift($context['dishes'], 'Trà sữa', 'Cà phê');
                }
            }
        }

        return $context;
    }

    /**
     * Lấy gợi ý từ database khớp với các món trong ngữ cảnh
     */
    private function fetchSmartSuggestions(array $context, string $tinhThanhId, int $limit): array
    {
        $results = [];
        $targetDishes = $context['dishes'];
        shuffle($targetDishes);

        foreach ($targetDishes as $dish) {
            if (count($results) >= $limit) break;

            $mons = MonTrongMenu::where('ten_mon', 'like', '%' . $dish . '%')
                ->whereHas('danhMuc.quan', function ($q) use ($tinhThanhId) {
                    $q->where('trang_thai', 'da_duyet');
                    if ($tinhThanhId !== '') {
                        $q->where('tinh_thanh_id', $tinhThanhId);
                    }
                })
                ->with(['danhMuc.quan:id,slug,ten_quan,anh_bia,anh_bia_key,dia_chi_chi_tiet,ten_quan_huyen'])
                ->inRandomOrder()
                ->take(2)
                ->get();

            foreach ($mons as $mon) {
                $quan = $mon->danhMuc?->quan;
                if (!$quan) continue;

                $price = (int) $mon->gia;

                $results[] = [
                    'type'            => 'dish_from_db',
                    'dish'            => $mon->ten_mon,
                    'dish_label'      => $dish,
                    'subtitle'        => $quan->ten_quan,
                    'quan_slug'       => $quan->slug,
                    'image'           => $mon->hinh_anh_url ?: $quan->anh_bia_url,
                    'price'           => $price > 0 ? $price : null,
                    'price_formatted' => $price > 0 ? number_format($price, 0, ',', '.') . 'đ' : null,
                    'search_term'     => $dish,
                    'source'          => 'database',
                ];
            }
        }

        // Bổ sung các món phổ biến từ các quán nổi bật nếu chưa đủ
        if (count($results) < $limit) {
            $extra = MonTrongMenu::whereHas('danhMuc.quan', function ($q) use ($tinhThanhId) {
                    $q->where('trang_thai', 'da_duyet')
                      ->where('is_noi_bat', true);
                    if ($tinhThanhId !== '') {
                        $q->where('tinh_thanh_id', $tinhThanhId);
                    }
                })
                ->with(['danhMuc.quan:id,slug,ten_quan,anh_bia,anh_bia_key'])
                ->inRandomOrder()
                ->take($limit - count($results))
                ->get();

            foreach ($extra as $mon) {
                $quan = $mon->danhMuc?->quan;
                if (!$quan) continue;

                $price = (int) $mon->gia;

                $results[] = [
                    'type'            => 'dish_from_db',
                    'dish'            => $mon->ten_mon,
                    'dish_label'      => $mon->ten_mon,
                    'subtitle'        => $quan->ten_quan,
                    'quan_slug'       => $quan->slug,
                    'image'           => $mon->hinh_anh_url ?: $quan->anh_bia_url,
                    'price'           => $price > 0 ? $price : null,
                    'price_formatted' => $price > 0 ? number_format($price, 0, ',', '.') . 'đ' : null,
                    'search_term'     => $mon->ten_mon,
                    'source'          => 'database_popular',
                ];
            }
        }

        return $results;
    }

    /**
     * Cá nhân hóa theo lịch sử tìm kiếm
     */
    private function personalizeSuggestions(array $suggestions, array $history, array $context, int $limit): array
    {
        if (empty($history)) {
            return array_slice($suggestions, 0, $limit);
        }

        $historyLower = array_map(fn($h) => Str::lower(Str::ascii($h)), $history);
        $fresh = [];
        $matched = [];

        foreach ($suggestions as $item) {
            $dishLower = Str::lower(Str::ascii($item['search_term'] ?? ''));
            $isMatched = false;

            foreach ($historyLower as $h) {
                if (str_contains($h, $dishLower) || str_contains($dishLower, $h)) {
                    $isMatched = true;
                    break;
                }
            }

            if ($isMatched) {
                $item['matched_history'] = true;
                $matched[] = $item;
            } else {
                $item['matched_history'] = false;
                $fresh[] = $item;
            }
        }

        // Xen kẽ: 1 món đúng gu lịch sử, còn lại là món mới để người dùng khám phá
        $combined = array_merge(array_slice($matched, 0, 3), $fresh);
        return array_slice($combined, 0, $limit);
    }

    /**
     * Bổ sung món chuẩn nếu database chưa đủ món
     */
    private function fillWithFallback(array $existing, array $context, int $limit): array
    {
        if (count($existing) >= $limit) {
            return $existing;
        }

        $existingTerms = array_map(fn($i) => Str::lower($i['search_term'] ?? ''), $existing);

        $unsplashMap = [
            'Phở bò'           => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=160&q=80',
            'Cơm tấm'          => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=160&q=80',
            'Bún bò Huế'       => 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&w=160&q=80',
            'Bánh mì'          => 'https://images.unsplash.com/photo-1509722747041-616f39b57569?auto=format&fit=crop&w=160&q=80',
            'Trà sữa'          => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=160&q=80',
            'Cà phê'           => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=160&q=80',
            'Lẩu nướng'        => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=160&q=80',
            'Hải sản'          => 'https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&fit=crop&w=160&q=80',
            'Đồ ăn vặt'        => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=160&q=80',
            'Bún đậu mắm tôm'  => 'https://images.unsplash.com/photo-1564834724105-918b73d1b9e0?auto=format&fit=crop&w=160&q=80',
            'Xôi'              => 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?auto=format&fit=crop&w=160&q=80',
            'Cháo'             => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=160&q=80',
            'Ốc'               => 'https://images.unsplash.com/photo-1559410545-0bdcd187e0a6?auto=format&fit=crop&w=160&q=80',
            'BBQ'              => 'https://images.unsplash.com/photo-1529193591184-b1d58069ecdd?auto=format&fit=crop&w=160&q=80',
            'Cơm gà'           => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=160&q=80',
            'Hủ tiếu'          => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=160&q=80',
            'Mì Quảng'         => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=160&q=80',
            'Gỏi cuốn'         => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=160&q=80',
            'Cơm chay'         => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=160&q=80',
            'Bánh cuốn'        => 'https://images.unsplash.com/photo-1509722747041-616f39b57569?auto=format&fit=crop&w=160&q=80',
            'Bún riêu'         => 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&w=160&q=80',
        ];

        $subtitleMap = [
            'Phở bò'           => 'Nước dùng ninh xương đậm vị',
            'Cơm tấm'          => 'Sườn nướng thơm lừng nóng hổi',
            'Bún bò Huế'       => 'Cay thơm chuẩn vị cố đô',
            'Bánh mì'          => 'Giòn rụm, nhân đậm đà',
            'Trà sữa'          => 'Giải nhiệt mát lạnh ngày dài',
            'Cà phê'           => 'Tỉnh táo & chill sảng khoái',
            'Lẩu nướng'        => 'Thịt nướng xèo xèo, lẩu chua cay',
            'Hải sản'          => 'Tươi sống chế biến tại chỗ',
            'Đồ ăn vặt'        => 'Ngon mê ly, giá học sinh',
            'Bún đậu mắm tôm'  => 'Đậu rán giòn rụm chấm mắm tôm',
            'Xôi'              => 'Xôi dẻo thơm no lâu',
            'Cháo'             => 'Nóng hổi, êm bụng dễ tiêu',
            'Ốc'               => 'Sốt bơ tỏi, me cay hấp dẫn',
            'BBQ'              => 'Nướng than hoa đậm vị sốt',
            'Cơm gà'           => 'Gà ta xé phay, cơm vàng ươm',
            'Hủ tiếu'          => 'Nước lèo ngọt thanh tự nhiên',
            'Mì Quảng'         => 'Sợi mì vàng óng tôm thịt giòn',
            'Gỏi cuốn'         => 'Tôm thịt tươi kèm nước chấm tương',
            'Cơm chay'         => 'Thanh tịnh nhẹ nhàng tốt sức khỏe',
            'Bánh cuốn'        => 'Bánh tráng mỏng mềm mướt chả lụa',
            'Bún riêu'         => 'Riêu cua đồng thơm phức cà chua',
        ];

        $defaultImg = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=160&q=80';

        foreach ($context['dishes'] as $dish) {
            if (count($existing) >= $limit) break;
            if (in_array(Str::lower($dish), $existingTerms)) continue;

            $existing[] = [
                'type'            => 'static',
                'dish'            => $dish,
                'dish_label'      => $dish,
                'subtitle'        => $subtitleMap[$dish] ?? 'Món ngon được yêu thích',
                'quan_slug'       => null,
                'image'           => $unsplashMap[$dish] ?? $defaultImg,
                'price'           => null,
                'price_formatted' => null,
                'search_term'     => $dish,
                'source'          => 'static',
                'matched_history' => false,
            ];

            $existingTerms[] = Str::lower($dish);
        }

        return array_slice($existing, 0, $limit);
    }

    /**
     * Gán lý do và badge AI thông minh cho từng món
     */
    private function attachAiMetadata(array $suggestions, array $context, array $history): array
    {
        $badgePool = ['AI Chọn', 'Đúng Gu', 'Hợp Giờ', 'Top Quán', 'Bán Chạy', 'Nổi Bật'];

        foreach ($suggestions as $index => &$item) {
            // Lý do
            if (!empty($item['matched_history'])) {
                $item['reason'] = '🎯 Phù hợp gu tìm kiếm quen thuộc của bạn';
                $item['badge']  = 'Đúng Gu Bạn';
            } elseif ($item['source'] === 'database' && !empty($item['subtitle'])) {
                $item['reason'] = '⭐ Có tại quán ' . $item['subtitle'];
                $item['badge']  = 'Quán Đã Duyệt';
            } elseif ($index === 0) {
                $item['reason'] = '✨ Món AI gợi ý hàng đầu cho ' . mb_strtolower($context['label']);
                $item['badge']  = 'AI Đề Xuất';
            } elseif (!empty($context['reason'])) {
                $item['reason'] = '💡 ' . $context['reason'];
                $item['badge']  = $context['label'];
            } else {
                $item['reason'] = '👍 Được đông đảo thực khách lựa chọn';
                $item['badge']  = 'Phổ Biến';
            }
        }

        return $suggestions;
    }

    /**
     * Parse lịch sử tìm kiếm từ JSON string
     */
    private function parseHistory(mixed $raw): array
    {
        if (is_array($raw)) {
            return array_filter(array_slice($raw, 0, 10), fn($v) => is_string($v) && trim($v) !== '');
        }

        if (is_string($raw) && str_starts_with(trim($raw), '[')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return array_filter(array_slice($decoded, 0, 10), fn($v) => is_string($v) && trim($v) !== '');
            }
        }

        return [];
    }
}
