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
        $tuKhoa      = trim(strip_tags(is_array($request->input('tu_khoa')) ? '' : (string) $request->input('tu_khoa')));
        $danhMuc     = trim(strip_tags(is_array($request->input('danh_muc')) ? '' : (string) $request->input('danh_muc')));
        $tinhThanhId = trim(strip_tags(is_array($request->input('tinh_thanh_id')) ? '' : (string) $request->input('tinh_thanh_id')));
        $quanHuyenId = trim(strip_tags(is_array($request->input('quan_huyen_id')) ? '' : (string) $request->input('quan_huyen_id')));
        $mucGia      = trim(strip_tags(is_array($request->input('muc_gia')) ? '' : (string) $request->input('muc_gia')));
        $sapXep      = trim(strip_tags(is_array($request->input('sap_xep')) ? '' : (string) $request->input('sap_xep')));
        $lat         = $request->input('lat');
        $lon         = $request->input('lon', $request->input('lng'));
        $banKinh     = $request->input('ban_kinh');
        $isXacThuc   = $request->boolean('is_xac_thuc');
        $isNoiBat    = $request->boolean('is_noi_bat');
        $dangMoCua   = $request->boolean('dang_mo_cua');
        $coShopee    = $request->boolean('co_shopeefood');
        $coVideo     = $request->boolean('co_video');
        
        $query = Quan::search($tuKhoa)->whereIn('trang_thai', ['da_duyet']);

        if (!empty($danhMuc) && !in_array($danhMuc, ['Quán Nổi Bật', 'Quán Mới'])) {
            if ($danhMuc === 'Cà phê' || $danhMuc === 'Cà phê & Trà') {
                $query->whereIn('loai_hinh_kinh_doanh.keyword', ['Cà phê & Trà', 'Cà phê']);
            } else {
                $query->whereIn('loai_hinh_kinh_doanh.keyword', [$danhMuc]);
            }
        }

        if (!empty($tinhThanhId)) {
            $query->whereIn('tinh_thanh_id', [$tinhThanhId]);
        }

        if (!empty($quanHuyenId)) {
            $query->whereIn('quan_huyen_id', [$quanHuyenId]);
        }

        if ($isXacThuc) {
            $query->whereIn('is_xac_thuc', [true]);
        }

        if ($isNoiBat) {
            $query->whereIn('is_noi_bat', [true]);
        }

        if ($coShopee) {
            $query->whereIn('co_shopeefood', [true]);
        }

        if ($coVideo) {
            $query->whereIn('co_video_review', [true]);
        }

        if ($dangMoCua) {
            $query->where('_dang_mo_cua', true);
        }

        // 1. Khoảng giá thực tế (muc_gia)
        $rangeGia = match ($mucGia) {
            'duoi_50k' => ['range' => ['gia_nho_nhat' => ['lte' => 50000]]],
            '50k_150k' => ['must' => [['gia_nho_nhat' => ['lte' => 150000]], ['gia_lon_nhat' => ['gte' => 50000]]]],
            '150k_300k' => ['must' => [['gia_nho_nhat' => ['lte' => 300000]], ['gia_lon_nhat' => ['gte' => 150000]]]],
            'tren_300k' => ['range' => ['gia_lon_nhat' => ['gte' => 300000]]],
            default => null,
        };
        if ($rangeGia !== null) {
            $query->where('_range_gia', $rangeGia);
        }

        // 2. Định vị GPS thực tế qua Elasticsearch
        $hasGps = !empty($lat) && !empty($lon) && is_numeric($lat) && is_numeric($lon);
        if ($hasGps) {
            if (!empty($banKinh) && is_numeric($banKinh)) {
                $query->where('_geo_distance', [
                    'lat' => (float) $lat,
                    'lon' => (float) $lon,
                    'distance' => "{$banKinh}km",
                ]);
            }

            if ($sapXep === 'near_me') {
                $query->where('_geo_sort', [
                    'lat' => (float) $lat,
                    'lon' => (float) $lon,
                    'order' => 'asc',
                ]);
            }
        }

        // 3. Xử lý sắp xếp thông thường
        if ($sapXep === 'view_desc') {
            $query->orderBy('luot_xem', 'desc');
        } elseif ($sapXep === 'created_desc') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sapXep !== 'near_me') {
            if (empty($tuKhoa)) {
                $query->orderBy('created_at', 'desc');
            }
        }

        $quans = $query->paginate(12);
        $quans->appends($request->all());

        // Tính toán khoảng cách hiển thị km nếu có toạ độ người dùng
        if ($hasGps) {
            $quans->getCollection()->transform(function ($quan) use ($lat, $lon) {
                if (!empty($quan->vi_do) && !empty($quan->kinh_do)) {
                    $quan->khoang_cach_km = $this->calculateDistance((float)$lat, (float)$lon, (float)$quan->vi_do, (float)$quan->kinh_do);
                }
                return $quan;
            });
        }

        // Gợi ý quán nổi bật khi kết quả rỗng (Smart Empty State)
        $quansGoiY = collect();
        if ($quans->isEmpty()) {
            $quansGoiY = Quan::where('trang_thai', 'da_duyet')
                ->where('is_noi_bat', true)
                ->inRandomOrder()
                ->take(4)
                ->get();

            if ($quansGoiY->isEmpty()) {
                $quansGoiY = Quan::where('trang_thai', 'da_duyet')
                    ->inRandomOrder()
                    ->take(4)
                    ->get();
            }
        }

        $tinhThanhMap = $this->getTinhThanhMap();
        $tenTinhThanh = $tinhThanhMap[$tinhThanhId] ?? $tinhThanhId;

        // Tìm các món ăn khớp với từ khóa (chỉ hiển thị ở trang đầu)
        $monAns = collect();
        if ($tuKhoa !== '' && $quans->currentPage() === 1) {
            $monAns = \App\Models\MonTrongMenu::query()
                ->where('ten_mon', 'like', '%' . $tuKhoa . '%')
                ->whereHas('danhMuc.quan', function ($q) use ($tinhThanhId, $quanHuyenId) {
                    $q->where('trang_thai', 'da_duyet');
                    if (!empty($tinhThanhId)) {
                        $q->where('tinh_thanh_id', $tinhThanhId);
                    }
                    if (!empty($quanHuyenId)) {
                        $q->where('quan_huyen_id', $quanHuyenId);
                    }
                })
                ->with(['danhMuc.quan:id,slug,ten_quan,ten_quan_huyen,ten_tinh_thanh,anh_bia'])
                ->orderByDesc('con_hang')
                ->take(12)
                ->get();
        }

        return view('nguoi-dung.kham-pha.index', [
            'quans'        => $quans,
            'quansGoiY'    => $quansGoiY,
            'monAns'       => $monAns,
            'danhMuc'      => $danhMuc,
            'tuKhoa'       => $tuKhoa,
            'tinhThanhId'  => $tinhThanhId,
            'tenTinhThanh' => $tenTinhThanh,
            'quanHuyenId'  => $quanHuyenId,
            'mucGia'       => $mucGia,
            'sapXep'       => $sapXep,
            'isXacThuc'    => $isXacThuc,
            'isNoiBat'     => $isNoiBat,
            'dangMoCua'    => $dangMoCua,
            'coShopee'     => $coShopee,
            'coVideo'      => $coVideo,
            'lat'          => $lat,
            'lon'          => $lon,
            'banKinh'      => $banKinh,
        ]);
    }

    public function danhMuc($slug, Request $request)
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

        $request->merge(['danh_muc' => $danhMuc]);
        return $this->index($request);
    }

    public function quanMoi(Request $request)
    {
        $request->merge([
            'danh_muc' => 'Quán Mới',
            'sap_xep'  => $request->input('sap_xep', 'created_desc'),
        ]);
        return $this->index($request);
    }

    public function quanNoiBat(Request $request)
    {
        $request->merge([
            'danh_muc'   => 'Quán Nổi Bật',
            'is_noi_bat' => 1,
        ]);
        return $this->index($request);
    }

    /**
     * Tính khoảng cách đường chim bay giữa hai toạ độ GPS theo công thức Haversine (km).
     */
    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 1);
    }

    protected function getTinhThanhMap(): array
    {
        return [
            'HCM'  => 'Hồ Chí Minh',
            'HN'   => 'Hà Nội',
            'DN'   => 'Đà Nẵng',
            'CT'   => 'Cần Thơ',
            'BD'   => 'Bình Dương',
            'DNai' => 'Đồng Nai',
            'VT'   => 'Bà Rịa - Vũng Tàu',
            'HUE'  => 'Thừa Thiên Huế',
            'HP'   => 'Hải Phòng',
            'KH'   => 'Khánh Hòa',
            'LD'   => 'Lâm Đồng',
        ];
    }
}
