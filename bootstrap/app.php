<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'blog/*/luot-xem',
            'blog/*/ai-tom-tat',
            'api/blog/*',
        ]);

        $middleware->alias([
            'KiemTraQuyenHan' => \App\Http\Middleware\KiemTraQuyenHan::class,
            'CheckAdmin'      => \App\Http\Middleware\KiemTraQuyenHan::class,
            'CheckPermission' => \App\Http\Middleware\CheckPermission::class,
            'permission'      => \App\Http\Middleware\CheckPermission::class,
            'guest'           => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->expectsJson() || $request->is('api/*'),
        );
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
            }
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['message' => 'Dữ liệu hoặc trang yêu cầu không tồn tại trong hệ thống.'], 404);
            }
        });
    })->create();
