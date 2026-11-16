<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DiaChiService
{
    protected string $baseUrl = 'https://provinces.open-api.vn/api/v1';

    /**
     * Lấy danh sách Tỉnh / Thành phố
     */
    public function getTinhThanh()
    {
        $cached = Cache::get('tinh_thanh_list');
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
                Cache::put('tinh_thanh_list', $data, 86400);

                return $data;
            }
        } catch (\Exception $e) {
            // Rơi xuống danh sách dự phòng bên dưới
        }

        return $this->getFallbackProvinces();
    }

    /**
     * Lấy danh sách Quận / Huyện theo Tỉnh / Thành
     */
    public function getQuanHuyen(string $tinhCode)
    {
        $cached = Cache::get("quan_huyen_{$tinhCode}");
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

            $data = $response->successful() ? ($response->json()['districts'] ?? []) : [];
            Cache::put("quan_huyen_{$tinhCode}", $data, 86400);

            return $data;
        } catch (\Exception $e) {
            return Cache::get("quan_huyen_{$tinhCode}", []);
        }
    }

    /**
     * Lấy danh sách Phường / Xã theo Quận / Huyện
     */
    public function getPhuongXa(string $huyenCode)
    {
        $cached = Cache::get("phuong_xa_{$huyenCode}");
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
                ->get("{$this->baseUrl}/d/{$huyenCode}", ['depth' => 2]);

            $data = $response->successful() ? ($response->json()['wards'] ?? []) : [];
            Cache::put("phuong_xa_{$huyenCode}", $data, 86400);

            return $data;
        } catch (\Exception $e) {
            return Cache::get("phuong_xa_{$huyenCode}", []);
        }
    }

    /**
     * Fallback danh sách 63 Tỉnh/Thành phố nếu API offline
     */
    private function getFallbackProvinces()
    {
        return [
            ['code' => 79, 'name' => 'Thành phố Hồ Chí Minh'],
            ['code' => 1, 'name' => 'Thành phố Hà Nội'],
            ['code' => 48, 'name' => 'Thành phố Đà Nẵng'],
            ['code' => 31, 'name' => 'Thành phố Hải Phòng'],
            ['code' => 92, 'name' => 'Thành phố Cần Thơ'],
            ['code' => 46, 'name' => 'Thành phố Huế'],
            ['code' => 89, 'name' => 'Tỉnh An Giang'],
            ['code' => 77, 'name' => 'Tỉnh Bà Rịa - Vũng Tàu'],
            ['code' => 24, 'name' => 'Tỉnh Bắc Giang'],
            ['code' => 6, 'name' => 'Tỉnh Bắc Kạn'],
            ['code' => 95, 'name' => 'Tỉnh Bạc Liêu'],
            ['code' => 27, 'name' => 'Tỉnh Bắc Ninh'],
            ['code' => 83, 'name' => 'Tỉnh Bến Tre'],
            ['code' => 52, 'name' => 'Tỉnh Bình Định'],
            ['code' => 74, 'name' => 'Tỉnh Bình Dương'],
            ['code' => 70, 'name' => 'Tỉnh Bình Phước'],
            ['code' => 60, 'name' => 'Tỉnh Bình Thuận'],
            ['code' => 96, 'name' => 'Tỉnh Cà Mau'],
            ['code' => 4, 'name' => 'Tỉnh Cao Bằng'],
            ['code' => 66, 'name' => 'Tỉnh Đắk Lắk'],
            ['code' => 67, 'name' => 'Tỉnh Đắk Nông'],
            ['code' => 11, 'name' => 'Tỉnh Điện Biên'],
            ['code' => 75, 'name' => 'Tỉnh Đồng Nai'],
            ['code' => 87, 'name' => 'Tỉnh Đồng Tháp'],
            ['code' => 64, 'name' => 'Tỉnh Gia Lai'],
            ['code' => 2, 'name' => 'Tỉnh Hà Giang'],
            ['code' => 35, 'name' => 'Tỉnh Hà Nam'],
            ['code' => 42, 'name' => 'Tỉnh Hà Tĩnh'],
            ['code' => 30, 'name' => 'Tỉnh Hải Dương'],
            ['code' => 93, 'name' => 'Tỉnh Hậu Giang'],
            ['code' => 17, 'name' => 'Tỉnh Hòa Bình'],
            ['code' => 33, 'name' => 'Tỉnh Hưng Yên'],
            ['code' => 56, 'name' => 'Tỉnh Khánh Hòa'],
            ['code' => 91, 'name' => 'Tỉnh Kiên Giang'],
            ['code' => 62, 'name' => 'Tỉnh Kon Tum'],
            ['code' => 12, 'name' => 'Tỉnh Lai Châu'],
            ['code' => 68, 'name' => 'Tỉnh Lâm Đồng'],
            ['code' => 20, 'name' => 'Tỉnh Lạng Sơn'],
            ['code' => 10, 'name' => 'Tỉnh Lào Cai'],
            ['code' => 80, 'name' => 'Tỉnh Long An'],
            ['code' => 36, 'name' => 'Tỉnh Nam Định'],
            ['code' => 40, 'name' => 'Tỉnh Nghệ An'],
            ['code' => 37, 'name' => 'Tỉnh Ninh Bình'],
            ['code' => 58, 'name' => 'Tỉnh Ninh Thuận'],
            ['code' => 25, 'name' => 'Tỉnh Phú Thọ'],
            ['code' => 54, 'name' => 'Tỉnh Phú Yên'],
            ['code' => 44, 'name' => 'Tỉnh Quảng Bình'],
            ['code' => 49, 'name' => 'Tỉnh Quảng Nam'],
            ['code' => 51, 'name' => 'Tỉnh Quảng Ngãi'],
            ['code' => 22, 'name' => 'Tỉnh Quảng Ninh'],
            ['code' => 45, 'name' => 'Tỉnh Quảng Trị'],
            ['code' => 94, 'name' => 'Tỉnh Sóc Trăng'],
            ['code' => 14, 'name' => 'Tỉnh Sơn La'],
            ['code' => 72, 'name' => 'Tỉnh Tây Ninh'],
            ['code' => 34, 'name' => 'Tỉnh Thái Bình'],
            ['code' => 19, 'name' => 'Tỉnh Thái Nguyên'],
            ['code' => 38, 'name' => 'Tỉnh Thanh Hóa'],
            ['code' => 82, 'name' => 'Tỉnh Tiền Giang'],
            ['code' => 84, 'name' => 'Tỉnh Trà Vinh'],
            ['code' => 8, 'name' => 'Tỉnh Tuyên Quang'],
            ['code' => 86, 'name' => 'Tỉnh Vĩnh Long'],
            ['code' => 26, 'name' => 'Tỉnh Vĩnh Phúc'],
            ['code' => 15, 'name' => 'Tỉnh Yên Bái'],
        ];
    }
}
