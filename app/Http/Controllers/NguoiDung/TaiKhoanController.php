<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapNhatTaiKhoanRequest;
use App\Http\Requests\DoiMatKhauRequest;
use App\Services\DiaChiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TaiKhoanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $savedQuan = $user->savedQuan()
            ->with(['hinhAnh' => function ($q) {
                $q->limit(1);
            }])
            ->withCount('danhGia')
            ->withAvg('danhGia', 'so_sao')
            ->orderByPivot('created_at', 'desc')
            ->get();

        return view('nguoi-dung.tai-khoan.index', compact('user', 'savedQuan'));
    }

    public function settings(DiaChiService $diaChiService)
    {
        $user = Auth::user();
        $tinhThanhs = $diaChiService->getTinhThanh();

        // Lấy danh sách phiên đăng nhập gần đây từ bảng sessions
        $currentSessionId = session()->getId();
        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->take(5)
            ->get()
            ->map(function ($s) use ($currentSessionId) {
                $device = $this->parseUserAgent($s->user_agent);
                return [
                    'id' => $s->id,
                    'is_current' => $s->id === $currentSessionId,
                    'ip_address' => $s->ip_address,
                    'device_name' => $device['name'],
                    'device_icon' => $device['icon'],
                    'platform' => $device['platform'],
                    'last_activity' => Carbon::createFromTimestamp($s->last_activity)->diffForHumans(),
                ];
            });

        return view('nguoi-dung.tai-khoan.cai-dat', compact('user', 'tinhThanhs', 'sessions'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        // 1. Khóa lạc quan (Optimistic Locking): Tránh xung đột nhiều tab
        $lastUpdatedAt = $request->input('last_updated_at');
        if ($lastUpdatedAt && $user->updated_at) {
            try {
                $clientTime = Carbon::parse($lastUpdatedAt);
                if ($user->updated_at->timestamp > $clientTime->timestamp) {
                    return redirect()->route('tai-khoan.cai-dat')
                        ->with('error_conflict', 'Cài đặt đã được cập nhật ở tab hoặc thiết bị khác, vui lòng tải lại trang.');
                }
            } catch (\Throwable $e) {
                // Bỏ qua nếu thời gian không hợp lệ
            }
        }

        $validated = $request->validate([
            'khu_vuc_mac_dinh' => 'nullable|string|max:100',
            'ban_kinh_tim_kiem' => 'nullable|integer|min:1|max:50',
            'so_thich_am_thuc' => 'nullable|array',
            'so_thich_am_thuc.*' => 'string|max:50',
            'thong_bao_email_quan_moi' => 'nullable|boolean',
            'thong_bao_email_tuong_tac' => 'nullable|boolean',
            'thong_bao_khuyen_mai' => 'nullable|boolean',
            'che_do_danh_gia' => 'nullable|in:cong_khai,an_danh',
            'cong_khai_quan_da_luu' => 'nullable|boolean',
            'giao_dien_che_do' => 'nullable|in:light,dark,system',
        ]);

        $settings = $user->cai_dat ?? [];

        // Gộp các cấu hình mới
        $settings['khu_vuc_mac_dinh'] = $validated['khu_vuc_mac_dinh'] ?? $settings['khu_vuc_mac_dinh'] ?? '';
        $settings['ban_kinh_tim_kiem'] = isset($validated['ban_kinh_tim_kiem']) ? (int) $validated['ban_kinh_tim_kiem'] : ($settings['ban_kinh_tim_kiem'] ?? 5);
        $settings['so_thich_am_thuc'] = $validated['so_thich_am_thuc'] ?? [];

        $settings['thong_bao_email_quan_moi'] = $request->boolean('thong_bao_email_quan_moi');
        $settings['thong_bao_email_tuong_tac'] = $request->boolean('thong_bao_email_tuong_tac');
        $settings['thong_bao_khuyen_mai'] = $request->boolean('thong_bao_khuyen_mai');

        $settings['che_do_danh_gia'] = $validated['che_do_danh_gia'] ?? ($settings['che_do_danh_gia'] ?? 'cong_khai');
        $settings['cong_khai_quan_da_luu'] = $request->boolean('cong_khai_quan_da_luu');
        $settings['giao_dien_che_do'] = $validated['giao_dien_che_do'] ?? 'light';

        $user->cai_dat = $settings;
        $user->save();

        return redirect()->route('tai-khoan.cai-dat')->with('success', 'Lưu tùy chọn cài đặt thành công.');
    }

    private function parseUserAgent(?string $userAgent): array
    {
        if (empty($userAgent)) {
            return [
                'name' => 'Thiết bị không xác định',
                'icon' => 'devices',
                'platform' => 'Không rõ',
            ];
        }

        $platform = 'Máy tính';
        $icon = 'desktop_windows';

        if (preg_match('/(iPhone|iPad|iPod)/i', $userAgent)) {
            $platform = 'iOS';
            $icon = 'phone_iphone';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $platform = 'Android';
            $icon = 'smartphone';
        } elseif (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
            $platform = 'macOS';
            $icon = 'laptop_mac';
        } elseif (preg_match('/Windows/i', $userAgent)) {
            $platform = 'Windows';
            $icon = 'desktop_windows';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $platform = 'Linux';
            $icon = 'terminal';
        }

        $browser = 'Trình duyệt web';
        if (preg_match('/Edg/i', $userAgent)) {
            $browser = 'Microsoft Edge';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Google Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Mozilla Firefox';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browser = 'Opera';
        }

        return [
            'name' => "{$browser} ({$platform})",
            'icon' => $icon,
            'platform' => $platform,
        ];
    }

    public function update(CapNhatTaiKhoanRequest $request, \App\Services\UploadAnhService $uploadService)
    {
        $user = Auth::user();

        // 1. Khóa lạc quan (Optimistic Locking): Kiểm tra nếu dữ liệu đã được cập nhật ở tab khác
        $lastUpdatedAt = $request->input('last_updated_at');
        if ($lastUpdatedAt && $user->updated_at) {
            try {
                $clientTime = \Carbon\Carbon::parse($lastUpdatedAt);
                if ($user->updated_at->timestamp > $clientTime->timestamp) {
                    return redirect()->route('tai-khoan.index')
                        ->with('error_conflict', 'Dữ liệu đã được cập nhật ở tab khác, vui lòng tải lại trang để thấy dữ liệu mới nhất.');
                }
            } catch (\Throwable $e) {
                // Bỏ qua nếu parse không hợp lệ
            }
        }

        $validated = $request->validated();
        unset($validated['last_updated_at']);

        if ($request->hasFile('anh_dai_dien')) {
            $result = $uploadService->uploadAvatar(
                $request->file('anh_dai_dien'),
                $user->id,
                $user->anh_dai_dien_key
            );

            $validated['anh_dai_dien']     = $result['url'];
            $validated['anh_dai_dien_key'] = $result['key'];
        } else {
            // Không upload ảnh mới → không thay đổi anh_dai_dien & anh_dai_dien_key
            unset($validated['anh_dai_dien']);
        }

        $user->update($validated);

        return redirect()->route('tai-khoan.index')->with('success', 'Cập nhật thông tin tài khoản thành công.');
    }

    public function updatePassword(DoiMatKhauRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'mat_khau' => Hash::make($request->password),
        ]);

        return redirect()->route('tai-khoan.index')->with('success', 'Đổi mật khẩu thành công.');
    }

    public function requestVerification(Request $request)
    {
        $user = Auth::user();
        if ($user->da_xac_thuc) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tài khoản của bạn đã được xác thực danh tính đầy đủ.',
                    'already_verified' => true,
                ]);
            }
            return back()->with('info', 'Tài khoản của bạn đã được xác thực danh tính đầy đủ.');
        }

        $validated = $request->validate([
            'ly_do' => 'nullable|string|max:500',
        ]);

        $settings = $user->cai_dat ?? [];
        $settings['yeu_cau_xac_thuc'] = [
            'thoi_gian' => now()->toIso8601String(),
            'ly_do' => $validated['ly_do'] ?? 'Yêu cầu cấp huy hiệu xác thực tài khoản',
            'trang_thai' => 'cho_duyet',
        ];

        $user->cai_dat = $settings;
        $user->save();

        try {
            \App\Models\AdminAuditLog::create([
                'actor_id' => $user->id,
                'action' => 'user.verification_requested',
                'target_type' => \App\Models\User::class,
                'target_id' => (string) $user->id,
                'metadata' => [
                    'ho_ten' => $user->ho_ten,
                    'email' => $user->email,
                    'ly_do' => $validated['ly_do'] ?? 'Yêu cầu cấp huy hiệu xác thực tài khoản',
                ],
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        try {
            $user->notify(new \App\Notifications\UserRoleStatusUpdatedNotification(
                'verification_requested',
                'Yêu cầu xác thực tài khoản của bạn đã được gửi thành công và đang chờ ban quản trị kiểm duyệt.',
                $validated['ly_do'] ?? null,
                route('tai-khoan.index')
            ));
        } catch (\Throwable $e) {}

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu xác thực tài khoản của bạn đã được gửi đến Ban quản trị thành công!',
            ]);
        }

        return back()->with('success', 'Yêu cầu xác thực tài khoản của bạn đã được gửi đến Ban quản trị thành công!');
    }
}
