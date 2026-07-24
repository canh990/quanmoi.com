<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class QuenMatKhauController extends Controller
{
    /**
     * Hiển thị form nhập email quên mật khẩu
     */
    public function showForgotForm()
    {
        return view('auth.quen-mat-khau');
    }

    /**
     * Gửi link đặt lại mật khẩu qua email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email'    => 'Email không đúng định dạng.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản với email này.',
            ], 404);
        }

        // Xoá token cũ nếu có
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Tạo token mới (random 64 ký tự)
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Gửi email chứa link đặt lại
        $resetUrl = url('/dat-lai-mat-khau/' . $token . '?email=' . urlencode($request->email));

        try {
            Mail::send('emails.reset-password', [
                'user'     => $user,
                'resetUrl' => $resetUrl,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->ho_ten)
                        ->subject('Đặt lại mật khẩu — Quán Mới');
            });
        } catch (\Exception $e) {
            // Log lỗi nhưng không trả lỗi về client để tránh lộ thông tin
            \Log::error('Lỗi gửi email đặt lại mật khẩu: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Chúng tôi đã gửi link đặt lại mật khẩu đến email của bạn. Kiểm tra hộp thư (kể cả thư mục Spam).',
        ]);
    }

    /**
     * Hiển thị form đặt lại mật khẩu (truy cập từ link email)
     */
    public function showResetForm(Request $request, string $token)
    {
        $email = $request->query('email');

        return view('auth.dat-lai-mat-khau', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Xử lý đặt lại mật khẩu mới
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required|string',
            'mat_khau'              => 'required|string|min:6',
            'mat_khau_xac_nhan'     => 'required|same:mat_khau',
        ], [
            'email.required'              => 'Email là bắt buộc.',
            'mat_khau.required'           => 'Vui lòng nhập mật khẩu mới.',
            'mat_khau.min'                => 'Mật khẩu phải ít nhất 6 ký tự.',
            'mat_khau_xac_nhan.required'  => 'Vui lòng xác nhận mật khẩu.',
            'mat_khau_xac_nhan.same'      => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Tìm token trong DB
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
            ], 400);
        }

        // Kiểm tra token khớp
        if (!Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Link đặt lại mật khẩu không hợp lệ.',
            ], 400);
        }

        // Kiểm tra token hết hạn (60 phút)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Link đặt lại mật khẩu đã hết hạn (sau 60 phút). Vui lòng yêu cầu lại.',
            ], 400);
        }

        // Cập nhật mật khẩu mới
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Tài khoản không tồn tại.'], 404);
        }

        $user->update(['mat_khau' => Hash::make($request->mat_khau)]);

        // Xoá token đã dùng
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mật khẩu đã được đặt lại thành công! Đang chuyển bạn đến trang đăng nhập...',
            'redirect_to' => '/dang-nhap',
        ]);
    }
}
