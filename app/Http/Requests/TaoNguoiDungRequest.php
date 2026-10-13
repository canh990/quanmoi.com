<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaoNguoiDungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'ho_ten'        => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:nguoi_dung,email',
            'mat_khau'      => 'required|string|min:6',
            'so_dien_thoai' => 'nullable|string|max:20',
            'gioi_tinh'     => 'nullable|in:nam,nu,khac',
            'ngay_sinh'     => 'nullable|date',
            'dia_chi'       => 'nullable|string|max:255',
            'anh_dai_dien'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'vai_tro_id'    => 'nullable|string|exists:vai_tro,id',
            'trang_thai'    => 'required|in:hoat_dong,bi_khoa',
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
