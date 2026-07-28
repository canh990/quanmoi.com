<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy danh sách quán nổi bật (hiện tại lấy ngẫu nhiên hoặc theo lượt xem/đánh giá, tạm thời lấy random)
        $quanNoiBat = Quan::where('trang_thai', 'da_duyet')
            ->where('is_noi_bat', true)
            ->orderBy('updated_at', 'desc')
            ->take(9)
            ->get();

        // Lấy danh sách quán mới nhất
        $quanMoi = Quan::where('trang_thai', 'da_duyet')
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();
        $totalQuanMoi = Quan::where('trang_thai', 'da_duyet')->count();
        $totalQuanNoiBat = Quan::where('trang_thai', 'da_duyet')->where('is_noi_bat', true)->count();

        return view('welcome', compact('quanNoiBat', 'quanMoi', 'totalQuanMoi', 'totalQuanNoiBat'));
    }

    public function show($slug)
    {
        $quan = Quan::with(['danhMucMenu.monAns', 'hinhAnh'])
            ->where('slug', $slug)
            ->where('trang_thai', 'da_duyet')
            ->firstOrFail();

        return view('nguoi-dung.chi-tiet', compact('quan'));
    }
}

