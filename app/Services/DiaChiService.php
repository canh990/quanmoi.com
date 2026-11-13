<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DiaChiService
{
    protected string $baseUrl = 'https://provinces.open-api.vn/api/v2';

    /**
     * Lấy danh sách Tỉnh / Thành phố (34 đơn vị sau sáp nhập)
     */
    public function getTinhThanh()
    {
        $cached = Cache::get('tinh_thanh_list_v2');
        if (is_array($cached) && ! empty($cached)) {
            return $cached;
        }

        try {
            $response = Http::timeout(10)
                ->retry(2, 300)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'QuanMoi/1.0',
                ])
                ->get("{$this->baseUrl}/p/");

            $data = $response->successful() ? $response->json() : [];
            if (is_array($data) && ! empty($data)) {
                Cache::put('tinh_thanh_list_v2', $data, 86400);

                return $data;
            }
        } catch (\Exception $e) {
            // Rơi xuống danh sách dự phòng bên dưới
        }

        return $this->getFallbackProvinces();
    }

    /**
     * Lấy danh sách Phường / Xã / Đặc khu trực tiếp theo Tỉnh / Thành (mô hình 2 cấp sau sáp nhập)
     */
    public function getPhuongXaTheoTinh(string $tinhCode): array
    {
        $cacheKey = "phuong_xa_tinh_v2_{$tinhCode}";
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && ! empty($cached)) {
            return $cached;
        }

        try {
            $response = Http::timeout(10)
                ->retry(2, 300)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'QuanMoi/1.0',
                ])
                ->get("{$this->baseUrl}/p/{$tinhCode}", ['depth' => 2]);

            $json = $response->successful() ? $response->json() : [];
            $data = is_array($json) ? ($json['wards'] ?? []) : [];
            if (is_array($data) && ! empty($data)) {
                Cache::put($cacheKey, $data, 86400);

                return $data;
            }
        } catch (\Exception $e) {
            return Cache::get($cacheKey, []);
        }

        return [];
    }

    /**
     * Lấy danh sách khu vực trực thuộc Tỉnh / Thành (tương thích ngược: trả về Phường/Xã ở v2 nếu không còn Quận/Huyện)
     */
    public function getQuanHuyen(string $tinhCode)
    {
        $cacheKey = "quan_huyen_v2_{$tinhCode}";
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && ! empty($cached)) {
            return $cached;
        }

        try {
            $response = Http::timeout(10)
                ->retry(2, 300)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'QuanMoi/1.0',
                ])
                ->get("{$this->baseUrl}/p/{$tinhCode}", ['depth' => 2]);

            $json = $response->successful() ? $response->json() : [];
            $data = is_array($json) ? ($json['districts'] ?? ($json['wards'] ?? [])) : [];
            if (is_array($data) && ! empty($data)) {
                Cache::put($cacheKey, $data, 86400);
            }

            return is_array($data) ? $data : [];
        } catch (\Exception $e) {
            return Cache::get($cacheKey, []);
        }
    }

    /**
     * Lấy danh sách Phường / Xã theo mã Tỉnh/Thành (v2) hoặc mã Quận/Huyện cũ (v1)
     */
    public function getPhuongXa(string $code)
    {
        $cacheKey = "phuong_xa_v2_{$code}";
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && ! empty($cached)) {
            return $cached;
        }

        try {
            $response = Http::timeout(10)
                ->retry(2, 300)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'QuanMoi/1.0',
                ])
                ->get("{$this->baseUrl}/p/{$code}", ['depth' => 2]);

            $data = $response->successful() ? ($response->json()['wards'] ?? []) : [];
            if (is_array($data) && ! empty($data)) {
                Cache::put($cacheKey, $data, 86400);

                return $data;
            }
        } catch (\Exception $e) {
            // Fallback sang cache bên dưới
        }

        return Cache::get($cacheKey, []);
    }

    /**
     * Fallback danh sách 34 Tỉnh/Thành phố sau sáp nhập (Nghị quyết 202/2025/QH15) nếu API offline
     */
    private function getFallbackProvinces()
    {
        return [
            ['code' => 1, 'name' => 'Thành phố Hà Nội'],
            ['code' => 4, 'name' => 'Tỉnh Cao Bằng'],
            ['code' => 8, 'name' => 'Tỉnh Tuyên Quang'],
            ['code' => 11, 'name' => 'Tỉnh Điện Biên'],
            ['code' => 12, 'name' => 'Tỉnh Lai Châu'],
            ['code' => 14, 'name' => 'Tỉnh Sơn La'],
            ['code' => 15, 'name' => 'Tỉnh Lào Cai'],
            ['code' => 19, 'name' => 'Tỉnh Thái Nguyên'],
            ['code' => 20, 'name' => 'Tỉnh Lạng Sơn'],
            ['code' => 22, 'name' => 'Thành phố Quảng Ninh'],
            ['code' => 24, 'name' => 'Thành phố Bắc Ninh'],
            ['code' => 25, 'name' => 'Tỉnh Phú Thọ'],
            ['code' => 31, 'name' => 'Thành phố Hải Phòng'],
            ['code' => 33, 'name' => 'Tỉnh Hưng Yên'],
            ['code' => 37, 'name' => 'Tỉnh Ninh Bình'],
            ['code' => 38, 'name' => 'Tỉnh Thanh Hóa'],
            ['code' => 40, 'name' => 'Tỉnh Nghệ An'],
            ['code' => 42, 'name' => 'Tỉnh Hà Tĩnh'],
            ['code' => 44, 'name' => 'Tỉnh Quảng Trị'],
            ['code' => 46, 'name' => 'Thành phố Huế'],
            ['code' => 48, 'name' => 'Thành phố Đà Nẵng'],
            ['code' => 51, 'name' => 'Tỉnh Quảng Ngãi'],
            ['code' => 52, 'name' => 'Tỉnh Gia Lai'],
            ['code' => 56, 'name' => 'Tỉnh Khánh Hòa'],
            ['code' => 66, 'name' => 'Tỉnh Đắk Lắk'],
            ['code' => 68, 'name' => 'Tỉnh Lâm Đồng'],
            ['code' => 75, 'name' => 'Thành phố Đồng Nai'],
            ['code' => 79, 'name' => 'Thành phố Hồ Chí Minh'],
            ['code' => 80, 'name' => 'Tỉnh Tây Ninh'],
            ['code' => 82, 'name' => 'Tỉnh Đồng Tháp'],
            ['code' => 86, 'name' => 'Tỉnh Vĩnh Long'],
            ['code' => 91, 'name' => 'Tỉnh An Giang'],
            ['code' => 92, 'name' => 'Thành phố Cần Thơ'],
            ['code' => 96, 'name' => 'Tỉnh Cà Mau'],
        ];
    }
}
