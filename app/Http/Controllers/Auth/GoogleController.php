<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

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
        } catch (InvalidStateException $e) {
            // State mismatch — thường xảy ra khi dùng back button hoặc session expire
            Log::warning('Google OAuth InvalidStateException: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('social_error', 'Phiên đăng nhập Google đã hết hạn. Vui lòng thử lại.');
        } catch (\Exception $e) {
            Log::error('Google OAuth callback error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('social_error', 'Có lỗi xảy ra khi đăng nhập bằng Google. Vui lòng thử lại.');
        }

        try {
            // Tìm user theo google_id trước (nhanh nhất), sau đó theo email
            $user = User::withTrashed()
                ->where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Tài khoản bị xóa mềm
                if ($user->trashed()) {
                    return redirect()->route('login')
                        ->with('social_error', 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.');
                }

                // Tài khoản bị khóa
                if ($user->trang_thai === 'bi_khoa') {
                    return redirect()->route('login')
                        ->with('social_error', 'Tài khoản của bạn đã bị khoá. Vui lòng liên hệ quản trị viên.');
                }

                // Liên kết google_id nếu chưa có (user đã đăng ký bằng email trước đó)
                $updates = [];
                if (! $user->google_id) {
                    $updates['google_id'] = $googleUser->getId();
                }
                // Cập nhật ảnh đại diện nếu chưa có
                if (! $user->anh_dai_dien && $googleUser->getAvatar()) {
                    $updates['anh_dai_dien'] = $googleUser->getAvatar();
                }
                // Đánh dấu đã xác thực nếu chưa (trường hợp đăng ký email nhưng chưa OTP)
                if (! $user->da_xac_thuc) {
                    $updates['da_xac_thuc'] = true;
                    $updates['ngay_xac_thuc'] = now();
                }
                if (! empty($updates)) {
                    $user->update($updates);
                }
            } else {
                // Tạo tài khoản mới từ Google
                $vaiTroNguoiDung = VaiTro::where('ten', 'nguoi_dung')->first();

                $user = DB::transaction(function () use ($googleUser, $vaiTroNguoiDung) {
                    return User::create([
                        'ho_ten'        => $googleUser->getName() ?? 'Người dùng',
                        'email'         => $googleUser->getEmail(),
                        'google_id'     => $googleUser->getId(),
                        'anh_dai_dien'  => $googleUser->getAvatar(),
                        'mat_khau'      => Hash::make(Str::random(32)),
                        'vai_tro_id'    => $vaiTroNguoiDung?->id,
                        'da_xac_thuc'   => true,
                        'ngay_xac_thuc' => now(),
                        'trang_thai'    => 'hoat_dong',
                    ]);
                });
            }

            Auth::login($user, remember: true);
            request()->session()->regenerate();

            // Redirect theo vai trò
            if ($user->hasRole('admin')) {
                return redirect()->intended('/admin');
            } elseif ($user->hasRole('chu_quan')) {
                return redirect()->intended('/chu-quan/quan');
            }

            return redirect()->intended('/');

        } catch (\Exception $e) {
            Log::error('Google OAuth login processing error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('login')
                ->with('social_error', 'Có lỗi xảy ra khi xử lý đăng nhập. Vui lòng thử lại.');
        }
    }
}
