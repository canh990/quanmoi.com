<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AiToolsController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BaoMatController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogTagController;
use App\Http\Controllers\Admin\DuyetXacThucController;
use App\Http\Controllers\Admin\PhanQuyenController;
use App\Http\Controllers\Admin\QuanLyNguoiDungController;
use App\Http\Controllers\Admin\QuanLyQuanController;
use App\Http\Controllers\Admin\QuanNoiBatController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ThongBaoAdminController;
use App\Http\Controllers\Admin\ThongKeController;
use App\Http\Controllers\Admin\VaiTroController;
use Illuminate\Support\Facades\Route;

// Protected Group: Admin Auth & Admin Role Check
Route::middleware(['auth', 'KiemTraQuyenHan:admin'])->group(function () {

    // 1. Dashboard Main
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.alias');

    // 2. Thống kê
    Route::get('/thong-ke', [ThongKeController::class, 'index'])->name('thong-ke.index');
    Route::get('/thong-ke/export', [ThongKeController::class, 'export'])->name('thong-ke.export');

    // 3. Quản lý Người dùng
    Route::get('/nguoi-dung', [QuanLyNguoiDungController::class, 'index'])->name('nguoi-dung.index');
    Route::get('/nguoi-dung/{id}/edit', [QuanLyNguoiDungController::class, 'edit'])->name('nguoi-dung.edit');
    Route::put('/nguoi-dung/{id}', [QuanLyNguoiDungController::class, 'update'])->name('nguoi-dung.update');
    Route::delete('/nguoi-dung/{id}', [QuanLyNguoiDungController::class, 'destroy'])->name('nguoi-dung.destroy');
    Route::post('/nguoi-dung/{id}/restore', [QuanLyNguoiDungController::class, 'restore'])->name('nguoi-dung.restore');
    Route::delete('/nguoi-dung/{id}/force', [QuanLyNguoiDungController::class, 'forceDestroy'])->name('nguoi-dung.force-destroy');
    Route::put('/nguoi-dung/{id}/toggle-xac-thuc', [QuanLyNguoiDungController::class, 'toggleXacThuc'])->name('nguoi-dung.toggle-xac-thuc');

    // 4. Quản lý Vai trò
    Route::get('/vai-tro', [VaiTroController::class, 'index'])->name('vai-tro.index');
    Route::post('/vai-tro', [VaiTroController::class, 'store'])->name('vai-tro.store');
    Route::put('/vai-tro/{id}/permissions', [VaiTroController::class, 'updatePermissions'])->name('vai-tro.update-permissions');

    // 5. Quản lý & Override Phân quyền
    Route::get('/phan-quyen', [PhanQuyenController::class, 'index'])->name('phan-quyen.index');
    Route::post('/phan-quyen', [PhanQuyenController::class, 'store'])->name('phan-quyen.store');
    Route::post('/phan-quyen/override', [PhanQuyenController::class, 'overrideUserPermission'])->name('phan-quyen.override');

    // 6. Quản lý Quán
    Route::get('/quan', [QuanLyQuanController::class, 'index'])->name('quan.index');
    Route::get('/quan/{id}/edit', [QuanLyQuanController::class, 'edit'])->name('quan.edit');
    Route::put('/quan/{id}', [QuanLyQuanController::class, 'update'])->name('quan.update');
    Route::delete('/quan/{id}', [QuanLyQuanController::class, 'destroy'])->name('quan.destroy');
    Route::post('/quan/{id}/restore', [QuanLyQuanController::class, 'restore'])->name('quan.restore');
    Route::delete('/quan/{id}/force', [QuanLyQuanController::class, 'forceDestroy'])->name('quan.force-destroy');
    Route::put('/quan/{id}/toggle-noi-bat', [QuanLyQuanController::class, 'toggleNoiBat'])->name('quan.toggle-noi-bat');
    Route::put('/quan/{id}/toggle-xac-thuc', [QuanLyQuanController::class, 'toggleXacThuc'])->name('quan.toggle-xac-thuc');

    // 7. Duyệt xác thực (Quán / User)
    Route::get('/duyet-xac-thuc', [DuyetXacThucController::class, 'index'])->name('duyet-xac-thuc.index');
    Route::put('/duyet-xac-thuc/quan/{id}', [DuyetXacThucController::class, 'approveQuan'])->name('duyet-xac-thuc.quan');
    Route::put('/duyet-xac-thuc/user/{id}', [DuyetXacThucController::class, 'verifyUser'])->name('duyet-xac-thuc.user');

    // 8. Thông báo Hệ Thống
    Route::get('/thong-bao', [ThongBaoAdminController::class, 'index'])->name('thong-bao.index');
    Route::post('/thong-bao', [ThongBaoAdminController::class, 'sendSystemNotification'])->name('thong-bao.send');

    // 9. Bảo mật
    Route::get('/bao-mat', [BaoMatController::class, 'index'])->name('bao-mat.index');
    Route::put('/bao-mat', [BaoMatController::class, 'update'])->name('bao-mat.update');

    // 10. Audit Log
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

    // 11. Quán Nổi Bật
    Route::get('/quan-noi-bat', [QuanNoiBatController::class, 'index'])->name('quan-noi-bat.index');
    Route::put('/quan-noi-bat/{id}/toggle', [QuanNoiBatController::class, 'toggle'])->name('quan-noi-bat.toggle');

    // 12. AI Tools
    Route::get('/ai-tools', [AiToolsController::class, 'index'])->name('ai-tools.index');
    Route::post('/ai-tools/generate', [AiToolsController::class, 'generateSeoDescription'])->name('ai-tools.generate');

    // Quản lý SEO
    Route::get('/seo', [SeoController::class, 'index'])->name('seo.index');
    Route::get('/seo/{trang}/edit', [SeoController::class, 'edit'])->name('seo.edit');
    Route::put('/seo/{trang}', [SeoController::class, 'update'])->name('seo.update');

    // Quản lý Blog
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [BlogController::class, 'dashboard'])->name('dashboard');

        Route::prefix('bai-viet')->name('posts.')->group(function () {
            Route::get('/', [BlogController::class, 'index'])->name('index');
            Route::get('/cho-duyet', [BlogController::class, 'pending'])->name('pending');
            Route::get('/{blog}', [BlogController::class, 'show'])->name('show');
            Route::put('/{blog}/status', [BlogController::class, 'updateStatus'])->name('update-status');
            Route::put('/{blog}/hero', [BlogController::class, 'toggleHero'])->name('toggle-hero');
            Route::delete('/{blog}', [BlogController::class, 'destroy'])->name('destroy');
        });

        Route::get('/danh-muc', [BlogCategoryController::class, 'index'])->name('categories.index');
        Route::post('/danh-muc', [BlogCategoryController::class, 'store'])->name('categories.store');

        Route::get('/the', [BlogTagController::class, 'index'])->name('tags.index');
        Route::post('/the', [BlogTagController::class, 'store'])->name('tags.store');
    });
});
