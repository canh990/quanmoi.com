<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\QuanDanhGia;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChuQuanAiReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function createChuQuan(): User
    {
        $vaiTro = VaiTro::firstOrCreate(
            ['ten' => 'chu_quan'],
            ['ten_hien_thi' => 'Chủ quán', 'mo_ta' => 'Chủ quán']
        );

        return User::factory()->create(['vai_tro_id' => $vaiTro->id]);
    }

    public function test_owner_can_request_ai_review_summary()
    {
        $owner = $this->createChuQuan();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'slug' => 'quan-an-ngon-test-ai',
            'ten_quan' => 'Quán Ăn Ngon Test AI',
        ]);

        // Tạo 2 đánh giá từ 2 người dùng khác nhau
        QuanDanhGia::create([
            'quan_id' => $quan->id,
            'nguoi_dung_id' => $user1->id,
            'so_sao' => 5,
            'binh_luan' => 'Món ăn rất ngon, nhân viên phục vụ tận tình chu đáo.',
        ]);

        QuanDanhGia::create([
            'quan_id' => $quan->id,
            'nguoi_dung_id' => $user2->id,
            'so_sao' => 4,
            'binh_luan' => 'Không gian thoáng mát, đồ ăn lên nhanh.',
        ]);

        $response = $this->actingAs($owner)->postJson("/chu-quan/quan/{$quan->slug}/danh-gia/ai-summary", [
            'refresh' => true,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'eligible' => true,
            ],
        ]);
        $this->assertArrayHasKey('tong_quan', $response->json('data'));
        $this->assertArrayHasKey('ti_le_hai_long', $response->json('data'));
    }

    public function test_owner_can_request_ai_suggest_reply()
    {
        $owner = $this->createChuQuan();
        $customer = User::factory()->create(['ho_ten' => 'Trần Văn B']);

        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'slug' => 'quan-lau-demo',
        ]);

        $danhGia = QuanDanhGia::create([
            'quan_id' => $quan->id,
            'nguoi_dung_id' => $customer->id,
            'so_sao' => 5,
            'binh_luan' => 'Nước lẩu rất ngon, sẽ quay lại!',
        ]);

        $response = $this->actingAs($owner)->postJson("/chu-quan/quan/{$quan->slug}/danh-gia/{$danhGia->id}/ai-suggest-reply");

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertNotEmpty($response->json('reply'));
        $this->assertIsArray($response->json('suggestions'));
        $this->assertGreaterThanOrEqual(6, count($response->json('suggestions')));
    }

    public function test_non_owner_cannot_access_ai_summary()
    {
        $owner = $this->createChuQuan();
        $stranger = $this->createChuQuan();

        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'slug' => 'quan-rieng-tu',
        ]);

        $response = $this->actingAs($stranger)->postJson("/chu-quan/quan/{$quan->slug}/danh-gia/ai-summary");
        $response->assertNotFound();
    }

    public function test_owner_can_submit_reply_and_clear_cache()
    {
        $owner = $this->createChuQuan();
        $customer = User::factory()->create();

        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'slug' => 'quan-test-reply',
        ]);

        $danhGia = QuanDanhGia::create([
            'quan_id' => $quan->id,
            'nguoi_dung_id' => $customer->id,
            'so_sao' => 4,
            'binh_luan' => 'Món ăn ngon và phục vụ tốt.',
        ]);

        $response = $this->actingAs($owner)->put("/chu-quan/quan/{$quan->slug}/danh-gia/{$danhGia->id}/phan-hoi", [
            'phan_hoi_' . $danhGia->id => 'Dạ quán chân thành cảm ơn bạn nhiều ạ!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quan_danh_gia', [
            'id' => $danhGia->id,
            'phan_hoi' => 'Dạ quán chân thành cảm ơn bạn nhiều ạ!',
        ]);
    }

    public function test_owner_can_check_realtime_review_updates()
    {
        $owner = $this->createChuQuan();
        $customer1 = User::factory()->create(['ho_ten' => 'Khách Hàng Một']);

        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'slug' => 'quan-realtime-test',
            'ten_quan' => 'Quán Realtime Test',
        ]);

        $review1 = QuanDanhGia::create([
            'quan_id' => $quan->id,
            'nguoi_dung_id' => $customer1->id,
            'so_sao' => 5,
            'binh_luan' => 'Tuyệt vời, món ăn rất vừa miệng!',
        ]);

        // 1. Khi client gửi đúng ID và count -> has_updates = false
        $response = $this->actingAs($owner)->getJson("/chu-quan/quan/{$quan->slug}/danh-gia/realtime-check?" . http_build_query([
            'last_id' => (string) $review1->id,
            'last_count' => 1,
            'last_updated' => $review1->updated_at->toISOString(),
        ]));

        $response->assertOk();
        $response->assertJson([
            'has_updates' => false,
            'total_count' => 1,
        ]);

        // 2. Khách hàng thứ 2 bình luận mới
        $customer2 = User::factory()->create(['ho_ten' => 'Khách Hàng Hai']);
        $review2 = QuanDanhGia::create([
            'quan_id' => $quan->id,
            'nguoi_dung_id' => $customer2->id,
            'so_sao' => 4,
            'binh_luan' => 'Quán đẹp, nước chấm ngon.',
        ]);

        // 3. Chủ quán kiểm tra lại với state cũ -> has_updates = true và có HTML
        $responseAfterNew = $this->actingAs($owner)->getJson("/chu-quan/quan/{$quan->slug}/danh-gia/realtime-check?" . http_build_query([
            'last_id' => (string) $review1->id,
            'last_count' => 1,
            'last_updated' => $review1->updated_at->toISOString(),
        ]));

        $responseAfterNew->assertOk();
        $responseAfterNew->assertJson([
            'has_updates' => true,
            'total_count' => 2,
            'unreplied_count' => 2,
            'latest_review' => [
                'id' => (string) $review2->id,
                'user_name' => 'Khách Hàng Hai',
                'so_sao' => 4,
            ],
        ]);
        $this->assertNotEmpty($responseAfterNew->json('html'));
        $this->assertStringContainsString('Khách Hàng Hai', $responseAfterNew->json('html'));
    }

    public function test_owner_detail_page_passes_unreplied_review_count(): void
    {
        $owner = $this->createChuQuan();
        $customer = User::factory()->create();

        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'slug' => 'quan-unreplied-badge-test',
        ]);

        QuanDanhGia::create([
            'quan_id' => $quan->id,
            'nguoi_dung_id' => $customer->id,
            'so_sao' => 5,
            'binh_luan' => 'Món ngon!',
            'phan_hoi' => 'Cảm ơn bạn nhiều ạ!',
        ]);

        // Cả 1 đánh giá đều đã được phản hồi -> unreplied_count = 0
        $response = $this->actingAs($owner)->get("/chu-quan/quan/{$quan->slug}");
        $response->assertOk();
        $response->assertViewHas('ownerReviewCount', 1);
        $response->assertViewHas('ownerUnrepliedCount', 0);
        // Badge chưa trả lời phải có class hidden vì không có đánh giá nào chưa phản hồi
        $this->assertStringContainsString('owner-reviews-tab-badge', $response->getContent());
        $this->assertStringContainsString('chưa trả lời', $response->getContent());
        $this->assertStringContainsString('id="owner-reviews-unreplied-count">0</span>', $response->getContent());
    }

    public function test_owner_viewing_customer_page_sees_back_to_admin_banner(): void
    {
        $owner = $this->createChuQuan();

        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'slug' => 'quan-owner-preview-banner-test',
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->actingAs($owner)->get("/quan/{$quan->slug}");
        $response->assertOk();
        $response->assertSee('Chế độ xem thực khách:');
        $response->assertSee('Quay lại Quản lý quán');
    }
}
