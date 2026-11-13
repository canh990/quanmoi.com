<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\Quan;
use App\Models\User;
use App\Policies\BlogPolicy;
use App\Policies\NguoiDungPolicy;
use App\Policies\QuanPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Elastic\ScoutDriver\Factories\SearchParametersFactoryInterface::class,
            \App\Search\CustomSearchParametersFactory::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bỏ qua lỗi E_WARNING đặc thù của Docker trên Windows (NTFS volume) khi touch() view cache
        set_error_handler(function (int $errno, string $errstr): bool {
            if (str_contains($errstr, 'Utime failed')) {
                return true;
            }
            return false;
        }, E_WARNING);

        if (! $this->app->runningInConsole() && (request()->header('x-forwarded-proto') === 'https' || request()->isSecure())) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        \Carbon\Carbon::setLocale('vi');
        app()->setLocale('vi');

        Schema::defaultStringLength(191);

        Gate::before(function ($user, $ability) {
            if ($user && method_exists($user, 'hasPermissionTo')) {
                if (method_exists($user, 'hasPermissionExplicitlyDenied') && $user->hasPermissionExplicitlyDenied($ability)) {
                    return false;
                }
                if ($user->hasPermissionTo($ability)) {
                    return true;
                }
            }
        });

        Gate::policy(User::class, NguoiDungPolicy::class);
        Gate::policy(Quan::class, QuanPolicy::class);
        Gate::policy(Blog::class, BlogPolicy::class);

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($this->rateLimitKey($request)));
        RateLimiter::for('register', fn (Request $request) => Limit::perMinutes(10, 3)->by($this->rateLimitKey($request)));
        RateLimiter::for('otp-send', fn (Request $request) => Limit::perMinutes(10, 3)->by($this->rateLimitKey($request)));
        RateLimiter::for('otp-verify', fn (Request $request) => Limit::perMinute(5)->by($this->rateLimitKey($request)));
        RateLimiter::for('password-email', fn (Request $request) => Limit::perMinutes(10, 3)->by($this->rateLimitKey($request)));
        RateLimiter::for('venue-submission', fn (Request $request) => Limit::perDay(10)->by($this->rateLimitKey($request)));

        View::composer('layouts.navigation', function ($view): void {
            $currentUser = Auth::user();
            $isOwnerNav = false;
            $ownerNavLabel = 'Đăng quán';
            $ownerNavIcon = 'add_location_alt';
            $ownerNavUrl = route('chu-quan.dang-quan');
            $navIsAdmin = false;
            $navUnreadCount = 0;
            $navNotifications = collect();

            if ($currentUser) {
                if ($currentUser->isAdmin()) {
                    $navIsAdmin = true;
                    $isOwnerNav = true;
                    $ownerNavLabel = 'Trang quản trị';
                    $ownerNavIcon = 'admin_panel_settings';
                    $ownerNavUrl = Route::has('admin.quan.index')
                        ? route('admin.quan.index')
                        : route('chu-quan.dang-quan');

                    try {
                        $adminNotifData = \App\Http\Controllers\Admin\ThongBaoAdminController::buildRealtimeNotificationData($currentUser);
                        $navUnreadCount = $adminNotifData['unread_count'] ?? 0;
                        $navNotifications = $adminNotifData['all_notifications'] ?? [];
                    } catch (\Throwable $e) {
                        $navUnreadCount = 0;
                        $navNotifications = [];
                    }
                } else {
                    $isOwnerRole = $currentUser->hasRole('chu_quan');

                    if ($isOwnerRole) {
                        $isOwnerNav = true;
                        $ownerNavLabel = 'Quản lý cửa hàng';
                        $ownerNavIcon = 'storefront';
                        $ownerNavUrl = Route::has('chu-quan.quan.index')
                            ? route('chu-quan.quan.index')
                            : route('chu-quan.dang-quan');
                    }

                    try {
                        $navUnreadCount = $currentUser->unreadNotifications()->count();
                        $navNotifications = $currentUser->notifications()->latest()->take(20)->get();
                    } catch (\Throwable $e) {
                        $navUnreadCount = 0;
                        $navNotifications = collect();
                    }
                }
            }

            $view->with([
                'currentUser' => $currentUser,
                'isOwnerNav' => $isOwnerNav,
                'ownerNavLabel' => $ownerNavLabel,
                'ownerNavIcon' => $ownerNavIcon,
                'ownerNavUrl' => $ownerNavUrl,
                'navIsAdmin' => $navIsAdmin,
                'navUnreadCount' => $navUnreadCount,
                'navNotifications' => $navNotifications,
            ]);
        });

        View::composer(['admin.*', 'admin.partials.sidebar-nav'], function ($view): void {
            if (Auth::check() && Auth::user()->isAdmin()) {
                try {
                    $admin = Auth::user();
                    $notifData = \App\Http\Controllers\Admin\ThongBaoAdminController::buildRealtimeNotificationData($admin);

                    $view->with([
                        'sidebarPendingQuanCount' => $notifData['pending_quan'],
                        'sidebarPendingBlogCount' => $notifData['pending_blog'],
                        'sidebarUnverifiedUserCount' => $notifData['unverified_user'],
                        'sidebarTotalPending' => $notifData['total_pending'],
                        'sidebarUnreadCount' => $notifData['unread_count'],
                        'adminInitialNotifications' => app()->runningUnitTests() ? [] : $notifData['all_notifications'],
                    ]);
                } catch (\Throwable $e) {
                    // Ignore during testing / setup
                }
            }
        });

        // Tự động xóa sạch mọi cache liên quan khi có bất kỳ thao tác Thêm / Sửa / Xóa nào trên Models
        $clearAdminStatsCache = function () {
            \Illuminate\Support\Facades\Cache::forget('admin_dashboard_overview_stats');
            \Illuminate\Support\Facades\Cache::forget('admin_dashboard_charts_6m');
            \Illuminate\Support\Facades\Cache::forget('bando_categories_v2');
            \Illuminate\Support\Facades\Cache::forever('db_last_mutated_at', (string) microtime(true));
        };

        \App\Models\Quan::saved(function ($quan) use ($clearAdminStatsCache) {
            $clearAdminStatsCache();
            try {
                app(\App\Services\ElasticAdminSearchService::class)->syncQuan($quan);
            } catch (\Throwable $e) {}
        });
        \App\Models\Quan::deleted(function ($quan) use ($clearAdminStatsCache) {
            $clearAdminStatsCache();
            \Illuminate\Support\Facades\Cache::forever('last_deleted_quan_info', [
                'id' => (string) $quan->getKey(),
                'name' => $quan->ten_quan,
                'deleted_at' => (string) microtime(true),
            ]);
            try {
                app(\App\Services\ElasticAdminSearchService::class)->deleteDocument('quan', $quan->id);
            } catch (\Throwable $e) {}
        });
        \App\Models\Quan::restored(function ($quan) use ($clearAdminStatsCache) {
            $clearAdminStatsCache();
            try {
                app(\App\Services\ElasticAdminSearchService::class)->syncQuan($quan);
            } catch (\Throwable $e) {}
        });

        \App\Models\User::saved(function ($user) use ($clearAdminStatsCache) {
            $clearAdminStatsCache();
            try {
                app(\App\Services\ElasticAdminSearchService::class)->syncUser($user);
            } catch (\Throwable $e) {}
        });
        \App\Models\User::deleted(function ($user) use ($clearAdminStatsCache) {
            $clearAdminStatsCache();
            \Illuminate\Support\Facades\Cache::forever('last_deleted_user_info', [
                'id' => (string) $user->getKey(),
                'name' => $user->ho_ten,
                'email' => $user->email,
                'deleted_at' => (string) microtime(true),
            ]);
            try {
                app(\App\Services\ElasticAdminSearchService::class)->deleteDocument('users', $user->id);
            } catch (\Throwable $e) {}
        });
        \App\Models\User::restored(function ($user) use ($clearAdminStatsCache) {
            $clearAdminStatsCache();
            try {
                app(\App\Services\ElasticAdminSearchService::class)->syncUser($user);
            } catch (\Throwable $e) {}
        });

        \App\Models\DanhMucQuan::saved(function ($dm) use ($clearAdminStatsCache) {
            $clearAdminStatsCache();
            try {
                app(\App\Services\ElasticAdminSearchService::class)->syncDanhMuc($dm);
            } catch (\Throwable $e) {}
        });
        \App\Models\DanhMucQuan::deleted(function ($dm) use ($clearAdminStatsCache) {
            $clearAdminStatsCache();
            try {
                app(\App\Services\ElasticAdminSearchService::class)->deleteDocument('danh_muc_quan', $dm->id);
            } catch (\Throwable $e) {}
        });

        \App\Models\Blog::saved(function ($blog) use ($clearAdminStatsCache) {
            $clearAdminStatsCache();
            try {
                app(\App\Services\ElasticAdminSearchService::class)->syncBlog($blog);
            } catch (\Throwable $e) {}
        });
        \App\Models\Blog::deleted(function ($blog) use ($clearAdminStatsCache) {
            $clearAdminStatsCache();
            try {
                app(\App\Services\ElasticAdminSearchService::class)->deleteDocument('blogs', $blog->id);
            } catch (\Throwable $e) {}
        });

        \App\Models\ThanhToan::saved($clearAdminStatsCache);
        \App\Models\ThanhToan::deleted($clearAdminStatsCache);

        if (class_exists(\App\Models\KhuyenMai::class)) {
            \App\Models\KhuyenMai::saved(function ($km) use ($clearAdminStatsCache) {
                $clearAdminStatsCache();
                try {
                    app(\App\Services\ElasticAdminSearchService::class)->syncKhuyenMai($km);
                } catch (\Throwable $e) {}
            });
            \App\Models\KhuyenMai::deleted(function ($km) use ($clearAdminStatsCache) {
                $clearAdminStatsCache();
                try {
                    app(\App\Services\ElasticAdminSearchService::class)->deleteDocument('khuyen_mai', $km->id);
                } catch (\Throwable $e) {}
            });
        }

        if (class_exists(\App\Models\VideoShort::class)) {
            \App\Models\VideoShort::saved(function ($v) use ($clearAdminStatsCache) {
                $clearAdminStatsCache();
                try {
                    app(\App\Services\ElasticAdminSearchService::class)->syncVideo($v);
                } catch (\Throwable $e) {}
            });
            \App\Models\VideoShort::deleted(function ($v) use ($clearAdminStatsCache) {
                $clearAdminStatsCache();
                try {
                    app(\App\Services\ElasticAdminSearchService::class)->deleteDocument('video_shorts', $v->id);
                } catch (\Throwable $e) {}
            });
        }
    }

    private function rateLimitKey(Request $request): string
    {
        $email = mb_strtolower(trim((string) $request->input('email')));
        $identifier = $email !== '' ? $email : (string) $request->user()?->getAuthIdentifier();

        return sha1($request->ip().'|'.$identifier);
    }
}
