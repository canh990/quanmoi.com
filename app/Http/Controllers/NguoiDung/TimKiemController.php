<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimKiemQuanRequest;
use App\Services\TimKiemService;
use App\Services\DiaChiService;
use App\Models\DanhMucQuan;
use Illuminate\Http\Request;

class TimKiemController extends Controller
{
    protected TimKiemService $timKiemService;
    protected DiaChiService $diaChiService;

    public function __construct(TimKiemService $timKiemService, DiaChiService $diaChiService)
    {
        $this->timKiemService = $timKiemService;
        $this->diaChiService = $diaChiService;
    }

    /**
     * Hiển thị trang tìm kiếm chính
     */
    public function index(Request $request)
    {
        $danhMucs = DanhMucQuan::all();
        $tinhThanhs = $this->diaChiService->getTinhThanh();
        
        $quans = collect();
        $type = $request->input('type');

        if ($type === 'lan_can') {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'ban_kinh' => 'nullable|numeric|in:1,3,5,10',
                'danh_muc_id' => 'nullable|uuid|exists:danh_muc_quans,id',
            ]);

            $quans = $this->timKiemService->timLanCan(
                (float) $request->latitude,
                (float) $request->longitude,
                (float) ($request->ban_kinh ?? 3.0),
                $request->danh_muc_id
            );
        } elseif ($type === 'dia_chi') {
            $request->validate([
                'tinh_thanh_id' => 'required|string',
                'quan_huyen_id' => 'nullable|string',
                'phuong_xa_id' => 'nullable|string',
                'danh_muc_id' => 'nullable|uuid|exists:danh_muc_quans,id',
            ]);

            $quans = $this->timKiemService->timTheoDiaChi(
                $request->tinh_thanh_id,
                $request->quan_huyen_id,
                $request->phuong_xa_id,
                $request->danh_muc_id
            );
        }

        return view('nguoi-dung.tim-kiem', compact('danhMucs', 'tinhThanhs', 'quans', 'type'));
    }
}
