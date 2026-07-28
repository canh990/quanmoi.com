<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuanDaLuuTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_saved_places()
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

    public function test_user_can_toggle_saved_place()
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

    public function test_unauthenticated_user_cannot_save_place()
    {
        $quan = Quan::factory()->create();

        $response = $this->postJson(route('quan-da-luu.toggle', $quan->id));

        $response->assertStatus(401);
    }
}
