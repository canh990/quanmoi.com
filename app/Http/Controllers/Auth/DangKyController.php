<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DangKyController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.dangky');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'mat_khau' => 'required|string|min:6',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không đúng định dạng.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu.',
            'mat_khau.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;

        // Check if user exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'errors' => ['email' => ['Email này đã được đăng ký tài khoản.']]
            ], 422);
        }

        // Look up default role
        $vaiTroNguoiDung = DB::table('vai_tro')->where('ten', 'nguoi_dung')->first();

        // Create verified user directly
        $user = User::create([
            'ho_ten' => $request->ho_ten,
            'email' => $email,
            'mat_khau' => Hash::make($request->mat_khau),
            'da_xac_thuc' => true,
            'ngay_xac_thuc' => now(),
            'vai_tro_id' => $vaiTroNguoiDung?->id,
            'trang_thai' => 'hoat_dong',
        ]);

        // Auto log in user
        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký tài khoản thành công!',
            'redirect_to' => '/'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $email = $request->email;
        $otpCode = $request->otp;

        // Find OTP record
        $otpRecord = DB::table('xac_thuc_otp')
            ->where('email', $email)
            ->where('otp', $otpCode)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Mã OTP không chính xác.'
            ], 400);
        }

        if (now()->gt($otpRecord->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Mã OTP đã hết hạn.'
            ], 400);
        }

        // Find and activate user
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản.'
            ], 404);
        }

        $user->da_xac_thuc = true;
        $user->ngay_xac_thuc = now();
        $user->save();

        // Delete OTP record
        DB::table('xac_thuc_otp')->where('email', $email)->delete();

        // Log user in
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Xác thực tài khoản thành công!'
        ]);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        // Check if user is registered and unverified
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng ký không hợp lệ.'
            ], 404);
        }

        if ($user->da_xac_thuc) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản đã được xác thực.'
            ], 400);
        }

        // Generate and save new OTP
        $otp = (string) rand(100000, 999999);
        
        DB::table('xac_thuc_otp')->where('email', $email)->delete();

        DB::table('xac_thuc_otp')->insert([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(3),
            'created_at' => now(),
        ]);

        Log::info("=== GỬI LẠI MÃ OTP ĐĂNG KÝ CHO {$email} ===");
        Log::info("OTP: {$otp}");
        Log::info("===========================================");

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi lại mã OTP mới.'
        ]);
    }
}
