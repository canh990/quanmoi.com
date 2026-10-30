<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuanDaLuuController extends Controller
{
    /**
     * View saved places page with rich ratings, categories, and coordinates
     */
    public function index()
    {
        $user = Auth::user();

        // Load saved places with images, rating aggregates, ordered newest first
        $quanDaLuu = $user->savedQuan()
            ->where('quan.trang_thai', 'da_duyet')
            ->with([
                'hinhAnh' => function ($q) {
                    $q->limit(1);
                }
            ])
            ->withAvg('danhGia', 'so_sao')
            ->withCount('danhGia')
            ->orderBy('quan_da_luu.created_at', 'desc')
            ->get();

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
     * Toggle saved place status via AJAX
     */
    public function toggle(Request $request, $quanId)
    {
        $user = Auth::user();
        $quan = Quan::whereKey($quanId)
            ->where('trang_thai', 'da_duyet')
            ->firstOrFail();

        if ($user->savedQuan()->where('quan_id', $quanId)->exists()) {
            // Unsave
            $user->savedQuan()->detach($quanId);

            return response()->json([
                'success' => true,
                'status' => 'unsaved',
                'message' => 'Đã bỏ lưu quán thành công.',
            ]);
        } else {
            // Save
            $user->savedQuan()->attach($quanId);

            return response()->json([
                'success' => true,
                'status' => 'saved',
                'message' => 'Đã lưu quán vào danh sách yêu thích!',
            ]);
        }
    }
}
