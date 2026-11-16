<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoiMatKhauRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function prepareForValidation(): void
    {
        foreach (['current_password', 'password', 'password_confirmation'] as $field) {
            $val = $this->input($field);
            if (is_string($val) && preg_replace('/[\s\p{Zs}\x{3000}]+/u', '', $val) === '') {
                $this->merge([$field => '']);
            }
        }
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'max:255', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'last_updated_at' => ['nullable', 'string'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.max' => 'Mật khẩu hiện tại không được vượt quá 255 ký tự.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.required' => 'Vui lòng nhập mật khẩu mới (không được chỉ nhập khoảng trắng).',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.max' => 'Mật khẩu mới không được vượt quá 255 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ];
    }
}
