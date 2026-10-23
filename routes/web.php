<?php

use App\Http\Controllers\Api\AiFoodSuggestionController;
use App\Http\Controllers\Api\BanDoController;
use App\Http\Controllers\Api\DiaChiController;
use App\Http\Controllers\Auth\DangKyController;
use App\Http\Controllers\Auth\DangNhapController;
use App\Http\Controllers\Auth\QuenMatKhauController;
use App\Http\Controllers\ChuQuan\HinhAnhQuanController;
use App\Http\Controllers\ChuQuan\QuanController;
use App\Http\Controllers\NguoiDung\HomeController;
use App\Http\Controllers\NguoiDung\TaiKhoanController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Frontend\BlogController as FrontendBlogController;
use App\Http\Controllers\NguoiDung\BlogController as UserBlogController;
use App\Http\Controllers\NguoiDung\BanDoController as WebBanDoController;
use App\Http\Controllers\NguoiDung\KhamPhaController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kham-pha', [KhamPhaController::class, 'index'])->name('kham-pha');
Route::get('/ban-do', [WebBanDoController::class, 'index'])->name('ban-do');
Route::get('/danh-muc/{slug}', [KhamPhaController::class, 'danhMuc'])->name('danh-muc');
Route::get('/quan-moi', [KhamPhaController::class, 'quanMoi'])->name('quan-moi');
Route::get('/quan-noi-bat', [KhamPhaController::class, 'quanNoiBat'])->name('quan-noi-bat');
Route::get('/blog', [FrontendBlogController::class, 'index'])->name('blog.index');
Route::post('/blog/dang-ky-nhan-tin', [FrontendBlogController::class, 'subscribeNewsletter'])->middleware('throttle:15,1')->name('blog.newsletter.subscribe');
Route::get('/blog/tac-gia/{id}', [FrontendBlogController::class, 'author'])->name('blog.author');
Route::get('/blog/{slug}', [FrontendBlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{slug}/luot-xem', [FrontendBlogController::class, 'recordView'])->middleware('throttle:60,1')->name('blog.view.record');
Route::post('/blog/{slug}/binh-luan', [FrontendBlogController::class, 'storeComment'])->middleware(['auth', 'throttle:30,1'])->name('blog.comment.store');
Route::delete('/blog/{slug}/binh-luan/{commentId}', [FrontendBlogController::class, 'deleteComment'])->middleware('auth')->name('blog.comment.destroy');
Route::post('/blog/{slug}/binh-luan/{commentId}/thich', [FrontendBlogController::class, 'toggleLikeComment'])->middleware('auth')->name('blog.comment.like');
Route::post('/blog/{slug}/ai-tom-tat', [FrontendBlogController::class, 'summarize'])->middleware('throttle:15,1')->name('blog.ai.summarize');
Route::get('/api/blog/ai-search', [FrontendBlogController::class, 'aiSearch'])->middleware('throttle:30,1')->name('blog.ai.search');
Route::post('/api/blog/ai-chat', [FrontendBlogController::class, 'aiChat'])->middleware('throttle:15,1')->name('blog.ai.chat');
Route::get('/api/blog/recommendations', [FrontendBlogController::class, 'recommendations'])->middleware('throttle:30,1')->name('blog.ai.recommendations');
Route::get('/video-review', [\App\Http\Controllers\NguoiDung\VideoShortController::class, 'index'])->name('video-review.index');
Route::view('/gioi-thieu', 'pages.about')->name('about');
Route::get('/media/venue/{path}', [HinhAnhQuanController::class, 'localImage'])
    ->where('path', '.*')
    ->name('media.venue.local');

// ─── Auth routes (chỉ dành cho khách chưa đăng nhập) ──────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/dang-ky', [DangKyController::class, 'showRegistrationForm'])->name('register');
    Route::get('/dang-nhap', [DangNhapController::class, 'showLoginForm'])->name('login');
    Route::get('/dang-nhap/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/quen-mat-khau', [QuenMatKhauController::class, 'showForgotForm'])->name('password.request');
    Route::get('/dat-lai-mat-khau/{token}', [QuenMatKhauController::class, 'showResetForm'])->name('password.reset');
});

