<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatTaiKhoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // --- Thông tin cá nhân ---
            'ho_ten'        => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|regex:/(0)[0-9]{9}/',
            'dia_chi'       => 'nullable|string|max:255',
            'ngay_sinh'     => 'nullable|date|before_or_equal:today',
            'gioi_tinh'     => 'nullable|in:nam,nu,khac',

            // --- Ảnh đại diện (upload file) ---
            // Chỉ hiện diện khi user gửi file từ tab ảnh đại diện
            'anh_dai_dien'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten.required'           => 'Vui lòng nhập họ tên.',
            'ho_ten.max'                => 'Họ tên không được vượt quá 255 ký tự.',
            'so_dien_thoai.regex'       => 'Số điện thoại không hợp lệ (phải bắt đầu bằng 0 và có 10 số).',
            'ngay_sinh.date'            => 'Ngày sinh không hợp lệ.',
            'ngay_sinh.before_or_equal' => 'Ngày sinh không thể là ngày trong tương lai.',
            'gioi_tinh.in'              => 'Giới tính không hợp lệ.',
            'anh_dai_dien.image'        => 'File tải lên phải là hình ảnh hợp lệ.',
            'anh_dai_dien.mimes'        => 'Định dạng ảnh phải là: jpeg, png, jpg, gif hoặc webp.',
            'anh_dai_dien.max'          => 'Kích thước ảnh không được vượt quá 5MB.',
        ];
    }
}
