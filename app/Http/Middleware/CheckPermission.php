<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isActive()) {
            if ($user) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Phiên đăng nhập không hợp lệ.'], 401);
            }

            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        if (! $user->hasPermissionTo($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => "Bạn không có quyền '{$permission}' để thực hiện thao tác này."], 403);
            }

            abort(403, "Bạn không có quyền '{$permission}' để thực hiện thao tác này.");
        }

        return $next($request);
    }
}
