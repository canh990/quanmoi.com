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
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
            }
            return redirect()->route('login');
        }

        $unreadCount = $user->unreadNotifications()->count();

        if ($request->expectsJson() || $request->ajax()) {
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
                        'link' => self::resolveTargetLink($data),
                        'cover_image_url' => $data['cover_image_url'] ?? null,
                        'blog_id' => $data['blog_id'] ?? null,
                        'slug' => $data['slug'] ?? null,
                        'quan_id' => $data['quan_id'] ?? null,
                    ], $data);
                });

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'notifications' => $notifications,
            ]);
        }

        // Web view for user notification center
        $tab = $request->query('tab', 'tat_ca');
        $query = $user->notifications()->latest();

        if ($tab === 'chua_doc') {
            $query->whereNull('read_at');
        } elseif ($tab === 'quan') {
            $query->where(function ($q) {
                $q->whereNotNull('data->quan_id')
                  ->orWhere('data->action', 'like', 'venue%')
                  ->orWhere('data->action', 'like', 'featured%')
                  ->orWhere('data->action', 'like', 'verified%');
            });
        } elseif ($tab === 'blog') {
            $query->where(function ($q) {
                $q->whereNotNull('data->blog_id')
                  ->orWhereIn('data->action', ['published', 'rejected', 'requested_revision', 'need_revision', 'hidden', 'submitted', 'hero']);
            });
        } elseif ($tab === 'danh_gia') {
            $query->where('data->action', 'new_review');
        } elseif ($tab === 'he_thong') {
            $query->where(function ($q) {
                $q->whereIn('data->action', ['broadcast', 'role_updated', 'verified', 'verification_rejected', 'permission_granted', 'permission_denied', 'permission_reset', 'status_updated']);
            });
        }

        $notifications = $query->paginate(15)->withQueryString();

        return view('nguoi-dung.thong-bao.index', compact('notifications', 'unreadCount', 'tab', 'user'));
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
                'link' => self::resolveTargetLink($data),
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
     * Nhấn 1 lần: Đánh dấu đã đọc và chuyển hướng ngay tới nội dung thông báo.
     */
    public function readAndRedirect(Request $request, string $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $notification = $user->notifications()->where('id', $id)->first();
        $targetLink = route('nguoi-dung.thong-bao.index');

        if ($notification) {
            if (!$notification->read_at) {
                $notification->markAsRead();
            }
            $data = $notification->data ?? [];
            $targetLink = self::resolveTargetLink($data);
        }

        return redirect($targetLink);
    }

    /**
     * Tự động phân giải link đích chuẩn xác cho từng loại thông báo.
     */
    public static function resolveTargetLink(array $data): string
    {
        if (!empty($data['link']) && $data['link'] !== '#' && $data['link'] !== 'javascript:void(0)') {
            return $data['link'];
        }

        $action = $data['action'] ?? ($data['status'] ?? '');

        // Quán ăn
        if (!empty($data['slug'])) {
            if (in_array($action, ['submitted', 'rejected', 'menu_updated'])) {
                return route('chu-quan.quan.show', $data['slug']);
            }
            return route('quan.detail', $data['slug']);
        }

        if (!empty($data['quan_id'])) {
            $quan = \App\Models\Quan::find($data['quan_id']);
            if ($quan && $quan->slug) {
                if (in_array($action, ['submitted', 'rejected', 'menu_updated'])) {
                    return route('chu-quan.quan.show', $quan->slug);
                }
                return route('quan.detail', $quan->slug);
            }
            return route('chu-quan.quan.index');
        }

        if (str_starts_with($action, 'venue_') || in_array($action, ['submitted', 'created', 'updated', 'approved', 'rejected', 'featured_granted', 'featured_revoked', 'verified_granted', 'verified_revoked', 'menu_updated'])) {
            return route('chu-quan.quan.index');
        }

        // Bài viết Blog
        if (!empty($data['blog_id'])) {
            $blog = \App\Models\Blog::find($data['blog_id']);
            if ($blog) {
                if (!empty($data['comment_id'])) {
                    return route('blog.show', $blog->slug ?? $blog->id) . '#comment-' . $data['comment_id'];
                }
                if (in_array($action, ['published', 'hero'])) {
                    return route('blog.show', $blog->slug ?? $blog->id);
                }
                if (in_array($action, ['rejected', 'requested_revision'])) {
                    return route('nguoi-dung.blog.edit', $blog->id);
                }
            }
            return route('nguoi-dung.blog.index');
        }

        if (!empty($data['slug'])) {
            return route('blog.show', $data['slug']);
        }

        // Tài khoản, phân quyền, xác thực
        if (in_array($action, ['verified', 'verification_rejected', 'verification_requested', 'role_updated', 'status_updated', 'broadcast']) || str_starts_with($action, 'permission_')) {
            return route('tai-khoan.index');
        }

        return route('nguoi-dung.thong-bao.index');
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

    /**
     * Xóa 1 thông báo.
     */
    public function destroy(Request $request, string $id)
    {
        $user = Auth::user();
        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
            }
            return redirect()->route('login');
        }

        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->delete();
        }

        $unreadCount = $user->unreadNotifications()->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa thông báo thành công.',
                'unread_count' => $unreadCount,
            ]);
        }

        return back()->with('success', 'Đã xóa thông báo thành công.');
    }
}

