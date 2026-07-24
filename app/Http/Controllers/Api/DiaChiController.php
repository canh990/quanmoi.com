<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DiaChiService;
use Illuminate\Http\Request;

class DiaChiController extends Controller
{
    protected DiaChiService $diaChiService;

    public function __construct(DiaChiService $diaChiService)
    {
        $this->diaChiService = $diaChiService;
    }

    public function getTinhThanh()
    {
        $data = $this->diaChiService->getTinhThanh();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getQuanHuyen($tinhCode)
    {
        $data = $this->diaChiService->getQuanHuyen($tinhCode);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getPhuongXa($huyenCode)
    {
        $data = $this->diaChiService->getPhuongXa($huyenCode);
        return response()->json(['success' => true, 'data' => $data]);
    }
}
