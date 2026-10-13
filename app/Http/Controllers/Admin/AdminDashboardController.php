<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Blog;
use App\Models\Quan;
use App\Models\ThanhToan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Cards overview stats
        $totalQuan = Quan::count();
        $pendingQuan = Quan::where('trang_thai', 'chua_duyet')->count();
        $totalUsers = User::count();
        
        $doanhThuGoi = Schema::hasTable('thanh_toan')
            ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->sum('so_tien')
            : 0;

        $totalBlogs = Blog::count();

        $stats = [
            'total_quan' => $totalQuan,
            'pending_quan' => $pendingQuan,
            'total_users' => $totalUsers,
            'doanh_thu_goi' => $doanhThuGoi,
            'total_blogs' => $totalBlogs,
            'active_users' => User::where('trang_thai', 'hoat_dong')->count(),
            'featured_quan' => Quan::where('is_noi_bat', true)->count(),
            'recent_logs' => AdminAuditLog::with('admin')->latest()->take(5)->get(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_quan' => Quan::latest()->take(5)->get(),
            'recent_payments' => Schema::hasTable('thanh_toan') ? ThanhToan::with(['user', 'quan'])->latest()->take(5)->get() : collect(),
        ];

        // 2. Monthly registration & revenue chart data (last 6 months)
        $chartLabels = [];
        $quanMonthly = [];
        $usersMonthly = [];
        $revenueMonthly = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $start = $monthDate->copy()->startOfMonth();
            $end = $monthDate->copy()->endOfMonth();

            $chartLabels[] = 'T' . $monthDate->format('m/Y');
            $quanMonthly[] = Quan::whereBetween('created_at', [$start, $end])->count();
            $usersMonthly[] = User::whereBetween('created_at', [$start, $end])->count();
            $revenueMonthly[] = Schema::hasTable('thanh_toan') 
                ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$start, $end])->sum('so_tien')
                : 0;
        }

        $chartData = [
            'labels' => $chartLabels,
            'quan' => $quanMonthly,
            'users' => $usersMonthly,
            'revenue' => $revenueMonthly,
        ];

        return view('admin.dashboard', compact('stats', 'chartData'));
    }
}
