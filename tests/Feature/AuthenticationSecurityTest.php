<?php

namespace Tests\Feature;

use App\Mail\DangKyOtpMail;
use App\Models\User;
use App\Models\VaiTro;
use App\Models\XacThucOtp;
use Database\Seeders\VaiTroSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(VaiTroSeeder::class);
    }

    public function test_valid_login_regenerates_an_authenticated_session(): void
    {
        $user = User::factory()->create(['mat_khau' => Hash::make('correct-password')]);

        $response = $this->postJson(route('login.submit'), [
            'email' => $user->email,
            'mat_khau' => 'correct-password',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_login_is_rejected_and_rate_limited(): void
    {
        $user = User::factory()->create();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson(route('login.submit'), [
                'email' => $user->email,
                'mat_khau' => 'incorrect-password',
            ])->assertUnauthorized();
        }

        $this->postJson(route('login.submit'), [
            'email' => $user->email,
            'mat_khau' => 'incorrect-password',
        ])->assertTooManyRequests();
    }

    public function test_logout_invalidates_the_authenticated_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('logout'))->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_expired_otp_is_rejected(): void
    {
        $user = $this->unverifiedUser();
        $this->otpFor($user, '123456', now()->subMinute());

        $this->postJson(route('register.verify'), [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertStatus(400);

        $this->assertFalse($user->fresh()->da_xac_thuc);
    }

    public function test_otp_is_consumed_after_one_successful_use(): void
    {
        $user = $this->unverifiedUser();
        $this->otpFor($user, '123456', now()->addMinutes(3));

        $this->postJson(route('register.verify'), [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertOk();

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseMissing('xac_thuc_otp', ['email' => $user->email]);

        $this->postJson(route('register.verify'), [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertStatus(400);
    }

    public function test_incorrect_otp_locks_after_five_attempts(): void
    {
        $user = $this->unverifiedUser();
        $this->otpFor($user, '123456', now()->addMinutes(3));

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson(route('register.verify'), [
                'email' => $user->email,
                'otp' => '000000',
            ])->assertStatus(400);
        }

        $record = XacThucOtp::where('email', $user->email)->firstOrFail();
        $this->assertSame(5, $record->attempts);
        $this->assertTrue($record->locked_until->isFuture());
    }

    public function test_resending_otp_invalidates_the_prior_code_without_logging_it(): void
    {
        Mail::fake();
        Log::spy();

        $user = $this->unverifiedUser();
        $this->otpFor($user, '123456', now()->addMinutes(3));

        $this->postJson(route('register.resend'), ['email' => $user->email])->assertOk();

        $record = XacThucOtp::where('email', $user->email)->firstOrFail();
        $this->assertFalse(Hash::check('123456', $record->otp_hash));
        $this->assertSame(0, $record->attempts);
        Mail::assertSent(DangKyOtpMail::class);
        Log::shouldNotHaveReceived('info');
    }

    public function test_expired_reset_token_is_rejected(): void
    {
        $user = User::factory()->create();
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make('a'.str_repeat('b', 63)),
            'created_at' => now()->subMinutes(61),
        ]);

        $this->postJson(route('password.update'), $this->resetPayload($user->email, 'a'.str_repeat('b', 63)))
            ->assertStatus(400);
    }

    public function test_reset_token_cannot_be_reused(): void
    {
        $user = User::factory()->create();
        $token = 'a'.str_repeat('b', 63);
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $this->postJson(route('password.update'), $this->resetPayload($user->email, $token))->assertOk();
        $this->postJson(route('password.update'), $this->resetPayload($user->email, $token))->assertStatus(400);
    }

    private function unverifiedUser(): User
    {
        return User::factory()->create([
            'da_xac_thuc' => false,
            'ngay_xac_thuc' => null,
            'vai_tro_id' => VaiTro::where('ten', 'nguoi_dung')->value('id'),
        ]);
    }

    private function otpFor(User $user, string $otp, $expiresAt): void
    {
        XacThucOtp::create([
            'email' => $user->email,
            'otp' => hash('sha256', 'test-marker-'.$otp),
            'otp_hash' => Hash::make($otp),
            'expires_at' => $expiresAt,
            'last_sent_at' => now(),
        ]);
    }

    private function resetPayload(string $email, string $token): array
    {
        return [
            'email' => $email,
            'token' => $token,
            'mat_khau' => 'new-password',
            'mat_khau_xac_nhan' => 'new-password',
        ];
    }
}
