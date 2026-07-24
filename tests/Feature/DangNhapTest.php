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
        $response = $this->postJson('/dangnhap', [
            'email' => '',
            'mat_khau' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['success', 'errors']);
    }

    public function test_login_failure_with_wrong_credentials(): void
    {
        $response = $this->postJson('/dangnhap', [
            'email' => 'wrong@example.com',
            'mat_khau' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không chính xác.'
            ]);
    }

    public function test_login_failure_with_unverified_account(): void
    {
        // Create an unverified user
        $user = User::create([
            'ho_ten' => 'Nguyen Van Unverified',
            'email' => 'unverified@example.com',
            'mat_khau' => Hash::make('secret123'),
            'da_xac_thuc' => false,
        ]);

        $response = $this->postJson('/dangnhap', [
            'email' => 'unverified@example.com',
            'mat_khau' => 'secret123'
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'needs_verification' => true,
                'email' => 'unverified@example.com'
            ]);

        // User should not be authenticated
        $this->assertGuest();
    }

    public function test_login_success_with_verified_account(): void
    {
        // Create a verified user
        $user = User::create([
            'ho_ten' => 'Nguyen Van Verified',
            'email' => 'verified@example.com',
            'mat_khau' => Hash::make('secret123'),
            'da_xac_thuc' => true,
            'ngay_xac_thuc' => now(),
        ]);

        $response = $this->postJson('/dangnhap', [
            'email' => 'verified@example.com',
            'mat_khau' => 'secret123'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'redirect_to' => '/'
            ]);

        // User should be authenticated
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout(): void
    {
        $user = User::create([
            'ho_ten' => 'Nguyen Van A',
            'email' => 'nva@example.com',
            'mat_khau' => Hash::make('secret123'),
            'da_xac_thuc' => true,
        ]);

        $this->actingAs($user);

        $response = $this->post('/dangxuat');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
