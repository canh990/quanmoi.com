<?php

namespace App\Services;

use App\Models\Quan;
use Illuminate\Support\Facades\DB;

class TimKiemService
{
    /**
     * Tìm kiếm quán lân cận dựa trên tọa độ bằng công thức Haversine
     */
    public function timLanCan(float $latitude, float $longitude, float $radius = 3.0, ?string $danhMucId = null)
    {
        // 6371 là bán kính Trái Đất tính bằng km.
        // Dùng Haversine formula trong SQL
        $query = Quan::query()
            ->select('quan.*')
            ->selectRaw(
                'CAST((6371 * acos(cos(radians(?)) * cos(radians(vi_do)) * cos(radians(kinh_do) - radians(?)) + sin(radians(?)) * sin(radians(vi_do)))) AS FLOAT) AS khoang_cach',
                [$latitude, $longitude, $latitude]
            )
            ->where('trang_thai', 'da_duyet')
            ->whereRaw(
                'CAST((6371 * acos(cos(radians(?)) * cos(radians(vi_do)) * cos(radians(kinh_do) - radians(?)) + sin(radians(?)) * sin(radians(vi_do)))) AS FLOAT) <= ?',
                [$latitude, $longitude, $latitude, $radius]
            );

        if ($danhMucId) {
            $query->where('danh_muc_id', $danhMucId);
        }

        // Sắp xếp theo khoảng cách gần nhất
        return $query->orderBy('khoang_cach', 'asc')
            ->get();
    }

    /**
     * Tìm kiếm quán theo mã địa chỉ Tỉnh, Quận, Huyện
     */
    public function timTheoDiaChi(?string $tinhThanhId, ?string $quanHuyenId, ?string $phuongXaId, ?string $danhMucId = null)
    {
        $query = Quan::query()->where('trang_thai', 'da_duyet');

        if ($tinhThanhId) {
            $query->where('tinh_thanh_id', $tinhThanhId);
        }

        if ($quanHuyenId) {
            $query->where('quan_huyen_id', $quanHuyenId);
        }

        if ($phuongXaId) {
            $query->where('phuong_xa_id', $phuongXaId);
        }

        if ($danhMucId) {
            $query->where('danh_muc_id', $danhMucId);
        }

        return $query->latest()->get();
    }
}
