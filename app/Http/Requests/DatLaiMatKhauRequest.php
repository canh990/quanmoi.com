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
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'token' => ['required', 'string', 'size:64'],
            'mat_khau' => ['required', 'string', 'min:8'],
            'mat_khau_xac_nhan' => ['required', 'same:mat_khau'],
        ];
    }
}
