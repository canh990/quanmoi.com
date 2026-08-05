<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use App\Models\VideoShort;
use Illuminate\Http\Request;

class VideoShortController extends Controller
{
    public function index(Request $request)
    {
        $query = VideoShort::with('quan')
            ->where('trang_thai', 'da_duyet')
            ->whereHas('quan', fn ($query) => $query->where('trang_thai', 'da_duyet'));

        // 1. Tìm kiếm theo tên quán hoặc từ khóa tiêu đề
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('tieu_de', 'like', "%{$search}%")
                    ->orWhereHas('quan', fn ($r) => $r->where('ten_quan', 'like', "%{$search}%"));
            });
        }

        // 2. Lọc theo quận/huyện
        if ($request->filled('quan_huyen')) {
            $query->whereHas('quan', fn ($q) => $q->where('ten_quan_huyen', $request->quan_huyen));
        }

        // 3. Lọc theo loại hình kinh doanh (thể loại quán)
        if ($request->filled('loai_hinh')) {
            $query->whereHas('quan', fn ($q) => $q->where('loai_hinh_kinh_doanh', $request->loai_hinh));
        }

        // 4. Sắp xếp
        switch ($request->input('sort', 'moi_nhat')) {
            case 'nhieu_view':
                $query->orderBy('luot_xem', 'desc');
                break;
            case 'nhieu_like':
                $query->orderBy('luot_thich', 'desc');
                break;
            default: // moi_nhat
                $query->orderBy('created_at', 'desc');
                break;
        }

        $videos = $query->paginate(12)->withQueryString();

        // Lấy danh sách quận/huyện và loại hình có video
        $danhSachQuanHuyen = Quan::where('trang_thai', 'da_duyet')->whereHas('videos', fn ($query) => $query->where('trang_thai', 'da_duyet'))
            ->whereNotNull('ten_quan_huyen')
            ->distinct()
            ->orderBy('ten_quan_huyen')
            ->pluck('ten_quan_huyen');

        $danhSachLoaiHinh = Quan::where('trang_thai', 'da_duyet')->whereHas('videos', fn ($query) => $query->where('trang_thai', 'da_duyet'))
            ->whereNotNull('loai_hinh_kinh_doanh')
            ->distinct()
            ->orderBy('loai_hinh_kinh_doanh')
            ->pluck('loai_hinh_kinh_doanh');

        return view('nguoi-dung.video.index', compact('videos', 'danhSachQuanHuyen', 'danhSachLoaiHinh'));
    }

    public function apiGetVideos(Request $request)
    {
        $page = $request->input('page', 1);
        $videos = VideoShort::with('quan')
            ->where('trang_thai', 'da_duyet')
            ->whereHas('quan', fn ($query) => $query->where('trang_thai', 'da_duyet'))
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'page', $page);

        return response()->json($videos);
    }
}
