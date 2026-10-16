<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use App\Services\DiaChiService;
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
        
        $isElastic   = in_array(config('scout.driver'), ['elastic', 'elasticsearch']);
        $query       = Quan::search($tuKhoa)->whereIn('trang_thai', ['da_duyet']);

        if (!empty($danhMuc) && !in_array($danhMuc, ['Quán Nổi Bật', 'Quán Mới'])) {
            $categoryField = $isElastic ? 'loai_hinh_kinh_doanh.keyword' : 'loai_hinh_kinh_doanh';
            if ($danhMuc === 'Cà phê' || $danhMuc === 'Cà phê & Trà') {
                $query->whereIn($categoryField, ['Cà phê & Trà', 'Cà phê']);
            } else {
                $query->whereIn($categoryField, [$danhMuc]);
            }
        }

        $hasGps = !empty($lat) && !empty($lon) && is_numeric($lat) && is_numeric($lon);
        $isNearMe = $hasGps && ($sapXep === 'near_me' || !empty($banKinh));

        // Tách biệt giữa chọn khu vực (tỉnh thành / quận huyện) và tìm kiếm gần bạn (GPS)
        if ($isNearMe) {
            $tinhThanhId = '';
            $quanHuyenId = '';
        } elseif (!empty($tinhThanhId) || !empty($quanHuyenId)) {
            $banKinh = null;
            if ($sapXep === 'near_me') {
                $sapXep = '';
            }
        }

        // Mã hành chính theo provinces.open-api.vn (v1), cùng nguồn với form đăng quán
        $tinhThanhId = $this->normalizeLegacyTinhThanh($tinhThanhId);
        $tinhCodes   = $this->codeVariants($tinhThanhId);
        $huyenCodes  = $this->codeVariants($quanHuyenId);

        if (!empty($tinhCodes)) {
            $query->whereIn('tinh_thanh_id', $tinhCodes);
        }

        if (!empty($huyenCodes)) {
            $query->whereIn('quan_huyen_id', $huyenCodes);
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

        if ($dangMoCua && $isElastic) {
            $query->where('_dang_mo_cua', true);
        }

        // 1. Khoảng giá thực tế (muc_gia)
        if ($isElastic) {
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
        } else {
            match ($mucGia) {
                'duoi_50k' => $query->query(fn ($q) => $q->where('gia_nho_nhat', '<=', 50000)),
                '50k_150k' => $query->query(fn ($q) => $q->where('gia_nho_nhat', '<=', 150000)->where('gia_lon_nhat', '>=', 50000)),
                '150k_300k' => $query->query(fn ($q) => $q->where('gia_nho_nhat', '<=', 300000)->where('gia_lon_nhat', '>=', 150000)),
                'tren_300k' => $query->query(fn ($q) => $q->where('gia_lon_nhat', '>=', 300000)),
                default => null,
            };
        }

        // 2. Định vị GPS thực tế qua Elasticsearch
        if ($hasGps && $isElastic) {
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

            if (!$isElastic) {
                $collection = $quans->getCollection();
                if (!empty($banKinh) && is_numeric($banKinh)) {
                    $collection = $collection->filter(function ($quan) use ($banKinh) {
                        return isset($quan->khoang_cach_km) && $quan->khoang_cach_km <= (float) $banKinh;
                    });
                }
                if ($sapXep === 'near_me') {
                    $collection = $collection->sortBy('khoang_cach_km');
                }
                $quans->setCollection($collection->values());
            }
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

        [$tenTinhThanh, $tenQuanHuyen] = $this->resolveTenKhuVuc($tinhThanhId, $quanHuyenId);

        // Tìm các món ăn khớp với từ khóa theo từng trang phân trang.
        // Chỉ lấy món thuộc các quán thỏa TOÀN BỘ bộ lọc hiện tại (dùng lại truy vấn ES của danh sách quán).
        $monAns = collect();
        $totalMonAns = 0;
        $totalMonAnPages = 0;
        if ($tuKhoa !== '' && $quans->total() > 0) {
            $quanIds = (clone $query)->take(500)->keys()->map(fn ($id) => (string) $id)->values();
            $thuTuQuan = $quanIds->flip();

            $allMatchingMonAns = \App\Models\MonTrongMenu::query()
                ->where('ten_mon', 'like', '%' . $tuKhoa . '%')
                ->whereHas('danhMuc', fn ($q) => $q->whereIn('quan_id', $quanIds->all()))
                ->with(['danhMuc.quan:id,slug,ten_quan,ten_quan_huyen,ten_tinh_thanh,anh_bia'])
                ->get()
                ->sortBy([
                    fn ($a, $b) => (int) $b->con_hang <=> (int) $a->con_hang,
                    fn ($a, $b) => ($thuTuQuan[$a->danhMuc?->quan_id] ?? PHP_INT_MAX) <=> ($thuTuQuan[$b->danhMuc?->quan_id] ?? PHP_INT_MAX),
                ]);

            $totalMonAns = $allMatchingMonAns->count();
            $perPageMonAn = 12;
            $totalMonAnPages = (int) ceil($totalMonAns / $perPageMonAn);
            $currentPage = (int) $request->input('page', 1);

            $monAns = $allMatchingMonAns->forPage($currentPage, $perPageMonAn)->values();
        }

        return view('nguoi-dung.kham-pha.index', [
            'quans'           => $quans,
            'quansGoiY'       => $quansGoiY,
            'monAns'          => $monAns,
            'totalMonAns'     => $totalMonAns,
            'totalMonAnPages' => $totalMonAnPages,
            'danhMuc'         => $danhMuc,
            'tuKhoa'          => $tuKhoa,
            'tinhThanhId'  => $tinhThanhId,
            'tenTinhThanh' => $tenTinhThanh,
            'quanHuyenId'  => $quanHuyenId,
            'tenQuanHuyen' => $tenQuanHuyen,
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

    /**
     * Mã viết tắt cũ (HN, HCM...) từ các link đã chia sẻ trước đây -> mã tỉnh của provinces.open-api.vn.
     */
    protected const LEGACY_TINH_THANH = [
        'HN' => '1', 'HCM' => '79', 'DN' => '48', 'CT' => '92', 'BD' => '74', 'DNAI' => '75',
        'VT' => '77', 'HUE' => '46', 'HP' => '31', 'KH' => '56', 'LD' => '68',
    ];

    protected function normalizeLegacyTinhThanh(string $tinhThanhId): string
    {
        return self::LEGACY_TINH_THANH[strtoupper($tinhThanhId)] ?? $tinhThanhId;
    }

    /**
     * API trả mã dạng số (1, 79, 760) nhưng dữ liệu cũ có thể lưu kèm số 0 ('01', '001').
     * Trả về mọi biến thể để so khớp chính xác.
     */
    protected function codeVariants(string $code): array
    {
        if ($code === '') {
            return [];
        }

        if (!ctype_digit($code)) {
            return [$code];
        }

        $n = (string) (int) $code;

        return array_values(array_unique([
            $code,
            $n,
            str_pad($n, 2, '0', STR_PAD_LEFT),
            str_pad($n, 3, '0', STR_PAD_LEFT),
            str_pad($n, 5, '0', STR_PAD_LEFT),
        ]));
    }

    /**
     * Lấy tên tỉnh/thành và quận/huyện từ provinces.open-api.vn (đã cache qua DiaChiService).
     *
     * @return array{0: string, 1: string}
     */
    protected function resolveTenKhuVuc(string $tinhThanhId, string $quanHuyenId): array
    {
        if ($tinhThanhId === '' && $quanHuyenId === '') {
            return ['', ''];
        }

        $tenTinh = '';
        $tenHuyen = '';

        try {
            $diaChi = app(DiaChiService::class);

            if (ctype_digit($tinhThanhId)) {
                $tinh = collect($diaChi->getTinhThanh())->first(
                    fn ($p) => (int) ($p['code'] ?? -1) === (int) $tinhThanhId
                );
                $tenTinh = $tinh['name'] ?? '';

                if (ctype_digit($quanHuyenId)) {
                    $huyen = collect($diaChi->getQuanHuyen((string) (int) $tinhThanhId))->first(
                        fn ($d) => (int) ($d['code'] ?? -1) === (int) $quanHuyenId
                    );
                    $tenHuyen = $huyen['name'] ?? '';
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // Fallback: lấy tên đã lưu trong DB nếu API không phản hồi
        if (($tenTinh === '' && $tinhThanhId !== '') || ($tenHuyen === '' && $quanHuyenId !== '')) {
            $q = Quan::query();
            if ($tinhThanhId !== '') {
                $q->whereIn('tinh_thanh_id', $this->codeVariants($tinhThanhId));
            }
            if ($quanHuyenId !== '') {
                $q->whereIn('quan_huyen_id', $this->codeVariants($quanHuyenId));
            }
            $mau = $q->first(['ten_tinh_thanh', 'ten_quan_huyen']);
            $tenTinh = $tenTinh ?: (string) ($mau->ten_tinh_thanh ?? '');
            $tenHuyen = $tenHuyen ?: ($quanHuyenId !== '' ? (string) ($mau->ten_quan_huyen ?? '') : '');
        }

        return [$tenTinh, $tenHuyen];
    }
}
