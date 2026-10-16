<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    /**
     * Lấy danh sách thông báo của người dùng hiện tại.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
        }

        $notifications = $user->notifications()
            ->latest()
            ->take(30)
            ->get()
            ->map(function ($notif) {
                $data = $notif->data ?? [];
                return array_merge([
                    'id' => $notif->id,
                    'read' => $notif->read_at !== null,
                    'read_at' => $notif->read_at ? $notif->read_at->format('d/m/Y H:i') : null,
                    'created_at' => $notif->created_at ? $notif->created_at->format('d/m/Y H:i') : null,
                    'time_ago' => $notif->created_at ? $notif->created_at->diffForHumans() : '',
                    'data' => $data,
                    'title' => $data['title'] ?? 'Thông báo hệ thống',
                    'message' => $data['message'] ?? '',
                    'note' => $data['note'] ?? null,
                    'status' => $data['status'] ?? null,
                    'status_label' => $data['status_label'] ?? 'Đã cập nhật',
                    'action' => $data['action'] ?? 'updated',
                    'link' => $data['link'] ?? route('nguoi-dung.blog.index'),
                    'cover_image_url' => $data['cover_image_url'] ?? null,
                    'blog_id' => $data['blog_id'] ?? null,
                    'slug' => $data['slug'] ?? null,
                    'quan_id' => $data['quan_id'] ?? null,
                ], $data);
            });

        $unreadCount = $user->unreadNotifications()->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'notifications' => $notifications,
            ]);
        }

        return redirect()->route('tai-khoan.index');
    }

    /**
     * Endpoint kiểm tra thông báo thời gian thực (nhẹ, phục vụ polling).
     */
    public function realtimeCheck(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'unread_count' => 0], 401);
        }

        $unreadCount = $user->unreadNotifications()->count();
        $latest = $user->notifications()->latest()->first();

        $latestFormatted = null;
        if ($latest) {
            $data = $latest->data ?? [];
            $latestFormatted = array_merge([
                'id' => $latest->id,
                'read' => $latest->read_at !== null,
                'created_at' => $latest->created_at ? $latest->created_at->toIso8601String() : null,
                'time_ago' => $latest->created_at ? $latest->created_at->diffForHumans() : '',
                'title' => $data['title'] ?? 'Thông báo mới',
                'message' => $data['message'] ?? '',
                'note' => $data['note'] ?? null,
                'status' => $data['status'] ?? null,
                'status_label' => $data['status_label'] ?? 'Đã cập nhật',
                'action' => $data['action'] ?? 'updated',
                'link' => $data['link'] ?? route('nguoi-dung.blog.index'),
                'cover_image_url' => $data['cover_image_url'] ?? null,
                'blog_id' => $data['blog_id'] ?? null,
                'slug' => $data['slug'] ?? null,
                'quan_id' => $data['quan_id'] ?? null,
            ], $data);
        }

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'latest' => $latestFormatted,
        ]);
    }

    /**
     * Đánh dấu 1 thông báo cụ thể là đã đọc.
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
        }

        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
        }

        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu thông báo là đã đọc.',
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Đánh dấu tất cả thông báo của người dùng là đã đọc.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
        }

        $user->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả thông báo là đã đọc.',
            'unread_count' => 0,
        ]);
    }
}
