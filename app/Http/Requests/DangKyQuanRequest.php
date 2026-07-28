<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DangKyQuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Chống tấn công XSS: Làm sạch dữ liệu đầu vào chuỗi
        $sanitized = [];
        foreach ($this->all() as $key => $value) {
            if (is_string($value) && $key !== 'menu_data') {
                $sanitized[$key] = trim(strip_tags($value));
            }
        }
        $this->merge($sanitized);

        if ($this->has('menu_data')) {
            $menuData = json_decode($this->input('menu_data'), true);
            $this->merge([
                'menu_categories' => is_array($menuData) ? $menuData : [],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'ten_quan'              => 'required|string|max:255',
            'loai_hinh_kinh_doanh'  => 'required|string|max:255',
            'so_dien_thoai'         => 'required|string|max:20',
            'email'             => 'nullable|email|max:255',
            'dia_chi_chi_tiet'  => 'required|string|max:500',
            'ten_tinh_thanh'    => 'required|string|max:255',
            'ten_quan_huyen'    => 'required|string|max:255',
            'ten_phuong_xa'     => 'nullable|string|max:255',
            'mo_ta'             => 'nullable|string|max:2000',
            'gio_mo_cua'        => 'required|string',
            'gio_dong_cua'      => 'required|string',
            'gia_nho_nhat'      => 'nullable|numeric|min:0',
            'gia_lon_nhat'      => 'nullable|numeric|min:0',
            'anh_bia'           => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'danh_sach_anh.*'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'danh_sach_anh'     => 'nullable|array|max:5',
            'menu_categories'   => 'nullable|array',
            'menu_categories.*.name' => 'required|string|max:255',
            'menu_categories.*.items' => 'required|array|min:1',
            'menu_categories.*.items.*.name' => 'required|string|max:255',
            'menu_categories.*.items.*.price' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_quan.required'         => 'Vui lòng nhập tên quán ăn.',
            'so_dien_thoai.required'    => 'Vui lòng nhập số điện thoại liên hệ.',
            'dia_chi_chi_tiet.required' => 'Vui lòng nhập địa chỉ cụ thể.',
            'ten_tinh_thanh.required'   => 'Vui lòng chọn Tỉnh/Thành phố.',
            'ten_quan_huyen.required'   => 'Vui lòng chọn Quận/Huyện.',
            'gio_mo_cua.required'       => 'Vui lòng chọn giờ mở cửa.',
            'gio_dong_cua.required'     => 'Vui lòng chọn giờ đóng cửa.',
            'anh_bia.image'             => 'File ảnh bìa không hợp lệ.',
            'anh_bia.max'               => 'Ảnh bìa tối đa 5MB.',
            'menu_categories.*.name.required' => 'Tên danh mục không được để trống.',
            'menu_categories.*.items.min' => 'Mỗi danh mục phải có ít nhất 1 món ăn.',
            'menu_categories.*.items.*.name.required' => 'Tên món ăn không được để trống.',
            'menu_categories.*.items.*.price.numeric' => 'Giá món ăn phải là số.',
            'menu_categories.*.items.*.price.min' => 'Giá món ăn không được nhỏ hơn 0.',
        ];
    }
}
