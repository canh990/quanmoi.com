<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Quan;
use App\Models\ThanhToan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ThongKeController extends Controller
{
    public function index(Request $request)
    {
        $tuNgayStr = $request->input('tu_ngay', now()->subDays(30)->format('Y-m-d'));
        $denNgayStr = $request->input('den_ngay', now()->format('Y-m-d'));

        $tuNgay = Carbon::parse($tuNgayStr)->startOfDay();
        $denNgay = Carbon::parse($denNgayStr)->endOfDay();

        // Overview stats
        $quanStats = [
            'total' => Quan::count(),
            'filtered' => Quan::whereBetween('created_at', [$tuNgay, $denNgay])->count(),
            'da_duyet' => Quan::where('trang_thai', 'da_duyet')->count(),
            'chua_duyet' => Quan::where('trang_thai', 'chua_duyet')->count(),
            'noi_bat' => Quan::where('is_noi_bat', true)->count(),
        ];

        $userStats = [
            'total' => User::count(),
            'filtered' => User::whereBetween('created_at', [$tuNgay, $denNgay])->count(),
            'hoat_dong' => User::where('trang_thai', 'hoat_dong')->count(),
            'bi_khoa' => User::where('trang_thai', 'bi_khoa')->count(),
        ];

        $revenueStats = [
            'total' => Schema::hasTable('thanh_toan') ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->sum('so_tien') : 0,
            'filtered' => Schema::hasTable('thanh_toan') ? (float) ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$tuNgay, $denNgay])->sum('so_tien') : 0,
            'count_filtered' => Schema::hasTable('thanh_toan') ? ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$tuNgay, $denNgay])->count() : 0,
        ];

        $blogStats = [
            'total' => Blog::count(),
            'filtered' => Blog::whereBetween('created_at', [$tuNgay, $denNgay])->count(),
            'published' => Blog::where('status', 'published')->count(),
        ];

        // Detailed payments list for table
        $payments = Schema::hasTable('thanh_toan')
            ? ThanhToan::with(['user', 'quan'])
                ->whereBetween('created_at', [$tuNgay, $denNgay])
                ->latest()
                ->paginate(15)
                ->withQueryString()
            : collect();

        return view('admin.thong-ke.index', compact(
            'tuNgayStr',
            'denNgayStr',
            'quanStats',
            'userStats',
            'revenueStats',
            'blogStats',
            'payments'
        ));
    }

    public function export(Request $request)
    {
        $tuNgayStr = $request->input('tu_ngay', now()->subDays(30)->format('Y-m-d'));
        $denNgayStr = $request->input('den_ngay', now()->format('Y-m-d'));

        $tuNgay = Carbon::parse($tuNgayStr)->startOfDay();
        $denNgay = Carbon::parse($denNgayStr)->endOfDay();

        $fileName = 'thong_ke_quanmoi_' . $tuNgayStr . '_den_' . $denNgayStr . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($tuNgay, $denNgay) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['BÁO CÁO THỐNG KÊ QUÁN MỚI SYSTEM']);
            fputcsv($file, ['Khoảng thời gian', $tuNgay->format('d/m/Y') . ' - ' . $denNgay->format('d/m/Y')]);
            fputcsv($file, ['Ngày xuất file', now()->format('d/m/Y H:i:s')]);
            fputcsv($file, []);

            fputcsv($file, ['CHỈ SỐ TỔNG QUAN', 'TỔNG CỘNG', 'TRONG KHOẢNG THỜI GIAN LỌC']);
            fputcsv($file, ['Tổng số Quán', Quan::count(), Quan::whereBetween('created_at', [$tuNgay, $denNgay])->count()]);
            fputcsv($file, ['Tổng Người dùng', User::count(), User::whereBetween('created_at', [$tuNgay, $denNgay])->count()]);
            fputcsv($file, ['Doanh thu gói (VND)', Schema::hasTable('thanh_toan') ? ThanhToan::where('trang_thai', 'thanh_cong')->sum('so_tien') : 0, Schema::hasTable('thanh_toan') ? ThanhToan::where('trang_thai', 'thanh_cong')->whereBetween('created_at', [$tuNgay, $denNgay])->sum('so_tien') : 0]);
            fputcsv($file, ['Số bài viết Blog', Blog::count(), Blog::whereBetween('created_at', [$tuNgay, $denNgay])->count()]);
            fputcsv($file, []);

            fputcsv($file, ['DANH SÁCH GIAO DỊCH GÓI DỊCH VỤ TRONG KHOẢNG THỜI GIAN']);
            fputcsv($file, ['Mã giao dịch', 'Người dùng', 'Quán', 'Gói dịch vụ', 'Số tiền (VND)', 'Phương thức', 'Trạng thái', 'Ngày tạo']);

            if (Schema::hasTable('thanh_toan')) {
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
                        strtoupper($tx->phuong_thuc),
                        $tx->trang_thai === 'thanh_cong' ? 'Thành công' : $tx->trang_thai,
                        $tx->created_at->format('d/m/Y H:i'),
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
