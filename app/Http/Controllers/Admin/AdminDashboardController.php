<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Blog;
use App\Models\Quan;
use App\Models\ThanhToan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Cards overview stats (cached 30s for lightning speed response)
        $overviewStats = Cache::remember('admin_dashboard_overview_stats', 30, function () {
            $hasThanhToan = Schema::hasTable('thanh_toan');
            return [
                'total_quan'    => Quan::count(),
                'pending_quan'  => Quan::where('trang_thai', 'chua_duyet')->count(),
                'total_users'   => User::count(),
                'doanh_thu_goi' => $hasThanhToan ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->sum('so_tien') : 0,
                'total_blogs'   => Blog::count(),
                'active_users'  => User::where('trang_thai', 'hoat_dong')->count(),
                'featured_quan' => Quan::where('is_noi_bat', true)->count(),
                'has_thanh_toan'=> $hasThanhToan,
            ];
        });

        $stats = [
            'total_quan'      => $overviewStats['total_quan'],
            'pending_quan'    => $overviewStats['pending_quan'],
            'total_users'     => $overviewStats['total_users'],
            'doanh_thu_goi'   => $overviewStats['doanh_thu_goi'],
            'total_blogs'     => $overviewStats['total_blogs'],
            'active_users'    => $overviewStats['active_users'],
            'featured_quan'   => $overviewStats['featured_quan'],
            'recent_logs'     => AdminAuditLog::with('admin')->latest()->take(5)->get(),
            'recent_users'    => User::latest()->take(5)->get(),
            'recent_quan'     => Quan::latest()->take(5)->get(),
            'recent_payments' => $overviewStats['has_thanh_toan'] ? ThanhToan::with(['user', 'quan'])->latest()->take(5)->get() : collect(),
        ];

        // 2. Monthly registration & revenue chart data (cached 60s for ultra-fast response)
        $chartData = Cache::remember('admin_dashboard_charts_6m', 60, function () {
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

            return [
                'labels' => $chartLabels,
                'quan' => $quanMonthly,
                'users' => $usersMonthly,
                'revenue' => $revenueMonthly,
            ];
        });

        return view('admin.dashboard', compact('stats', 'chartData'));
    }
}
