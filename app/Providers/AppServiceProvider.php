<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
                    $hasOwnedQuan = $currentUser->quan()->exists();
                    $isOwnerRole = $currentUser->hasRole('chu_quan');

                    if ($hasOwnedQuan || $isOwnerRole) {
                        $isOwnerNav = true;
                        $ownerNavLabel = 'Quản lý cửa hàng';
                        $ownerNavIcon = 'storefront';
                        $ownerNavUrl = $hasOwnedQuan && Route::has('chu-quan.quan.index')
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
}
