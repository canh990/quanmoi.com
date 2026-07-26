<?php

use App\Http\Controllers\Api\BanDoController;
use App\Http\Controllers\Api\DiaChiController;
use App\Http\Controllers\Auth\DangKyController;
use App\Http\Controllers\Auth\DangNhapController;
use App\Http\Controllers\ChuQuan\HinhAnhQuanController;
use App\Http\Controllers\ChuQuan\QuanController;
use App\Http\Controllers\NguoiDung\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

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
        Route::delete('/hinh-anh/{id}', [HinhAnhQuanController::class, 'destroy'])->name('chu-quan.hinh-anh.destroy');
    });
});

Route::prefix('api')->group(function () {
    Route::get('/dia-chi/tinh-thanh', [DiaChiController::class, 'getTinhThanh']);
    Route::get('/dia-chi/quan-huyen/{tinhCode}', [DiaChiController::class, 'getQuanHuyen']);
    Route::get('/dia-chi/phuong-xa/{huyenCode}', [DiaChiController::class, 'getPhuongXa']);
    Route::get('/ban-do/geocode', [BanDoController::class, 'geocode']);
});

Route::get('/quan/{slug}', [HomeController::class, 'show'])->name('quan.detail');
