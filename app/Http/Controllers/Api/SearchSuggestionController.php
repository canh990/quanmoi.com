<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\Request;

class SearchSuggestionController extends Controller
{
    /**
     * Trả về kết quả gợi ý tức thì (Live Autocomplete)
     */
    public function suggestions(Request $request)
    {
        $q = trim(strip_tags((string) $request->input('q', '')));

        if (empty($q)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'quans' => [],
                    'mons' => [],
                ]
            ]);
        }

        // Tìm tối đa 5 quán phù hợp nhất
        $quans = Quan::search($q)
            ->whereIn('trang_thai', ['da_duyet'])
            ->take(5)
            ->get();

        $quanResults = $quans->map(function ($quan) {
            return [
                'id' => $quan->id,
                'ten_quan' => $quan->ten_quan,
                'dia_chi' => $quan->dia_chi_chi_tiet, // Địa chỉ rút gọn
                'anh_bia' => $quan->anh_bia_url,
                'slug' => $quan->slug,
                'loai_hinh_kinh_doanh' => $quan->loai_hinh_kinh_doanh,
            ];
        });

        // Tìm món ăn liên quan trong kết quả (Lấy danh sách món từ các quán tìm được)
        // Đây là mock dữ liệu món ăn, thực tế có thể lấy từ bảng MonTrongMenu hoặc ES
        // Vì trong ES đã lưu trường `mon_an`, ta có thể bóc tách món ăn phù hợp với từ khóa $q.
        
        $monResults = [];
        // Lặp qua các quán để lấy ra các món khớp với từ khóa
        $qLower = mb_strtolower($q, 'UTF-8');
        foreach ($quans as $quan) {
            // Danh sách món ăn được eager load nếu có, hoặc dùng raw DB.
            // Để tối ưu tốc độ Autocomplete, chỉ dùng text matching đơn giản trên danh sách món nếu đã được load.
            // Tuy nhiên, để chính xác, chúng ta sẽ query bảng mon_trong_menu:
            
            // Ở đây tạm thời ta giới hạn 3 món ăn khớp nhất.
        }

        // Lấy 3 món ăn từ DB
        $mons = \App\Models\MonTrongMenu::where('ten_mon', 'like', '%' . $q . '%')
            ->whereHas('danhMuc.quan', function ($qBuilder) {
                $qBuilder->where('trang_thai', 'da_duyet');
            })
            ->with(['danhMuc.quan:id,slug,ten_quan'])
            ->take(3)
            ->get();

        $monResults = $mons->map(function ($mon) {
            return [
                'id' => $mon->id,
                'ten_mon' => $mon->ten_mon,
                'gia' => $mon->gia,
                'quan_slug' => $mon->danhMuc->quan->slug,
                'ten_quan' => $mon->danhMuc->quan->ten_quan,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'quans' => $quanResults,
                'mons' => $monResults,
            ]
        ]);
    }
}
