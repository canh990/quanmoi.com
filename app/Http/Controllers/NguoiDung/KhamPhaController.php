<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class KhamPhaController extends Controller
{
    /**
     * Display a listing of the restaurants, optionally filtered by category.
     */
    public function index(Request $request)
    {
        $tuKhoa      = trim(strip_tags(is_array($request->input('tu_khoa')) ? '' : (string) $request->input('tu_khoa')));
        $danhMuc     = trim(strip_tags(is_array($request->input('danh_muc')) ? '' : (string) $request->input('danh_muc')));
        $tinhThanhId = trim(strip_tags(is_array($request->input('tinh_thanh_id')) ? '' : (string) $request->input('tinh_thanh_id')));
        $quanHuyenId = trim(strip_tags(is_array($request->input('quan_huyen_id')) ? '' : (string) $request->input('quan_huyen_id')));
        $mucGia      = trim(strip_tags(is_array($request->input('muc_gia')) ? '' : (string) $request->input('muc_gia')));
        $sapXep      = trim(strip_tags(is_array($request->input('sap_xep')) ? '' : (string) $request->input('sap_xep')));
        
        $query = Quan::search($tuKhoa)->whereIn('trang_thai', ['da_duyet']);

        if (!empty($danhMuc)) {
            $query->whereIn('loai_hinh_kinh_doanh.keyword', [$danhMuc]);
        }

        if (!empty($tinhThanhId)) {
            $query->whereIn('tinh_thanh_id', [$tinhThanhId]);
        }

        if (!empty($quanHuyenId)) {
            $query->whereIn('quan_huyen_id', [$quanHuyenId]);
        }

        // Xử lý sắp xếp
        if ($sapXep === 'view_desc') {
            $query->orderBy('luot_xem', 'desc');
        } elseif ($sapXep === 'created_desc') {
            $query->orderBy('created_at', 'desc');
        } else {
            // Nếu không có từ khóa thì ưu tiên quán mới nhất
            if (empty($tuKhoa)) {
                $query->orderBy('created_at', 'desc');
            }
            // Nếu có từ khóa, Elasticsearch sẽ tự sắp xếp theo relevance (_score)
        }

        $quans = $query->paginate(12);
        $quans->appends($request->all());

        // Tìm các món ăn khớp với từ khóa (chỉ hiển thị ở trang đầu)
        $monAns = collect();
        if ($tuKhoa !== '' && $quans->currentPage() === 1) {
            $monAns = \App\Models\MonTrongMenu::query()
                ->where('ten_mon', 'like', '%' . $tuKhoa . '%')
                ->whereHas('danhMuc.quan', function ($q) use ($tinhThanhId, $quanHuyenId) {
                    $q->where('trang_thai', 'da_duyet');
                    if (!empty($tinhThanhId)) {
                        $q->where('tinh_thanh_id', $tinhThanhId);
                    }
                    if (!empty($quanHuyenId)) {
                        $q->where('quan_huyen_id', $quanHuyenId);
                    }
                })
                ->with(['danhMuc.quan:id,slug,ten_quan,ten_quan_huyen,ten_tinh_thanh,anh_bia'])
                ->orderByDesc('con_hang')
                ->take(12)
                ->get();
        }

        return view('nguoi-dung.kham-pha.index', [
            'quans'       => $quans,
            'monAns'      => $monAns,
            'danhMuc'     => $danhMuc,
            'tuKhoa'      => $tuKhoa,
            'tinhThanhId' => $tinhThanhId,
            'quanHuyenId' => $quanHuyenId,
            'mucGia'      => $mucGia,
            'sapXep'      => $sapXep,
        ]);
    }

    public function danhMuc($slug)
    {
        $danhMucMap = [
            'nha-hang' => 'Nhà hàng',
            'ca-phe-tra' => 'Cà phê & Trà',
            'billiards-giai-tri' => 'Billiards & Giải trí',
            'do-an-vat' => 'Đồ ăn vặt',
            'lau-nuong' => 'Lẩu & Nướng',
            'quan-dem-24-7' => 'Quán Đêm 24/7'
        ];

        $danhMuc = $danhMucMap[$slug] ?? null;
        if (!$danhMuc) {
            abort(404);
        }

        $quans = Quan::search('')->whereIn('trang_thai', ['da_duyet'])
            ->whereIn('loai_hinh_kinh_doanh.keyword', [$danhMuc])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('nguoi-dung.kham-pha.index', [
            'quans' => $quans,
            'danhMuc' => $danhMuc,
        ]);
    }

    public function quanMoi()
    {
        $quans = Quan::search('')->whereIn('trang_thai', ['da_duyet'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('nguoi-dung.kham-pha.index', [
            'quans' => $quans,
            'danhMuc' => 'Quán Mới',
        ]);
    }

    public function quanNoiBat()
    {
        $query = Quan::search('')->whereIn('trang_thai', ['da_duyet']);

        if (Schema::hasColumn('quan', 'is_noi_bat')) {
            $query->whereIn('is_noi_bat', [true]);
        }

        $quans = $query->orderBy('updated_at', 'desc')->paginate(12);

        return view('nguoi-dung.kham-pha.index', [
            'quans' => $quans,
            'danhMuc' => 'Quán Nổi Bật',
        ]);
    }
}
