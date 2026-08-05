<?php

use App\Http\Controllers\Admin\QuanLyNguoiDungController;
use App\Http\Controllers\Admin\QuanLyQuanController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogTagController;
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
    Route::put('/nguoi-dung/{id}/toggle-xac-thuc', [QuanLyNguoiDungController::class, 'toggleXacThuc'])->name('nguoi-dung.toggle-xac-thuc');

    // Quản lý quán CRUD & Restore & Approve
    Route::get('/quan', [QuanLyQuanController::class, 'index'])->name('quan.index');
    Route::get('/quan/{id}/edit', [QuanLyQuanController::class, 'edit'])->name('quan.edit');
    Route::put('/quan/{id}', [QuanLyQuanController::class, 'update'])->name('quan.update');
    Route::delete('/quan/{id}', [QuanLyQuanController::class, 'destroy'])->name('quan.destroy');
    Route::post('/quan/{id}/restore', [QuanLyQuanController::class, 'restore'])->name('quan.restore');
    Route::delete('/quan/{id}/force', [QuanLyQuanController::class, 'forceDestroy'])->name('quan.force-destroy');
    Route::put('/quan/{id}/toggle-noi-bat', [QuanLyQuanController::class, 'toggleNoiBat'])->name('quan.toggle-noi-bat');
    Route::put('/quan/{id}/toggle-xac-thuc', [QuanLyQuanController::class, 'toggleXacThuc'])->name('quan.toggle-xac-thuc');

    // Quản lý SEO
    Route::get('/seo', [SeoController::class, 'index'])->name('seo.index');
    Route::get('/seo/{trang}/edit', [SeoController::class, 'edit'])->name('seo.edit');
    Route::put('/seo/{trang}', [SeoController::class, 'update'])->name('seo.update');
    
    // Quản lý Blog
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [BlogController::class, 'dashboard'])->name('dashboard');
        
        // Bài viết
        Route::prefix('bai-viet')->name('posts.')->group(function () {
            Route::get('/', [BlogController::class, 'index'])->name('index');
            Route::get('/cho-duyet', [BlogController::class, 'pending'])->name('pending');
            Route::get('/{blog}', [BlogController::class, 'show'])->name('show');
            Route::put('/{blog}/status', [BlogController::class, 'updateStatus'])->name('update-status');
            Route::put('/{blog}/hero', [BlogController::class, 'toggleHero'])->name('toggle-hero');
            Route::delete('/{blog}', [BlogController::class, 'destroy'])->name('destroy');
        });
        
        // Danh mục
        Route::get('/danh-muc', [BlogCategoryController::class, 'index'])->name('categories.index');
        Route::post('/danh-muc', [BlogCategoryController::class, 'store'])->name('categories.store');
        
        // Thẻ
        Route::get('/the', [BlogTagController::class, 'index'])->name('tags.index');
        Route::post('/the', [BlogTagController::class, 'store'])->name('tags.store');
        
        // Bình luận
        Route::get('/binh-luan', function() { return "Coming soon"; })->name('comments.index');
        
        // Thống kê
        Route::get('/thong-ke', function() { return "Coming soon"; })->name('stats.index');
    });
});
