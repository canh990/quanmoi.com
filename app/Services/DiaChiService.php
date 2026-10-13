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
        return Cache::remember('tinh_thanh_list', 86400, function () {
            try {
                $response = Http::timeout(10)
                    ->retry(2, 300)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => 'QuanMoi/1.0',
                    ])
                    ->get("{$this->baseUrl}/p/");

                return $response->successful() ? $response->json() : [];
            } catch (\Exception $e) {
                return $this->getFallbackProvinces();
            }
        });
    }

    /**
     * Lấy danh sách Quận / Huyện theo Tỉnh / Thành
     */
    public function getQuanHuyen(string $tinhCode)
    {
        return Cache::remember("quan_huyen_{$tinhCode}", 86400, function () use ($tinhCode) {
            try {
                $response = Http::timeout(10)
                    ->retry(2, 300)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => 'QuanMoi/1.0',
                    ])
                    ->get("{$this->baseUrl}/p/{$tinhCode}", ['depth' => 2]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['districts'] ?? [];
                }
                return [];
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Lấy danh sách Phường / Xã theo Quận / Huyện
     */
    public function getPhuongXa(string $huyenCode)
    {
        return Cache::remember("phuong_xa_{$huyenCode}", 86400, function () use ($huyenCode) {
            try {
                $response = Http::timeout(10)
                    ->retry(2, 300)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => 'QuanMoi/1.0',
                    ])
                    ->get("{$this->baseUrl}/d/{$huyenCode}", ['depth' => 2]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['wards'] ?? [];
                }
                return [];
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Fallback danh sách Tỉnh/Thành phổ biến nếu API offline
     */
    private function getFallbackProvinces()
    {
        return [
            ['code' => 79, 'name' => 'TP. Hồ Chí Minh'],
            ['code' => 1, 'name' => 'TP. Hà Nội'],
            ['code' => 48, 'name' => 'TP. Đà Nẵng'],
            ['code' => 46, 'name' => 'Thừa Thiên Huế'],
            ['code' => 74, 'name' => 'Bình Dương'],
            ['code' => 75, 'name' => 'Đồng Nai'],
            ['code' => 77, 'name' => 'Bà Rịa - Vũng Tàu'],
            ['code' => 92, 'name' => 'TP. Cần Thơ'],
        ];
    }
}
