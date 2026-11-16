<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuanDaLuuTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_saved_places(): void
    {
        $user = User::factory()->create();
        $quan1 = Quan::factory()->create();
        $quan2 = Quan::factory()->create();

        $user->savedQuan()->attach($quan1->id);

        $response = $this->actingAs($user)->get(route('quan-da-luu.index'));

        $response->assertStatus(200);
        $response->assertSee($quan1->ten_quan);
        $response->assertDontSee($quan2->ten_quan);
    }

    public function test_guest_is_redirected_when_accessing_saved_places_page(): void
    {
        $response = $this->get(route('quan-da-luu.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_saved_places_page_shows_empty_state_when_no_places_saved(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('quan-da-luu.index'));

        $response->assertStatus(200);
        $response->assertSee('Bạn chưa lưu địa điểm nào.');
    }

    public function test_user_can_toggle_saved_place(): void
    {
        $user = User::factory()->create();
        $quan = Quan::factory()->create();

        // 1. Save place
        $response = $this->actingAs($user)->postJson(route('quan-da-luu.toggle', $quan->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'saved', 'success' => true]);

        $this->assertDatabaseHas('quan_da_luu', [
            'nguoi_dung_id' => $user->id,
            'quan_id' => $quan->id,
        ]);

        // 2. Unsave place
        $response = $this->actingAs($user)->postJson(route('quan-da-luu.toggle', $quan->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'unsaved', 'success' => true]);

        $this->assertDatabaseMissing('quan_da_luu', [
            'nguoi_dung_id' => $user->id,
            'quan_id' => $quan->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_save_place(): void
    {
        $quan = Quan::factory()->create();

        $response = $this->postJson(route('quan-da-luu.toggle', $quan->id));

        $response->assertStatus(401);
    }

    public function test_user_cannot_save_unapproved_place(): void
    {
        $user = User::factory()->create();
        $pendingQuan = Quan::factory()->create(['trang_thai' => 'chua_duyet']);

        $response = $this->actingAs($user)->postJson(route('quan-da-luu.toggle', $pendingQuan->id));

        $response->assertStatus(404);
        $this->assertDatabaseMissing('quan_da_luu', [
            'nguoi_dung_id' => $user->id,
            'quan_id' => $pendingQuan->id,
        ]);
    }

    public function test_user_cannot_save_nonexistent_place(): void
    {
        $user = User::factory()->create();
        $fakeUuid = '00000000-0000-0000-0000-000000000000';

        $response = $this->actingAs($user)->postJson(route('quan-da-luu.toggle', $fakeUuid));

        $response->assertStatus(404);
    }

    public function test_saved_places_appear_in_tai_khoan_profile(): void
    {
        $user = User::factory()->create();
        $quan = Quan::factory()->create();

        $user->savedQuan()->attach($quan->id);

        $response = $this->actingAs($user)->get(route('tai-khoan.index'));

        $response->assertStatus(200);
        $response->assertSee($quan->ten_quan);
        $response->assertSee('toggleSavePlace', false);
    }

    public function test_user_can_unsave_already_saved_place_even_if_unapproved_later(): void
    {
        $user = User::factory()->create();
        $quan = Quan::factory()->create(['trang_thai' => 'da_duyet']);
        $user->savedQuan()->attach($quan->id);

        $quan->update(['trang_thai' => 'chua_duyet']);

        $response = $this->actingAs($user)->postJson(route('quan-da-luu.toggle', $quan->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'unsaved',
            'saved_count' => 0,
        ]);

        $this->assertDatabaseMissing('quan_da_luu', [
            'nguoi_dung_id' => $user->id,
            'quan_id' => $quan->id,
        ]);
    }

    public function test_tai_khoan_profile_renders_six_saved_places_payload_and_counts(): void
    {
        $user = User::factory()->create();
        $quans = Quan::factory()->count(6)->create(['trang_thai' => 'da_duyet']);

        foreach ($quans as $quan) {
            $user->savedQuan()->attach($quan->id);
        }

        $response = $this->actingAs($user)->get(route('tai-khoan.index'));

        $response->assertStatus(200);
        $response->assertSee('(6)');
        $response->assertSee('Xem thêm 1 địa điểm đã lưu');
        $response->assertSee('savedPlacesState');
    }

    public function test_home_page_includes_saved_quan_ids_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $quan = Quan::factory()->create();

        $user->savedQuan()->attach($quan->id);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewHas('savedQuanIds', function ($savedIds) use ($quan) {
            return in_array($quan->id, $savedIds, true);
        });
    }

    public function test_idempotent_save_and_unsave_actions_prevent_duplicate_tab_inversion(): void
    {
        $user = User::factory()->create();
        $quan = Quan::factory()->create(['trang_thai' => 'da_duyet']);

        // Giả lập Tab 1 và Tab 2 (nhân đôi): Tab 1 bấm Lưu thành công, Tab 2 bấm Lưu trùng báo lỗi lưu không hợp lệ (không bị bỏ lưu ngược)
        $resTab1Save = $this->actingAs($user)->postJson(route('quan-da-luu.toggle', $quan->id), ['action' => 'save']);
        $resTab1Save->assertStatus(200)->assertJson(['success' => true, 'status' => 'saved', 'saved_count' => 1]);

        $resTab2Save = $this->actingAs($user)->postJson(route('quan-da-luu.toggle', $quan->id), ['action' => 'save']);
        $resTab2Save->assertStatus(400)->assertJson([
            'success' => false,
            'status' => 'saved',
            'already_saved' => true,
            'saved_count' => 1,
        ]);

        $this->assertDatabaseHas('quan_da_luu', [
            'nguoi_dung_id' => $user->id,
            'quan_id' => $quan->id,
        ]);

        // Giả lập Tab 1 bấm Bỏ lưu trước OK, rồi Tab 2 cũ bấm Bỏ lưu -> Báo lỗi xóa không hợp lệ (400) và không bao giờ tự lưu lại
        $resTab1Unsave = $this->actingAs($user)->postJson(route('quan-da-luu.toggle', $quan->id), ['action' => 'unsave']);
        $resTab1Unsave->assertStatus(200)->assertJson(['success' => true, 'status' => 'unsaved', 'saved_count' => 0]);

        $resTab2Unsave = $this->actingAs($user)->postJson(route('quan-da-luu.toggle', $quan->id), ['action' => 'unsave']);
        $resTab2Unsave->assertStatus(400)->assertJson([
            'success' => false,
            'status' => 'unsaved',
            'already_unsaved' => true,
            'saved_count' => 0,
        ]);

        $this->assertDatabaseMissing('quan_da_luu', [
            'nguoi_dung_id' => $user->id,
            'quan_id' => $quan->id,
        ]);

        // Giá trị action không hợp lệ bị từ chối 422
        $invalid = $this->actingAs($user)->postJson(route('quan-da-luu.toggle', $quan->id), ['action' => 'invalid']);
        $invalid->assertStatus(422);
    }

    public function test_sync_endpoint_returns_current_saved_state_for_guest_and_user(): void
    {
        $guestRes = $this->getJson(route('quan-da-luu.sync'));
        $guestRes->assertStatus(200)->assertJson([
            'authenticated' => false,
            'saved_ids' => [],
            'saved_count' => 0,
        ]);

        $user = User::factory()->create();
        $approved = Quan::factory()->create(['trang_thai' => 'da_duyet']);
        $pending = Quan::factory()->create(['trang_thai' => 'chua_duyet']);
        $user->savedQuan()->attach([$approved->id, $pending->id]);

        $authRes = $this->actingAs($user)->getJson(route('quan-da-luu.sync', ['with_places' => 1]));
        $authRes->assertStatus(200)
            ->assertJson([
                'authenticated' => true,
                'saved_ids' => [$approved->id],
                'saved_count' => 1,
            ])
            ->assertJsonCount(1, 'places')
            ->assertJsonPath('places.0.id', $approved->id);
        $this->assertNotEmpty($authRes->json('csrf_token'));
    }

    public function test_pages_load_cross_tab_saved_places_sync_script(): void
    {
        $user = User::factory()->create();
        $quan = Quan::factory()->create(['trang_thai' => 'da_duyet']);
        $user->savedQuan()->attach($quan->id);

        foreach ([
            route('home'),
            route('quan.detail', $quan->slug),
            route('quan-da-luu.index'),
            route('tai-khoan.index'),
        ] as $url) {
            $res = $this->actingAs($user)->get($url);
            $res->assertStatus(200);
            $res->assertSee('QuanMoiSavedSync', false);
            $res->assertSee('qm_saved_places_sync', false);
        }
    }
}
