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
        $this->merge([
            'ho_ten' => trim(strip_tags((string) $this->input('ho_ten'))),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'ho_ten' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('nguoi_dung', 'email')->whereNull('ngay_xoa'),
            ],
            'mat_khau' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email này đã được sử dụng.',
        ];
    }
}
