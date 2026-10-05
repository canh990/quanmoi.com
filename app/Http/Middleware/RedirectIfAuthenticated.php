<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Chặn người dùng đã đăng nhập truy cập các trang guest-only
     * (đăng nhập, đăng ký, quên mật khẩu, ...).
     * Redirect theo vai trò để tránh UX nhầm lẫn.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                $redirectTo = '/';
                if ($user?->hasRole('admin')) {
                    $redirectTo = '/admin';
                } elseif ($user?->hasRole('chu_quan')) {
                    $redirectTo = '/chu-quan/quan';
                }

                return redirect($redirectTo);
            }
        }

        return $next($request);
    }
}
