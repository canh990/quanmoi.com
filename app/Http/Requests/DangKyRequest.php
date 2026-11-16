<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DangKyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ.',
            'errors' => $validator->errors()->toArray(),
        ], 422));
    }

    protected function prepareForValidation(): void
    {
        $rawName = is_string($this->input('ho_ten')) ? $this->input('ho_ten') : '';
        $trimmedName = preg_replace('/^[\s\p{Zs}\x{3000}]+|[\s\p{Zs}\x{3000}]+$/u', '', $rawName) ?? trim($rawName);
        $cleanName = preg_replace('/[\s\p{Zs}\x{3000}]+/u', ' ', $trimmedName) ?? $trimmedName;

        $rawEmail = is_string($this->input('email')) ? $this->input('email') : '';
        $cleanEmail = mb_strtolower(preg_replace('/^[\s\p{Zs}\x{3000}]+|[\s\p{Zs}\x{3000}]+$/u', '', $rawEmail) ?? trim($rawEmail), 'UTF-8');

        $rawPass = $this->input('mat_khau');
        if (is_string($rawPass) && preg_replace('/[\s\p{Zs}\x{3000}]+/u', '', $rawPass) === '') {
            $rawPass = '';
        }

        $this->merge([
            'ho_ten' => $cleanName,
            'email' => $cleanEmail,
            'mat_khau' => $rawPass,
        ]);
    }

    public function rules(): array
    {
        return [
            'ho_ten' => ['required', 'string', 'max:255', 'not_regex:/[<>]/'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('nguoi_dung', 'email')->whereNull('ngay_xoa'),
            ],
            'mat_khau' => ['required', 'string', 'min:6', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten.required' => 'Vui lòng nhập họ và tên (không được chỉ nhập khoảng trắng).',
            'ho_ten.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'ho_ten.not_regex' => 'Họ và tên không được chứa mã HTML hoặc ký tự < >.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email này đã được sử dụng.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu (không được chỉ nhập khoảng trắng).',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'mat_khau.max' => 'Mật khẩu không được vượt quá 255 ký tự.',
        ];
    }
}
