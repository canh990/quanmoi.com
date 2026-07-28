<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapNhatTaiKhoanTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_profile_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tai-khoan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('nguoi-dung.tai-khoan.index');
    }

    public function test_user_can_update_profile_info()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => 'New Name',
            'gioi_tinh' => 'nam',
            'ngay_sinh' => '1990-01-01',
            'dia_chi' => '123 Test St',
            'so_dien_thoai' => '0123456789'
        ]);

        $response->assertRedirect(route('tai-khoan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'ho_ten' => 'New Name',
            'gioi_tinh' => 'nam',
            'ngay_sinh' => '1990-01-01',
            'dia_chi' => '123 Test St',
            'so_dien_thoai' => '0123456789'
        ]);
    }

    public function test_user_cannot_update_invalid_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => '', // required
            'gioi_tinh' => 'invalid', // not in enum
        ]);

        $response->assertSessionHasErrors(['ho_ten', 'gioi_tinh']);
    }
}
