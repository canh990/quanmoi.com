<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CapNhatNguoiDungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('id') ?? $this->route('nguoi_dung');

        return [
            'ho_ten'          => 'required|string|max:191',
            'email'           => ['required', 'email', 'max:191', Rule::unique('nguoi_dung', 'email')->ignore($userId)],
            'so_dien_thoai'   => 'nullable|string|max:20',
            'vai_tro_id'      => 'nullable|string|exists:vai_tro,id',
            'trang_thai'      => 'required|in:hoat_dong,bi_khoa',
            'mat_khau'        => 'nullable|string|min:6|max:191',
            'da_xac_thuc'     => 'nullable|boolean',
            'last_updated_at' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];
        foreach ($this->all() as $key => $value) {
            if (is_string($value)) {
                // Chuẩn hóa khoảng trắng toàn giác và nửa giác
                $value = preg_replace('/[\x{3000}\s]+/u', ' ', $value);
                $value = trim(strip_tags($value));

                // Chuẩn hóa số full-width (０-９ -> 0-9) cho số điện thoại
                if ($key === 'so_dien_thoai') {
                    $value = mb_convert_kana($value, 'n', 'UTF-8');
                }

                $sanitized[$key] = $value;
            }
        }
        $this->merge($sanitized);
    }

    public function messages(): array
    {
        return [
            'ho_ten.required' => 'Họ và tên người dùng không được để trống.',
            'ho_ten.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'email.required' => 'Địa chỉ email không được để trống.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng.',
            'so_dien_thoai.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'vai_tro_id.exists' => 'Vai trò được chọn không tồn tại trong hệ thống.',
            'trang_thai.required' => 'Trạng thái tài khoản không được để trống.',
            'trang_thai.in' => 'Trạng thái tài khoản được chọn không hợp lệ.',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ];
    }
}
