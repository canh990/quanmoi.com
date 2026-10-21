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

        $replyText = trim($validator->validated()[$field]);
        $danhGia->timestamps = false;
        $danhGia->update([
            'phan_hoi' => $replyText,
            'phan_hoi_luc' => now(),
        ]);
        $danhGia->timestamps = true;

        // Notify reviewer (customer)
        if ($danhGia->nguoiDung && $danhGia->nguoiDung->id !== Auth::id()) {
            try {
                $danhGia->nguoiDung->notify(new \App\Notifications\QuanReviewReplyNotification(
                    $quan,
                    Auth::user(),
                    $replyText
                ));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Review reply notification error: ' . $e->getMessage());
            }
        }

        // Record for Admin Activity Feed
        try {
            \App\Models\AdminAuditLog::create([
                'actor_id' => Auth::id(),
                'action' => 'venue.review_replied',
                'target_type' => Quan::class,
                'target_id' => (string) $quan->id,
                'metadata' => [
                    'quan_name' => $quan->ten_quan,
                    'customer_name' => $danhGia->nguoiDung?->ho_ten ?? 'Khách',
                    'phan_hoi' => \Illuminate\Support\Str::limit($replyText, 100),
                ],
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        return redirect()
            ->route('chu-quan.quan.show', $quan->slug)
            ->withFragment('owner-reviews-section')
            ->with('success', 'Phản hồi đánh giá đã được lưu.');
    }
}
