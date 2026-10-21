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

        $review = $quan->danhGia()->updateOrCreate(
            ['nguoi_dung_id' => $request->user()->id],
            $validated,
        );

        // Notify restaurant owner
        if ($quan->chuQuan && $quan->chuQuan->id !== $request->user()->id) {
            try {
                $quan->chuQuan->notify(new \App\Notifications\QuanReviewNotification(
                    $quan,
                    $request->user(),
                    $validated['so_sao'],
                    $validated['binh_luan']
                ));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Review notification error: ' . $e->getMessage());
            }
        }

        // Notify reviewer (confirmation)
        try {
            $request->user()->notify(new \App\Notifications\QuanReviewNotification(
                $quan,
                $request->user(),
                $validated['so_sao'],
                $validated['binh_luan']
            ));
        } catch (\Throwable $e) {}

        // Record for Admin Activity Feed
        try {
            \App\Models\AdminAuditLog::create([
                'actor_id' => $request->user()->id,
                'action' => 'venue.reviewed',
                'target_type' => Quan::class,
                'target_id' => (string) $quan->id,
                'metadata' => [
                    'quan_name' => $quan->ten_quan,
                    'so_sao' => $validated['so_sao'],
                    'binh_luan' => \Illuminate\Support\Str::limit($validated['binh_luan'], 100),
                    'user_name' => $request->user()->ho_ten,
                ],
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Review audit log error: ' . $e->getMessage());
        }

        return redirect()->route('quan.detail', $quan->slug)
            ->withFragment('reviews-section')
            ->with('success', 'Đánh giá của bạn đã được lưu.');
    }
}
