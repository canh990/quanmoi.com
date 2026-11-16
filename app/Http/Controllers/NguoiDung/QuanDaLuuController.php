<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuanDaLuuController extends Controller
{
    /**
     * Truy vấn các quán đã duyệt mà người dùng hiện tại đã lưu.
     */
    private function approvedSavedQuery($user)
    {
        return $user->savedQuan()->where('quan.trang_thai', 'da_duyet');
    }

    /**
     * Tải danh sách quán đã lưu đầy đủ thông tin để hiển thị ở /quan-da-luu hoặc đồng bộ đa tab.
     */
    private function loadApprovedSavedQuan($user)
    {
        return $this->approvedSavedQuery($user)
            ->with([
                'hinhAnh' => function ($q) {
                    $q->limit(1);
                },
            ])
            ->withAvg('danhGia', 'so_sao')
            ->withCount('danhGia')
            ->orderBy('quan_da_luu.created_at', 'desc')
            ->get();
    }

    /**
     * View saved places page with rich ratings, categories, and coordinates
     */
    public function index()
    {
        $user = Auth::user();

        // Load saved places with images, rating aggregates, ordered newest first
        $quanDaLuu = $this->loadApprovedSavedQuan($user);

        // Extract unique categories from user's actual saved places
        $categories = $quanDaLuu->pluck('loai_hinh_kinh_doanh')
            ->filter(fn ($cat) => !empty($cat))
            ->unique()
            ->values()
            ->toArray();

        // Prepare clean structured payload for client-side search & Leaflet map
        $placesPayload = $quanDaLuu->map(function ($quan) {
            $address = trim(implode(', ', array_filter([
                $quan->dia_chi_chi_tiet,
                $quan->ten_phuong_xa,
                $quan->ten_quan_huyen,
                $quan->ten_tinh_thanh,
            ])));

            $primaryImg = $quan->hinhAnh->first()->url ?? ($quan->anh_bia_url ?? 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=400&q=80');

            $isOpen = method_exists($quan, 'isDangMoCua') ? $quan->isDangMoCua() : true;
            $avgRating = round((float) ($quan->danh_gia_avg_so_sao ?? 0), 1);
            $ratingCount = (int) ($quan->danh_gia_count ?? 0);

            $priceText = ($quan->gia_nho_nhat && $quan->gia_lon_nhat)
                ? number_format($quan->gia_nho_nhat, 0, ',', '.') . 'đ - ' . number_format($quan->gia_lon_nhat, 0, ',', '.') . 'đ'
                : ($quan->gia_nho_nhat ? 'Từ ' . number_format($quan->gia_nho_nhat, 0, ',', '.') . 'đ' : 'Đang cập nhật');

            return [
                'id' => (string) $quan->id,
                'name' => (string) $quan->ten_quan,
                'slug' => (string) $quan->slug,
                'url' => route('quan.detail', $quan->slug),
                'image' => (string) $primaryImg,
                'category' => (string) ($quan->loai_hinh_kinh_doanh ?? 'Quán ăn'),
                'address' => (string) $address,
                'isOpen' => (bool) $isOpen,
                'rating' => (float) $avgRating,
                'ratingCount' => (int) $ratingCount,
                'priceText' => (string) $priceText,
                'lat' => $quan->vi_do ? (float) $quan->vi_do : null,
                'lng' => $quan->kinh_do ? (float) $quan->kinh_do : null,
            ];
        })->values()->toArray();

        return view('nguoi-dung.quan-da-luu.index', compact('user', 'quanDaLuu', 'categories', 'placesPayload'));
    }

    /**
     * Trả về trạng thái lưu quán hiện tại và CSRF token mới nhất cho đồng bộ đa tab / bfcache.
     */
    public function sync(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->reflash();
        }

        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'authenticated' => false,
                'csrf_token' => csrf_token(),
                'saved_ids' => [],
                'saved_count' => 0,
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        }

        $savedIds = $this->approvedSavedQuery($user)
            ->orderBy('quan_da_luu.created_at', 'desc')
            ->pluck('quan.id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();

        $payload = [
            'authenticated' => true,
            'csrf_token' => csrf_token(),
            'saved_ids' => $savedIds,
            'saved_count' => count($savedIds),
        ];

        if ($request->boolean('with_places')) {
            $savedQuan = $this->loadApprovedSavedQuan($user);
            $payload['places'] = $savedQuan->values()->map(function ($quan, $index) {
                $fallbackImg = $index === 0
                    ? 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80'
                    : 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=500&q=80';
                $img = $quan->hinhAnh->first()->url ?? ($quan->anh_bia_url ?? $fallbackImg);
                $rating = $quan->danh_gia_avg_so_sao ? number_format($quan->danh_gia_avg_so_sao, 1) : '4.9';
                $loc = $quan->ten_quan_huyen
                    ? ($quan->ten_quan_huyen . ($quan->ten_tinh_thanh ? ', ' . Str::replace(['Thành phố ', 'Tỉnh '], '', $quan->ten_tinh_thanh) : ''))
                    : ($quan->dia_chi_chi_tiet ?? 'Việt Nam');

                return [
                    'id' => (string) $quan->id,
                    'name' => (string) $quan->ten_quan,
                    'url' => route('quan.detail', $quan->slug),
                    'image' => (string) $img,
                    'rating' => (string) $rating,
                    'location' => (string) $loc,
                    'isOpen' => (bool) (method_exists($quan, 'isDangMoCua') ? $quan->isDangMoCua() : false),
                ];
            })->all();
        }

        return response()->json($payload)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Toggle hoặc thực thi idempotent save/unsave quán qua AJAX (an toàn khi nhân đôi tab).
     */
    public function toggle(Request $request, $quanId)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'action' => ['nullable', 'in:save,unsave'],
        ]);
        $action = $validated['action'] ?? null;

        if ($action === 'unsave') {
            $detached = $user->savedQuan()->detach($quanId);

            if ($detached === 0) {
                return response()->json([
                    'success' => false,
                    'status' => 'unsaved',
                    'already_unsaved' => true,
                    'message' => 'Xóa không hợp lệ: Địa điểm này không còn trong danh sách đã lưu hoặc đã được bỏ lưu ở tab khác.',
                    'saved_count' => $this->approvedSavedQuery($user)->count(),
                ], 400);
            }

            return response()->json([
                'success' => true,
                'status' => 'unsaved',
                'message' => 'Đã bỏ lưu quán thành công.',
                'saved_count' => $this->approvedSavedQuery($user)->count(),
            ]);
        }

        if ($action === 'save') {
            $quan = Quan::whereKey($quanId)
                ->where('trang_thai', 'da_duyet')
                ->firstOrFail();

            if ($user->savedQuan()->where('quan.id', $quan->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'status' => 'saved',
                    'already_saved' => true,
                    'message' => 'Lưu không hợp lệ: Địa điểm này đã được lưu ở tab khác trước đó.',
                    'saved_count' => $this->approvedSavedQuery($user)->count(),
                ], 400);
            }

            $user->savedQuan()->syncWithoutDetaching([$quan->id]);

            return response()->json([
                'success' => true,
                'status' => 'saved',
                'message' => 'Đã lưu quán vào danh sách yêu thích!',
                'saved_count' => $this->approvedSavedQuery($user)->count(),
            ]);
        }

        if ($user->savedQuan()->detach($quanId) > 0) {
            // Unsave — luôn cho phép bỏ lưu kể cả khi trạng thái quán thay đổi sau khi lưu
            return response()->json([
                'success' => true,
                'status' => 'unsaved',
                'message' => 'Đã bỏ lưu quán thành công.',
                'saved_count' => $this->approvedSavedQuery($user)->count(),
            ]);
        }

        $quan = Quan::whereKey($quanId)
            ->where('trang_thai', 'da_duyet')
            ->firstOrFail();

        // Save (dùng syncWithoutDetaching tránh lỗi trùng khóa khi request đua song song giữa các tab)
        $user->savedQuan()->syncWithoutDetaching([$quan->id]);

        return response()->json([
            'success' => true,
            'status' => 'saved',
            'message' => 'Đã lưu quán vào danh sách yêu thích!',
            'saved_count' => $this->approvedSavedQuery($user)->count(),
        ]);
    }
}
