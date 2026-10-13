<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use App\Models\User;
use Illuminate\Http\Request;

class DuyetXacThucController extends Controller
{
    public function index()
    {
        $pendingQuan = Quan::where('trang_thai', 'chua_duyet')->with('chuQuan')->latest()->paginate(10);
        $unverifiedUsers = User::where('da_xac_thuc', false)->latest()->paginate(10, ['*'], 'users_page');

        return view('admin.duyet-xac-thuc.index', compact('pendingQuan', 'unverifiedUsers'));
    }

    public function approveQuan($id)
    {
        $quan = Quan::findOrFail($id);
        $quan->update([
            'trang_thai' => 'da_duyet',
            'is_xac_thuc' => true,
        ]);

        return redirect()->back()->with('success', "Đã duyệt quán '{$quan->ten_quan}' thành công!");
    }

    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'da_xac_thuc' => true,
            'ngay_xac_thuc' => now(),
        ]);

        return redirect()->back()->with('success', "Đã xác thực tài khoản người dùng '{$user->ho_ten}' thành công!");
    }
}
