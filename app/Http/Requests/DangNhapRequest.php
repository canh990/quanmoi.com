<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DangNhapRequest extends FormRequest
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
            'mat_khau' => ['required', 'string', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu.',
            'mat_khau.max' => 'Mật khẩu không được vượt quá 255 ký tự.',
        ];
    }
}
