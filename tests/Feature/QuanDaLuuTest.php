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
}
