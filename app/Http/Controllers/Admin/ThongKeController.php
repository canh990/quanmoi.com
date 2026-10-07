<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Quan;
use App\Models\ThanhToan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ThongKeController extends Controller
{
    public function index(Request $request)
    {
        $preset = $request->input('preset');
        $tuNgayStr = $request->input('tu_ngay');
        $denNgayStr = $request->input('den_ngay');

        // Preset handling if selected
        if ($preset) {
            switch ($preset) {
                case 'today':
                    $tuNgay = now()->startOfDay();
                    $denNgay = now()->endOfDay();
                    break;
                case '7days':
                    $tuNgay = now()->subDays(6)->startOfDay();
                    $denNgay = now()->endOfDay();
                    break;
                case '30days':
                    $tuNgay = now()->subDays(29)->startOfDay();
                    $denNgay = now()->endOfDay();
                    break;
                case 'this_month':
                    $tuNgay = now()->startOfMonth();
                    $denNgay = now()->endOfMonth();
                    break;
                case 'last_month':
                    $tuNgay = now()->subMonth()->startOfMonth();
                    $denNgay = now()->subMonth()->endOfMonth();
                    break;
                case 'this_year':
                    $tuNgay = now()->startOfYear();
                    $denNgay = now()->endOfYear();
                    break;
                default:
                    $tuNgay = now()->subDays(29)->startOfDay();
                    $denNgay = now()->endOfDay();
                    break;
            }
            $tuNgayStr = $tuNgay->format('Y-m-d');
            $denNgayStr = $denNgay->format('Y-m-d');
        } else {
            $tuNgayStr = $tuNgayStr ?? now()->subDays(29)->format('Y-m-d');
            $denNgayStr = $denNgayStr ?? now()->format('Y-m-d');

            try {
                $tuNgay = Carbon::parse($tuNgayStr)->startOfDay();
            } catch (\Throwable $e) {
                $tuNgayStr = now()->subDays(29)->format('Y-m-d');
                $tuNgay = now()->subDays(29)->startOfDay();
            }

            try {
                $denNgay = Carbon::parse($denNgayStr)->endOfDay();
            } catch (\Throwable $e) {
                $denNgayStr = now()->format('Y-m-d');
                $denNgay = now()->endOfDay();
            }

            if ($tuNgay > $denNgay) {
                $temp = $tuNgay;
                $tuNgay = $denNgay->copy()->startOfDay();
                $denNgay = $temp->copy()->endOfDay();
                $tuNgayStr = $tuNgay->format('Y-m-d');
                $denNgayStr = $denNgay->format('Y-m-d');
            }
        }

        // 1. Overview KPIs
        $quanStats = [
            'total' => Quan::count(),
            'filtered' => Quan::whereBetween('created_at', [$tuNgay, $denNgay])->count(),
            'da_duyet' => Quan::where('trang_thai', 'da_duyet')->count(),
            'chua_duyet' => Quan::where('trang_thai', 'chua_duyet')->count(),
            'bi_khoa' => Quan::where('trang_thai', 'bi_khoa')->count(),
            'noi_bat' => Quan::where('is_noi_bat', true)->count(),
            'xac_thuc' => Quan::where('is_xac_thuc', true)->count(),
            'total_views' => Quan::sum('luot_xem') ?? 0,
        ];

        $userStats = [
            'total' => User::count(),
            'filtered' => User::whereBetween('created_at', [$tuNgay, $denNgay])->count(),
            'hoat_dong' => User::where('trang_thai', 'hoat_dong')->count(),
            'bi_khoa' => User::where('trang_thai', 'bi_khoa')->count(),
        ];

        $hasThanhToan = Schema::hasTable('thanh_toan');

        $revenueStats = [
            'total' => $hasThanhToan ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->sum('so_tien') : 0,
            'filtered' => $hasThanhToan ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$tuNgay, $denNgay])->sum('so_tien') : 0,
            'count_filtered' => $hasThanhToan ? ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$tuNgay, $denNgay])->count() : 0,
            'pending_count' => $hasThanhToan ? ThanhToan::where('trang_thai', 'cho_xu_ly')->whereBetween('created_at', [$tuNgay, $denNgay])->count() : 0,
            'failed_count' => $hasThanhToan ? ThanhToan::where('trang_thai', 'that_bai')->whereBetween('created_at', [$tuNgay, $denNgay])->count() : 0,
        ];

        $blogStats = [
            'total' => Blog::count(),
            'filtered' => Blog::whereBetween('created_at', [$tuNgay, $denNgay])->count(),
            'published' => Blog::where('status', 'published')->count(),
        ];

        // 2. Chart data generation over selected date range
        $diffInDays = $tuNgay->diffInDays($denNgay);
        $chartLabels = [];
        $chartRevenue = [];
        $chartQuan = [];
        $chartUsers = [];

        if ($diffInDays <= 60) {
            // Daily interval
            $current = $tuNgay->copy();
            while ($current <= $denNgay) {
                $dayStart = $current->copy()->startOfDay();
                $dayEnd = $current->copy()->endOfDay();
                $dateKey = $current->format('d/m');

                $chartLabels[] = $dateKey;
                $chartQuan[] = Quan::whereBetween('created_at', [$dayStart, $dayEnd])->count();
                $chartUsers[] = User::whereBetween('created_at', [$dayStart, $dayEnd])->count();
                $chartRevenue[] = $hasThanhToan
                    ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$dayStart, $dayEnd])->sum('so_tien')
                    : 0;

                $current->addDay();
            }
        } else {
            // Monthly interval
            $current = $tuNgay->copy()->startOfMonth();
            while ($current <= $denNgay) {
                $mStart = $current->copy()->startOfMonth();
                $mEnd = $current->copy()->endOfMonth();
                $dateKey = 'T' . $current->format('m/Y');

                $chartLabels[] = $dateKey;
                $chartQuan[] = Quan::whereBetween('created_at', [$mStart, $mEnd])->count();
                $chartUsers[] = User::whereBetween('created_at', [$mStart, $mEnd])->count();
                $chartRevenue[] = $hasThanhToan
                    ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$mStart, $mEnd])->sum('so_tien')
                    : 0;

                $current->addMonth();
            }
        }

        $chartData = [
            'labels' => $chartLabels,
            'revenue' => $chartRevenue,
            'quan' => $chartQuan,
            'users' => $chartUsers,
        ];

        // 3. Top Most Viewed Venues
        $topVenues = Quan::with('chuQuan')
            ->orderBy('luot_xem', 'desc')
            ->take(6)
            ->get();

        // 4. Regional Breakdown (Thống kê theo Tỉnh/Thành phố)
        $regionStats = Quan::select('ten_tinh_thanh', DB::raw('COUNT(*) as total_quan'), DB::raw('SUM(luot_xem) as total_views'))
            ->whereNotNull('ten_tinh_thanh')
            ->where('ten_tinh_thanh', '!=', '')
            ->groupBy('ten_tinh_thanh')
            ->orderBy('total_quan', 'desc')
            ->take(6)
            ->get();

        // 5. Package Breakdown (Doanh thu theo Gói dịch vụ)
        $packageStats = $hasThanhToan
            ? ThanhToan::where('trang_thai', 'thanh_cong')
                ->whereBetween('created_at', [$tuNgay, $denNgay])
                ->select('ten_goi_dich_vu', DB::raw('COUNT(*) as total_count'), DB::raw('SUM(so_tien) as total_revenue'))
                ->groupBy('ten_goi_dich_vu')
                ->orderBy('total_revenue', 'desc')
                ->get()
            : collect();

        // 6. Detailed Payments List with Filter & Search
        $statusFilter = $request->input('trang_thai_tt');
        $searchKey = $request->input('search');

        $query = $hasThanhToan ? ThanhToan::with(['user', 'quan'])->whereBetween('created_at', [$tuNgay, $denNgay]) : null;

        if ($query) {
            if ($statusFilter) {
                $query->where('trang_thai', $statusFilter);
            }
            if ($searchKey) {
                $query->where(function ($q) use ($searchKey) {
                    $q->where('ma_giao_dich', 'like', "%{$searchKey}%")
                      ->orWhere('ten_goi_dich_vu', 'like', "%{$searchKey}%")
                      ->orWhereHas('user', function ($uq) use ($searchKey) {
                          $uq->where('ho_ten', 'like', "%{$searchKey}%")
                             ->orWhere('email', 'like', "%{$searchKey}%");
                      })
                      ->orWhereHas('quan', function ($qq) use ($searchKey) {
                          $qq->where('ten_quan', 'like', "%{$searchKey}%");
                      });
                });
            }
            $payments = $query->latest()->paginate(15)->withQueryString();
        } else {
            $payments = collect();
        }

        return view('admin.thong-ke.index', compact(
            'preset',
            'tuNgayStr',
            'denNgayStr',
            'quanStats',
            'userStats',
            'revenueStats',
            'blogStats',
            'chartData',
            'topVenues',
            'regionStats',
            'packageStats',
            'payments',
            'statusFilter',
            'searchKey'
        ));
    }

    public function export(Request $request)
    {
        $preset = $request->input('preset');
        $tuNgayStr = $request->input('tu_ngay');
        $denNgayStr = $request->input('den_ngay');

        if ($preset) {
            switch ($preset) {
                case 'today':
                    $tuNgay = now()->startOfDay();
                    $denNgay = now()->endOfDay();
                    break;
                case '7days':
                    $tuNgay = now()->subDays(6)->startOfDay();
                    $denNgay = now()->endOfDay();
                    break;
                case '30days':
                    $tuNgay = now()->subDays(29)->startOfDay();
                    $denNgay = now()->endOfDay();
                    break;
                case 'this_month':
                    $tuNgay = now()->startOfMonth();
                    $denNgay = now()->endOfMonth();
                    break;
                case 'last_month':
                    $tuNgay = now()->subMonth()->startOfMonth();
                    $denNgay = now()->subMonth()->endOfMonth();
                    break;
                case 'this_year':
                    $tuNgay = now()->startOfYear();
                    $denNgay = now()->endOfYear();
                    break;
                default:
                    $tuNgay = now()->subDays(29)->startOfDay();
                    $denNgay = now()->endOfDay();
                    break;
            }
            $tuNgayStr = $tuNgay->format('Y-m-d');
            $denNgayStr = $denNgay->format('Y-m-d');
        } else {
            $tuNgayStr = $tuNgayStr ?? now()->subDays(29)->format('Y-m-d');
            $denNgayStr = $denNgayStr ?? now()->format('Y-m-d');

            try {
                $tuNgay = Carbon::parse($tuNgayStr)->startOfDay();
            } catch (\Throwable $e) {
                $tuNgayStr = now()->subDays(29)->format('Y-m-d');
                $tuNgay = now()->subDays(29)->startOfDay();
            }

            try {
                $denNgay = Carbon::parse($denNgayStr)->endOfDay();
            } catch (\Throwable $e) {
                $denNgayStr = now()->format('Y-m-d');
                $denNgay = now()->endOfDay();
            }

            if ($tuNgay > $denNgay) {
                $temp = $tuNgay;
                $tuNgay = $denNgay->copy()->startOfDay();
                $denNgay = $temp->copy()->endOfDay();
                $tuNgayStr = $tuNgay->format('Y-m-d');
                $denNgayStr = $denNgay->format('Y-m-d');
            }
        }

        $fileName = 'baocao_thongke_quanmoi_' . $tuNgayStr . '_den_' . $denNgayStr . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($tuNgay, $denNgay) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['BÁO CÁO THỐNG KÊ CHI TIẾT - HỆ THỐNG QUÁN MỚI']);
            fputcsv($file, ['Khoảng thời gian báo cáo', $tuNgay->format('d/m/Y') . ' - ' . $denNgay->format('d/m/Y')]);
            fputcsv($file, ['Thời điểm xuất báo cáo', now()->format('d/m/Y H:i:s')]);
            fputcsv($file, []);

            // 1. Overview Section
            fputcsv($file, ['I. CHỈ SỐ TỔNG QUAN HỆ THỐNG']);
            fputcsv($file, ['Chỉ số', 'Tổng toàn hệ thống', 'Mới trong khoảng thời gian lọc']);
            fputcsv($file, ['Tổng số địa điểm Quán', Quan::count(), Quan::whereBetween('created_at', [$tuNgay, $denNgay])->count()]);
            fputcsv($file, ['Quán đã được duyệt', Quan::where('trang_thai', 'da_duyet')->count(), 'N/A']);
            fputcsv($file, ['Quán chờ kiểm duyệt', Quan::where('trang_thai', 'chua_duyet')->count(), 'N/A']);
            fputcsv($file, ['Quán được xác thực', Quan::where('is_xac_thuc', true)->count(), 'N/A']);
            fputcsv($file, ['Quán đánh dấu nổi bật', Quan::where('is_noi_bat', true)->count(), 'N/A']);
            fputcsv($file, ['Tổng người dùng hệ thống', User::count(), User::whereBetween('created_at', [$tuNgay, $denNgay])->count()]);
            fputcsv($file, ['Người dùng đang hoạt động', User::where('trang_thai', 'hoat_dong')->count(), 'N/A']);
            
            $hasThanhToan = Schema::hasTable('thanh_toan');
            $totalRevAll = $hasThanhToan ? ThanhToan::where('trang_thai', 'thanh_cong')->sum('so_tien') : 0;
            $totalRevFiltered = $hasThanhToan ? ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$tuNgay, $denNgay])->sum('so_tien') : 0;
            
            fputcsv($file, ['Doanh thu dịch vụ (VND)', number_format($totalRevAll, 0, ',', '.'), number_format($totalRevFiltered, 0, ',', '.')]);
            fputcsv($file, ['Tổng số bài viết Blog', Blog::count(), Blog::whereBetween('created_at', [$tuNgay, $denNgay])->count()]);
            fputcsv($file, []);

            // 2. Package Revenue Section
            fputcsv($file, ['II. DOANH THU THEO GÓI DỊCH VỤ TRONG KỲ LỌC']);
            fputcsv($file, ['Tên gói dịch vụ', 'Số lượt đăng ký thành công', 'Tổng doanh thu (VND)']);

            if ($hasThanhToan) {
                $packages = ThanhToan::where('trang_thai', 'thanh_cong')
                    ->whereBetween('created_at', [$tuNgay, $denNgay])
                    ->select('ten_goi_dich_vu', DB::raw('COUNT(*) as total_count'), DB::raw('SUM(so_tien) as total_revenue'))
                    ->groupBy('ten_goi_dich_vu')
                    ->orderBy('total_revenue', 'desc')
                    ->get();

                foreach ($packages as $pkg) {
                    fputcsv($file, [
                        $pkg->ten_goi_dich_vu ?? 'Gói mặc định',
                        $pkg->total_count,
                        number_format($pkg->total_revenue, 0, ',', '.'),
                    ]);
                }
            }
            fputcsv($file, []);

            // 3. Top Venues Section
            fputcsv($file, ['III. TOP QUÁN CÓ LƯỢT XEM CAO NHẤT']);
            fputcsv($file, ['Tên quán', 'Chủ quán', 'Địa bàn (Tỉnh/Thành)', 'Trạng thái', 'Lượt xem']);

            $topVenues = Quan::with('chuQuan')->orderBy('luot_xem', 'desc')->take(10)->get();
            foreach ($topVenues as $tv) {
                fputcsv($file, [
                    $tv->ten_quan,
                    $tv->chuQuan->ho_ten ?? 'N/A',
                    $tv->ten_tinh_thanh ?? 'Chưa cập nhật',
                    $tv->trang_thai === 'da_duyet' ? 'Đã duyệt' : 'Chưa duyệt',
                    number_format($tv->luot_xem ?? 0),
                ]);
            }
            fputcsv($file, []);

            // 4. Detailed Payment Transactions
            fputcsv($file, ['IV. DANH SÁCH CHI TIẾT GIAO DỊCH TRONG KỲ LỌC']);
            fputcsv($file, ['Mã giao dịch', 'Tài khoản người dùng', 'Quán liên quan', 'Gói dịch vụ', 'Số tiền (VND)', 'Phương thức', 'Trạng thái', 'Ngày tạo']);

            if ($hasThanhToan) {
                $transactions = ThanhToan::with(['user', 'quan'])
                    ->whereBetween('created_at', [$tuNgay, $denNgay])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($transactions as $tx) {
                    fputcsv($file, [
                        $tx->ma_giao_dich,
                        $tx->user->ho_ten ?? 'N/A',
                        $tx->quan->ten_quan ?? 'N/A',
                        $tx->ten_goi_dich_vu,
                        number_format($tx->so_tien, 0, ',', '.'),
                        strtoupper($tx->phuong_thuc ?? 'VNPAY'),
                        $tx->trang_thai === 'thanh_cong' ? 'Thành công' : ($tx->trang_thai === 'cho_xu_ly' ? 'Chờ xử lý' : 'Thất bại'),
                        $tx->created_at ? $tx->created_at->format('d/m/Y H:i') : '',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
