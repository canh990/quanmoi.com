<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapNhatTaiKhoanRequest;
use App\Http\Requests\DoiMatKhauRequest;
use Illuminate\Support\Facades\Auth;
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
        return view('nguoi-dung.tai-khoan.index', compact('user'));
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
}
