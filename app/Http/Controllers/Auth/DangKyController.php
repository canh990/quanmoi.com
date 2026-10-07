<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangKyRequest;
use App\Http\Requests\GuiLaiOtpRequest;
use App\Http\Requests\XacThucOtpRequest;
use App\Mail\DangKyOtpMail;
use App\Models\User;
use App\Models\XacThucOtp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DangKyController extends Controller
{
    private const OTP_TTL_MINUTES = 3;

    private const OTP_MAX_ATTEMPTS = 5;

    private const OTP_LOCK_MINUTES = 15;

    public function showRegistrationForm()
    {
        return view('auth.dangky');
    }

    public function register(DangKyRequest $request)
    {
        $data = $request->validated();
        $user = User::withTrashed()->where('email', $data['email'])->first();

        if (! $user) {
            $roleId = DB::table('vai_tro')->where('ten', 'nguoi_dung')->value('id');

            $user = User::create([
                'ho_ten' => $data['ho_ten'],
                'email' => $data['email'],
                'mat_khau' => Hash::make($data['mat_khau']),
                'da_xac_thuc' => true,
                'ngay_xac_thuc' => now(),
                'vai_tro_id' => $roleId,
                'trang_thai' => 'hoat_dong',
            ]);
        } else {
            $user->update([
                'ho_ten' => $data['ho_ten'],
                'mat_khau' => Hash::make($data['mat_khau']),
                'da_xac_thuc' => true,
                'ngay_xac_thuc' => $user->ngay_xac_thuc ?? now(),
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký tài khoản thành công!',
        ]);
    }

    public function verifyOtp(XacThucOtpRequest $request)
    {
        $data = $request->validated();

        $result = DB::transaction(function () use ($data) {
            $record = XacThucOtp::where('email', $data['email'])
                ->latest('id')
                ->lockForUpdate()
                ->first();

            // Legacy plaintext records are deliberately no longer accepted.
            if (! $record || ! $record->otp_hash) {
                return ['status' => 'invalid'];
            }

            if ($record->locked_until?->isFuture()) {
                return ['status' => 'locked'];
            }

            if ($record->expires_at->isPast()) {
                $record->delete();

                return ['status' => 'expired'];
            }

            if (! Hash::check($data['otp'], $record->otp_hash)) {
                $record->attempts++;
                if ($record->attempts >= self::OTP_MAX_ATTEMPTS) {
                    $record->locked_until = now()->addMinutes(self::OTP_LOCK_MINUTES);
                }
                $record->save();

                return ['status' => $record->locked_until?->isFuture() ? 'locked' : 'invalid'];
            }

            $user = User::where('email', $data['email'])->lockForUpdate()->first();
            if (! $user || $user->trang_thai !== 'hoat_dong') {
                return ['status' => 'invalid'];
            }

            $user->update([
                'da_xac_thuc' => true,
                'ngay_xac_thuc' => now(),
            ]);

            // Consume the OTP in the same transaction that verifies the account.
            $record->delete();

            return ['status' => 'verified', 'user' => $user];
        });

        if ($result['status'] !== 'verified') {
            $message = match ($result['status']) {
                'expired' => 'Mã OTP đã hết hạn. Vui lòng gửi lại mã mới.',
                'locked' => 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau.',
                default => 'Mã OTP không chính xác hoặc không còn hiệu lực.',
            };

            return response()->json(['success' => false, 'message' => $message], 400);
        }

        Auth::login($result['user']);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Xác thực tài khoản thành công!',
        ]);
    }

    public function resendOtp(GuiLaiOtpRequest $request)
    {
        $email = $request->validated('email');
        $user = User::where('email', $email)->first();

        if ($user && ! $user->da_xac_thuc && $user->trang_thai === 'hoat_dong') {
            $this->sendOtp($user);
        }

        return response()->json([
            'success' => true,
            'message' => 'Nếu email đủ điều kiện, mã xác thực mới đã được gửi.',
        ]);
    }

    private function sendOtp(User $user): void
    {
        $otp = (string) random_int(100000, 999999);

        DB::transaction(function () use ($user, $otp) {
            // A resend invalidates every prior code for this address.
            XacThucOtp::where('email', $user->email)->delete();

            XacThucOtp::create([
                'email' => $user->email,
                // The legacy non-null column receives an unrelated marker, never the OTP.
                'otp' => hash('sha256', random_bytes(32)),
                'otp_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
                'attempts' => 0,
                'locked_until' => null,
                'last_sent_at' => now(),
            ]);
        });

        try {
            Mail::to($user->email)->send(new DangKyOtpMail($otp));
        } catch (\Throwable $exception) {
            // Do not include email, OTP, or transport details in application logs.
            Log::error('Không thể gửi email xác thực tài khoản.');
        }
    }
}
