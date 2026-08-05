<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\DatLaiMatKhauRequest;
use App\Http\Requests\QuenMatKhauRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class QuenMatKhauController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.quen-mat-khau');
    }

    public function sendResetLink(QuenMatKhauRequest $request)
    {
        $email = $request->validated('email');
        $user = User::where('email', $email)->where('trang_thai', 'hoat_dong')->first();

        if ($user) {
            $token = Str::random(64);

            DB::transaction(function () use ($email, $token) {
                DB::table('password_reset_tokens')->where('email', $email)->delete();
                DB::table('password_reset_tokens')->insert([
                    'email' => $email,
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]);
            });

            $resetUrl = url('/dat-lai-mat-khau/'.$token.'?email='.urlencode($email));

            try {
                Mail::send('emails.reset-password', compact('user', 'resetUrl'), function ($message) use ($user) {
                    $message->to($user->email, $user->ho_ten)
                        ->subject('Đặt lại mật khẩu - Quán Mới');
                });
            } catch (\Throwable $exception) {
                // Keep the response neutral and avoid logging tokens or transport details.
                Log::error('Không thể gửi email đặt lại mật khẩu.');
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Nếu email thuộc một tài khoản hợp lệ, chúng tôi đã gửi hướng dẫn đặt lại mật khẩu.',
        ]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.dat-lai-mat-khau', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(DatLaiMatKhauRequest $request)
    {
        $data = $request->validated();
        $result = DB::transaction(function () use ($data) {
            $record = DB::table('password_reset_tokens')
                ->where('email', $data['email'])
                ->lockForUpdate()
                ->first();

            if (! $record || ! Hash::check($data['token'], $record->token)) {
                return false;
            }

            $expiration = (int) config('auth.passwords.users.expire', 60);
            if (Carbon::parse($record->created_at)->addMinutes($expiration)->isPast()) {
                DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

                return false;
            }

            $user = User::where('email', $data['email'])
                ->where('trang_thai', 'hoat_dong')
                ->lockForUpdate()
                ->first();

            if (! $user) {
                return false;
            }

            $user->update([
                'mat_khau' => Hash::make($data['mat_khau']),
                'remember_token' => Str::random(60),
            ]);

            DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

            if (config('session.driver') === 'database') {
                DB::table(config('session.table'))->where('user_id', $user->id)->delete();
            }

            return true;
        });

        if (! $result) {
            return response()->json([
                'success' => false,
                'message' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mật khẩu đã được đặt lại. Vui lòng đăng nhập lại.',
            'redirect_to' => route('login'),
        ]);
    }
}
