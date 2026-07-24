<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DangKyTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_validation_errors(): void
    {
        $response = $this->postJson('/dangky', [
            'ho_ten' => '',
            'email' => 'invalid-email',
            'mat_khau' => '123'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['success', 'errors']);
    }

    public function test_successful_first_step_registration(): void
    {
        $response = $this->postJson('/dangky', [
            'ho_ten' => 'Nguyen Van A',
            'email' => 'nva@gmail.com',
            'mat_khau' => '123456'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Mã OTP đã được gửi đến email của bạn.',
                'email' => 'nva@gmail.com'
            ]);

        // Assert user exists but is not verified
        $user = User::where('email', 'nva@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Nguyen Van A', $user->ho_ten);
        $this->assertFalse((bool)$user->da_xac_thuc);

        // Assert OTP is created in database
        $otpRecord = DB::table('xac_thuc_otp')->where('email', 'nva@gmail.com')->first();
        $this->assertNotNull($otpRecord);
        $this->assertEquals(6, strlen($otpRecord->otp));
    }

    public function test_otp_verification_failure_with_wrong_code(): void
    {
        // 1. Register first
        $this->postJson('/dangky', [
            'ho_ten' => 'Nguyen Van A',
            'email' => 'nva@gmail.com',
            'mat_khau' => '123456'
        ]);

        // 2. Submit wrong OTP
        $response = $this->postJson('/dangky/xac-thuc', [
            'email' => 'nva@gmail.com',
            'otp' => '000000'
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Mã OTP không chính xác.'
            ]);

        $user = User::where('email', 'nva@gmail.com')->first();
        $this->assertFalse((bool)$user->da_xac_thuc);
    }

    public function test_otp_verification_success(): void
    {
        // 1. Register first
        $this->postJson('/dangky', [
            'ho_ten' => 'Nguyen Van A',
            'email' => 'nva@gmail.com',
            'mat_khau' => '123456'
        ]);

        // Get the OTP code from database
        $otpRecord = DB::table('xac_thuc_otp')->where('email', 'nva@gmail.com')->first();
        $this->assertNotNull($otpRecord);

        // 2. Submit correct OTP
        $response = $this->postJson('/dangky/xac-thuc', [
            'email' => 'nva@gmail.com',
            'otp' => $otpRecord->otp
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xác thực tài khoản thành công!'
            ]);

        // Assert user is verified and logged in
        $user = User::where('email', 'nva@gmail.com')->first();
        $this->assertTrue((bool)$user->da_xac_thuc);
        $this->assertNotNull($user->ngay_xac_thuc);

        $this->assertAuthenticatedAs($user);

        // Assert OTP is deleted from database
        $otpDeleted = DB::table('xac_thuc_otp')->where('email', 'nva@gmail.com')->first();
        $this->assertNull($otpDeleted);
    }

    public function test_otp_resending(): void
    {
        // 1. Register first
        $this->postJson('/dangky', [
            'ho_ten' => 'Nguyen Van A',
            'email' => 'nva@gmail.com',
            'mat_khau' => '123456'
        ]);

        $otpRecord1 = DB::table('xac_thuc_otp')->where('email', 'nva@gmail.com')->first();

        // 2. Resend OTP
        $response = $this->postJson('/dangky/gui-lai', [
            'email' => 'nva@gmail.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đã gửi lại mã OTP mới.'
            ]);

        $otpRecord2 = DB::table('xac_thuc_otp')->where('email', 'nva@gmail.com')->first();
        $this->assertNotNull($otpRecord2);
        
        // Assert OTP has changed
        $this->assertNotEquals($otpRecord1->otp, $otpRecord2->otp);
    }
}
