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
            ->inRandomOrder()
            ->take(6)
            ->get();

        // Lấy danh sách quán mới nhất
        $quanMoi = Quan::where('trang_thai', 'da_duyet')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('welcome', compact('quanNoiBat', 'quanMoi'));
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