// POST routes không cần middleware guest (AJAX / form submit)
Route::post('/dang-ky', [DangKyController::class, 'register'])->middleware('throttle:register')->name('register.submit');
Route::post('/dang-ky/xac-thuc', [DangKyController::class, 'verifyOtp'])->middleware('throttle:otp-verify')->name('register.verify');
Route::post('/dang-ky/gui-lai', [DangKyController::class, 'resendOtp'])->middleware('throttle:otp-send')->name('register.resend');
Route::post('/dang-nhap', [DangNhapController::class, 'login'])->middleware('throttle:login')->name('login.submit');
Route::post('/dang-xuat', [DangNhapController::class, 'logout'])->name('logout');
Route::get('/dang-nhap/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('login.google.callback');
Route::post('/quen-mat-khau', [QuenMatKhauController::class, 'sendResetLink'])->middleware('throttle:password-email')->name('password.email');
Route::post('/dat-lai-mat-khau', [QuenMatKhauController::class, 'resetPassword'])->middleware('throttle:password-email')->name('password.update');

Route::redirect('/dangnhap', '/dang-nhap', 301);
Route::redirect('/dangky', '/dang-ky', 301);
Route::redirect('/dangxuat', '/dang-xuat', 301);

Route::prefix('chu-quan')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/dang-quan', [QuanController::class, 'create'])->name('chu-quan.dang-quan');
        Route::post('/dang-quan', [QuanController::class, 'store'])->middleware('throttle:venue-submission')->name('chu-quan.quan.store');
    });

    Route::middleware(['auth', 'KiemTraQuyenHan:chu_quan'])->group(function () {
        Route::get('/quan', [QuanController::class, 'ownerIndex'])->name('chu-quan.quan.index');
        Route::get('/quan/{slug}', [QuanController::class, 'show'])->name('chu-quan.quan.show');
        Route::put('/quan/{slug}/danh-gia/{reviewId}/phan-hoi', [\App\Http\Controllers\ChuQuan\QuanDanhGiaController::class, 'reply'])
            ->name('chu-quan.quan.danh-gia.reply');
        Route::post('/quan/{quanId}/hinh-anh', [HinhAnhQuanController::class, 'store'])->name('chu-quan.hinh-anh.store');
        Route::delete('/hinh-anh/{id}', [HinhAnhQuanController::class, 'destroy'])->name('chu-quan.hinh-anh.destroy');
        Route::get('/quan/{slug}/thuc-don', [\App\Http\Controllers\ChuQuan\MenuController::class, 'edit'])->name('chu-quan.quan.menu.edit');
        Route::post('/quan/{slug}/thuc-don', [\App\Http\Controllers\ChuQuan\MenuController::class, 'update'])->name('chu-quan.quan.menu.update');
        Route::put('/quan/{slug}', [QuanController::class, 'update'])->name('chu-quan.quan.update');

        // Quản lý Khuyến mãi & AI Tạo Nội Dung (Kira AI)
        Route::post('/quan/{slug}/khuyen-mai/ai-generate', [\App\Http\Controllers\ChuQuan\KhuyenMaiController::class, 'generateAi'])
            ->middleware('throttle:15,1')
            ->name('chu-quan.quan.khuyen-mai.ai-generate');
        Route::post('/quan/{slug}/khuyen-mai', [\App\Http\Controllers\ChuQuan\KhuyenMaiController::class, 'store'])
            ->name('chu-quan.quan.khuyen-mai.store');
        Route::put('/quan/{slug}/khuyen-mai/{id}/toggle', [\App\Http\Controllers\ChuQuan\KhuyenMaiController::class, 'toggle'])
            ->name('chu-quan.quan.khuyen-mai.toggle');
        Route::delete('/quan/{slug}/khuyen-mai/{id}', [\App\Http\Controllers\ChuQuan\KhuyenMaiController::class, 'destroy'])
            ->name('chu-quan.quan.khuyen-mai.destroy');
        Route::post('/quan/{slug}/khuyen-mai/kiem-tra', [\App\Http\Controllers\ChuQuan\KhuyenMaiController::class, 'kiemTraVoucher'])
            ->name('chu-quan.quan.khuyen-mai.kiem-tra');
    });
});

Route::prefix('api')->group(function () {
    Route::get('/dia-chi/tinh-thanh', [DiaChiController::class, 'getTinhThanh']);
    Route::get('/dia-chi/quan-huyen/{tinhCode}', [DiaChiController::class, 'getQuanHuyen']);
    Route::get('/dia-chi/phuong-xa/{huyenCode}', [DiaChiController::class, 'getPhuongXa']);
    
    // Giới hạn 20 request/phút để chống spam bào tiền API Google Maps
    Route::get('/ban-do/geocode', [BanDoController::class, 'geocode'])->middleware('throttle:20,1');

    // API quét quán ăn lân cận từ Database Quán Mới (Giới hạn 30 request/phút chống spam)
    Route::get('/ban-do/quan-lan-can', [BanDoController::class, 'quanLanCan'])
        ->middleware('throttle:30,1')
        ->name('api.ban-do.quan-lan-can');
    // Trợ lý AI Chatbot ẩm thực Quán Mới
    Route::post('/ai-chatbot/chat', [\App\Http\Controllers\Api\AiChatbotController::class, 'chat'])
        ->middleware('throttle:30,1')
        ->name('api.ai-chatbot.chat');
    Route::get('/ai-chatbot/suggestions', [\App\Http\Controllers\Api\AiChatbotController::class, 'suggestions'])
        ->name('api.ai-chatbot.suggestions');

    // AI Gợi ý món ăn thông minh theo ngữ cảnh thời gian (không cần đăng nhập)
    Route::get('/ai-food-suggestions', [\App\Http\Controllers\Api\AiFoodSuggestionController::class, 'suggest'])
        ->middleware('throttle:60,1')
        ->name('api.ai-food-suggestions');
});

