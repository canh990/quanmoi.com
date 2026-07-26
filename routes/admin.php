<?php

use App\Http\Controllers\Admin\QuanLyNguoiDungController;
use App\Http\Controllers\Admin\QuanLyQuanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'KiemTraQuyenHan:admin'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.nguoi-dung.index');
    })->name('dashboard');

    // Quản lý người dùng CRUD & Restore
    Route::get('/nguoi-dung', [QuanLyNguoiDungController::class, 'index'])->name('nguoi-dung.index');
    Route::get('/nguoi-dung/{id}/edit', [QuanLyNguoiDungController::class, 'edit'])->name('nguoi-dung.edit');
    Route::put('/nguoi-dung/{id}', [QuanLyNguoiDungController::class, 'update'])->name('nguoi-dung.update');
    Route::delete('/nguoi-dung/{id}', [QuanLyNguoiDungController::class, 'destroy'])->name('nguoi-dung.destroy');
    Route::post('/nguoi-dung/{id}/restore', [QuanLyNguoiDungController::class, 'restore'])->name('nguoi-dung.restore');
    Route::delete('/nguoi-dung/{id}/force', [QuanLyNguoiDungController::class, 'forceDestroy'])->name('nguoi-dung.force-destroy');

    // Quản lý quán CRUD & Restore & Approve
    Route::get('/quan', [QuanLyQuanController::class, 'index'])->name('quan.index');
    Route::get('/quan/{id}/edit', [QuanLyQuanController::class, 'edit'])->name('quan.edit');
    Route::put('/quan/{id}', [QuanLyQuanController::class, 'update'])->name('quan.update');
    Route::delete('/quan/{id}', [QuanLyQuanController::class, 'destroy'])->name('quan.destroy');
    Route::post('/quan/{id}/restore', [QuanLyQuanController::class, 'restore'])->name('quan.restore');
    Route::delete('/quan/{id}/force', [QuanLyQuanController::class, 'forceDestroy'])->name('quan.force-destroy');
});
