<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangNhapRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DangNhapController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.dangnhap');
    }

    public function login(DangNhapRequest $request)
    {
        $data = $request->validated();
        $remember = (bool) ($data['remember'] ?? false);

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['mat_khau']], $remember)) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không chính xác.',
            ], 401);
        }

        $user = Auth::user();
        if ($user->trang_thai !== 'hoat_dong' || ! $user->da_xac_thuc) {
            $this->invalidateAuthenticatedSession($request);

            return response()->json([
                'success' => false,
                'message' => 'Không thể đăng nhập bằng thông tin tài khoản này.',
                'require_otp' => $user->trang_thai === 'hoat_dong' && ! $user->da_xac_thuc,
                'needs_verification' => $user->trang_thai === 'hoat_dong' && ! $user->da_xac_thuc,
                'email' => $user->trang_thai === 'hoat_dong' && ! $user->da_xac_thuc ? $user->email : null,
            ], 403);
        }

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'redirect_to' => '/',
        ]);
    }

    public function logout(Request $request)
    {
        $this->invalidateAuthenticatedSession($request);

        return redirect('/');
    }

    private function invalidateAuthenticatedSession(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
