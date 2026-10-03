<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThongBaoAdminController extends Controller
{
    public function index()
    {
        $notifications = []; // Placeholder for system admin notifications list
        return view('admin.thong-bao.index', compact('notifications'));
    }

    public function sendSystemNotification(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'doi_tuong' => 'required|in:tat_ca,chu_quan,nguoi_dung',
        ]);

        return redirect()->back()->with('success', 'Đã gửi thông báo hệ thống thành công!');
    }
}
