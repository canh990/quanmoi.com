<?php

use App\Http\Controllers\Api\BanDoController;
use App\Http\Controllers\Api\DiaChiController;
use App\Http\Controllers\Auth\DangKyController;
use App\Http\Controllers\Auth\DangNhapController;
use App\Http\Controllers\ChuQuan\HinhAnhQuanController;
use App\Http\Controllers\ChuQuan\QuanController;
use App\Http\Controllers\NguoiDung\HomeController;
use App\Http\Controllers\NguoiDung\TaiKhoanController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NguoiDung\BlogController;
use App\Http\Controllers\NguoiDung\KhamPhaController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kham-pha', [KhamPhaController::class, 'index'])->name('kham-pha');
Route::get('/quan-moi', [KhamPhaController::class, 'quanMoi'])->name('quan-moi');
Route::get('/quan-noi-bat', [KhamPhaController::class, 'quanNoiBat'])->name('quan-noi-bat');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::view('/gioi-thieu', 'pages.about')->name('about');

Route::get('/dang-ky', [DangKyController::class, 'showRegistrationForm'])->name('register');
Route::post('/dang-ky', [DangKyController::class, 'register'])->name('register.submit');
Route::post('/dang-ky/xac-thuc', [DangKyController::class, 'verifyOtp'])->name('register.verify');
Route::post('/dang-ky/gui-lai', [DangKyController::class, 'resendOtp'])->name('register.resend');

Route::get('/dang-nhap', [DangNhapController::class, 'showLoginForm'])->name('login');
Route::post('/dang-nhap', [DangNhapController::class, 'login'])->name('login.submit');
Route::post('/dang-xuat', [DangNhapController::class, 'logout'])->name('logout');

Route::get('/dang-nhap/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/dang-nhap/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('login.google.callback');

Route::redirect('/dangnhap', '/dang-nhap', 301);
Route::redirect('/dangky', '/dang-ky', 301);
Route::redirect('/dangxuat', '/dang-xuat', 301);

Route::prefix('chu-quan')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/dang-quan', [QuanController::class, 'create'])->name('chu-quan.dang-quan');
        Route::post('/dang-quan', [QuanController::class, 'store'])->name('chu-quan.quan.store');
        Route::get('/quan', [QuanController::class, 'ownerIndex'])->name('chu-quan.quan.index');
        Route::get('/quan/{slug}', [QuanController::class, 'show'])->name('chu-quan.quan.show');
        Route::post('/quan/{quanId}/hinh-anh', [HinhAnhQuanController::class, 'store'])->name('chu-quan.hinh-anh.store');
        Route::get('/quan/{slug}/thuc-don', [\App\Http\Controllers\ChuQuan\MenuController::class, 'edit'])->name('chu-quan.quan.menu.edit');
        Route::post('/quan/{slug}/thuc-don', [\App\Http\Controllers\ChuQuan\MenuController::class, 'update'])->name('chu-quan.quan.menu.update');
    });
});

Route::prefix('api')->group(function () {
    Route::get('/dia-chi/tinh-thanh', [DiaChiController::class, 'getTinhThanh']);
    Route::get('/dia-chi/quan-huyen/{tinhCode}', [DiaChiController::class, 'getQuanHuyen']);
    Route::get('/dia-chi/phuong-xa/{huyenCode}', [DiaChiController::class, 'getPhuongXa']);
    Route::get('/ban-do/geocode', [BanDoController::class, 'geocode']);
});

Route::middleware('auth')->group(function () {
    Route::get('/tai-khoan', [TaiKhoanController::class, 'index'])->name('tai-khoan.index');
    Route::get('/quan-da-luu', [\App\Http\Controllers\NguoiDung\QuanDaLuuController::class, 'index'])->name('quan-da-luu.index');
    Route::post('/quan-da-luu/{quanId}/toggle', [\App\Http\Controllers\NguoiDung\QuanDaLuuController::class, 'toggle'])->name('quan-da-luu.toggle');
    Route::put('/tai-khoan', [TaiKhoanController::class, 'update'])->name('tai-khoan.update');
    Route::put('/tai-khoan/mat-khau', [TaiKhoanController::class, 'updatePassword'])->name('tai-khoan.update-password');
});

Route::get('/quan/{slug}', [HomeController::class, 'show'])->name('quan.detail');
