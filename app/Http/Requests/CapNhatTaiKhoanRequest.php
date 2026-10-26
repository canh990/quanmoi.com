<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatTaiKhoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('ngay_sinh')) {
            $raw = $this->input('ngay_sinh');
            if (is_string($raw) && trim($raw) === '') {
                $this->merge(['ngay_sinh' => null]);
            }
        }
    }

    public function rules(): array
    {
        return [
            // --- Thông tin cá nhân ---
            'ho_ten'        => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|regex:/^0[0-9]{9,10}$/',
            'dia_chi'       => 'nullable|string|max:255',
            'ngay_sinh'     => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '') {
                        return;
                    }

                    if (!is_string($value)) {
                        $fail('Ngày sinh không hợp lệ.');
                        return;
                    }

                    $val = trim($value);

                    // 1. Định dạng dd/mm/yyyy hoặc d/m/yyyy (tối đa 10 ký tự)
                    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $val, $matches)) {
                        $day = (int) $matches[1];
                        $month = (int) $matches[2];
                        $year = (int) $matches[3];

                        if ($day < 1 || $day > 31 || $month < 1 || $month > 12) {
                            $fail('Ngày hoặc tháng không hợp lệ.');
                            return;
                        }

                        if (!checkdate($month, $day, $year)) {
                            $fail('Ngày sinh không tồn tại trong lịch.');
                            return;
                        }

                        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        if ($dateStr > now()->format('Y-m-d')) {
                            $fail('Ngày sinh không thể là ngày trong tương lai.');
                            return;
                        }

                        return;
                    }

                    // 2. Định dạng chuẩn HTML5 datepicker: YYYY-MM-DD
                    if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $val, $matches)) {
                        $year = (int) $matches[1];
                        $month = (int) $matches[2];
                        $day = (int) $matches[3];

                        if (!checkdate($month, $day, $year)) {
                            $fail('Ngày sinh không tồn tại trong lịch.');
                            return;
                        }

                        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        if ($dateStr > now()->format('Y-m-d')) {
                            $fail('Ngày sinh không thể là ngày trong tương lai.');
                            return;
                        }

                        return;
                    }

                    // 3. Nếu không khớp định dạng
                    $fail('Định dạng ngày sinh phải là dd/mm/yyyy.');
                },
            ],
            'gioi_tinh'     => 'nullable|in:nam,nữ,khác',
            'last_updated_at' => 'nullable|string',

            // --- Ảnh đại diện (upload file) ---
            'anh_dai_dien'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten.required'           => 'Vui lòng nhập họ tên.',
            'ho_ten.max'                => 'Họ tên không được vượt quá 255 ký tự.',
            'so_dien_thoai.regex'       => 'Số điện thoại không hợp lệ (phải bắt đầu bằng số 0 và có từ 10 đến 11 số).',
            'gioi_tinh.in'              => 'Giới tính không hợp lệ.',
            'anh_dai_dien.image'        => 'File tải lên phải là hình ảnh hợp lệ.',
            'anh_dai_dien.mimes'        => 'Định dạng ảnh phải là: jpeg, png, jpg hoặc webp.',
            'anh_dai_dien.max'          => 'Kích thước ảnh không được vượt quá 2MB.',
        ];
    }
}
