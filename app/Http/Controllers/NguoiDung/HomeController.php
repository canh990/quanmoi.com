<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use App\Models\User;
use App\Models\VideoShort;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $hasFeaturedColumn = Schema::hasColumn('quan', 'is_noi_bat');

        $quanNoiBat = Quan::query()->where('trang_thai', 'da_duyet');
        if ($hasFeaturedColumn) {
            $quanNoiBat = $quanNoiBat->where('is_noi_bat', true);
        }
        $quanNoiBat = $quanNoiBat->orderBy('updated_at', 'desc')->take(9)->get();

        $quanMoi = Quan::where('trang_thai', 'da_duyet')
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();
        $totalQuanMoi = Quan::where('trang_thai', 'da_duyet')->count();
        $totalQuanNoiBat = $hasFeaturedColumn
            ? Quan::where('trang_thai', 'da_duyet')->where('is_noi_bat', true)->count()
            : 0;

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
        $user = Auth::user();
        if ($user instanceof User) {
            $savedQuanIds = $user->savedQuan()->pluck('quan_id')->toArray();
        }

        return view('welcome', compact('quanNoiBat', 'quanMoi', 'totalQuanMoi', 'totalQuanNoiBat', 'savedQuanIds', 'videoShorts', 'latestBlogs'));
    }

    public function show($slug)
    {
        $quan = Quan::with([
            'danhMucMenu.monAn',
            'hinhAnh',
            'danhGia.nguoiDung',
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Nếu quán chưa được duyệt hoặc bị khóa, chỉ cho phép Admin hoặc Chủ quán xem trước
        if ($quan->trang_thai !== 'da_duyet') {
            $user = Auth::user();
            $canPreview = $user instanceof User && ($user->isAdmin() || $user->id === $quan->chu_quan_id);
            if (! $canPreview) {
                abort(404);
            }
        }

        // Chống Spam View: Lưu ID quán vào Session trong vòng 2 tiếng (chỉ tính view khi quán đã được duyệt)
        if ($quan->trang_thai === 'da_duyet') {
            $sessionKey = 'viewed_quan_'.$quan->id;
            if (! session()->has($sessionKey) && Schema::hasColumn('quan', 'luot_xem')) {
                $quan->increment('luot_xem');
                session()->put($sessionKey, true);
            }
        }

        $user = Auth::user();
        $danhGiaCuaToi = $user instanceof User
            ? $quan->danhGia->firstWhere('nguoi_dung_id', $user->id)
            : null;

        $isSaved = $user instanceof User
            ? $user->savedQuan()->where('quan_id', $quan->id)->exists()
            : false;

        return view('nguoi-dung.chi-tiet', compact('quan', 'danhGiaCuaToi', 'isSaved'));
    }
}
