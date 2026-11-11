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
        $query       = Quan::search($tuKhoa)
            ->whereIn('trang_thai', ['da_duyet'])
            ->query(fn ($eloquentQuery) => $eloquentQuery->withAvg('danhGia', 'so_sao')->withCount('danhGia'));

        if (!empty($danhMuc) && !in_array($danhMuc, ['Quán Nổi Bật', 'Quán Mới'])) {
            $categoryField = $isElastic ? 'loai_hinh_kinh_doanh.keyword' : 'loai_hinh_kinh_doanh';
            if ($danhMuc === 'Cà phê' || $danhMuc === 'Cà phê & Trà') {
                $query->whereIn($categoryField, ['Cà phê & Trà', 'Cà phê']);
            } elseif ($danhMuc === 'Ăn vặt' || $danhMuc === 'Đồ ăn vặt') {
                $query->whereIn($categoryField, ['Đồ ăn vặt', 'Ăn vặt']);
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

        // 3. Xử lý sắp xếp thông thường (Full tiêu chí)
        if ($sapXep === 'view_desc') {
            $query->orderBy('luot_xem', 'desc');
        } elseif ($sapXep === 'rating_desc') {
            $query->orderBy('diem_danh_gia', 'desc');
        } elseif ($sapXep === 'price_asc') {
            $query->orderBy('gia_nho_nhat', 'asc');
        } elseif ($sapXep === 'price_desc') {
            $query->orderBy('gia_lon_nhat', 'desc');
        } elseif ($sapXep === 'created_desc') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sapXep !== 'near_me') {
            if (empty($tuKhoa)) {
                $query->orderBy('created_at', 'desc');
            }
        }

        $usedEloquentFallback = false;
        $fallbackQuanIds = collect();
        $useSqliteTesting = app()->environment('testing') && config('database.default') === 'sqlite';

        if (!$useSqliteTesting && config('scout.driver') === 'elastic') {
            try {
                $quans = $query->paginate(12);

                // Phong ve: Neu Scout bao co ket qua (total > 0) nhung so luong ban ghi thuc te bi thieu
                // hoac so luong trong index Elasticsearch bi lech so voi Database MySQL (chua dong bo scout:import)
                $expectedCount = min(12, max(0, $quans->total() - ($quans->currentPage() - 1) * 12));
                $isIndexOutOfSync = false;

                if ($tuKhoa === '' && !$hasGps && !$dangMoCua && $mucGia === '' && empty($tinhCodes) && empty($huyenCodes)) {
                    $dbCountQuery = Quan::query()->where('trang_thai', 'da_duyet');
                    if ($isNoiBat) {
                        $dbCountQuery->where('is_noi_bat', true);
                    }
                    if (!empty($danhMuc) && !in_array($danhMuc, ['Quán Nổi Bật', 'Quán Mới'])) {
                        if ($danhMuc === 'Cà phê' || $danhMuc === 'Cà phê & Trà') {
                            $dbCountQuery->whereIn('loai_hinh_kinh_doanh', ['Cà phê & Trà', 'Cà phê']);
                        } elseif ($danhMuc === 'Ăn vặt' || $danhMuc === 'Đồ ăn vặt') {
                            $dbCountQuery->whereIn('loai_hinh_kinh_doanh', ['Đồ ăn vặt', 'Ăn vặt']);
                        } else {
                            $dbCountQuery->where('loai_hinh_kinh_doanh', $danhMuc);
                        }
                    }
                    if ($isXacThuc) {
                        $dbCountQuery->where('is_xac_thuc', true);
                    }
                    $dbCount = $dbCountQuery->count();
                    if ($quans->total() !== $dbCount) {
                        $isIndexOutOfSync = true;
                    }
                }

                if ($isIndexOutOfSync || ($quans->total() > 0 && ($quans->isEmpty() || $quans->count() < $expectedCount))) {
                    $usedEloquentFallback = true;
                    [$quans, $fallbackQuanIds] = $this->paginateWithEloquentFallback(
                        $request,
                        $tuKhoa,
                        $danhMuc,
                        $tinhCodes,
                        $huyenCodes,
                        $isXacThuc,
                        $isNoiBat,
                        $coShopee,
                        $coVideo,
                        $dangMoCua,
                        $mucGia,
                        $hasGps,
                        $lat,
                        $lon,
                        $banKinh,
                        $sapXep
                    );
                }
            } catch (\Throwable $e) {
                report($e);
                $usedEloquentFallback = true;
                [$quans, $fallbackQuanIds] = $this->paginateWithEloquentFallback(
                    $request,
                    $tuKhoa,
                    $danhMuc,
                    $tinhCodes,
                    $huyenCodes,
                    $isXacThuc,
                    $isNoiBat,
                    $coShopee,
                    $coVideo,
                    $dangMoCua,
                    $mucGia,
                    $hasGps,
                    $lat,
                    $lon,
                    $banKinh,
                    $sapXep
                );
            }
        } else {
            $usedEloquentFallback = true;
            [$quans, $fallbackQuanIds] = $this->paginateWithEloquentFallback(
                $request,
                $tuKhoa,
                $danhMuc,
                $tinhCodes,
                $huyenCodes,
                $isXacThuc,
                $isNoiBat,
                $coShopee,
                $coVideo,
                $dangMoCua,
                $mucGia,
                $hasGps,
                $lat,
                $lon,
                $banKinh,
                $sapXep
            );
        }

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
            $quanIds = $usedEloquentFallback
                ? $fallbackQuanIds
                : (clone $query)->take(500)->keys()->map(fn ($id) => (string) $id)->values();
            $thuTuQuan = $quanIds->flip();

            $tuKhoaAscii = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($tuKhoa));
            $tuKhoaLower = mb_strtolower($tuKhoa, 'UTF-8');

            $allMatchingMonAns = \App\Models\MonTrongMenu::query()
                ->whereHas('danhMuc', fn ($q) => $q->whereIn('quan_id', $quanIds->all()))
                ->with(['danhMuc.quan:id,slug,ten_quan,ten_quan_huyen,ten_tinh_thanh,anh_bia,anh_bia_key'])
                ->get()
                ->filter(function ($mon) use ($tuKhoaLower, $tuKhoaAscii) {
                    $tenLower = mb_strtolower((string) $mon->ten_mon, 'UTF-8');
                    if (str_contains($tenLower, $tuKhoaLower)) {
                        return true;
                    }
                    $tenAscii = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii((string) $mon->ten_mon));
                    return $tuKhoaAscii !== '' && str_contains($tenAscii, $tuKhoaAscii);
                })
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

        $savedQuanIds = [];
        $user = auth()->user();
        if ($user) {
            $savedQuanIds = $user->savedQuan()->pluck('quan_id')->toArray();
        }

        return view('nguoi-dung.kham-pha.index', [
            'quans'           => $quans,
            'savedQuanIds'    => $savedQuanIds,
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
        $category = \App\Models\DanhMucQuan::where('slug', $slug)->first();
        if ($category) {
            $danhMuc = $category->ten_danh_muc;
        } else {
            $danhMucMap = [
                'nha-hang' => 'Nhà hàng',
                'ca-phe-tra' => 'Cà phê & Trà',
                'billiards-giai-tri' => 'Billiards & Giải trí',
                'do-an-vat' => 'Đồ ăn vặt',
                'lau-nuong' => 'Lẩu & Nướng',
                'quan-dem-24-7' => 'Quán Đêm 24/7',
                'an-vat' => 'Đồ ăn vặt',
                'quan-nhau' => 'Quán nhậu & Bar',
                'tra-sua' => 'Trà sữa & Nước ép',
                'tra-sua-nuoc-ep' => 'Trà sữa & Nước ép',
                'tiem-banh' => 'Tiệm bánh',
                'binh-dan' => 'Quán ăn bình dân',
                'mon-ngon-duong-pho' => 'Đồ ăn vặt',
                'ca-phe-tra-sua' => 'Cà phê & Trà',
                'quan-an-binh-dan' => 'Quán ăn bình dân',
            ];
            $danhMuc = $danhMucMap[$slug] ?? null;
        }

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

    /**
     * Fallback truy vấn bằng Eloquent khi Elasticsearch không khả dụng hoặc chạy test SQLite.
     *
     * @return array{0: \Illuminate\Pagination\LengthAwarePaginator, 1: \Illuminate\Support\Collection}
     */
    protected function paginateWithEloquentFallback(
        Request $request,
        string $tuKhoa,
        string $danhMuc,
        array $tinhCodes,
        array $huyenCodes,
        bool $isXacThuc,
        bool $isNoiBat,
        bool $coShopee,
        bool $coVideo,
        bool $dangMoCua,
        string $mucGia,
        bool $hasGps,
        mixed $lat,
        mixed $lon,
        mixed $banKinh,
        string $sapXep
    ): array {
        $q = Quan::query()->where('trang_thai', 'da_duyet')->withAvg('danhGia', 'so_sao')->withCount('danhGia');

        if (!empty($danhMuc) && !in_array($danhMuc, ['Quán Nổi Bật', 'Quán Mới'])) {
            if ($danhMuc === 'Cà phê' || $danhMuc === 'Cà phê & Trà') {
                $q->whereIn('loai_hinh_kinh_doanh', ['Cà phê & Trà', 'Cà phê']);
            } elseif ($danhMuc === 'Ăn vặt' || $danhMuc === 'Đồ ăn vặt') {
                $q->whereIn('loai_hinh_kinh_doanh', ['Đồ ăn vặt', 'Ăn vặt']);
            } else {
                $q->where('loai_hinh_kinh_doanh', $danhMuc);
            }
        }

        if (!empty($tinhCodes)) {
            $q->whereIn('tinh_thanh_id', $tinhCodes);
        }

        if (!empty($huyenCodes)) {
            $q->whereIn('quan_huyen_id', $huyenCodes);
        }

        if ($isXacThuc) {
            $q->where('is_xac_thuc', true);
        }

        if ($isNoiBat) {
            $q->where('is_noi_bat', true);
        }

        if ($coShopee) {
            $q->whereNotNull('shopeefood_url')->where('shopeefood_url', '!=', '');
        }

        if ($coVideo) {
            $q->whereHas('videos', fn ($v) => $v->where('trang_thai', 'da_duyet'));
        }

        if ($mucGia === 'duoi_50k') {
            $q->where('gia_nho_nhat', '<=', 50000);
        } elseif ($mucGia === '50k_150k') {
            $q->where('gia_nho_nhat', '<=', 150000)->where('gia_lon_nhat', '>=', 50000);
        } elseif ($mucGia === '150k_300k') {
            $q->where('gia_nho_nhat', '<=', 300000)->where('gia_lon_nhat', '>=', 150000);
        } elseif ($mucGia === 'tren_300k') {
            $q->where('gia_lon_nhat', '>=', 300000);
        }

        if ($tuKhoa !== '') {
            $q->where(function ($sub) use ($tuKhoa) {
                $sub->where('ten_quan', 'like', "%{$tuKhoa}%")
                    ->orWhere('dia_chi_chi_tiet', 'like', "%{$tuKhoa}%")
                    ->orWhere('ten_quan_huyen', 'like', "%{$tuKhoa}%")
                    ->orWhere('ten_tinh_thanh', 'like', "%{$tuKhoa}%")
                    ->orWhere('loai_hinh_kinh_doanh', 'like', "%{$tuKhoa}%")
                    ->orWhere('mo_ta', 'like', "%{$tuKhoa}%")
                    ->orWhereHas('danhMucMenu.monAn', fn ($m) => $m->where('ten_mon', 'like', "%{$tuKhoa}%"));
            });
        }

        $items = $q->get();

        if ($dangMoCua) {
            $items = $items->filter(fn (Quan $quan) => $quan->isDangMoCua())->values();
        }

        if ($hasGps) {
            $items = $items->map(function (Quan $quan) use ($lat, $lon) {
                if ($quan->vi_do !== null && $quan->kinh_do !== null && is_numeric($quan->vi_do) && is_numeric($quan->kinh_do)) {
                    $quan->khoang_cach_km = $this->calculateDistance((float) $lat, (float) $lon, (float) $quan->vi_do, (float) $quan->kinh_do);
                } else {
                    $quan->khoang_cach_km = null;
                }
                return $quan;
            });

            if (!empty($banKinh) && is_numeric($banKinh)) {
                $maxKm = (float) $banKinh;
                $items = $items->filter(fn (Quan $quan) => $quan->khoang_cach_km !== null && $quan->khoang_cach_km <= $maxKm)->values();
            }

            if ($sapXep === 'near_me') {
                $items = $items->sortBy(fn (Quan $quan) => $quan->khoang_cach_km ?? PHP_FLOAT_MAX)->values();
            }
        }

        if ($sapXep === 'view_desc') {
            $items = $items->sortByDesc('luot_xem')->values();
        } elseif ($sapXep === 'rating_desc') {
            $items = $items->sortByDesc(fn (Quan $quan) => (float) ($quan->danh_gia_avg_so_sao ?: 4.9))->values();
        } elseif ($sapXep === 'price_asc') {
            $items = $items->sortBy(fn (Quan $quan) => (float) ($quan->gia_nho_nhat ?? PHP_FLOAT_MAX))->values();
        } elseif ($sapXep === 'price_desc') {
            $items = $items->sortByDesc(fn (Quan $quan) => (float) ($quan->gia_lon_nhat ?? 0))->values();
        } elseif ($sapXep === 'created_desc' || ($sapXep !== 'near_me' && $tuKhoa === '')) {
            $items = $items->sortByDesc('created_at')->values();
        }

        $quanIds = $items->pluck('id')->map(fn ($id) => (string) $id)->values();
        $perPage = 12;
        $currentPage = max(1, (int) $request->input('page', 1));
        $pagedItems = $items->forPage($currentPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedItems,
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return [$paginator, $quanIds];
    }
}
