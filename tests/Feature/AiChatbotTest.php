<?php

namespace Tests\Feature;

use App\Models\DanhMucMenu;
use App\Models\MonTrongMenu;
use App\Models\Quan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::factory()->create();

        // 1. Quán lẩu tại Quận 1
        $quanLauQ1 = Quan::create([
            'chu_quan_id'          => $owner->id,
            'ten_quan'             => 'Lẩu Bò Cô Thảo Quận 1',
            'slug'                 => 'lau-bo-co-thao-quan-1',
            'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
            'dia_chi_chi_tiet'     => '123 Nguyễn Du',
            'tinh_thanh_id'        => '79',
            'ten_tinh_thanh'       => 'Thành phố Hồ Chí Minh',
            'quan_huyen_id'        => '760',
            'ten_quan_huyen'       => 'Quận 1',
            'vi_do'                => 10.7750,
            'kinh_do'              => 106.6980,
            'gia_nho_nhat'         => 60000,
            'gia_lon_nhat'         => 180000,
            'gio_mo_cua'           => '10:00:00',
            'gio_dong_cua'         => '23:00:00',
            'trang_thai'           => 'da_duyet',
            'is_xac_thuc'          => true,
            'is_noi_bat'           => true,
        ]);

        $dmLau = DanhMucMenu::create([
            'quan_id'      => $quanLauQ1->id,
            'ten_danh_muc' => 'Món lẩu',
            'thu_tu'       => 1,
        ]);

        MonTrongMenu::create([
            'danh_muc_id' => $dmLau->id,
            'ten_mon'     => 'Lẩu bò nhúng giấm đặc biệt',
            'gia'         => 150000,
            'con_hang'    => true,
        ]);

        // 2. Quán cafe bình dân tại Ba Đình, Hà Nội (dưới 50k)
        $quanCafe = Quan::create([
            'chu_quan_id'          => $owner->id,
            'ten_quan'             => 'Cà Phê Phố Cổ Ba Đình',
            'slug'                 => 'ca-phe-pho-co-ba-dinh',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'dia_chi_chi_tiet'     => '45 Phan Đình Phùng',
            'tinh_thanh_id'        => '01',
            'ten_tinh_thanh'       => 'Thành phố Hà Nội',
            'quan_huyen_id'        => '001',
            'ten_quan_huyen'       => 'Quận Ba Đình',
            'vi_do'                => 21.0380,
            'kinh_do'              => 105.8420,
            'gia_nho_nhat'         => 25000,
            'gia_lon_nhat'         => 45000,
            'gio_mo_cua'           => '07:00:00',
            'gio_dong_cua'         => '22:30:00',
            'trang_thai'           => 'da_duyet',
        ]);

        $dmCafe = DanhMucMenu::create([
            'quan_id'      => $quanCafe->id,
            'ten_danh_muc' => 'Đồ uống truyền thống',
            'thu_tu'       => 1,
        ]);

        MonTrongMenu::create([
            'danh_muc_id' => $dmCafe->id,
            'ten_mon'     => 'Cà phê trứng Hà Nội',
            'gia'         => 35000,
            'con_hang'    => true,
        ]);

        // 3. Quán lẩu thứ 2 tại Quận 1 (dùng để test loại trừ quán đã xem khi hỏi thêm)
        $quanLauQ1So2 = Quan::create([
            'chu_quan_id'          => $owner->id,
            'ten_quan'             => 'Lẩu Nấm Sài Gòn Q1',
            'slug'                 => 'lau-nam-sai-gon-q1',
            'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
            'dia_chi_chi_tiet'     => '456 Lê Thánh Tôn',
            'tinh_thanh_id'        => '79',
            'ten_tinh_thanh'       => 'Thành phố Hồ Chí Minh',
            'quan_huyen_id'        => '760',
            'ten_quan_huyen'       => 'Quận 1',
            'gia_nho_nhat'         => 120000,
            'gia_lon_nhat'         => 300000,
            'gio_mo_cua'           => '11:00:00',
            'gio_dong_cua'         => '22:00:00',
            'trang_thai'           => 'da_duyet',
        ]);

        // 4. Quán lẩu tại Quận 3 (dùng để test đổi địa điểm)
        Quan::create([
            'chu_quan_id'          => $owner->id,
            'ten_quan'             => 'Lẩu Dê Trương Định Quận 3',
            'slug'                 => 'lau-de-truong-dinh-quan-3',
            'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
            'dia_chi_chi_tiet'     => '88 Trương Định',
            'tinh_thanh_id'        => '79',
            'ten_tinh_thanh'       => 'Thành phố Hồ Chí Minh',
            'quan_huyen_id'        => '770',
            'ten_quan_huyen'       => 'Quận 3',
            'gia_nho_nhat'         => 80000,
            'gia_lon_nhat'         => 220000,
            'gio_mo_cua'           => '15:00:00',
            'gio_dong_cua'         => '23:30:00',
            'trang_thai'           => 'da_duyet',
        ]);

        // 5. Quán chưa duyệt (chờ duyệt) - AI KHÔNG ĐƯỢC PHÉP gợi ý quán này
        Quan::create([
            'chu_quan_id'          => $owner->id,
            'ten_quan'             => 'Quán Ẩn Chưa Duyệt',
            'slug'                 => 'quan-an-chua-duyet',
            'loai_hinh_kinh_doanh' => 'Lẩu nướng',
            'dia_chi_chi_tiet'     => '99 Bí Mật',
            'tinh_thanh_id'        => '79',
            'ten_tinh_thanh'       => 'Thành phố Hồ Chí Minh',
            'quan_huyen_id'        => '760',
            'ten_quan_huyen'       => 'Quận 1',
            'gia_nho_nhat'         => 50000,
            'gia_lon_nhat'         => 100000,
            'trang_thai'           => 'chua_duyet',
        ]);
    }

    public function test_suggestions_endpoint_returns_recommended_prompts(): void
    {
        $response = $this->getJson('/api/ai-chatbot/suggestions');

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'suggestions',
            ]);

        $this->assertIsArray($response->json('suggestions'));
        $this->assertNotEmpty($response->json('suggestions'));
    }

    public function test_chat_endpoint_validates_required_message(): void
    {
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_chat_recommends_grounded_venues_by_district(): void
    {
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Tìm giúp mình quán lẩu ở Quận 1 nhé',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'reply',
                'venues' => [
                    '*' => [
                        'id',
                        'ten_quan',
                        'slug',
                        'url',
                        'dia_chi',
                        'loai_hinh',
                        'gia_trung_binh',
                        'hinh_anh',
                    ]
                ],
                'parsed_intent',
            ]);

        $venues = $response->json('venues');
        $this->assertNotEmpty($venues);

        // Đảm bảo chỉ gợi ý quán đã duyệt, đúng Quận 1
        $slugs = array_column($venues, 'slug');
        $this->assertContains('lau-bo-co-thao-quan-1', $slugs);
        $this->assertNotContains('quan-an-chua-duyet', $slugs);

        // URL phải trỏ đến trang chi tiết thực tế
        $firstVenue = $venues[0];
        $this->assertStringContainsString('/quan/lau-bo-co-thao-quan-1', $firstVenue['url']);
    }

    public function test_chat_filters_by_budget(): void
    {
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Có quán cafe nào dưới 50k không bạn?',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $venues = $response->json('venues');
        $this->assertNotEmpty($venues);
        $this->assertEquals('Cà Phê Phố Cổ Ba Đình', $venues[0]['ten_quan']);
    }

    public function test_chat_maintains_conversational_history(): void
    {
        $history = [
            ['role' => 'user', 'content' => 'Mình đang ở Hà Nội'],
            ['role' => 'assistant', 'content' => 'Chào bạn! Bạn đang muốn tìm món ăn hay quán cafe nào ở Hà Nội ạ?'],
        ];

        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Cho mình quán cafe ở Ba Đình',
            'history' => $history,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $venues = $response->json('venues');
        $this->assertNotEmpty($venues);
        $this->assertEquals('ca-phe-pho-co-ba-dinh', $venues[0]['slug']);
    }

    public function test_chat_handles_unmatched_query_gracefully_without_hallucination(): void
    {
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Tìm quán sushi cá ngừ Côn Đảo mở lúc 3h sáng',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        // Không bịa quán giả khi DB không có
        $venues = $response->json('venues');
        $this->assertEmpty($venues);

        $reply = $response->json('reply');
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    public function test_legacy_blog_ai_chat_endpoint_compatibility(): void
    {
        $response = $this->postJson('/api/blog/ai-chat', [
            'message' => 'Tìm quán lẩu ở Quận 1',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'reply',
            ]);
    }

    public function test_chat_handles_follow_up_for_more_venues_without_repeating_seen(): void
    {
        // Lịch sử: người dùng đã được gợi ý quán 'lau-bo-co-thao-quan-1'
        $history = [
            ['role' => 'user', 'content' => 'Tìm quán lẩu ở Quận 1'],
            ['role' => 'assistant', 'content' => 'Gợi ý quán: [Lẩu Bò Cô Thảo Quận 1](/quan/lau-bo-co-thao-quan-1)'],
        ];

        // Câu hỏi tiếp nối yêu cầu quán khác
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Còn quán nào khác nữa không bạn?',
            'history' => $history,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $venues = $response->json('venues');
        $this->assertNotEmpty($venues);

        // Quán đầu tiên trả về phải là quán khác chưa được xem
        $this->assertEquals('lau-nam-sai-gon-q1', $venues[0]['slug']);
    }

    public function test_chat_answers_detail_question_about_previous_venue(): void
    {
        $history = [
            ['role' => 'user', 'content' => 'Tìm quán lẩu Quận 1'],
            [
                'role' => 'assistant',
                'content' => 'Dưới đây là quán lẩu: [Lẩu Bò Cô Thảo Quận 1](/quan/lau-bo-co-thao-quan-1)',
                'venues' => [
                    [
                        'name' => 'Lẩu Bò Cô Thảo Quận 1',
                        'open_hours' => '10:00 - 23:00',
                        'is_open_now' => true,
                        'address' => '123 Nguyễn Du, Quận 1',
                        'price_range' => '60.000đ - 180.000đ',
                        'url' => '/quan/lau-bo-co-thao-quan-1',
                        'rating' => 4.8,
                        'review_count' => 50,
                    ]
                ]
            ],
        ];

        // Hỏi giờ mở cửa của quán 1
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Quán 1 mở mấy giờ vậy bạn?',
            'history' => $history,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $reply = $response->json('reply');
        $this->assertStringContainsString('10:00 - 23:00', $reply);
        $this->assertStringContainsString('Lẩu Bò Cô Thảo Quận 1', $reply);
    }

    public function test_chat_asks_clarification_on_vague_prompts_and_suggests_top_venues(): void
    {
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Tìm quán ăn ngon',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $reply = $response->json('reply');
        // Phải hỏi lại thông tin (Khu vực? Thèm món gì?)
        $this->assertStringContainsString('Khu vực', $reply);
        $this->assertStringContainsString('món gì', $reply);

        // Kèm theo gợi ý top quán
        $venues = $response->json('venues');
        $this->assertNotEmpty($venues);
    }

    public function test_chat_maintains_food_context_when_switching_location(): void
    {
        $history = [
            ['role' => 'user', 'content' => 'Cho mình quán lẩu ngon ở Quận 1'],
            ['role' => 'assistant', 'content' => 'Gợi ý cho bạn quán [Lẩu Bò Cô Thảo Quận 1](/quan/lau-bo-co-thao-quan-1)'],
        ];

        // Đổi sang Quận 3 (không nhắc lại chữ "lẩu")
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Đổi sang Quận 3 thì sao?',
            'history' => $history,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $venues = $response->json('venues');
        $this->assertNotEmpty($venues);

        // Phải giữ ngữ cảnh món lẩu và tìm ra quán lẩu ở Quận 3
        $this->assertEquals('lau-de-truong-dinh-quan-3', $venues[0]['slug']);
    }

    public function test_chat_answers_comparison_question_cheapest_venue(): void
    {
        $history = [
            ['role' => 'user', 'content' => 'Tìm quán lẩu ở Quận 1'],
            [
                'role' => 'assistant',
                'content' => 'Gợi ý: [Lẩu Bò Cô Thảo Quận 1](/quan/lau-bo-co-thao-quan-1) và [Lẩu Nấm Sài Gòn Q1](/quan/lau-nam-sai-gon-q1)',
                'venues' => [
                    [
                        'name' => 'Lẩu Bò Cô Thảo Quận 1',
                        'slug' => 'lau-bo-co-thao-quan-1',
                        'price_range' => '60.000đ - 180.000đ',
                        'min_price' => 60000,
                        'url' => '/quan/lau-bo-co-thao-quan-1',
                    ],
                    [
                        'name' => 'Lẩu Nấm Sài Gòn Q1',
                        'slug' => 'lau-nam-sai-gon-q1',
                        'price_range' => '120.000đ - 300.000đ',
                        'min_price' => 120000,
                        'url' => '/quan/lau-nam-sai-gon-q1',
                    ],
                ]
            ],
        ];

        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Trong mấy quán đó, quán nào rẻ nhất vậy?',
            'history' => $history,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $reply = $response->json('reply');
        $this->assertStringContainsString('rẻ và tiết kiệm nhất', $reply);
        $this->assertStringContainsString('Lẩu Bò Cô Thảo Quận 1', $reply);
        $this->assertEquals('lau-bo-co-thao-quan-1', $response->json('venues.0.slug'));
    }

    public function test_chat_answers_comparison_question_latest_open_venue(): void
    {
        $history = [
            ['role' => 'user', 'content' => 'Tìm quán lẩu ở Quận 1'],
            [
                'role' => 'assistant',
                'content' => 'Gợi ý: [Lẩu Bò Cô Thảo Quận 1](/quan/lau-bo-co-thao-quan-1) và [Lẩu Nấm Sài Gòn Q1](/quan/lau-nam-sai-gon-q1)',
                'venues' => [
                    [
                        'name' => 'Lẩu Bò Cô Thảo Quận 1',
                        'slug' => 'lau-bo-co-thao-quan-1',
                        'open_hours' => '10:00 - 23:00',
                        'close_time' => '23:00:00',
                        'url' => '/quan/lau-bo-co-thao-quan-1',
                    ],
                    [
                        'name' => 'Lẩu Nấm Sài Gòn Q1',
                        'slug' => 'lau-nam-sai-gon-q1',
                        'open_hours' => '11:00 - 22:00',
                        'close_time' => '22:00:00',
                        'url' => '/quan/lau-nam-sai-gon-q1',
                    ],
                ]
            ],
        ];

        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Quán nào mở muộn nhất vậy bạn?',
            'history' => $history,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $reply = $response->json('reply');
        $this->assertStringContainsString('mở cửa muộn nhất', $reply);
        $this->assertStringContainsString('Lẩu Bò Cô Thảo Quận 1', $reply);
        $this->assertEquals('lau-bo-co-thao-quan-1', $response->json('venues.0.slug'));
    }

    public function test_chat_answers_amenity_parking_inquiry(): void
    {
        $history = [
            ['role' => 'user', 'content' => 'Tìm quán lẩu ở Quận 1'],
            [
                'role' => 'assistant',
                'content' => 'Gợi ý: [Lẩu Bò Cô Thảo Quận 1](/quan/lau-bo-co-thao-quan-1)',
                'venues' => [
                    [
                        'name' => 'Lẩu Bò Cô Thảo Quận 1',
                        'slug' => 'lau-bo-co-thao-quan-1',
                        'district' => 'Quận 1',
                        'address' => '123 Nguyễn Du, Quận 1',
                        'url' => '/quan/lau-bo-co-thao-quan-1',
                    ]
                ]
            ],
        ];

        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Quán 1 có chỗ để xe máy và đỗ ô tô không?',
            'history' => $history,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $reply = $response->json('reply');
        $this->assertStringContainsString('bãi đỗ & gửi xe', $reply);
        $this->assertStringContainsString('Xe máy', $reply);
        $this->assertStringContainsString('Ô tô', $reply);
    }

    public function test_chat_answers_dish_check_accurately(): void
    {
        $quan = Quan::where('slug', 'lau-bo-co-thao-quan-1')->first();

        $history = [
            ['role' => 'user', 'content' => 'Tìm quán lẩu ở Quận 1'],
            [
                'role' => 'assistant',
                'content' => 'Gợi ý: [Lẩu Bò Cô Thảo Quận 1](/quan/lau-bo-co-thao-quan-1)',
                'venues' => [
                    [
                        'id' => $quan->id,
                        'name' => 'Lẩu Bò Cô Thảo Quận 1',
                        'slug' => 'lau-bo-co-thao-quan-1',
                        'district' => 'Quận 1',
                        'address' => '123 Nguyễn Du, Quận 1',
                        'highlight_dishes' => ['Lẩu bò nhúng giấm đặc biệt'],
                        'url' => '/quan/lau-bo-co-thao-quan-1',
                    ]
                ]
            ],
        ];

        // Hỏi món có sẵn trong menu
        $response1 = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Quán 1 có bán lẩu bò nhúng giấm không bạn?',
            'history' => $history,
        ]);
        $response1->assertOk();
        $this->assertStringContainsString('có phục vụ', $response1->json('reply'));

        // Hỏi món không có trong menu
        $response2 = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Quán 1 có bán sushi cá hồi không?',
            'history' => $history,
        ]);
        $response2->assertOk();
        $this->assertStringContainsString('chưa có món', $response2->json('reply'));
    }

    public function test_chat_turn_responses_avoid_boilerplate_repetition(): void
    {
        // Lượt 1
        $res1 = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Tìm quán lẩu ở Quận 1',
        ]);
        $res1->assertOk();
        $reply1 = $res1->json('reply');

        // Lượt 2
        $history = [
            ['role' => 'user', 'content' => 'Tìm quán lẩu ở Quận 1'],
            ['role' => 'assistant', 'content' => $reply1, 'venues' => $res1->json('venues')],
        ];

        $res2 = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Còn quán nướng nào ở Quận 1 nữa không?',
            'history' => $history,
        ]);
        $res2->assertOk();
        $reply2 = $res2->json('reply');

        // Lượt 2 không lặp lại câu chào hoặc mẹo tĩnh như lượt 1
        $this->assertStringNotContainsString('Dựa trên sở thích ẩm thực của bạn', $reply1);
        $this->assertStringNotContainsString('Dựa trên sở thích ẩm thực của bạn', $reply2);
        $this->assertNotEquals($reply1, $reply2);
    }

    public function test_chat_answers_everyday_cooking_recipe_request(): void
    {
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Chỉ mình cách nấu sườn xào chua ngọt tại nhà với',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $reply = $response->json('reply');
        $this->assertStringContainsString('Sườn Xào Chua Ngọt', $reply);
        $this->assertStringContainsString('nguyên liệu', $reply);
        $this->assertEmpty($response->json('venues'));
    }

    public function test_chat_answers_everyday_emotional_support_request(): void
    {
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Hôm nay tôi thấy mệt mỏi và stress quá, an ủi tôi với',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $reply = $response->json('reply');
        $this->assertStringContainsString('đồng cảm', $reply);
        $this->assertStringContainsStringIgnoringCase('tâm trí', $reply);
        $this->assertEmpty($response->json('venues'));
    }

    public function test_chat_answers_everyday_nutrition_diet_request(): void
    {
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Tư vấn cho mình chế độ ăn eat clean giảm cân',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $reply = $response->json('reply');
        $this->assertStringContainsString('Eat Clean', $reply);
        $this->assertStringContainsString('Thâm hụt calo', $reply);
        $this->assertEmpty($response->json('venues'));
    }

    public function test_ai_key_rotation_pool_handles_rate_limits_seamlessly(): void
    {
        \Illuminate\Support\Facades\Config::set('ai.provider', 'gemini');
        \Illuminate\Support\Facades\Config::set('ai.gemini.api_keys', ['KEY_EXHAUSTED_1', 'KEY_HEALTHY_2']);

        // Mock Http: Key 1 trả về 429 ResourceExhausted, Key 2 trả về 200 thành công
        \Illuminate\Support\Facades\Http::fake([
            '*key=KEY_EXHAUSTED_1*' => \Illuminate\Support\Facades\Http::response([
                'error' => ['message' => 'Resource has been exhausted (e.g. check quota).', 'code' => 429]
            ], 429),
            '*key=KEY_HEALTHY_2*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Chào bạn, câu trả lời từ Key 2 trong Key Rotation Pool đây nhé!']
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Làm sao để sống vui vẻ hơn mỗi ngày?',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $reply = $response->json('reply');
        $this->assertStringContainsString('Key 2 trong Key Rotation Pool', $reply);
    }

    public function test_chat_continuous_refresh_and_suggest_newest_venues(): void
    {
        $owner = User::first();
        Quan::create([
            'chu_quan_id'          => $owner->id,
            'ten_quan'             => 'Tiệm Trà Sữa Mới Toanh Q1',
            'slug'                 => 'tiem-tra-sua-moi-toanh-q1',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'dia_chi_chi_tiet'     => '100 Đồng Khởi',
            'tinh_thanh_id'        => '79',
            'ten_tinh_thanh'       => 'Thành phố Hồ Chí Minh',
            'quan_huyen_id'        => '760',
            'ten_quan_huyen'       => 'Quận 1',
            'gia_nho_nhat'         => 30000,
            'gia_lon_nhat'         => 65000,
            'trang_thai'           => 'da_duyet',
            'created_at'           => now(),
        ]);
        Quan::create([
            'chu_quan_id'          => $owner->id,
            'ten_quan'             => 'Bánh Mì Nóng Giòn Mới Mở',
            'slug'                 => 'banh-mi-nong-gion-moi-mo',
            'loai_hinh_kinh_doanh' => 'Ăn vặt',
            'dia_chi_chi_tiet'     => '200 Hai Bà Trưng',
            'tinh_thanh_id'        => '79',
            'ten_tinh_thanh'       => 'Thành phố Hồ Chí Minh',
            'quan_huyen_id'        => '760',
            'ten_quan_huyen'       => 'Quận 1',
            'gia_nho_nhat'         => 20000,
            'gia_lon_nhat'         => 40000,
            'trang_thai'           => 'da_duyet',
            'created_at'           => now()->subMinute(),
        ]);

        // Lượt 1: Người dùng yêu cầu 'Gợi ý quán mới' -> AI trả về quán mới trực tiếp, không hỏi lại mơ hồ
        $turn1 = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Gợi ý quán mới',
        ]);

        $turn1->assertOk()
            ->assertJsonPath('success', true);

        $venuesTurn1 = $turn1->json('venues');
        $this->assertNotEmpty($venuesTurn1);
        $seenSlugsTurn1 = array_column($venuesTurn1, 'slug');

        // Lượt 2: Người dùng yêu cầu 'Làm mới quán khác' kèm lịch sử đã xem ở Lượt 1
        $turn2 = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Làm mới quán khác',
            'history' => [
                ['role' => 'user', 'content' => 'Gợi ý quán mới'],
                ['role' => 'assistant', 'content' => $turn1->json('reply'), 'venues' => $venuesTurn1],
            ],
        ]);

        $turn2->assertOk()
            ->assertJsonPath('success', true);

        $venuesTurn2 = $turn2->json('venues');
        $this->assertNotEmpty($venuesTurn2);
        $seenSlugsTurn2 = array_column($venuesTurn2, 'slug');

        // Khẳng định: Quán ở Lượt 2 không được trùng lặp với bất kỳ quán nào đã thấy ở Lượt 1
        foreach ($seenSlugsTurn2 as $slug2) {
            $this->assertNotContains($slug2, $seenSlugsTurn1, "Slug {$slug2} bị lặp lại sau khi làm mới gợi ý!");
        }

        // Khẳng định phản hồi có lời chào mở đầu làm mới dữ liệu
        $this->assertStringContainsString('Làm mới dữ liệu gợi ý thành công', $turn2->json('reply'));
    }

    public function test_grounded_venue_data_accuracy_and_strict_category_filtering(): void
    {
        $owner = User::first();
        // Tạo 1 quán cafe nhưng trong mô tả có nhắc từ "lẩu"
        $cafeWithHotpotDesc = Quan::create([
            'chu_quan_id'          => $owner->id,
            'ten_quan'             => 'The Coffee House Đối Diện Quán Lẩu',
            'slug'                 => 'the-coffee-house-doi-dien-quan-lau',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'mo_ta'                => 'Nằm đối diện quán lẩu bò nổi tiếng, view phố chill',
            'dia_chi_chi_tiet'     => '15 Lê Duẩn',
            'tinh_thanh_id'        => '79',
            'ten_tinh_thanh'       => 'Thành phố Hồ Chí Minh',
            'quan_huyen_id'        => '760',
            'ten_quan_huyen'       => 'Quận 1',
            'gia_nho_nhat'         => 35000,
            'gia_lon_nhat'         => 75000,
            'gio_mo_cua'           => '07:30:00',
            'gio_dong_cua'         => '22:30:00',
            'trang_thai'           => 'da_duyet',
            'is_xac_thuc'          => true,
        ]);

        // Gửi yêu cầu tìm quán LẨU ở Quận 1
        $response = $this->postJson('/api/ai-chatbot/chat', [
            'message' => 'Tìm quán lẩu ở Quận 1',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $venues = $response->json('venues');
        $this->assertNotEmpty($venues);

        // Khẳng định: Quán cafe không được trả về trong kết quả tìm kiếm Lẩu dù mô tả có nhắc chữ "lẩu"
        $slugs = array_column($venues, 'slug');
        $this->assertNotContains('the-coffee-house-doi-dien-quan-lau', $slugs);

        // Khẳng định dữ liệu quán trả về chính xác từ database
        $firstVenue = $venues[0];
        $this->assertArrayHasKey('name', $firstVenue);
        $this->assertArrayHasKey('address', $firstVenue);
        $this->assertArrayHasKey('price_range', $firstVenue);
        $this->assertArrayHasKey('open_hours', $firstVenue);
        $this->assertArrayHasKey('is_open_now', $firstVenue);
        $this->assertArrayHasKey('image', $firstVenue);
        $this->assertNotEmpty($firstVenue['image']);

        // Khẳng định phản hồi văn bản gọn gàng, không lặp lại các dòng bullet metadata
        $reply = $response->json('reply');
        $this->assertStringNotContainsString('📍 **Địa chỉ:**', $reply);
        $this->assertStringNotContainsString('💵 **Giá:**', $reply);
    }
}

