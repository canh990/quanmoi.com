<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KiemTraQuyenHan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn chưa đăng nhập.'], 401);
            }
            return redirect()->route('dang-nhap')->with('error', 'Vui lòng đăng nhập với tài khoản Admin.');
        }

        if (!$user->isAdmin() && !$user->hasRole($role)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập trang Quản trị.'], 403);
            }
            abort(403, 'Bạn không có quyền truy cập trang Quản trị Admin.');
        }

        $response = $next($request);

        $response->headers->set('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
