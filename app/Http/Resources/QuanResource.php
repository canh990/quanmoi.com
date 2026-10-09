<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'ten_quan'             => $this->ten_quan,
            'loai_hinh_kinh_doanh' => $this->loai_hinh_kinh_doanh ?? 'Quán ăn',
            'slug'                 => $this->slug,
            'mo_ta'            => $this->mo_ta,
            'so_dien_thoai'    => $this->so_dien_thoai,
            'email'            => $this->email,
            'dia_chi_chi_tiet' => $this->dia_chi_chi_tiet,
            'ten_tinh_thanh'   => $this->ten_tinh_thanh,
            'ten_quan_huyen'   => $this->ten_quan_huyen,
            'ten_phuong_xa'    => $this->ten_phuong_xa,
            'kinh_do'          => $this->kinh_do,
            'vi_do'            => $this->vi_do,
            'gio_mo_cua'       => $this->gio_mo_cua,
            'gio_dong_cua'     => $this->gio_dong_cua,
            'gia_nho_nhat'     => number_format((float)$this->gia_nho_nhat, 0, ',', '.') . 'đ',
            'gia_lon_nhat'     => number_format((float)$this->gia_lon_nhat, 0, ',', '.') . 'đ',
            'anh_bia'          => $this->anh_bia_url,
            'trang_thai'       => $this->trang_thai,
            'created_at'       => $this->created_at?->format('d/m/Y H:i'),
        ];
    }
}
