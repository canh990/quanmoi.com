<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\NguoiDungPolicy;
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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasPermissionTo')) {
                // Check explicit user override
                if (method_exists($user, 'quyenHanOverrides')) {
                    $override = $user->quyenHanOverrides()
                        ->where(function ($query) use ($ability) {
                            $query->where('quyen_han.ten', $ability)->orWhere('quyen_han.id', $ability);
                        })
                        ->first();

                    if ($override !== null) {
                        return (bool) $override->pivot->cho_phep;
                    }
                }

                if ($user->hasPermissionTo($ability)) {
                    return true;
                }
            }
        });

        Gate::policy(User::class, NguoiDungPolicy::class);

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

            if ($currentUser) {
                if ($currentUser->isAdmin()) {
                    $isOwnerNav = true;
                    $ownerNavLabel = 'Trang quản trị';
                    $ownerNavIcon = 'admin_panel_settings';
                    $ownerNavUrl = Route::has('admin.quan.index')
                        ? route('admin.quan.index')
                        : route('chu-quan.dang-quan');
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
                }
            }

            $view->with([
                'currentUser' => $currentUser,
                'isOwnerNav' => $isOwnerNav,
                'ownerNavLabel' => $ownerNavLabel,
                'ownerNavIcon' => $ownerNavIcon,
                'ownerNavUrl' => $ownerNavUrl,
            ]);
        });
    }

    private function rateLimitKey(Request $request): string
    {
        $email = mb_strtolower(trim((string) $request->input('email')));
        $identifier = $email !== '' ? $email : (string) $request->user()?->getAuthIdentifier();

        return sha1($request->ip().'|'.$identifier);
    }
}
