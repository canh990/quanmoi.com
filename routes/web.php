<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\DangKyController;
use App\Http\Controllers\Auth\DangNhapController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dang-ky', [DangKyController::class, 'showRegistrationForm'])->name('register');
Route::post('/dang-ky', [DangKyController::class, 'register'])->name('register.submit');
Route::post('/dang-ky/xac-thuc', [DangKyController::class, 'verifyOtp'])->name('register.verify');
Route::post('/dang-ky/gui-lai', [DangKyController::class, 'resendOtp'])->name('register.resend');

Route::get('/dang-nhap', [DangNhapController::class, 'showLoginForm'])->name('login');
Route::post('/dang-nhap', [DangNhapController::class, 'login'])->name('login.submit');
Route::post('/dang-xuat', [DangNhapController::class, 'logout'])->name('logout');

use App\Http\Controllers\ChuQuan\QuanController;
use App\Http\Controllers\ChuQuan\HinhAnhQuanController;
use App\Http\Controllers\Api\DiaChiController;
use App\Http\Controllers\Api\BanDoController;

// 301 Permanent Redirects for SEO & Backward Compatibility
Route::redirect('/dangnhap', '/dang-nhap', 301);
Route::redirect('/dangky', '/dang-ky', 301);
Route::redirect('/dangxuat', '/dang-xuat', 301);

// Route Chủ Quán (Owner Venue Management)
Route::prefix('chu-quan')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/dang-quan', [QuanController::class, 'create'])->name('chu-quan.dang-quan');
        Route::post('/dang-quan', [QuanController::class, 'store'])->name('chu-quan.quan.store');
    });
    Route::get('/quan/{slug}', [QuanController::class, 'show'])->name('chu-quan.quan.show');
    Route::post('/quan/{quanId}/hinh-anh', [HinhAnhQuanController::class, 'store'])->name('chu-quan.hinh-anh.store');
    Route::delete('/hinh-anh/{id}', [HinhAnhQuanController::class, 'destroy'])->name('chu-quan.hinh-anh.destroy');
});

// Proxy API Chữa lỗi CORS cho Tỉnh Thành / Bản Đồ
Route::prefix('api')->group(function () {
    Route::get('/dia-chi/tinh-thanh', [DiaChiController::class, 'getTinhThanh']);
    Route::get('/dia-chi/quan-huyen/{tinhCode}', [DiaChiController::class, 'getQuanHuyen']);
    Route::get('/dia-chi/phuong-xa/{huyenCode}', [DiaChiController::class, 'getPhuongXa']);
    Route::get('/ban-do/geocode', [BanDoController::class, 'geocode']);
});

// SEO Friendly Restaurant Detail Route
Route::get('/quan/{slug}', function ($slug) {
    return view('nguoi-dung.chi-tiet');
})->name('quan.detail');
