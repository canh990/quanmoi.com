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

            // Check if user is locked
            if ($user->trang_thai === 'bi_khoa') {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản của bạn đã bị khoá. Vui lòng liên hệ quản trị viên.'
                ], 403);
            }

            // Check if user is verified via OTP
            if (!$user->da_xac_thuc) {
                Auth::logout();
                
                // Gửi lại OTP tự động (nếu cần thiết, hoặc frontend có nút gửi lại)
                // Ở đây ta trả về require_otp để frontend hiển thị modal nhập OTP
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản chưa được xác thực. Vui lòng kiểm tra email để nhập mã OTP.',
                    'require_otp' => true,
                    'email' => $user->email // Trả về email để frontend gọi API gửi lại OTP
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
