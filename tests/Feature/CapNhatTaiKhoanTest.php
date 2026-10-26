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

    public function test_phone_number_accepts_10_and_11_digits()
    {
        $user = User::factory()->create();

        // 10 digits
        $response1 = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => 'User 10 Digits',
            'so_dien_thoai' => '0912345678',
        ]);
        $response1->assertRedirect(route('tai-khoan.index'));
        $response1->assertSessionHasNoErrors();
        $this->assertDatabaseHas('nguoi_dung', ['id' => $user->id, 'so_dien_thoai' => '0912345678']);

        // 11 digits
        $response2 = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => 'User 11 Digits',
            'so_dien_thoai' => '01234567890',
        ]);
        $response2->assertRedirect(route('tai-khoan.index'));
        $response2->assertSessionHasNoErrors();
        $this->assertDatabaseHas('nguoi_dung', ['id' => $user->id, 'so_dien_thoai' => '01234567890']);
    }

    public function test_phone_number_rejects_more_than_11_digits()
    {
        $user = User::factory()->create();

        // 14 digits like user screenshot
        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => 'User Long Phone',
            'so_dien_thoai' => '03457374513333',
        ]);

        $response->assertSessionHasErrors(['so_dien_thoai']);
    }

    public function test_phone_number_rejects_less_than_10_digits()
    {
        $user = User::factory()->create();

        // 9 digits
        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => 'User Short Phone',
            'so_dien_thoai' => '012345678',
        ]);

        $response->assertSessionHasErrors(['so_dien_thoai']);
    }

    public function test_phone_number_rejects_not_starting_with_0()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => 'User Non Zero Phone',
            'so_dien_thoai' => '9123456789',
        ]);

        $response->assertSessionHasErrors(['so_dien_thoai']);
    }

    public function test_ngay_sinh_rejects_future_date()
    {
        $user = User::factory()->create();

        // Future date HTML5 format YYYY-MM-DD (e.g. 2099-02-20)
        $response1 = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => 'Future DOB User',
            'ngay_sinh' => '2099-02-20',
        ]);
        $response1->assertSessionHasErrors(['ngay_sinh']);

        // Future date Vietnamese format dd/mm/yyyy (e.g. 20/02/2099)
        $response2 = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => 'Future DOB User 2',
            'ngay_sinh' => '20/02/2099',
        ]);
        $response2->assertSessionHasErrors(['ngay_sinh']);
    }
}
