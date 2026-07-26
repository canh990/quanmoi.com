<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Find user by google_id or email
            $user = User::withTrashed()
                ->where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                if ($user->trashed()) {
                    return redirect('/dang-nhap')->withErrors(['error' => 'Tài khoản của bạn đã bị vô hiệu hóa/xóa. Vui lòng liên hệ quản trị viên.']);
                }

                // If user exists, update google_id and avatar if missing
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'anh_dai_dien' => $user->anh_dai_dien ?? $googleUser->getAvatar(),
                    ]);
                }
                
                if ($user->trang_thai === 'bi_khoa') {
                    return redirect('/dang-nhap')->withErrors(['error' => 'Tài khoản của bạn đã bị khoá. Vui lòng liên hệ quản trị viên.']);
                }

                Auth::login($user);
            } else {
                // Create new user
                $vaiTroThanhVien = VaiTro::where('ten', 'nguoi_dung')->first();

                $newUser = User::create([
                    'ho_ten' => $googleUser->getName() ?? 'Người dùng',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'anh_dai_dien' => $googleUser->getAvatar(),
                    'mat_khau' => Hash::make(Str::random(16)), // Required field in DB
                    'vai_tro_id' => $vaiTroThanhVien ? $vaiTroThanhVien->id : null,
                    'da_xac_thuc' => true,
                    'ngay_xac_thuc' => now(),
                    'trang_thai' => 'hoat_dong',
                ]);

                Auth::login($newUser);
            }

            return redirect()->intended('/');

        } catch (\Exception $e) {
            return redirect('/dang-nhap')->withErrors(['error' => 'Có lỗi xảy ra khi đăng nhập bằng Google. Vui lòng thử lại.']);
        }
    }
}
