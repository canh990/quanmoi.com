<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use App\Services\ReviewAiSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuanDanhGiaController extends Controller
{
    public function reply(Request $request, string $slug, string|int $reviewId, ReviewAiSummaryService $aiService): RedirectResponse
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

        // Xóa cache AI summary để cập nhật phản hồi mới
        $aiService->clearCache($quan->id);

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

    /**
     * API: Lấy tóm tắt phân tích đánh giá bằng AI cho Chủ Quán
     */
    public function aiSummary(Request $request, string $slug, ReviewAiSummaryService $aiService): JsonResponse
    {
        $quan = Quan::where('slug', $slug)
            ->where('chu_quan_id', Auth::id())
            ->firstOrFail();
        $this->authorize('update', $quan);

        $forceRefresh = $request->boolean('refresh', false);
        $summary = $aiService->getOwnerSummary($quan, $forceRefresh);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * API: AI Gợi ý câu trả lời đa phong cách cho một đánh giá cụ thể
     */
    public function suggestReply(Request $request, string $slug, string|int $reviewId, ReviewAiSummaryService $aiService): JsonResponse
    {
        $quan = Quan::where('slug', $slug)
            ->where('chu_quan_id', Auth::id())
            ->firstOrFail();
        $this->authorize('update', $quan);

        $danhGia = $quan->danhGia()->whereKey($reviewId)->firstOrFail();
        $tone = $request->input('tone');
        $seed = $request->input('seed');
        $seed = is_numeric($seed) ? (int) $seed : null;
        $result = $aiService->suggestReply($quan, $danhGia, $tone, $seed);

        return response()->json($result);
    }

    /**
     * API: Kiểm tra đánh giá thời gian thực dành cho Chủ Quán (Realtime Polling nhẹ)
     */
    public function realtimeCheck(Request $request, string $slug): JsonResponse
    {
        $quan = Quan::where('slug', $slug)
            ->where('chu_quan_id', Auth::id())
            ->firstOrFail();
        $this->authorize('update', $quan);

        $totalCount = (int) $quan->danhGia()->count();
        $latestReview = $quan->danhGia()
            ->with('nguoiDung:id,ho_ten,anh_dai_dien')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        $clientLastId = $request->query('last_id');
        $clientLastCount = $request->query('last_count');
        $clientLastUpdated = $request->query('last_updated');

        $latestUpdatedStr = $latestReview ? $latestReview->updated_at?->toISOString() : null;
        $latestId = $latestReview ? (string) $latestReview->id : null;

        // Nếu client đã gửi thông số và mọi thứ hoàn toàn trùng khớp -> Không có cập nhật
        if (
            $clientLastCount !== null
            && (int) $clientLastCount === $totalCount
            && (string) $clientLastId === (string) $latestId
            && (string) $clientLastUpdated === (string) $latestUpdatedStr
        ) {
            return response()->json([
                'has_updates' => false,
                'total_count' => $totalCount,
            ]);
        }

        // Có đánh giá mới hoặc có cập nhật nội dung/phản hồi
        $ownerReviews = $quan->danhGia()
            ->with('nguoiDung:id,ho_ten,anh_dai_dien')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();
        $ownerReviewCount = $ownerReviews->count();
        $ownerUnrepliedCount = $ownerReviews->whereNull('phan_hoi')->count();
        $ownerAverageRating = $ownerReviewCount ? round($ownerReviews->avg('so_sao'), 1) : 0;

        $html = view('chu-quan.partials.reviews-list', [
            'quan' => $quan,
            'ownerReviews' => $ownerReviews,
        ])->render();

        return response()->json([
            'has_updates' => true,
            'total_count' => $ownerReviewCount,
            'unreplied_count' => $ownerUnrepliedCount,
            'average_rating' => $ownerAverageRating,
            'latest_review' => $latestReview ? [
                'id' => (string) $latestReview->id,
                'user_name' => $latestReview->nguoiDung?->ho_ten ?? 'Khách hàng',
                'so_sao' => (int) $latestReview->so_sao,
                'binh_luan' => \Illuminate\Support\Str::limit($latestReview->binh_luan, 150),
                'updated_at' => $latestUpdatedStr,
            ] : null,
            'html' => $html,
        ]);
    }
}
