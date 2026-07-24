<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect sang trang đăng nhập Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Xử lý callback từ Google OAuth
     */
    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();
            return $this->handleSocialLogin($socialUser, 'google');
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Đăng nhập bằng Google thất bại. Vui lòng thử lại.');
        }
    }

    /**
     * Redirect sang trang đăng nhập Facebook OAuth
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Xử lý callback từ Facebook OAuth
     */
    public function handleFacebookCallback()
    {
        try {
            $socialUser = Socialite::driver('facebook')->user();
            return $this->handleSocialLogin($socialUser, 'facebook');
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Đăng nhập bằng Facebook thất bại. Vui lòng thử lại.');
        }
    }

    /**
     * Xử lý chung: Tìm/tạo tài khoản và đăng nhập
     */
    private function handleSocialLogin($socialUser, string $provider)
    {
        $column = $provider . '_id'; // google_id hoặc facebook_id

        // 1. Tìm user theo social_id trước
        $user = User::where($column, $socialUser->getId())->first();

        if (!$user) {
            // 2. Nếu không có, tìm theo email (tài khoản email đã đăng ký trước đó)
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Liên kết social_id vào tài khoản hiện có
                $user->update([$column => $socialUser->getId()]);
            } else {
                // 3. Nếu không có tài khoản, tạo mới
                $user = User::create([
                    'ho_ten'        => $socialUser->getName() ?? 'Người dùng Mới',
                    'email'         => $socialUser->getEmail(),
                    'mat_khau'      => bcrypt(Str::random(32)), // mật khẩu ngẫu nhiên
                    'anh_dai_dien'  => $socialUser->getAvatar(),
                    $column         => $socialUser->getId(),
                    'da_xac_thuc'   => true,           // OAuth đã xác thực email rồi
                    'ngay_xac_thuc' => now(),
                    'trang_thai'    => 'hoat_dong',
                ]);
            }
        }

        // Kiểm tra trạng thái tài khoản
        if ($user->trang_thai === 'bi_khoa') {
            return redirect()->route('login')
                ->with('error', 'Tài khoản của bạn đã bị khoá. Vui lòng liên hệ hỗ trợ.');
        }

        // Đăng nhập và redirect về trang chủ
        Auth::login($user, true); // remember=true
        request()->session()->regenerate();

        return redirect()->intended('/');
    }
}
