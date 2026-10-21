<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Blog;
use App\Models\Quan;
use App\Models\User;
use App\Notifications\SystemBroadcastNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ThongBaoAdminController extends Controller
{
    public function index(Request $request)
    {
        $admin = $request->user();
        if ($admin) {
            $caiDat = $admin->cai_dat ?? [];
            if (is_string($caiDat)) {
                $caiDat = json_decode($caiDat, true) ?: [];
            }
            $caiDat['last_read_admin_notifs_at'] = now()->toIso8601String();
            \Illuminate\Support\Facades\DB::table('nguoi_dung')
                ->where('id', $admin->id)
                ->update(['cai_dat' => json_encode($caiDat, JSON_UNESCAPED_UNICODE)]);
            $admin->cai_dat = $caiDat;
        }

        $realtimeData = self::buildRealtimeNotificationData($admin);

        $pendingQuanCount = $realtimeData['pending_quan'];
        $pendingBlogCount = $realtimeData['pending_blog'];
        $unverifiedUserCount = $realtimeData['unverified_user'];
        $broadcastCount = $realtimeData['broadcast_count'];
        $allNotifications = $realtimeData['all_notifications'];
        $unreadCount = $realtimeData['unread_count'];

        $notifications = AdminAuditLog::where('action', 'system.broadcast')
            ->with('actor')
            ->latest()
            ->paginate(15);

        return view('admin.thong-bao.index', compact(
            'notifications',
            'allNotifications',
            'unreadCount',
            'realtimeData',
            'pendingQuanCount',
            'pendingBlogCount',
            'unverifiedUserCount',
            'broadcastCount'
        ));
    }

    public function sendSystemNotification(Request $request)
    {
        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'doi_tuong' => 'required|in:tat_ca,chu_quan,nguoi_dung',
        ]);

        $senderName = $request->user()?->ho_ten ?? 'Ban quản trị Quán Mới';

        // 1. Lưu log hệ thống
        $log = null;
        if ($request->user()) {
            $log = AdminAuditLog::create([
                'actor_id' => $request->user()->id,
                'action' => 'system.broadcast',
                'target_type' => User::class,
                'target_id' => (string) $request->user()->id,
                'metadata' => [
                    'tieu_de' => $validated['tieu_de'],
                    'noi_dung' => $validated['noi_dung'],
                    'doi_tuong' => $validated['doi_tuong'],
                ],
                'ip_address' => $request->ip(),
            ]);
        }

        // 2. Gửi thông báo đến toàn bộ người dùng mục tiêu trong cơ sở dữ liệu
        try {
            $userQuery = User::where('trang_thai', 'hoat_dong');
            if ($validated['doi_tuong'] === 'chu_quan') {
                $userQuery->whereHas('vaiTro', fn($q) => $q->where('ten', 'chu_quan'));
            } elseif ($validated['doi_tuong'] === 'nguoi_dung') {
                $userQuery->whereHas('vaiTro', fn($q) => $q->where('ten', 'nguoi_dung'));
            }

            $recipients = $userQuery->get();
            if ($recipients->isNotEmpty()) {
                Notification::send(
                    $recipients,
                    new SystemBroadcastNotification(
                        $validated['tieu_de'],
                        $validated['noi_dung'],
                        $validated['doi_tuong'],
                        $senderName
                    )
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Send broadcast notifications error: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã phát sóng thông báo hệ thống đến các tài khoản thành công!',
                'notification' => [
                    'id' => $log ? $log->id : null,
                    'tieu_de' => $validated['tieu_de'],
                    'noi_dung' => $validated['noi_dung'],
                    'doi_tuong' => $validated['doi_tuong'],
                    'actor_name' => $senderName,
                    'created_at' => now()->format('d/m/Y H:i'),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Đã gửi thông báo hệ thống thành công!');
    }

    /**
     * Đánh dấu 1 thông báo quản trị là đã đọc.
     */
    public function markAsRead(Request $request, string $id)
    {
        $admin = $request->user();
        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
        }

        $caiDat = $admin->cai_dat ?? [];
        if (is_string($caiDat)) {
            $caiDat = json_decode($caiDat, true) ?: [];
        }
        $readIds = array_map('strval', $caiDat['read_admin_notifs'] ?? []);
        $idStr = (string) $id;

        if (str_starts_with($idStr, 'db_')) {
            $dbId = substr($idStr, 3);
            try {
                $admin->notifications()->where('id', $dbId)->update(['read_at' => now()]);
            } catch (\Throwable $e) {}
        }

        if (!in_array($idStr, $readIds, true)) {
            $readIds[] = $idStr;
            if (count($readIds) > 500) {
                $readIds = array_slice($readIds, -500);
            }
            $caiDat['read_admin_notifs'] = $readIds;

            // Lưu trực tiếp vào MySQL và cập nhật model để tránh dirty-checking quirk
            \Illuminate\Support\Facades\DB::table('nguoi_dung')
                ->where('id', $admin->id)
                ->update(['cai_dat' => json_encode($caiDat, JSON_UNESCAPED_UNICODE)]);

            $admin->cai_dat = $caiDat;
            $admin->saveQuietly();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu thông báo là đã đọc.',
            'id' => $id,
        ]);
    }

    /**
     * Nhấn 1 lần: Đánh dấu đã đọc và chuyển hướng ngay tới nội dung thông báo cho Admin.
     */
    public function readAndRedirect(Request $request, string $id)
    {
        $admin = $request->user();
        if (!$admin) {
            return redirect()->route('login');
        }

        // Đánh dấu đã đọc
        $this->markAsRead($request, $id);

        $url = route('admin.thong-bao.index');
        $idStr = (string) $id;

        if (str_starts_with($idStr, 'db_')) {
            $dbId = substr($idStr, 3);
            $dbNotif = $admin->notifications()->where('id', $dbId)->first();
            if ($dbNotif) {
                if (!$dbNotif->read_at) {
                    $dbNotif->markAsRead();
                }
                return redirect(\App\Http\Controllers\NguoiDung\ThongBaoController::resolveTargetLink($dbNotif->data ?? []));
            }
        } elseif (str_starts_with($idStr, 'quan_')) {
            $url = route('admin.duyet-xac-thuc.index');
        } elseif (str_starts_with($idStr, 'blog_')) {
            $url = route('admin.blog.posts.pending');
        } elseif (str_starts_with($idStr, 'user_')) {
            $url = route('admin.duyet-xac-thuc.index');
        } elseif (str_starts_with($idStr, 'broadcast_')) {
            $url = route('admin.thong-bao.index');
        } elseif (str_starts_with($idStr, 'audit_') || str_starts_with($idStr, 'review_')) {
            $auditId = str_starts_with($idStr, 'review_') ? substr($idStr, 7) : substr($idStr, 6);
            $log = AdminAuditLog::find($auditId);
            if ($log) {
                $action = $log->action;
                if (str_starts_with($action, 'venue.')) {
                    $url = ($action === 'venue.submitted' || $action === 'venue.rejected')
                        ? route('admin.duyet-xac-thuc.index')
                        : ($action === 'venue.featured_toggled' ? route('admin.quan-noi-bat.index') : route('admin.quan.index'));
                } elseif (str_starts_with($action, 'user.')) {
                    $url = in_array($action, ['user.verification_requested', 'user.verified', 'user.rejected'])
                        ? route('admin.duyet-xac-thuc.index')
                        : route('admin.nguoi-dung.index');
                } elseif (str_starts_with($action, 'blog.')) {
                    $url = ($action === 'blog.submitted') ? route('admin.blog.posts.pending') : route('admin.blog.posts.index');
                } elseif (str_starts_with($action, 'role.')) {
                    $url = route('admin.vai-tro.index');
                } elseif (str_starts_with($action, 'permission.')) {
                    $url = route('admin.phan-quyen.index');
                }
            }
        }

        return redirect($url);
    }

    /**
     * Đánh dấu tất cả thông báo quản trị là đã đọc.
     */
    public function markAllAsRead(Request $request)
    {
        $admin = $request->user();
        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
        }

        $caiDat = $admin->cai_dat ?? [];
        if (is_string($caiDat)) {
            $caiDat = json_decode($caiDat, true) ?: [];
        }
        $readIds = array_map('strval', $caiDat['read_admin_notifs'] ?? []);

        // Đánh dấu đã đọc trong bảng notifications nếu có
        try {
            if ($admin && method_exists($admin, 'unreadNotifications')) {
                $admin->unreadNotifications->markAsRead();
            }
        } catch (\Throwable $e) {}

        // Lấy tất cả IDs thông báo đang tồn tại để đưa trực tiếp vào read_admin_notifs
        $pendingQuanIds = Quan::where('trang_thai', 'chua_duyet')->pluck('id')->map(fn($id) => 'quan_' . $id)->all();
        $pendingBlogIds = Blog::where('status', 'pending')->pluck('id')->map(fn($id) => 'blog_' . $id)->all();
        $unverifiedUserIds = User::where('da_xac_thuc', false)->pluck('id')->map(fn($id) => 'user_' . $id)->all();
        $auditIds = AdminAuditLog::latest()->take(60)->get()->map(function($l) {
            return ($l->action === 'system.broadcast') ? ('broadcast_' . $l->id) : ('audit_' . $l->id);
        })->all();
        $dbNotifIds = ($admin && method_exists($admin, 'notifications'))
            ? $admin->notifications()->pluck('id')->map(fn($id) => 'db_' . $id)->all()
            : [];

        $allCurrentIds = array_unique(array_merge($readIds, $pendingQuanIds, $pendingBlogIds, $unverifiedUserIds, $auditIds, $dbNotifIds));
        if (count($allCurrentIds) > 500) {
            $allCurrentIds = array_slice($allCurrentIds, -500);
        }

        $caiDat['read_admin_notifs'] = array_values($allCurrentIds);
        $caiDat['last_read_admin_notifs_at'] = now()->toIso8601String();

        // Lưu trực tiếp vào MySQL và cập nhật model
        \Illuminate\Support\Facades\DB::table('nguoi_dung')
            ->where('id', $admin->id)
            ->update(['cai_dat' => json_encode($caiDat, JSON_UNESCAPED_UNICODE)]);

        $admin->cai_dat = $caiDat;
        $admin->saveQuietly();

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả thông báo là đã đọc.',
        ]);
    }

    /**
     * Real-time polling API for Admin notifications dashboard & topbar.
     */
    public function realtimeCheck(Request $request)
    {
        return response()->json(self::buildRealtimeNotificationData($request->user()));
    }

    /**
     * Build unified real-time notification dataset for Admin.
     */
    public static function buildRealtimeNotificationData($admin = null): array
    {
        $totalQuanCount = Quan::count();
        $totalUserCount = User::count();
        $activeUserCount = User::where('trang_thai', 'hoat_dong')->count();
        $totalBlogCount = Blog::count();
        $pendingQuanCount = Quan::where('trang_thai', 'chua_duyet')->count();
        $pendingBlogCount = Blog::where('status', 'pending')->count();
        $unverifiedUserCount = User::where('da_xac_thuc', false)->count();
        $broadcastCount = AdminAuditLog::where('action', 'system.broadcast')->count();

        // 1. Quán chờ duyệt
        $pendingQuans = Quan::where('trang_thai', 'chua_duyet')
            ->with(['chuQuan:id,ho_ten,email,so_dien_thoai', 'hinhAnh:id,quan_id,duong_dan,object_key'])
            ->select('id', 'chu_quan_id', 'ten_quan', 'loai_hinh_kinh_doanh', 'dia_chi_chi_tiet', 'ten_tinh_thanh', 'ten_quan_huyen', 'ten_phuong_xa', 'anh_bia', 'anh_bia_key', 'gio_mo_cua', 'gio_dong_cua', 'gia_nho_nhat', 'gia_lon_nhat', 'created_at', 'slug', 'trang_thai')
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($q) {
                $galleryUrls = $q->hinhAnh ? $q->hinhAnh->map(fn($img) => $img->duong_dan)->values()->all() : [];
                $ownerName = $q->chuQuan?->ho_ten ?? 'Chưa xác định';
                $ownerEmail = $q->chuQuan?->email ?? 'Chưa có email';
                $ownerPhone = $q->chuQuan?->so_dien_thoai ?? 'Chưa có SĐT';
                $fullAddress = trim(implode(', ', array_filter([
                    $q->dia_chi_chi_tiet,
                    $q->ten_phuong_xa,
                    $q->ten_quan_huyen,
                    $q->ten_tinh_thanh,
                ])));

                return [
                    'id' => $q->id,
                    'ten_quan' => $q->ten_quan,
                    'loai_hinh' => $q->loai_hinh_kinh_doanh,
                    'loai_hinh_kinh_doanh' => $q->loai_hinh_kinh_doanh,
                    'dia_chi' => $fullAddress ?: ($q->dia_chi_chi_tiet ?: 'Chưa cập nhật'),
                    'dia_chi_chi_tiet' => $q->dia_chi_chi_tiet,
                    'ten_tinh_thanh' => $q->ten_tinh_thanh,
                    'ten_quan_huyen' => $q->ten_quan_huyen,
                    'ten_phuong_xa' => $q->ten_phuong_xa,
                    'anh_bia_url' => $q->anh_bia_url,
                    'gallery' => $galleryUrls,
                    'gio_mo_cua' => $q->gio_mo_cua,
                    'gio_dong_cua' => $q->gio_dong_cua,
                    'gia_nho_nhat' => $q->gia_nho_nhat,
                    'gia_lon_nhat' => $q->gia_lon_nhat,
                    'slug' => $q->slug,
                    'trang_thai' => $q->trang_thai,
                    'owner_name' => $ownerName,
                    'owner_email' => $ownerEmail,
                    'owner_phone' => $ownerPhone,
                    'nguoi_dang' => [
                        'ho_ten' => $ownerName,
                        'email' => $ownerEmail,
                        'so_dien_thoai' => $ownerPhone,
                    ],
                    'created_at' => $q->created_at?->toIso8601String(),
                    'created_at_formatted' => $q->created_at?->format('d/m/Y H:i'),
                    'time_ago' => $q->created_at?->diffForHumans(),
                    'url' => route('admin.duyet-xac-thuc.index'),
                ];
            });

        // 2. Blog chờ duyệt
        $pendingBlogs = Blog::where('status', 'pending')
            ->with(['user', 'category:id,name'])
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'title' => $b->title,
                    'category_name' => $b->category?->name ?? 'Không phân loại',
                    'cover_image_url' => $b->cover_image_url ?? 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80',
                    'excerpt' => $b->excerpt ?? '',
                    'author' => $b->user?->ho_ten ?? 'Ẩn danh',
                    'author_avatar' => $b->user?->anh_dai_dien ?? ('https://ui-avatars.com/api/?name='.urlencode($b->user?->ho_ten ?? 'U')),
                    'created_at' => $b->created_at?->toIso8601String(),
                    'created_at_formatted' => $b->created_at?->format('d/m/Y H:i'),
                    'time_ago' => $b->created_at?->diffForHumans(),
                    'url' => route('admin.blog.posts.pending'),
                    'detail_url' => route('admin.blog.posts.show', $b->id),
                ];
            });

        // 3. User chờ xác thực
        $unverifiedUsers = User::where('da_xac_thuc', false)
            ->select('id', 'ho_ten', 'email', 'so_dien_thoai', 'created_at')
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'ho_ten' => $u->ho_ten,
                    'email' => $u->email,
                    'so_dien_thoai' => $u->so_dien_thoai,
                    'created_at' => $u->created_at?->toIso8601String(),
                    'created_at_formatted' => $u->created_at?->format('d/m/Y H:i'),
                    'time_ago' => $u->created_at?->diffForHumans(),
                    'url' => route('admin.duyet-xac-thuc.index'),
                ];
            });

        // 4. Lấy tất cả Audit Logs gần đây (tất cả các thao tác thêm, sửa, xóa, duyệt, từ chối, phân quyền, v.v.)
        $recentAuditLogs = AdminAuditLog::with('actor:id,ho_ten')
            ->latest()
            ->take(40)
            ->get();

        // 5. Tổng hợp toàn bộ danh sách thông báo đa chiều
        $allNotifications = [];

        // Thêm Quán chờ duyệt
        foreach ($pendingQuans as $q) {
            $owner = $q['nguoi_dang'] ? $q['nguoi_dang']['ho_ten'] : 'Chưa xác định';
            $allNotifications[] = [
                'id' => 'quan_' . $q['id'],
                'item_id' => $q['id'],
                'type' => 'quan',
                'category_label' => 'Quán chờ duyệt',
                'title' => $q['ten_quan'],
                'description' => 'Chủ quán: ' . $owner . ($q['dia_chi'] ? ' • ' . $q['dia_chi'] : ''),
                'time_ago' => $q['time_ago'],
                'created_at' => $q['created_at'],
                'created_at_formatted' => $q['created_at_formatted'] ?? '',
                'url' => $q['url'],
                'icon' => 'storefront',
                'image_url' => $q['anh_bia_url'] ?? null,
                'badge_class' => 'bg-amber-100 text-amber-700',
            ];
        }

        // Thêm Blog chờ duyệt
        foreach ($pendingBlogs as $b) {
            $allNotifications[] = [
                'id' => 'blog_' . $b['id'],
                'item_id' => $b['id'],
                'type' => 'blog',
                'category_label' => 'Bài viết chờ duyệt',
                'title' => $b['title'],
                'description' => 'Tác giả: ' . ($b['author'] ?: 'Ẩn danh'),
                'time_ago' => $b['time_ago'],
                'created_at' => $b['created_at'],
                'created_at_formatted' => $b['created_at'] ? date('d/m/Y H:i', strtotime($b['created_at'])) : '',
                'url' => $b['url'],
                'icon' => 'article',
                'badge_class' => 'bg-blue-100 text-blue-700',
            ];
        }

        // Thêm User chờ xác thực
        foreach ($unverifiedUsers as $u) {
            $allNotifications[] = [
                'id' => 'user_' . $u['id'],
                'item_id' => $u['id'],
                'type' => 'user',
                'category_label' => 'Chờ xác thực tài khoản',
                'title' => $u['ho_ten'],
                'description' => $u['email'] . ($u['so_dien_thoai'] ? ' • SĐT: ' . $u['so_dien_thoai'] : ''),
                'time_ago' => $u['time_ago'],
                'created_at' => $u['created_at'],
                'created_at_formatted' => $u['created_at_formatted'] ?? '',
                'url' => $u['url'],
                'icon' => 'person_add',
                'badge_class' => 'bg-indigo-100 text-indigo-700',
            ];
        }

        // Thêm các sự kiện Audit Logs vào Notification Feed
        foreach ($recentAuditLogs as $log) {
            $meta = $log->metadata ?? [];
            $actorName = $log->actor?->ho_ten ?? 'Hệ thống';
            $timeAgo = $log->created_at ? $log->created_at->diffForHumans() : '';
            $createdAtStr = $log->created_at?->toIso8601String() ?? '';
            $createdAtFmt = $log->created_at?->format('d/m/Y H:i') ?? '';

            $action = $log->action;
            $item = null;

            switch ($action) {
                case 'system.broadcast':
                    $item = [
                        'id' => 'broadcast_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'broadcast',
                        'category_label' => 'Phát sóng hệ thống',
                        'title' => $meta['tieu_de'] ?? 'Thông báo hệ thống',
                        'description' => ($meta['noi_dung'] ?? '') . ' • Người gửi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.thong-bao.index'),
                        'icon' => 'campaign',
                        'badge_class' => 'bg-emerald-100 text-emerald-700',
                    ];
                    break;

                case 'venue.created':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'quan',
                        'category_label' => 'Thêm quán mới',
                        'title' => 'Đã tạo cơ sở: ' . ($meta['name'] ?? 'Quán mới'),
                        'description' => 'Khởi tạo bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.quan.index'),
                        'icon' => 'add_business',
                        'badge_class' => 'bg-emerald-100 text-emerald-700',
                    ];
                    break;

                case 'venue.submitted':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'quan',
                        'category_label' => 'Hồ sơ quán mới',
                        'title' => 'Quán mới gửi duyệt: ' . ($meta['ten_quan'] ?? ($meta['name'] ?? 'Quán mới')),
                        'description' => 'Chủ quán: ' . ($meta['chu_quan'] ?? $actorName),
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.duyet-xac-thuc.index'),
                        'icon' => 'storefront',
                        'badge_class' => 'bg-amber-100 text-amber-800',
                    ];
                    break;

                case 'venue.menu_updated':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'quan',
                        'category_label' => 'Cập nhật thực đơn',
                        'title' => 'Đã cập nhật thực đơn: ' . ($meta['ten_quan'] ?? 'Quán ăn'),
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.quan.index'),
                        'icon' => 'restaurant_menu',
                        'badge_class' => 'bg-teal-100 text-teal-800',
                    ];
                    break;

                case 'venue.review_replied':
                    $item = [
                        'id' => 'review_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'review',
                        'category_label' => 'Phản hồi đánh giá',
                        'title' => 'Chủ quán phản hồi đánh giá tại "' . ($meta['quan_name'] ?? '') . '"',
                        'description' => ($meta['phan_hoi'] ?? 'Chủ quán đã phản hồi khách hàng') . ' • Bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.quan.index'),
                        'icon' => 'reply',
                        'badge_class' => 'bg-blue-100 text-blue-800',
                    ];
                    break;

                case 'venue.updated':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'quan',
                        'category_label' => 'Cập nhật quán',
                        'title' => 'Cập nhật thông tin cơ sở ẩm thực',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.quan.index'),
                        'icon' => 'edit_location',
                        'badge_class' => 'bg-blue-100 text-blue-700',
                    ];
                    break;

                case 'venue.approved':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'quan',
                        'category_label' => 'Duyệt quán',
                        'title' => 'Đã phê duyệt cơ sở ẩm thực',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.quan.index'),
                        'icon' => 'check_circle',
                        'badge_class' => 'bg-emerald-100 text-emerald-700',
                    ];
                    break;

                case 'venue.rejected':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'quan',
                        'category_label' => 'Từ chối quán',
                        'title' => 'Đã từ chối duyệt cơ sở ẩm thực',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.duyet-xac-thuc.index'),
                        'icon' => 'gpp_bad',
                        'badge_class' => 'bg-rose-100 text-rose-700',
                    ];
                    break;

                case 'venue.deleted':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'quan',
                        'category_label' => 'Tạm ẩn quán',
                        'title' => 'Đã tạm ẩn/khóa cơ sở ẩm thực',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.quan.index'),
                        'icon' => 'delete',
                        'badge_class' => 'bg-rose-100 text-rose-700',
                    ];
                    break;

                case 'venue.restored':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'quan',
                        'category_label' => 'Khôi phục quán',
                        'title' => 'Đã khôi phục cơ sở ẩm thực',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.quan.index'),
                        'icon' => 'restore_from_trash',
                        'badge_class' => 'bg-teal-100 text-teal-700',
                    ];
                    break;

                case 'venue.featured_toggled':
                    $isFeat = !empty($meta['featured']);
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'quan',
                        'category_label' => 'Quán nổi bật',
                        'title' => ($isFeat ? 'Đã bật' : 'Đã gỡ') . ' trạng thái Quán Nổi Bật',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.quan-noi-bat.index'),
                        'icon' => 'stars',
                        'badge_class' => 'bg-amber-100 text-amber-700',
                    ];
                    break;

                case 'venue.verification_toggled':
                    $isVer = !empty($meta['verified']);
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'quan',
                        'category_label' => 'Tick Xanh quán',
                        'title' => ($isVer ? 'Cấp' : 'Gỡ') . ' Tick Xanh xác thực cơ sở',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.quan.index'),
                        'icon' => 'verified',
                        'badge_class' => 'bg-blue-100 text-blue-700',
                    ];
                    break;

                case 'venue.reviewed':
                    $item = [
                        'id' => 'review_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'review',
                        'category_label' => 'Đánh giá mới',
                        'title' => ($meta['user_name'] ?? 'Khách') . ' đánh giá ' . ($meta['so_sao'] ?? 5) . '⭐ cho quán "' . ($meta['quan_name'] ?? '') . '"',
                        'description' => $meta['binh_luan'] ?? 'Đánh giá ẩm thực mới từ khách hàng',
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.quan.index'),
                        'icon' => 'rate_review',
                        'badge_class' => 'bg-amber-100 text-amber-800',
                    ];
                    break;

                case 'user.updated':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'user',
                        'category_label' => 'Cập nhật tài khoản',
                        'title' => 'Cập nhật thông tin tài khoản người dùng',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.nguoi-dung.index'),
                        'icon' => 'manage_accounts',
                        'badge_class' => 'bg-indigo-100 text-indigo-700',
                    ];
                    break;

                case 'user.deleted':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'user',
                        'category_label' => 'Khóa tài khoản',
                        'title' => 'Đã khóa/xóa tạm tài khoản người dùng',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.nguoi-dung.index'),
                        'icon' => 'person_off',
                        'badge_class' => 'bg-rose-100 text-rose-700',
                    ];
                    break;

                case 'user.restored':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'user',
                        'category_label' => 'Khôi phục tài khoản',
                        'title' => 'Đã khôi phục tài khoản người dùng',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.nguoi-dung.index'),
                        'icon' => 'how_to_reg',
                        'badge_class' => 'bg-emerald-100 text-emerald-700',
                    ];
                    break;

                case 'user.verified':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'user',
                        'category_label' => 'Xác thực tài khoản',
                        'title' => 'Đã duyệt xác thực danh tính người dùng',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.duyet-xac-thuc.index'),
                        'icon' => 'verified_user',
                        'badge_class' => 'bg-emerald-100 text-emerald-700',
                    ];
                    break;

                case 'user.rejected':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'user',
                        'category_label' => 'Từ chối xác thực',
                        'title' => 'Đã từ chối xác thực danh tính người dùng',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.duyet-xac-thuc.index'),
                        'icon' => 'gpp_bad',
                        'badge_class' => 'bg-rose-100 text-rose-700',
                    ];
                    break;

                case 'user.verification_requested':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'user',
                        'category_label' => 'Yêu cầu xác thực',
                        'title' => ($meta['ho_ten'] ?? $actorName) . ' gửi yêu cầu xác thực tài khoản',
                        'description' => 'Lý do: ' . ($meta['ly_do'] ?? 'Cấp huy hiệu xác thực'),
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.duyet-xac-thuc.index'),
                        'icon' => 'verified_user',
                        'badge_class' => 'bg-indigo-100 text-indigo-800',
                    ];
                    break;

                case 'user.verification_toggled':
                    $isVer = !empty($meta['verified']);
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'user',
                        'category_label' => 'Tick Xanh User',
                        'title' => ($isVer ? 'Cấp' : 'Gỡ') . ' Tick Xanh xác thực người dùng',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.nguoi-dung.index'),
                        'icon' => 'verified',
                        'badge_class' => 'bg-blue-100 text-blue-700',
                    ];
                    break;

                case 'role.created':
                case 'role.updated_permissions':
                case 'role.deleted':
                    $labels = [
                        'role.created' => 'Tạo vai trò mới: ' . ($meta['ten'] ?? ''),
                        'role.updated_permissions' => 'Cập nhật phân quyền vai trò: ' . ($meta['ten'] ?? ''),
                        'role.deleted' => 'Đã xóa vai trò: ' . ($meta['ten'] ?? ''),
                    ];
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'role_permission',
                        'category_label' => 'Vai trò & Quyền',
                        'title' => $labels[$action] ?? 'Cập nhật vai trò',
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.vai-tro.index'),
                        'icon' => 'admin_panel_settings',
                        'badge_class' => 'bg-purple-100 text-purple-700',
                    ];
                    break;

                case 'permission.created':
                case 'permission.overridden':
                case 'permission.deleted':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'role_permission',
                        'category_label' => 'Phân quyền',
                        'title' => ($action === 'permission.created')
                            ? ('Tạo mã quyền: ' . ($meta['ten'] ?? ''))
                            : (($action === 'permission.deleted')
                                ? ('Xóa mã quyền: ' . ($meta['ten'] ?? ''))
                                : ('Override quyền "' . ($meta['permission'] ?? '') . '" cho ' . ($meta['user_name'] ?? ''))),
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.phan-quyen.index'),
                        'icon' => 'tune',
                        'badge_class' => 'bg-teal-100 text-teal-700',
                    ];
                    break;

                case 'blog.submitted':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'blog',
                        'category_label' => 'Bài viết chờ duyệt',
                        'title' => 'Bài viết mới gửi duyệt: "' . ($meta['title'] ?? '') . '"',
                        'description' => 'Tác giả: ' . ($meta['author'] ?? $actorName),
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.blog.posts.pending'),
                        'icon' => 'article',
                        'badge_class' => 'bg-blue-100 text-blue-800',
                    ];
                    break;

                case 'blog.commented':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'blog',
                        'category_label' => 'Bình luận bài viết',
                        'title' => ($meta['user_name'] ?? $actorName) . ' bình luận về bài viết "' . ($meta['blog_title'] ?? '') . '"',
                        'description' => $meta['content'] ?? 'Bình luận mới từ người dùng',
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => !empty($meta['blog_slug']) ? route('blog.show', $meta['blog_slug']) : route('admin.blog.posts.index'),
                        'icon' => 'chat',
                        'badge_class' => 'bg-sky-100 text-sky-800',
                    ];
                    break;

                case 'blog.hero_toggled':
                case 'blog.deleted':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'blog',
                        'category_label' => ($action === 'blog.hero_toggled') ? 'Tiêu điểm Blog' : 'Xóa bài viết',
                        'title' => ($action === 'blog.hero_toggled')
                            ? ('Chọn làm bài viết Tiêu Điểm: "' . ($meta['title'] ?? '') . '"')
                            : ('Đã xóa bài viết: "' . ($meta['title'] ?? '') . '"'),
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => route('admin.blog.posts.index'),
                        'icon' => ($action === 'blog.hero_toggled') ? 'featured_play_list' : 'delete',
                        'badge_class' => ($action === 'blog.hero_toggled') ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700',
                    ];
                    break;

                case 'security.updated':
                case 'seo.updated':
                    $item = [
                        'id' => 'audit_' . $log->id,
                        'item_id' => $log->id,
                        'type' => 'system',
                        'category_label' => 'Cấu hình hệ thống',
                        'title' => ($action === 'security.updated') ? 'Cập nhật thiết lập bảo mật' : ('Cập nhật SEO trang ' . ($meta['trang'] ?? '')),
                        'description' => 'Thực hiện bởi: ' . $actorName,
                        'time_ago' => $timeAgo,
                        'created_at' => $createdAtStr,
                        'created_at_formatted' => $createdAtFmt,
                        'url' => ($action === 'security.updated') ? route('admin.bao-mat.index') : route('admin.seo.index'),
                        'icon' => ($action === 'security.updated') ? 'security' : 'travel_explore',
                        'badge_class' => 'bg-slate-200 text-slate-700',
                    ];
                    break;
            }

            if ($item) {
                $allNotifications[] = $item;
            }
        }

        // Thêm thông báo trực tiếp từ bảng notifications gửi riêng cho Admin (nếu có)
        if ($admin && method_exists($admin, 'notifications')) {
            try {
                $dbNotifs = $admin->notifications()->latest()->take(30)->get();
                foreach ($dbNotifs as $dn) {
                    $d = $dn->data ?? [];
                    $dTitle = $d['title'] ?? 'Thông báo hệ thống';
                    $dMsg = $d['message'] ?? '';
                    $dUrl = !empty($d['link']) ? $d['link'] : \App\Http\Controllers\NguoiDung\ThongBaoController::resolveTargetLink($d);
                    $allNotifications[] = [
                        'id' => 'db_' . $dn->id,
                        'item_id' => $dn->id,
                        'type' => 'user_notification',
                        'category_label' => $d['status_label'] ?? 'Thông báo',
                        'title' => $dTitle,
                        'description' => $dMsg,
                        'time_ago' => $dn->created_at ? $dn->created_at->diffForHumans() : '',
                        'created_at' => $dn->created_at ? $dn->created_at->toIso8601String() : '',
                        'created_at_formatted' => $dn->created_at ? $dn->created_at->format('d/m/Y H:i') : '',
                        'url' => $dUrl,
                        'icon' => 'notifications',
                        'badge_class' => 'bg-indigo-100 text-indigo-700',
                        'read' => $dn->read_at !== null,
                    ];
                }
            } catch (\Throwable $e) {}
        }

        // Sắp xếp giảm dần theo thời gian tạo mới nhất
        usort($allNotifications, function ($a, $b) {
            return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
        });

        // Đảm bảo không trùng lặp ID
        $deduped = [];
        $seenIds = [];
        foreach ($allNotifications as $n) {
            if (!isset($seenIds[$n['id']])) {
                $seenIds[$n['id']] = true;
                $deduped[] = $n;
            }
        }
        $allNotifications = array_slice($deduped, 0, 60);

        // Xử lý trạng thái Đã đọc / Chưa đọc của Admin
        $caiDat = $admin?->cai_dat ?? [];
        if (is_string($caiDat)) {
            $caiDat = json_decode($caiDat, true) ?: [];
        }
        $readAdminNotifs = array_map('strval', $caiDat['read_admin_notifs'] ?? []);
        $lastReadAt = !empty($caiDat['last_read_admin_notifs_at']) ? strtotime($caiDat['last_read_admin_notifs_at']) : 0;

        foreach ($allNotifications as &$item) {
            $itemCreatedTime = !empty($item['created_at']) ? strtotime($item['created_at']) : 0;
            $itemIdStr = (string) $item['id'];
            $isRead = in_array($itemIdStr, $readAdminNotifs, true) || ($lastReadAt > 0 && $itemCreatedTime <= $lastReadAt);
            $item['read'] = (bool) $isRead;
        }
        unset($item);

        $activeNotifications = array_values(array_filter($allNotifications, fn($n) => empty($n['read'])));
        $unreadAdminCount = count($activeNotifications);
        $totalPending = $pendingQuanCount + $pendingBlogCount + $unverifiedUserCount;

        return [
            'success' => true,
            'unread_count' => $unreadAdminCount,
            'total_quan' => $totalQuanCount,
            'total_users' => $totalUserCount,
            'active_users' => $activeUserCount,
            'total_blogs' => $totalBlogCount,
            'total_pending' => $totalPending,
            'pending_quan' => $pendingQuanCount,
            'pending_blog' => $pendingBlogCount,
            'unverified_user' => $unverifiedUserCount,
            'broadcast_count' => $broadcastCount,
            'pending_quans' => $pendingQuans,
            'pending_blogs' => $pendingBlogs,
            'unverified_users' => $unverifiedUsers,
            'notifications' => $activeNotifications,
            'all_notifications' => $allNotifications,
            'latest_quan' => $pendingQuans->first() ?? null,
            'latest_blog' => $pendingBlogs->first() ?? null,
            'latest_user' => $unverifiedUsers->first() ?? null,
            'latest_broadcast' => $recentAuditLogs->firstWhere('action', 'system.broadcast'),
        ];
    }
}
