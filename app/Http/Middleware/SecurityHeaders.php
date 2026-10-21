<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Chống Clickjacking: Ngăn website bị nhúng vào iframe trên trang của bên thứ ba
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Chống MIME-type sniffing: Yêu cầu trình duyệt tuân thủ Content-Type đã gửi
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Bộ lọc XSS trình duyệt kế thừa
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Kiểm soát Referer header khi chuyển hướng liên kết ngoại
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Hạn chế các tính năng phần cứng nhạy cảm nếu không cần thiết
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        return $response;
    }
}
