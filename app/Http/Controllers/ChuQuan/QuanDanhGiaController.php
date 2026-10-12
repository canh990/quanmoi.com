<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuanDanhGiaController extends Controller
{
    public function reply(Request $request, string $slug, int $reviewId): RedirectResponse
    {
        $quan = Quan::where('slug', $slug)
            ->where('chu_quan_id', Auth::id())
            ->firstOrFail();
        $this->authorize('update', $quan);

        $danhGia = $quan->danhGia()->whereKey($reviewId)->firstOrFail();
        $field = 'phan_hoi_'.$danhGia->id;
        $validator = Validator::make($request->all(), [
            $field => ['required', 'string', 'min:2', 'max:2000'],
        ], [
            $field.'.required' => 'Vui lòng nhập nội dung phản hồi.',
            $field.'.min' => 'Phản hồi cần có ít nhất 2 ký tự.',
            $field.'.max' => 'Phản hồi không được vượt quá 2.000 ký tự.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->withFragment('owner-reviews-section');
        }

        $danhGia->timestamps = false;
        $danhGia->update([
            'phan_hoi' => trim($validator->validated()[$field]),
            'phan_hoi_luc' => now(),
        ]);
        $danhGia->timestamps = true;

        return redirect()
            ->route('chu-quan.quan.show', $quan->slug)
            ->withFragment('owner-reviews-section')
            ->with('success', 'Phản hồi đánh giá đã được lưu.');
    }
}
