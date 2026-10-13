<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Blog;
use App\Models\Quan;
use App\Models\User;
use Illuminate\Http\Request;

class ThongBaoAdminController extends Controller
{
    public function index()
    {
        $notifications = AdminAuditLog::where('action', 'system.broadcast')
            ->with('actor')
            ->latest()
            ->paginate(15);

        $pendingQuanCount = Quan::where('trang_thai', 'chua_duyet')->count();
        $pendingBlogCount = Blog::where('status', 'pending')->count();
        $unverifiedUserCount = User::where('da_xac_thuc', false)->count();
        $broadcastCount = AdminAuditLog::where('action', 'system.broadcast')->count();

        return view('admin.thong-bao.index', compact(
            'notifications',
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

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã gửi thông báo hệ thống thành công!',
                'notification' => [
                    'id' => $log ? $log->id : null,
                    'tieu_de' => $validated['tieu_de'],
                    'noi_dung' => $validated['noi_dung'],
                    'doi_tuong' => $validated['doi_tuong'],
                    'actor_name' => $request->user()?->ho_ten ?? 'Ban quản trị',
                    'created_at' => now()->format('d/m/Y H:i'),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Đã gửi thông báo hệ thống thành công!');
    }

    /**
     * Real-time polling API for Admin notifications dashboard & topbar.
     */
    public function realtimeCheck(Request $request)
    {
        $totalQuanCount = Quan::count();
        $totalUserCount = User::count();
        $activeUserCount = User::where('trang_thai', 'hoat_dong')->count();
        $totalBlogCount = Blog::count();
        $pendingQuanCount = Quan::where('trang_thai', 'chua_duyet')->count();
        $pendingBlogCount = Blog::where('status', 'pending')->count();
        $unverifiedUserCount = User::where('da_xac_thuc', false)->count();
        $broadcastCount = AdminAuditLog::where('action', 'system.broadcast')->count();

        // 1. Danh sách Quán chờ duyệt
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
                    'created_at' => $q->created_at?->format('d/m/Y H:i'),
                    'created_at_formatted' => $q->created_at?->format('d/m/Y H:i'),
                    'time_ago' => $q->created_at?->diffForHumans(),
                    'url' => route('admin.duyet-xac-thuc.index'),
                ];
            });

        // 2. Danh sách Blog chờ duyệt
        $pendingBlogs = Blog::where('status', 'pending')
            ->with(['user:id,ho_ten,email'])
            ->select('id', 'user_id', 'title', 'created_at', 'slug')
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'title' => $b->title,
                    'author' => $b->user?->ho_ten,
                    'created_at' => $b->created_at?->toIso8601String(),
                    'time_ago' => $b->created_at?->diffForHumans(),
                    'url' => route('admin.blog.posts.pending'),
                ];
            });

        // 3. Danh sách Người dùng chưa xác thực
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

        // 4. Danh sách thông báo hệ thống gần nhất
        $latestBroadcasts = AdminAuditLog::where('action', 'system.broadcast')
            ->with('actor:id,ho_ten')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'tieu_de' => $log->metadata['tieu_de'] ?? 'Thông báo hệ thống',
                    'noi_dung' => $log->metadata['noi_dung'] ?? '',
                    'doi_tuong' => $log->metadata['doi_tuong'] ?? 'tat_ca',
                    'sender' => $log->actor?->ho_ten ?? 'Ban quản trị',
                    'created_at' => $log->created_at?->toIso8601String(),
                    'created_at_formatted' => $log->created_at?->format('d/m/Y H:i'),
                    'time_ago' => $log->created_at?->diffForHumans(),
                    'url' => route('admin.thong-bao.index'),
                ];
            });

        // 5. Tổng hợp toàn bộ danh sách thông báo để hiển thị trên Dropdown & Notification Center
        $allNotifications = [];

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

        foreach ($latestBroadcasts as $bc) {
            $allNotifications[] = [
                'id' => 'broadcast_' . $bc['id'],
                'item_id' => $bc['id'],
                'type' => 'broadcast',
                'category_label' => 'Thông báo hệ thống',
                'title' => $bc['tieu_de'],
                'description' => $bc['noi_dung'],
                'time_ago' => $bc['time_ago'],
                'created_at' => $bc['created_at'],
                'created_at_formatted' => $bc['created_at_formatted'] ?? '',
                'url' => $bc['url'],
                'icon' => 'campaign',
                'badge_class' => 'bg-emerald-100 text-emerald-700',
            ];
        }

        // Sắp xếp giảm dần theo thời gian tạo mới nhất
        usort($allNotifications, function ($a, $b) {
            return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
        });

        $totalPending = $pendingQuanCount + $pendingBlogCount + $unverifiedUserCount;

        return response()->json([
            'success' => true,
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
            'notifications' => array_slice($allNotifications, 0, 50),
            'latest_quan' => $pendingQuans->first() ?? null,
            'latest_blog' => $pendingBlogs->first() ?? null,
            'latest_user' => $unverifiedUsers->first() ?? null,
            'latest_broadcast' => $latestBroadcasts->first() ?? null,
        ]);
    }
}
