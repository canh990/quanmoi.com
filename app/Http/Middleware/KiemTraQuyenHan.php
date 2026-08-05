<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class KiemTraQuyenHan
{
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isActive()) {
            if ($user) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Phiên đăng nhập không còn hợp lệ.'], 401);
            }

            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập bằng tài khoản đang hoạt động.');
        }

        if (! $user->hasRole($role)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập tài nguyên này.'], 403);
            }

            abort(403, 'Bạn không có quyền truy cập tài nguyên này.');
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
