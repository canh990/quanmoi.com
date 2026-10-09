<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuanDanhGiaController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $validated = $request->validate([
            'so_sao' => ['required', 'integer', 'between:1,5'],
            'binh_luan' => ['required', 'string', 'min:3', 'max:2000'],
        ], [
            'so_sao.required' => 'Vui lòng chọn số sao đánh giá.',
            'so_sao.between' => 'Số sao phải từ 1 đến 5.',
            'binh_luan.required' => 'Vui lòng nhập bình luận.',
            'binh_luan.min' => 'Bình luận cần có ít nhất 3 ký tự.',
            'binh_luan.max' => 'Bình luận không được vượt quá 2.000 ký tự.',
        ]);

        $quan = Quan::where('slug', $slug)
            ->where('trang_thai', 'da_duyet')
            ->firstOrFail();

        $quan->danhGia()->updateOrCreate(
            ['nguoi_dung_id' => $request->user()->id],
            $validated,
        );

        return redirect()->route('quan.detail', $quan->slug)
            ->withFragment('reviews-section')
            ->with('success', 'Đánh giá của bạn đã được lưu.');
    }
}
