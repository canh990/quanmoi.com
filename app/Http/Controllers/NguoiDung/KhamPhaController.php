<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class KhamPhaController extends Controller
{
    /**
     * Display a listing of the restaurants, optionally filtered by category.
     */
    public function index(Request $request)
    {
        // Xử lý an toàn để tránh cảnh báo "Array to string conversion" của PHP 8
        $rawDanhMuc = $request->input('danh_muc', '');
        $danhMuc = is_array($rawDanhMuc) ? '' : (string) $rawDanhMuc;
        
        $rawTuKhoa = $request->input('tu_khoa', '');
        $tuKhoa = is_array($rawTuKhoa) ? '' : strip_tags($rawTuKhoa);
        
        $query = Quan::search($tuKhoa)->whereIn('trang_thai', ['da_duyet']);

        if (!empty($danhMuc)) {
            $query->whereIn('loai_hinh_kinh_doanh.keyword', [strip_tags($danhMuc)]);
        }

        // Lấy kết quả phân trang theo mới nhất
        $quans = $query->orderBy('created_at', 'desc')->paginate(12);

        // Giữ lại query string khi phân trang
        $quans->appends($request->all());

        return view('nguoi-dung.kham-pha.index', [
            'quans' => $quans,
            'danhMuc' => $danhMuc,
            'tuKhoa' => $tuKhoa,
        ]);
    }

    public function danhMuc($slug)
    {
        $danhMucMap = [
            'nha-hang' => 'Nhà hàng',
            'ca-phe-tra' => 'Cà phê & Trà',
            'billiards-giai-tri' => 'Billiards & Giải trí',
            'do-an-vat' => 'Đồ ăn vặt',
            'lau-nuong' => 'Lẩu & Nướng',
            'quan-dem-24-7' => 'Quán Đêm 24/7'
        ];

        $danhMuc = $danhMucMap[$slug] ?? null;
        if (!$danhMuc) {
            abort(404);
        }

        $quans = Quan::query()->where('trang_thai', 'da_duyet')
            ->where('loai_hinh_kinh_doanh', $danhMuc)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('nguoi-dung.kham-pha.index', [
            'quans' => $quans,
            'danhMuc' => $danhMuc,
        ]);
    }

    public function quanMoi()
    {
        $quans = Quan::query()->where('trang_thai', 'da_duyet')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('nguoi-dung.kham-pha.index', [
            'quans' => $quans,
            'danhMuc' => 'Quán Mới',
        ]);
    }

    public function quanNoiBat()
    {
        $query = Quan::query()->where('trang_thai', 'da_duyet');

        if (Schema::hasColumn('quan', 'is_noi_bat')) {
            $query->where('is_noi_bat', true);
        }

        $quans = $query->orderBy('updated_at', 'desc')->paginate(12);

        return view('nguoi-dung.kham-pha.index', [
            'quans' => $quans,
            'danhMuc' => 'Quán Nổi Bật',
        ]);
    }
}
