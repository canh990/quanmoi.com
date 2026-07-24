<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DoiMatKhauController extends Controller
{
    /**
     * Hiển thị form đổi mật khẩu
     */
    public function showChangeForm()
    {
        return view('auth.doi-mat-khau');
    }

    /**
     * Xử lý đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'mat_khau_hien_tai'     => 'required|string',
            'mat_khau_moi'          => 'required|string|min:6',
            'mat_khau_xac_nhan'     => 'required|same:mat_khau_moi',
        ], [
            'mat_khau_hien_tai.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'mat_khau_moi.required'      => 'Vui lòng nhập mật khẩu mới.',
            'mat_khau_moi.min'           => 'Mật khẩu mới phải ít nhất 6 ký tự.',
            'mat_khau_xac_nhan.required' => 'Vui lòng xác nhận mật khẩu mới.',
            'mat_khau_xac_nhan.same'     => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->mat_khau_hien_tai, $user->mat_khau)) {
            return response()->json([
                'success' => false,
                'errors'  => ['mat_khau_hien_tai' => ['Mật khẩu hiện tại không đúng.']],
            ], 422);
        }

        // Kiểm tra không trùng mật khẩu cũ
        if (Hash::check($request->mat_khau_moi, $user->mat_khau)) {
            return response()->json([
                'success' => false,
                'errors'  => ['mat_khau_moi' => ['Mật khẩu mới phải khác mật khẩu hiện tại.']],
            ], 422);
        }

        // Cập nhật mật khẩu mới
        $user->update(['mat_khau' => Hash::make($request->mat_khau_moi)]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!',
        ]);
    }
}
