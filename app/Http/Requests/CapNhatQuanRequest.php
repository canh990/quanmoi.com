<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatQuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'ten_quan'             => 'required|string|max:255',
            'loai_hinh_kinh_doanh' => 'required|string|max:255',
            'so_dien_thoai'        => 'required|string|max:20',
            'email'                => 'nullable|email|max:255',
            'dia_chi_chi_tiet'     => 'required|string|max:500',
            'ten_tinh_thanh'       => 'nullable|string|max:255',
            'ten_quan_huyen'       => 'nullable|string|max:255',
            'ten_phuong_xa'        => 'nullable|string|max:255',
            'gio_mo_cua'           => 'nullable|string|max:20',
            'gio_dong_cua'          => 'nullable|string|max:20',
            'gia_nho_nhat'         => 'nullable|numeric|min:0',
            'gia_lon_nhat'         => 'nullable|numeric|min:0',
            'trang_thai'           => 'required|in:chua_duyet,da_duyet,bi_khoa',
            'mo_ta'                => 'nullable|string|max:2000',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('ten_quan')) {
            $this->merge([
                'ten_quan' => strip_tags($this->input('ten_quan')),
            ]);
        }
    }
}
