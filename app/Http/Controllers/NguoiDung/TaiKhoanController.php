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

    public function update(CapNhatTaiKhoanRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        if ($request->hasFile('anh_dai_dien')) {
            // 1. Xóa ảnh cũ trên R2 (nếu có) trước khi upload ảnh mới
            if ($user->anh_dai_dien_key) {
                try {
                    Storage::disk('r2')->delete($user->anh_dai_dien_key);
                } catch (\Exception $e) {
                    // Ghi log nhưng không chặn việc upload ảnh mới
                    Log::warning('Không thể xóa ảnh cũ trên R2: ' . $e->getMessage(), [
                        'user_id' => $user->id,
                        'key'     => $user->anh_dai_dien_key,
                    ]);
                }
            }

            // 2. Upload ảnh mới lên R2 (convert sang WebP)
            // Tên file: avatars/{user_id}/{uuid}.webp
            $file     = $request->file('anh_dai_dien');
            $filename = Str::uuid() . '.webp';

            // Convert sang WebP với quality 80
            $manager = new ImageManager(new Driver());
            $image   = $manager->decode($file->getRealPath());
            $encoded = $image->encodeUsingFileExtension('webp', 80);

            $objectKey = 'avatars/' . $user->id . '/' . $filename;
            Storage::disk('r2')->put($objectKey, (string) $encoded);

            // 3. Lấy CDN URL qua disk (đọc từ config, không gọi env() trực tiếp)
            $validated['anh_dai_dien']     = Storage::disk('r2')->url($objectKey);
            $validated['anh_dai_dien_key'] = $objectKey;
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
