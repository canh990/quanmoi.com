<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaoQuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        return $user->isAdmin()
            || $user->hasRole('admin')
            || (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo('quan_ly_quan'));
    }

    public function rules(): array
    {
        return [
            'chu_quan_id'          => 'nullable|string|exists:nguoi_dung,id',
            'ten_quan'             => 'required|string|max:255',
            'loai_hinh_kinh_doanh' => 'required|string|max:255',
            'so_dien_thoai'        => 'required|string|max:20',
            'email'                => 'nullable|email|max:255',
            'dia_chi_chi_tiet'     => 'required|string|max:500',
            'tinh_thanh_id'        => 'nullable|string|max:50',
            'ten_tinh_thanh'       => 'nullable|string|max:255',
            'quan_huyen_id'        => 'nullable|string|max:50',
            'ten_quan_huyen'       => 'nullable|string|max:255',
            'phuong_xa_id'         => 'nullable|string|max:50',
            'ten_phuong_xa'        => 'nullable|string|max:255',
            'kinh_do'              => 'nullable|numeric',
            'vi_do'                => 'nullable|numeric',
            'gio_mo_cua'           => 'nullable|string|max:20',
            'gio_dong_cua'         => 'nullable|string|max:20',
            'gia_nho_nhat'         => 'nullable|numeric|min:0',
            'gia_lon_nhat'         => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $min = request()->input('gia_nho_nhat');
                    if ($min !== null && $min !== '' && is_numeric($min) && (float) $value < (float) $min) {
                        $fail('Giá lớn nhất phải lớn hơn hoặc bằng giá nhỏ nhất.');
                    }
                },
            ],
            'trang_thai'           => 'required|in:chua_duyet,da_duyet,bi_khoa',
            'is_noi_bat'           => 'nullable|boolean',
            'is_xac_thuc'          => 'nullable|boolean',
            'tiktok_url'           => 'nullable|url|max:255',
            'shopeefood_url'       => 'nullable|url|max:500',
            'anh_bia'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'mo_ta'                => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_quan.required'             => 'Vui lòng không để trống tên cơ sở quán.',
            'ten_quan.max'                  => 'Tên cơ sở không được vượt quá 255 ký tự.',
            'loai_hinh_kinh_doanh.required' => 'Vui lòng chọn hoặc nhập loại hình kinh doanh.',
            'so_dien_thoai.required'        => 'Số điện thoại liên hệ là bắt buộc.',
            'so_dien_thoai.max'             => 'Số điện thoại không được vượt quá 20 ký tự.',
            'email.email'                   => 'Email không đúng định dạng.',
            'dia_chi_chi_tiet.required'     => 'Vui lòng nhập địa chỉ chi tiết của quán.',
            'dia_chi_chi_tiet.max'          => 'Địa chỉ chi tiết không được vượt quá 500 ký tự.',
            'gia_nho_nhat.numeric'          => 'Giá bán tối thiểu phải là số hợp lệ.',
            'gia_nho_nhat.min'              => 'Giá bán tối thiểu không được âm.',
            'gia_lon_nhat.numeric'          => 'Giá bán tối đa phải là số hợp lệ.',
            'gia_lon_nhat.min'              => 'Giá bán tối đa không được âm.',
            'trang_thai.required'           => 'Vui lòng chọn trạng thái duyệt của quán.',
            'trang_thai.in'                 => 'Trạng thái quán không hợp lệ.',
            'tiktok_url.url'                => 'Đường dẫn TikTok phải là URL hợp lệ.',
            'shopeefood_url.url'            => 'Đường dẫn ShopeeFood phải là URL hợp lệ.',
            'anh_bia.image'                 => 'Ảnh tải lên phải là định dạng hình ảnh.',
            'anh_bia.mimes'                 => 'Ảnh bìa chỉ chấp nhận định dạng jpeg, png, jpg, webp.',
            'anh_bia.max'                   => 'Dung lượng ảnh bìa tối đa không quá 5MB.',
            'mo_ta.max'                     => 'Mô tả quán không được vượt quá 2000 ký tự.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];
        foreach ($this->all() as $key => $value) {
            if (is_string($value) && ! in_array($key, ['tiktok_url', 'shopeefood_url'], true)) {
                $sanitized[$key] = trim(strip_tags($value));
            }
        }
        $this->merge($sanitized);
    }
}
