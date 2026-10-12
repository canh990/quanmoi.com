<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Quan;
use App\Models\User;
use Illuminate\Http\Request;

class ThongBaoAdminController extends Controller
{
    public function index()
    {
        $notifications = []; // Placeholder for system admin notifications list
        return view('admin.thong-bao.index', compact('notifications'));
    }

    public function sendSystemNotification(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'doi_tuong' => 'required|in:tat_ca,chu_quan,nguoi_dung',
        ]);

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

        $latestQuan = Quan::where('trang_thai', 'chua_duyet')
            ->with(['chuQuan:id,ho_ten,email,so_dien_thoai'])
            ->select('id', 'chu_quan_id', 'ten_quan', 'loai_hinh_kinh_doanh', 'dia_chi_chi_tiet', 'created_at', 'slug')
            ->latest()
            ->first();

        $latestBlog = Blog::where('status', 'pending')
            ->with(['user:id,ho_ten,email'])
            ->select('id', 'user_id', 'title', 'created_at', 'slug')
            ->latest()
            ->first();

        $latestUser = User::latest()->select('id', 'ho_ten', 'email', 'so_dien_thoai', 'created_at')->first();

        $totalPending = $pendingQuanCount + $pendingBlogCount;

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
            'latest_quan' => $latestQuan ? [
                'id' => $latestQuan->id,
                'ten_quan' => $latestQuan->ten_quan,
                'loai_hinh' => $latestQuan->loai_hinh_kinh_doanh,
                'dia_chi' => $latestQuan->dia_chi_chi_tiet,
                'nguoi_dang' => $latestQuan->chuQuan ? [
                    'ho_ten' => $latestQuan->chuQuan->ho_ten,
                    'email' => $latestQuan->chuQuan->email,
                    'so_dien_thoai' => $latestQuan->chuQuan->so_dien_thoai,
                ] : null,
                'created_at' => $latestQuan->created_at->toIso8601String(),
                'time_ago' => $latestQuan->created_at->diffForHumans(),
                'url' => route('admin.duyet-xac-thuc.index'),
            ] : null,
            'latest_blog' => $latestBlog ? [
                'id' => $latestBlog->id,
                'title' => $latestBlog->title,
                'created_at' => $latestBlog->created_at->toIso8601String(),
                'time_ago' => $latestBlog->created_at->diffForHumans(),
                'url' => route('admin.blog.posts.pending'),
            ] : null,
            'latest_user' => $latestUser ? [
                'id' => $latestUser->id,
                'ho_ten' => $latestUser->ho_ten,
                'email' => $latestUser->email,
                'created_at' => $latestUser->created_at->toIso8601String(),
                'url' => route('admin.nguoi-dung.index'),
            ] : null,
        ]);
    }
}
