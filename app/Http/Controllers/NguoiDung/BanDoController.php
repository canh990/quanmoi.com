<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BanDoController extends Controller
{
    /**
     * Hiển thị trang bản đồ tương tác quán ăn lân cận.
     */
    public function index(Request $request)
    {
        $danhMuc = trim(strip_tags((string) $request->query('danh_muc', '')));
        $tuKhoa = trim(strip_tags((string) $request->query('q', $request->query('tu_khoa', ''))));
        $lat = $request->query('lat');
        $lng = $request->query('lng', $request->query('lon'));
        $rawRadius = (int) $request->query('ban_kinh', $request->query('radius', 5));
        $banKinh = in_array($rawRadius, [1, 3, 5, 10], true) ? $rawRadius : 5;
        $sapXep = (string) $request->query('sap_xep', 'distance_asc');
        if (! in_array($sapXep, ['distance_asc', 'rating_desc', 'price_asc'], true)) {
            $sapXep = 'distance_asc';
        }
        $dangMo = $request->boolean('dang_mo', false);

        // Lấy danh mục động thực tế từ database các quán đã duyệt có tọa độ
        $categories = Cache::remember('bando_categories_v2', 1800, function () {
            try {
                return Quan::query()
                    ->where('trang_thai', 'da_duyet')
                    ->whereNotNull('vi_do')
                    ->whereNotNull('kinh_do')
                    ->whereNotNull('loai_hinh_kinh_doanh')
                    ->where('loai_hinh_kinh_doanh', '!=', '')
                    ->select('loai_hinh_kinh_doanh', DB::raw('count(*) as total'))
                    ->groupBy('loai_hinh_kinh_doanh')
                    ->orderByDesc('total')
                    ->get()
                    ->map(fn ($item) => [
                        'name' => $item->loai_hinh_kinh_doanh,
                        'total' => (int) $item->total,
                    ])
                    ->toArray();
            } catch (\Throwable $e) {
                return [];
            }
        });

        return view('nguoi-dung.ban-do.index', compact(
            'danhMuc',
            'tuKhoa',
            'lat',
            'lng',
            'banKinh',
            'sapXep',
            'dangMo',
            'categories'
        ));
    }
}
