<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaoMatController extends Controller
{
    public function index()
    {
        $securitySettings = [
            'two_factor_auth' => false,
            'ip_whitelist' => '',
            'login_rate_limit' => 5,
            'session_timeout' => 120,
        ];

        return view('admin.bao-mat.index', compact('securitySettings'));
    }

    public function update(Request $request)
    {
        return redirect()->back()->with('success', 'Đã cập nhật thiết lập bảo mật thành công!');
    }
}
