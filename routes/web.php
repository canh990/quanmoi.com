<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\DangKyController;
use App\Http\Controllers\Auth\DangNhapController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dangky', [DangKyController::class, 'showRegistrationForm'])->name('register');
Route::post('/dangky', [DangKyController::class, 'register'])->name('register.submit');
Route::post('/dangky/xac-thuc', [DangKyController::class, 'verifyOtp'])->name('register.verify');
Route::post('/dangky/gui-lai', [DangKyController::class, 'resendOtp'])->name('register.resend');

Route::get('/dangnhap', [DangNhapController::class, 'showLoginForm'])->name('login');
Route::post('/dangnhap', [DangNhapController::class, 'login'])->name('login.submit');
Route::post('/dangxuat', [DangNhapController::class, 'logout'])->name('logout');

Route::get('/quan/{id}', function ($id) {
    return view('nguoi-dung.chi-tiet');
})->name('quan.detail');
