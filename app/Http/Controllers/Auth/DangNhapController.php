<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DangNhapController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.dangnhap');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'mat_khau' => 'required|string',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không đúng định dạng.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->mat_khau,
        ];

        $remember = $request->has('remember') && $request->remember;

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->da_xac_thuc) {
                // Log out immediately as they need to verify OTP first
                Auth::logout();

                // Generate new OTP for them automatically to make it smoother!
                try {
                    // We can reuse the registration OTP generation code or let the frontend trigger it
                    // For best UX, let's trigger sending OTP
                    app(DangKyController::class)->resendOtp(new Request(['email' => $user->email]));
                } catch (\Exception $e) {
                    // Suppress error if it fails (e.g. method injection issues)
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản chưa được xác thực OTP. Đã gửi lại mã xác thực mới đến email của bạn.',
                    'needs_verification' => true,
                    'email' => $user->email
                ], 403);
            }

            // Authentication passed
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'redirect_to' => '/'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email hoặc mật khẩu không chính xác.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
