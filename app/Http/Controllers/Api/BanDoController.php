<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BanDoController extends Controller
{
    /**
     * Tìm kiếm quán ăn lân cận dựa hoàn toàn trên dữ liệu bảng `quan` trong Database Quán Mới.
     * Tuyệt đối không gọi Google Places hay API bên ngoài để lấy danh sách quán.
     */
    public function quanLanCan(Request $request)
    {
        $input = [
            'lat' => $request->input('lat', $request->input('latitude')),
            'lng' => $request->input('lng', $request->input('lon', $request->input('longitude'))),
            'radius' => $request->input('radius', $request->input('ban_kinh', 5)),
            'danh_muc' => $request->input('danh_muc'),
            'q' => $request->input('q', $request->input('tu_khoa')),
            'page' => $request->input('page', 1),
            'per_page' => $request->input('per_page', 24),
        ];

        $validator = Validator::make($input, [
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['required', 'numeric', 'in:1,3,5,10,15,20,25,30,50'],
            'danh_muc' => ['nullable', 'string', 'max:100'],
            'q' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ], [
            'lat.required' => 'Vui lòng cung cấp vĩ độ (latitude) hợp lệ.',
            'lat.numeric' => 'Vĩ độ (latitude) phải là định dạng số.',
            'lat.between' => 'Vĩ độ (latitude) phải nằm trong khoảng từ -90 đến 90.',
            'lng.required' => 'Vui lòng cung cấp kinh độ (longitude) hợp lệ.',
            'lng.numeric' => 'Kinh độ (longitude) phải là định dạng số.',
            'lng.between' => 'Kinh độ (longitude) phải nằm trong khoảng từ -180 đến 180.',
            'radius.in' => 'Bán kính tìm kiếm chỉ hỗ trợ các mức: 1 km, 3 km, 5 km hoặc 10 km.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $userLat = (float) $validated['lat'];
        $userLng = (float) $validated['lng'];
        $radiusKm = (float) $validated['radius'];
        $danhMuc = isset($validated['danh_muc']) ? trim(strip_tags((string) $validated['danh_muc'])) : '';
        $tuKhoa = isset($validated['q']) ? trim(strip_tags((string) $validated['q'])) : '';
        $page = max(1, (int) ($validated['page'] ?? 1));
        $perPage = max(1, min(50, (int) ($validated['per_page'] ?? 24)));

        // 1. Tính Bounding Box để lọc nhanh trên B-Tree Index (trang_thai, vi_do, kinh_do)
        $earthKmPerDegLat = 111.045;
        $deltaLat = $radiusKm / $earthKmPerDegLat;
        $cosLat = max(abs(cos(deg2rad($userLat))), 0.01);
        $deltaLng = $radiusKm / ($earthKmPerDegLat * $cosLat);

        $minLat = max(-90.0, $userLat - $deltaLat);
        $maxLat = min(90.0, $userLat + $deltaLat);
        $minLng = max(-180.0, $userLng - $deltaLng);
        $maxLng = min(180.0, $userLng + $deltaLng);

        // 2. Truy vấn các quán công khai (trang_thai = da_duyet, chưa xóa mềm) có tọa độ hợp lệ trong Bounding Box
        $baseQuery = Quan::query()
            ->withAvg('danhGia', 'so_sao')
            ->withCount('danhGia')
            ->select([
                'id',
                'ten_quan',
                'slug',
                'loai_hinh_kinh_doanh',
                'dia_chi_chi_tiet',
                'ten_phuong_xa',
                'ten_quan_huyen',
                'ten_tinh_thanh',
                'vi_do',
                'kinh_do',
                'gio_mo_cua',
                'gio_dong_cua',
                'gia_nho_nhat',
                'gia_lon_nhat',
                'anh_bia',
                'anh_bia_key',
                'is_xac_thuc',
                'is_noi_bat',
            ])
            ->where('trang_thai', 'da_duyet')
            ->whereNotNull('vi_do')
            ->whereNotNull('kinh_do')
            ->whereBetween('vi_do', [$minLat, $maxLat])
            ->whereBetween('kinh_do', [$minLng, $maxLng]);

        if ($danhMuc !== '' && ! in_array($danhMuc, ['Quán Nổi Bật', 'Quán Mới'], true)) {
            if ($danhMuc === 'Cà phê' || $danhMuc === 'Cà phê & Trà') {
                $baseQuery->whereIn('loai_hinh_kinh_doanh', ['Cà phê & Trà', 'Cà phê']);
            } elseif ($danhMuc === 'Ăn vặt' || $danhMuc === 'Đồ ăn vặt') {
                $baseQuery->whereIn('loai_hinh_kinh_doanh', ['Đồ ăn vặt', 'Ăn vặt']);
            } else {
                $baseQuery->where('loai_hinh_kinh_doanh', $danhMuc);
            }
        }

        if ($tuKhoa !== '') {
            $escapedKeyword = addcslashes($tuKhoa, '%_\\');
            $likePattern = "%{$escapedKeyword}%";
            $baseQuery->where(function ($q) use ($likePattern) {
                $q->where('ten_quan', 'like', $likePattern)
                    ->orWhere('dia_chi_chi_tiet', 'like', $likePattern)
                    ->orWhere('ten_phuong_xa', 'like', $likePattern)
                    ->orWhere('ten_quan_huyen', 'like', $likePattern)
                    ->orWhere('loai_hinh_kinh_doanh', 'like', $likePattern)
                    ->orWhereHas('danhMucMenu.monAn', function ($dishQuery) use ($likePattern) {
                        $dishQuery->where('ten_mon', 'like', $likePattern);
                    });
            });
        }

        $driver = DB::connection()->getDriverName();

        // 3. Tính khoảng cách Haversine và lọc chính xác theo bán kính ngay tại Backend
        if (in_array($driver, ['mysql', 'mariadb', 'pgsql'], true)) {
            $haversineSql = '(6371 * ACOS(LEAST(1.0, GREATEST(-1.0, COS(RADIANS(?)) * COS(RADIANS(vi_do)) * COS(RADIANS(kinh_do) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(vi_do))))))';

            $query = (clone $baseQuery)
                ->selectRaw("{$haversineSql} AS khoang_cach_km", [$userLat, $userLng, $userLat])
                ->whereRaw("{$haversineSql} <= ?", [$userLat, $userLng, $userLat, $radiusKm])
                ->orderBy('khoang_cach_km', 'asc');

            $paginator = $query->paginate($perPage, ['*'], 'page', $page);
            $items = collect($paginator->items());
            $total = $paginator->total();
            $lastPage = $paginator->lastPage();
        } else {
            // Fallback cho SQLite (môi trường test không có hàm lượng giác ACOS/RADIANS mặc định)
            $matched = $baseQuery->get()
                ->map(function (Quan $quan) use ($userLat, $userLng) {
                    $quan->khoang_cach_km = $this->calculateHaversineKm(
                        $userLat,
                        $userLng,
                        (float) $quan->vi_do,
                        (float) $quan->kinh_do
                    );
                    return $quan;
                })
                ->filter(fn (Quan $quan) => $quan->khoang_cach_km <= $radiusKm)
                ->sortBy('khoang_cach_km')
                ->values();

            $total = $matched->count();
            $lastPage = max(1, (int) ceil($total / $perPage));
            $items = $matched->forPage($page, $perPage)->values();
        }

        // 4. Chuẩn hóa dữ liệu JSON trả về (chỉ trả trường công khai, không lộ thông tin riêng tư của chủ quán)
        $formattedQuans = $items->map(function (Quan $quan) {
            $addressParts = array_filter([
                $quan->dia_chi_chi_tiet,
                $quan->ten_phuong_xa,
                $quan->ten_quan_huyen,
                $quan->ten_tinh_thanh,
            ], fn ($val) => is_string($val) && trim($val) !== '');

            return [
                'id' => (string) $quan->id,
                'ten_quan' => $quan->ten_quan,
                'slug' => $quan->slug,
                'latitude' => round((float) $quan->vi_do, 6),
                'longitude' => round((float) $quan->kinh_do, 6),
                'dia_chi' => implode(', ', $addressParts),
                'anh_dai_dien' => $quan->anh_bia_url,
                'khoang_cach_km' => round((float) $quan->khoang_cach_km, 2),
                'loai_hinh_kinh_doanh' => $quan->loai_hinh_kinh_doanh ?: 'Quán ăn',
                'gia_nho_nhat' => (int) ($quan->gia_nho_nhat ?? 0),
                'gia_lon_nhat' => (int) ($quan->gia_lon_nhat ?? 0),
                'is_xac_thuc' => (bool) $quan->is_xac_thuc,
                'is_noi_bat' => (bool) $quan->is_noi_bat,
                'dang_mo_cua' => method_exists($quan, 'isDangMoCua') ? $quan->isDangMoCua() : true,
                'so_sao_trung_binh' => round((float) ($quan->danh_gia_avg_so_sao ?? 0), 1),
                'so_luong_danh_gia' => (int) ($quan->danh_gia_count ?? 0),
                'gio_mo_cua' => $quan->gio_mo_cua ? substr((string) $quan->gio_mo_cua, 0, 5) : null,
                'gio_dong_cua' => $quan->gio_dong_cua ? substr((string) $quan->gio_dong_cua, 0, 5) : null,
                'url' => route('quan.detail', ['slug' => $quan->slug]),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'meta' => [
                'center' => [
                    'latitude' => $userLat,
                    'longitude' => $userLng,
                ],
                'radius_km' => $radiusKm,
                'keyword' => $tuKhoa,
                'total' => $total,
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => $lastPage,
            ],
            'data' => $formattedQuans,
        ]);
    }

    /**
     * Tính khoảng cách Haversine (km) giữa 2 tọa độ địa lý.
     */
    private function calculateHaversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(max(0.0, 1 - $a)));

        return round($earthRadius * $c, 2);
    }

    /**
     * Proxy định vị tọa độ từ địa chỉ chuỗi kết hợp tìm nhanh quán ăn / món ăn trong hệ thống.
     */
    public function geocode(Request $request)
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'max:255'],
        ]);

        $address = trim(strip_tags($validated['address']));

        if ($address === '') {
            return response()->json(['success' => false, 'message' => 'Vui lòng cung cấp địa chỉ hợp lệ.'], 400);
        }

        // 1. Tìm nhanh các quán đã duyệt có tọa độ khớp với từ khóa (tên quán, địa chỉ, món ăn)
        $escapedKeyword = addcslashes($address, '%_\\');
        $likePattern = "%{$escapedKeyword}%";

        $matchedVenues = Quan::query()
            ->select([
                'id',
                'ten_quan',
                'slug',
                'loai_hinh_kinh_doanh',
                'dia_chi_chi_tiet',
                'ten_phuong_xa',
                'ten_quan_huyen',
                'ten_tinh_thanh',
                'vi_do',
                'kinh_do',
            ])
            ->where('trang_thai', 'da_duyet')
            ->whereNotNull('vi_do')
            ->whereNotNull('kinh_do')
            ->where(function ($q) use ($likePattern) {
                $q->where('ten_quan', 'like', $likePattern)
                    ->orWhere('dia_chi_chi_tiet', 'like', $likePattern)
                    ->orWhere('ten_quan_huyen', 'like', $likePattern)
                    ->orWhereHas('danhMucMenu.monAn', function ($dishQuery) use ($likePattern) {
                        $dishQuery->where('ten_mon', 'like', $likePattern);
                    });
            })
            ->limit(5)
            ->get()
            ->map(function (Quan $quan) {
                $addressParts = array_filter([
                    $quan->dia_chi_chi_tiet,
                    $quan->ten_phuong_xa,
                    $quan->ten_quan_huyen,
                    $quan->ten_tinh_thanh,
                ], fn ($val) => is_string($val) && trim($val) !== '');

                return [
                    'id' => (string) $quan->id,
                    'ten_quan' => $quan->ten_quan,
                    'slug' => $quan->slug,
                    'loai_hinh_kinh_doanh' => $quan->loai_hinh_kinh_doanh ?: 'Quán ăn',
                    'dia_chi' => implode(', ', $addressParts),
                    'display_name' => $quan->ten_quan . ' — ' . implode(', ', $addressParts),
                    'lat' => round((float) $quan->vi_do, 6),
                    'lng' => round((float) $quan->kinh_do, 6),
                    'url' => route('quan.detail', ['slug' => $quan->slug]),
                ];
            })
            ->values()
            ->toArray();

        // 2. Tra cứu tọa độ địa lý (hỗ trợ thử tuần tự: Địa chỉ chi tiết ➔ Phường/Xã ➔ Quận/Huyện ➔ Tỉnh/Thành)
        $candidates = [$address];
        if (str_contains($address, ',')) {
            $parts = array_values(array_filter(array_map('trim', explode(',', $address))));
            $partCount = count($parts);
            for ($i = 1; $i < $partCount; $i++) {
                $subQuery = implode(', ', array_slice($parts, $i));
                if (! in_array($subQuery, $candidates, true) && mb_strlen($subQuery) >= 3) {
                    $candidates[] = $subQuery;
                }
            }
        }

        $geoData = null;
        $matchedCandidate = null;
        $isFallback = false;

        foreach ($candidates as $idx => $cand) {
            $data = $this->queryGeocodingProvider($cand);
            if ($data !== null && ! empty($data['results'])) {
                $geoData = $data;
                $matchedCandidate = $cand;
                $isFallback = ($idx > 0);
                break;
            }
        }

        if ($geoData !== null && ! empty($geoData['results'])) {
            $primary = $geoData['results'][0];

            return response()->json([
                'success' => true,
                'lat' => $primary['lat'],
                'lng' => $primary['lng'],
                'display_name' => $primary['display_name'],
                'suggestions' => $geoData['results'],
                'venues' => $matchedVenues,
                'provider' => $geoData['provider'],
                'is_fallback' => $isFallback,
                'matched_query' => $matchedCandidate,
                'original_address' => $address,
            ]);
        }

        // Nếu Nominatim/Google không ra địa chỉ nhưng trong Database có quán ăn khớp tên/món ăn
        if (! empty($matchedVenues)) {
            $primaryVenue = $matchedVenues[0];

            return response()->json([
                'success' => true,
                'lat' => $primaryVenue['lat'],
                'lng' => $primaryVenue['lng'],
                'display_name' => $primaryVenue['display_name'],
                'suggestions' => [],
                'venues' => $matchedVenues,
                'provider' => 'database',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy địa điểm hoặc quán ăn phù hợp.',
            'suggestions' => [],
            'venues' => [],
        ], 404);
    }

    /**
     * Tra cứu tọa độ địa lý qua Google Maps API (nếu có key) hoặc OpenStreetMap Nominatim có cache 24h.
     */
    private function queryGeocodingProvider(string $query): ?array
    {
        $query = trim($query);
        if ($query === '') {
            return null;
        }

        $cacheKey = 'bando_geocode_v2_' . md5(mb_strtolower($query));
        $geoData = \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400, function () use ($query) {
            $apiKey = config('services.google.maps_api_key') ?? env('GOOGLE_MAPS_API_KEY');

            if ($apiKey) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                        'address' => $query,
                        'key' => $apiKey,
                        'language' => 'vi',
                        'region' => 'vn',
                    ]);

                    if ($response->successful() && $response->json('status') === 'OK') {
                        $results = [];
                        foreach ($response->json('results') as $res) {
                            $results[] = [
                                'display_name' => $res['formatted_address'],
                                'lat' => (float) $res['geometry']['location']['lat'],
                                'lng' => (float) $res['geometry']['location']['lng'],
                            ];
                        }
                        if (! empty($results)) {
                            return [
                                'provider' => 'google',
                                'results' => $results,
                            ];
                        }
                    }
                } catch (\Throwable $e) {
                    // Fallback xuống OpenStreetMap nếu Google Maps lỗi
                }
            }

            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                    'User-Agent' => 'QuanMoiApp/1.0 (contact: support@quanmoi.com)',
                ])->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 5,
                    'countrycodes' => 'vn',
                ]);

                if ($response->successful() && is_array($response->json()) && count($response->json()) > 0) {
                    $results = [];
                    foreach ($response->json() as $item) {
                        if (isset($item['lat'], $item['lon'], $item['display_name'])) {
                            $results[] = [
                                'display_name' => (string) $item['display_name'],
                                'lat' => (float) $item['lat'],
                                'lng' => (float) $item['lon'],
                            ];
                        }
                    }
                    if (! empty($results)) {
                        return [
                            'provider' => 'openstreetmap',
                            'results' => $results,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Không cache lỗi mạng tạm thời nếu cần
            }

            return null;
        });

        if ($geoData === null) {
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }

        return $geoData;
    }
}