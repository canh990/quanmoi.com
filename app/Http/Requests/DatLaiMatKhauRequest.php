<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatLaiMatKhauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawEmail = is_string($this->input('email')) ? $this->input('email') : '';
        $cleanEmail = mb_strtolower(preg_replace('/^[\s\p{Zs}\x{3000}]+|[\s\p{Zs}\x{3000}]+$/u', '', $rawEmail) ?? trim($rawEmail), 'UTF-8');

        $this->merge([
            'email' => $cleanEmail,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'token' => ['required', 'string', 'size:64'],
            'mat_khau' => ['required', 'string', 'min:6', 'max:255'],
            'mat_khau_xac_nhan' => ['required', 'same:mat_khau'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự.',
            'token.required' => 'Mã đặt lại mật khẩu không hợp lệ.',
            'token.size' => 'Mã đặt lại mật khẩu không hợp lệ.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu mới.',
            'mat_khau.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'mat_khau.max' => 'Mật khẩu mới không được vượt quá 255 ký tự.',
            'mat_khau_xac_nhan.required' => 'Vui lòng xác nhận mật khẩu mới.',
            'mat_khau_xac_nhan.same' => 'Xác nhận mật khẩu mới không khớp.',
        ];
    }
}
