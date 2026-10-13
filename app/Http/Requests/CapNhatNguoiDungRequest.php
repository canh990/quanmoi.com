<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CapNhatNguoiDungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('id') ?? $this->route('nguoi_dung');

        return [
            'ho_ten'        => 'required|string|max:255',
            'email'         => ['required', 'email', 'max:255', Rule::unique('nguoi_dung', 'email')->ignore($userId)],
            'so_dien_thoai' => 'nullable|string|max:20',
            'gioi_tinh'     => 'nullable|in:nam,nu,khac',
            'ngay_sinh'     => 'nullable|date',
            'dia_chi'       => 'nullable|string|max:255',
            'anh_dai_dien'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'vai_tro_id'    => 'nullable|string|exists:vai_tro,id',
            'trang_thai'    => 'required|in:hoat_dong,bi_khoa',
            'mat_khau'      => 'nullable|string|min:6',
            'da_xac_thuc'   => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('ho_ten')) {
            $this->merge([
                'ho_ten' => strip_tags($this->input('ho_ten')),
            ]);
        }
    }
}
