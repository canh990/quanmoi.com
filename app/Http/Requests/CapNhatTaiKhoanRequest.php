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
        $cleanUnicodeTrim = static fn ($val) => is_string($val)
            ? (preg_replace('/^[\s\p{Zs}\x{3000}]+|[\s\p{Zs}\x{3000}]+$/u', '', $val) ?? trim($val))
            : $val;

        $merged = [];

        if ($this->has('ho_ten') && is_string($this->input('ho_ten'))) {
            $trimmedName = $cleanUnicodeTrim($this->input('ho_ten'));
            $merged['ho_ten'] = preg_replace('/[\s\p{Zs}\x{3000}]+/u', ' ', $trimmedName) ?? $trimmedName;
        }

        foreach (['ngay_sinh', 'so_dien_thoai', 'dia_chi', 'gioi_tinh'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $cleaned = $cleanUnicodeTrim($this->input($field));
                $merged[$field] = $cleaned === '' ? null : $cleaned;
            }
        }

        if (! empty($merged)) {
            $this->merge($merged);
        }
    }

    public function rules(): array
    {
        return [
            // --- Thông tin cá nhân ---
            'ho_ten'        => 'required|string|max:255|not_regex:/[<>]/',
            'so_dien_thoai' => 'nullable|string|max:15|regex:/^0[0-9]{9,10}$/',
            'dia_chi'       => 'nullable|string|max:255|not_regex:/[<>]/',
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
            'ho_ten.required'           => 'Vui lòng nhập họ tên (không được để trống hoặc chỉ nhập khoảng trắng).',
            'ho_ten.max'                => 'Họ tên không được vượt quá 255 ký tự.',
            'so_dien_thoai.max'         => 'Số điện thoại không được vượt quá 15 ký tự.',
            'so_dien_thoai.regex'       => 'Số điện thoại không hợp lệ (chỉ nhập chữ số chuẩn 0-9, bắt đầu bằng số 0 và có từ 10 đến 11 số, không dùng số full-width).',
            'dia_chi.max'               => 'Địa chỉ không được vượt quá 255 ký tự.',
            'dia_chi.in'                => 'Tỉnh / Thành phố đã chọn không hợp lệ.',
            'gioi_tinh.in'              => 'Giới tính đã chọn không hợp lệ.',
            'anh_dai_dien.image'        => 'File tải lên phải là hình ảnh hợp lệ.',
            'anh_dai_dien.mimes'        => 'Định dạng ảnh phải là: jpeg, png, jpg hoặc webp.',
            'anh_dai_dien.max'          => 'Kích thước ảnh không được vượt quá 2MB.',
        ];
    }
}
