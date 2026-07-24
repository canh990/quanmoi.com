<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DangKyTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_validation_errors(): void
    {
        $response = $this->postJson('/dang-ky', [
            'ho_ten' => '',
            'email' => 'invalid-email',
            'mat_khau' => '123'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['success', 'errors']);
    }

    public function test_successful_direct_registration(): void
    {
        $response = $this->postJson('/dang-ky', [
            'ho_ten' => 'Nguyen Van A',
            'email' => 'nva@gmail.com',
            'mat_khau' => '123456'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đăng ký tài khoản thành công!',
            ]);

        // Assert user exists and is verified
        $user = User::where('email', 'nva@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Nguyen Van A', $user->ho_ten);
        $this->assertTrue((bool)$user->da_xac_thuc);
    }
}
