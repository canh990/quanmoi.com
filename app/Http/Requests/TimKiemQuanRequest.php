<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimKiemQuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:lan_can,dia_chi',
            'latitude' => 'required_if:type,lan_can|numeric|nullable',
            'longitude' => 'required_if:type,lan_can|numeric|nullable',
            'ban_kinh' => 'nullable|numeric|in:1,3,5,10',
            'tinh_thanh_id' => 'required_if:type,dia_chi|string|nullable',
            'quan_huyen_id' => 'nullable|string',
            'phuong_xa_id' => 'nullable|string',
            'danh_muc_id' => 'nullable|uuid|exists:danh_muc_quans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Phương thức tìm kiếm không hợp lệ.',
            'latitude.required_if' => 'Vui lòng cung cấp vĩ độ của bạn.',
            'longitude.required_if' => 'Vui lòng cung cấp kinh độ của bạn.',
            'tinh_thanh_id.required_if' => 'Vui lòng chọn Tỉnh/Thành phố cần tìm kiếm.',
        ];
    }
}
