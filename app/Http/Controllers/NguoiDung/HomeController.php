<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use App\Models\VideoShort;

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

        // Lấy video shorts
        $videoShorts = VideoShort::with('quan')
            ->where('trang_thai', 'da_duyet')
            ->whereHas('quan', fn ($query) => $query->where('trang_thai', 'da_duyet'))
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Lấy tin tức / bài viết mới nhất
        $latestBlogs = \App\Models\Blog::where('status', 'published')
            ->latest('published_at')
            ->take(4)
            ->get();

        $savedQuanIds = [];
        if (auth()->check()) {
            $savedQuanIds = auth()->user()->savedQuan()->pluck('quan_id')->toArray();
        }

        return view('welcome', compact('quanNoiBat', 'quanMoi', 'totalQuanMoi', 'totalQuanNoiBat', 'savedQuanIds', 'videoShorts', 'latestBlogs'));
    }

    public function show($slug)
    {
        $quan = Quan::with(['danhMucMenu.monAn', 'hinhAnh'])
            ->where('slug', $slug)
            ->where('trang_thai', 'da_duyet')
            ->firstOrFail();

        // Chống Spam View: Lưu ID quán vào Session trong vòng 2 tiếng
        $sessionKey = 'viewed_quan_'.$quan->id;
        if (! session()->has($sessionKey)) {
            $quan->increment('luot_xem');
            session()->put($sessionKey, true);
        }

        return view('nguoi-dung.chi-tiet', compact('quan'));
    }
}
