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
        $isXacThuc   = $request->boolean('is_xac_thuc');
        $isNoiBat    = $request->boolean('is_noi_bat');
        $dangMoCua   = $request->boolean('dang_mo_cua');
        $coShopee    = $request->boolean('co_shopeefood');
        
        $query = Quan::search($tuKhoa)->whereIn('trang_thai', ['da_duyet']);

        if (!empty($danhMuc)) {
            if ($danhMuc === 'Cà phê') {
                $query->whereIn('loai_hinh_kinh_doanh.keyword', ['Cà phê & Trà']);
            } else {
                $query->whereIn('loai_hinh_kinh_doanh.keyword', [$danhMuc]);
            }
        }

        if (!empty($tinhThanhId)) {
            $query->whereIn('tinh_thanh_id', [$tinhThanhId]);
        }

        if (!empty($quanHuyenId)) {
            $query->whereIn('quan_huyen_id', [$quanHuyenId]);
        }

        if ($isXacThuc) {
            $query->whereIn('is_xac_thuc', [true]);
        }

        if ($isNoiBat) {
            $query->whereIn('is_noi_bat', [true]);
        }

        if ($coShopee) {
            $query->whereIn('co_shopeefood', [true]);
        }

        if ($mucGia === 'duoi_50k') {
            $query->whereIn('duoi_50k', [true]);
        }

        if ($dangMoCua) {
            $query->whereIn('dang_mo_cua', [true]);
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

        // Gợi ý quán nổi bật khi kết quả rỗng (Smart Empty State)
        $quansGoiY = collect();
        if ($quans->isEmpty()) {
            $quansGoiY = Quan::where('trang_thai', 'da_duyet')
                ->where('is_noi_bat', true)
                ->inRandomOrder()
                ->take(4)
                ->get();

            if ($quansGoiY->isEmpty()) {
                $quansGoiY = Quan::where('trang_thai', 'da_duyet')
                    ->inRandomOrder()
                    ->take(4)
                    ->get();
            }
        }

        $tinhThanhMap = $this->getTinhThanhMap();
        $tenTinhThanh = $tinhThanhMap[$tinhThanhId] ?? $tinhThanhId;

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
            'quans'        => $quans,
            'quansGoiY'    => $quansGoiY,
            'monAns'       => $monAns,
            'danhMuc'      => $danhMuc,
            'tuKhoa'       => $tuKhoa,
            'tinhThanhId'  => $tinhThanhId,
            'tenTinhThanh' => $tenTinhThanh,
            'quanHuyenId'  => $quanHuyenId,
            'mucGia'       => $mucGia,
            'sapXep'       => $sapXep,
            'isXacThuc'    => $isXacThuc,
            'isNoiBat'     => $isNoiBat,
            'dangMoCua'    => $dangMoCua,
            'coShopee'     => $coShopee,
        ]);
    }

    public function danhMuc($slug, Request $request)
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

        $request->merge(['danh_muc' => $danhMuc]);
        return $this->index($request);
    }

    public function quanMoi(Request $request)
    {
        $tuKhoa      = trim(strip_tags(is_array($request->input('tu_khoa')) ? '' : (string) $request->input('tu_khoa')));
        $tinhThanhId = trim(strip_tags(is_array($request->input('tinh_thanh_id')) ? '' : (string) $request->input('tinh_thanh_id')));
        $quanHuyenId = trim(strip_tags(is_array($request->input('quan_huyen_id')) ? '' : (string) $request->input('quan_huyen_id')));
        $mucGia      = trim(strip_tags(is_array($request->input('muc_gia')) ? '' : (string) $request->input('muc_gia')));
        $sapXep      = trim(strip_tags(is_array($request->input('sap_xep')) ? '' : (string) $request->input('sap_xep')));
        $isXacThuc   = $request->boolean('is_xac_thuc');
        $isNoiBat    = $request->boolean('is_noi_bat');
        $dangMoCua   = $request->boolean('dang_mo_cua');
        $coShopee    = $request->boolean('co_shopeefood');

        $query = Quan::search($tuKhoa)->whereIn('trang_thai', ['da_duyet']);

        if (!empty($tinhThanhId)) {
            $query->whereIn('tinh_thanh_id', [$tinhThanhId]);
        }
        if (!empty($quanHuyenId)) {
            $query->whereIn('quan_huyen_id', [$quanHuyenId]);
        }
        if ($isXacThuc) {
            $query->whereIn('is_xac_thuc', [true]);
        }
        if ($isNoiBat) {
            $query->whereIn('is_noi_bat', [true]);
        }
        if ($coShopee) {
            $query->whereIn('co_shopeefood', [true]);
        }
        if ($mucGia === 'duoi_50k') {
            $query->whereIn('duoi_50k', [true]);
        }
        if ($dangMoCua) {
            $query->whereIn('dang_mo_cua', [true]);
        }

        if ($sapXep === 'view_desc') {
            $query->orderBy('luot_xem', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $quans = $query->paginate(12);
        $quans->appends($request->all());

        $quansGoiY = collect();
        if ($quans->isEmpty()) {
            $quansGoiY = Quan::where('trang_thai', 'da_duyet')
                ->where('is_noi_bat', true)
                ->inRandomOrder()
                ->take(4)
                ->get();

            if ($quansGoiY->isEmpty()) {
                $quansGoiY = Quan::where('trang_thai', 'da_duyet')
                    ->inRandomOrder()
                    ->take(4)
                    ->get();
            }
        }

        $tinhThanhMap = $this->getTinhThanhMap();
        $tenTinhThanh = $tinhThanhMap[$tinhThanhId] ?? $tinhThanhId;

        return view('nguoi-dung.kham-pha.index', [
            'quans'        => $quans,
            'quansGoiY'    => $quansGoiY,
            'monAns'       => collect(),
            'danhMuc'      => 'Quán Mới',
            'tuKhoa'       => $tuKhoa,
            'tinhThanhId'  => $tinhThanhId,
            'tenTinhThanh' => $tenTinhThanh,
            'quanHuyenId'  => $quanHuyenId,
            'mucGia'       => $mucGia,
            'sapXep'       => $sapXep ?: 'created_desc',
            'isXacThuc'    => $isXacThuc,
            'isNoiBat'     => $isNoiBat,
            'dangMoCua'    => $dangMoCua,
            'coShopee'     => $coShopee,
        ]);
    }

    public function quanNoiBat(Request $request)
    {
        $tuKhoa      = trim(strip_tags(is_array($request->input('tu_khoa')) ? '' : (string) $request->input('tu_khoa')));
        $tinhThanhId = trim(strip_tags(is_array($request->input('tinh_thanh_id')) ? '' : (string) $request->input('tinh_thanh_id')));
        $quanHuyenId = trim(strip_tags(is_array($request->input('quan_huyen_id')) ? '' : (string) $request->input('quan_huyen_id')));
        $mucGia      = trim(strip_tags(is_array($request->input('muc_gia')) ? '' : (string) $request->input('muc_gia')));
        $sapXep      = trim(strip_tags(is_array($request->input('sap_xep')) ? '' : (string) $request->input('sap_xep')));
        $isXacThuc   = $request->boolean('is_xac_thuc');
        $dangMoCua   = $request->boolean('dang_mo_cua');
        $coShopee    = $request->boolean('co_shopeefood');

        $query = Quan::search($tuKhoa)->whereIn('trang_thai', ['da_duyet'])->whereIn('is_noi_bat', [true]);

        if (!empty($tinhThanhId)) {
            $query->whereIn('tinh_thanh_id', [$tinhThanhId]);
        }
        if (!empty($quanHuyenId)) {
            $query->whereIn('quan_huyen_id', [$quanHuyenId]);
        }
        if ($isXacThuc) {
            $query->whereIn('is_xac_thuc', [true]);
        }
        if ($coShopee) {
            $query->whereIn('co_shopeefood', [true]);
        }
        if ($mucGia === 'duoi_50k') {
            $query->whereIn('duoi_50k', [true]);
        }
        if ($dangMoCua) {
            $query->whereIn('dang_mo_cua', [true]);
        }

        if ($sapXep === 'view_desc') {
            $query->orderBy('luot_xem', 'desc');
        } elseif ($sapXep === 'created_desc') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $quans = $query->paginate(12);
        $quans->appends($request->all());

        $quansGoiY = collect();
        if ($quans->isEmpty()) {
            $quansGoiY = Quan::where('trang_thai', 'da_duyet')
                ->where('is_noi_bat', true)
                ->inRandomOrder()
                ->take(4)
                ->get();

            if ($quansGoiY->isEmpty()) {
                $quansGoiY = Quan::where('trang_thai', 'da_duyet')
                    ->inRandomOrder()
                    ->take(4)
                    ->get();
            }
        }

        $tinhThanhMap = $this->getTinhThanhMap();
        $tenTinhThanh = $tinhThanhMap[$tinhThanhId] ?? $tinhThanhId;

        return view('nguoi-dung.kham-pha.index', [
            'quans'        => $quans,
            'quansGoiY'    => $quansGoiY,
            'monAns'       => collect(),
            'danhMuc'      => 'Quán Nổi Bật',
            'tuKhoa'       => $tuKhoa,
            'tinhThanhId'  => $tinhThanhId,
            'tenTinhThanh' => $tenTinhThanh,
            'quanHuyenId'  => $quanHuyenId,
            'mucGia'       => $mucGia,
            'sapXep'       => $sapXep,
            'isXacThuc'    => $isXacThuc,
            'isNoiBat'     => true,
            'dangMoCua'    => $dangMoCua,
            'coShopee'     => $coShopee,
        ]);
    }

    protected function getTinhThanhMap(): array
    {
        return [
            'HCM'  => 'Hồ Chí Minh',
            'HN'   => 'Hà Nội',
            'DN'   => 'Đà Nẵng',
            'CT'   => 'Cần Thơ',
            'BD'   => 'Bình Dương',
            'DNai' => 'Đồng Nai',
            'VT'   => 'Bà Rịa - Vũng Tàu',
            'HUE'  => 'Thừa Thiên Huế',
            'HP'   => 'Hải Phòng',
            'KH'   => 'Khánh Hòa',
            'LD'   => 'Lâm Đồng',
        ];
    }
}