Route::middleware('auth')->group(function () {
    Route::get('/tai-khoan', [TaiKhoanController::class, 'index'])->name('tai-khoan.index');
    Route::get('/tai-khoan/cai-dat', [TaiKhoanController::class, 'settings'])->name('tai-khoan.cai-dat');
    Route::put('/tai-khoan/cai-dat', [TaiKhoanController::class, 'updateSettings'])->name('tai-khoan.cai-dat.update');
    Route::post('/tai-khoan/yeu-cau-xac-thuc', [TaiKhoanController::class, 'requestVerification'])->name('tai-khoan.yeu-cau-xac-thuc');
    Route::get('/cai-dat', fn () => redirect()->route('tai-khoan.cai-dat'))->name('cai-dat');
    Route::get('/quan-da-luu', [\App\Http\Controllers\NguoiDung\QuanDaLuuController::class, 'index'])->name('quan-da-luu.index');
    Route::post('/quan-da-luu/{quanId}/toggle', [\App\Http\Controllers\NguoiDung\QuanDaLuuController::class, 'toggle'])->name('quan-da-luu.toggle');
    Route::put('/tai-khoan', [TaiKhoanController::class, 'update'])->name('tai-khoan.update');
    Route::put('/tai-khoan/mat-khau', [TaiKhoanController::class, 'updatePassword'])->name('tai-khoan.update-password');
    Route::post('/quan/{slug}/danh-gia', [\App\Http\Controllers\NguoiDung\QuanDanhGiaController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('quan.danh-gia.store');

    Route::prefix('ai/blog')->middleware('throttle:20,1')->name('ai.blog.')->group(function () {
        Route::post('/suggest-titles', [\App\Http\Controllers\NguoiDung\BlogAiController::class, 'suggestTitles'])->name('suggest-titles');
        Route::post('/generate-outline', [\App\Http\Controllers\NguoiDung\BlogAiController::class, 'generateOutline'])->name('generate-outline');
        Route::post('/fix-spelling', [\App\Http\Controllers\NguoiDung\BlogAiController::class, 'fixSpelling'])->name('fix-spelling');
        Route::post('/suggest-tags-category', [\App\Http\Controllers\NguoiDung\BlogAiController::class, 'suggestTagsCategory'])->name('suggest-tags-category');
    });

    Route::prefix('thanh-vien/blog')->name('nguoi-dung.blog.')->group(function () {
        Route::get('/', [UserBlogController::class, 'index'])->name('index');
        Route::get('/tao-moi', [UserBlogController::class, 'create'])->name('create');
        Route::post('/', [UserBlogController::class, 'store'])->name('store');
        Route::get('/{blog}/sua', [UserBlogController::class, 'edit'])->name('edit');
        Route::put('/{blog}', [UserBlogController::class, 'update'])->name('update');
        Route::delete('/{blog}', [UserBlogController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('thanh-vien/thong-bao')->name('nguoi-dung.thong-bao.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NguoiDung\ThongBaoController::class, 'index'])->name('index');
        Route::get('/realtime-check', [\App\Http\Controllers\NguoiDung\ThongBaoController::class, 'realtimeCheck'])->name('realtime-check');
        Route::get('/{id}/doc-va-chuyen', [\App\Http\Controllers\NguoiDung\ThongBaoController::class, 'readAndRedirect'])->name('read-and-redirect');
        Route::post('/{id}/danh-dau-da-doc', [\App\Http\Controllers\NguoiDung\ThongBaoController::class, 'markAsRead'])->name('mark-read');
        Route::post('/danh-dau-tat-ca-da-doc', [\App\Http\Controllers\NguoiDung\ThongBaoController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [\App\Http\Controllers\NguoiDung\ThongBaoController::class, 'destroy'])->name('destroy');
    });
});

Route::get('/quan/{slug}', [HomeController::class, 'show'])->name('quan.detail');

// Tự động chuyển hướng nếu người dùng truy cập /{slug} trực tiếp về /quan/{slug}
Route::get('/{slug}', function (string $slug) {
    if (\App\Models\Quan::where('slug', $slug)->exists()) {
        return redirect()->route('quan.detail', ['slug' => $slug]);
    }
    abort(404);
})->where('slug', '^(?!admin$|api$|chu-quan$|thanh-vien$)[a-zA-Z0-9\-]+$');

