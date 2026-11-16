<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class XacThucOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawEmail = is_string($this->input('email')) ? $this->input('email') : '';
        $rawOtp = is_string($this->input('otp')) ? $this->input('otp') : '';

        $this->merge([
            'email' => mb_strtolower(preg_replace('/^[\s\p{Zs}\x{3000}]+|[\s\p{Zs}\x{3000}]+$/u', '', $rawEmail) ?? trim($rawEmail), 'UTF-8'),
            'otp' => preg_replace('/^[\s\p{Zs}\x{3000}]+|[\s\p{Zs}\x{3000}]+$/u', '', $rawOtp) ?? trim($rawOtp),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'otp' => ['required', 'regex:/^[0-9]{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự.',
            'otp.required' => 'Vui lòng nhập mã xác thực OTP.',
            'otp.regex' => 'Mã OTP phải gồm đúng 6 chữ số chuẩn (0-9), không chấp nhận chữ số full-width hoặc ký tự khác.',
        ];
    }
}
