<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangKyQuanRequest;
use App\Models\Quan;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuanController extends Controller
{
    /**
     * Form Đăng quán
     */
    public function create()
    {
        return view('chu-quan.dang-quan');
    }

    /**
     * Xử lý lưu Quán mới vào Database
     */
    public function store(DangKyQuanRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();

        if ($user && !$user->isAdmin() && !$user->hasRole('chu_quan')) {
            $vaiTroChuQuan = VaiTro::where('ten', 'chu_quan')->first();

            if ($vaiTroChuQuan) {
                $user->update(['vai_tro_id' => $vaiTroChuQuan->id]);
            }
        }

        // Xử lý Upload Ảnh Bìa
        $anhBiaPath = null;
        if ($request->hasFile('anh_bia')) {
            $anhBiaPath = $request->file('anh_bia')->store('quan/anh-bia', 'public');
        }

        // Tạo Slug độc nhất
        $baseSlug = Str::slug($validated['ten_quan']);
        $slug = $baseSlug;
        $counter = 1;
        while (Quan::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // Người dùng hoặc tài khoản mặc định
        $chuQuanId = $user?->id ?? (Auth::check() ? Auth::id() : DB::table('nguoi_dung')->first()?->id);

        $quan = Quan::create([
            'chu_quan_id'          => $chuQuanId,
            'ten_quan'             => $validated['ten_quan'],
            'loai_hinh_kinh_doanh' => $validated['loai_hinh_kinh_doanh'] ?? 'Quán ăn',
            'slug'                 => $slug,
            'mo_ta'            => $validated['mo_ta'] ?? null,
            'so_dien_thoai'    => $validated['so_dien_thoai'],
            'email'            => $validated['email'] ?? null,
            'dia_chi_chi_tiet' => $validated['dia_chi_chi_tiet'],
            'tinh_thanh_id'    => $request->tinh_thanh_id ?? null,
            'ten_tinh_thanh'   => $validated['ten_tinh_thanh'],
            'quan_huyen_id'    => $request->quan_huyen_id ?? null,
            'ten_quan_huyen'   => $validated['ten_quan_huyen'],
            'phuong_xa_id'     => $request->phuong_xa_id ?? null,
            'ten_phuong_xa'    => $validated['ten_phuong_xa'] ?? null,
            'kinh_do'          => $request->kinh_do ?? 10.7769,
            'vi_do'            => $request->vi_do ?? 106.7009,
            'gio_mo_cua'       => $validated['gio_mo_cua'],
            'gio_dong_cua'     => $validated['gio_dong_cua'],
            'gia_nho_nhat'     => $validated['gia_nho_nhat'] ?? 0,
            'gia_lon_nhat'     => $validated['gia_lon_nhat'] ?? 0,
            'anh_bia'          => $anhBiaPath,
            'trang_thai'       => 'da_duyet',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'message'     => 'Đăng quán thành công!',
                'redirect_to' => route('chu-quan.quan.show', ['slug' => $quan->slug]),
                'data'        => $quan
            ]);
        }

        return redirect()->route('chu-quan.quan.show', ['slug' => $quan->slug])
            ->with('success', 'Đăng quán mới thành công!');
    }

    /**
     * Xem chi tiết quán dành cho Chủ quán
     */
    public function show($slug)
    {
        $quan = Quan::where('slug', $slug)->with(['hinhAnh', 'danhMucMenu.monAn'])->firstOrFail();

        return view('chu-quan.chi-tiet-quan', compact('quan'));
    }
}
