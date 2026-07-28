<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\Request;

class KhamPhaController extends Controller
{
    /**
     * Display a listing of the restaurants, optionally filtered by category.
     */
    public function index(Request $request)
    {
        $danhMuc = $request->input('danh_muc');
        
        $query = Quan::query()->where('trang_thai', 'da_duyet');

        if ($danhMuc) {
            $query->where('loai_hinh_kinh_doanh', $danhMuc);
        }

        // Get paginated results, latest first
        $quans = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('nguoi-dung.kham-pha.index', [
            'quans' => $quans,
            'danhMuc' => $danhMuc,
        ]);
    }

    public function quanMoi()
    {
        $quans = Quan::query()->where('trang_thai', 'da_duyet')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('nguoi-dung.kham-pha.index', [
            'quans' => $quans,
            'danhMuc' => 'Quán Mới',
        ]);
    }

    public function quanNoiBat()
    {
        $quans = Quan::query()->where('trang_thai', 'da_duyet')
            ->where('is_noi_bat', true)
            ->orderBy('updated_at', 'desc')
            ->paginate(12);

        return view('nguoi-dung.kham-pha.index', [
            'quans' => $quans,
            'danhMuc' => 'Quán Nổi Bật',
        ]);
    }
}
