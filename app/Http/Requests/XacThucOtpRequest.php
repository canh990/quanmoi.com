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
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'otp' => trim((string) $this->input('otp')),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'otp' => ['required', 'regex:/^\\d{6}$/'],
        ];
    }
}
