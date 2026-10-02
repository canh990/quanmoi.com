<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DangNhapTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_validation_errors(): void
    {
        $response = $this->postJson('/dang-nhap', [
            'email' => '',
            'mat_khau' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_login_failure_with_wrong_credentials(): void
    {
        $response = $this->postJson('/dang-nhap', [
            'email' => 'wrong@example.com',
            'mat_khau' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không chính xác.'
            ]);
    }

    public function test_login_success(): void
    {
        $user = User::create([
            'ho_ten' => 'Nguyen Van Verified',
            'email' => 'verified@example.com',
            'mat_khau' => Hash::make('secret123'),
            'da_xac_thuc' => true,
            'ngay_xac_thuc' => now(),
            'trang_thai' => 'hoat_dong',
        ]);

        $response = $this->postJson('/dang-nhap', [
            'email' => 'verified@example.com',
            'mat_khau' => 'secret123'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'redirect_to' => '/'
            ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_logout(): void
    {
        $user = User::create([
            'ho_ten' => 'Nguyen Van A',
            'email' => 'nva@example.com',
            'mat_khau' => Hash::make('secret123'),
            'da_xac_thuc' => true,
            'trang_thai' => 'hoat_dong',
        ]);

        $response = $this->actingAs($user)->post('/dang-xuat');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
