<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NguoiDungQuyenHan;
use App\Models\QuyenHan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PhanQuyenController extends Controller
{
    public function index()
    {
        $permissions = QuyenHan::withCount('vaiTro')->get();
        $roles = VaiTro::with('quyenHan')->get();
        $users = User::with(['vaiTro', 'quyenHanOverrides'])->paginate(15);

        return view('admin.phan-quyen.index', compact('permissions', 'roles', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten' => 'required|string|unique:quyen_han,ten|max:255',
            'mo_ta' => 'nullable|string|max:500',
        ]);

        QuyenHan::create($validated);

        return redirect()->back()->with('success', 'Thêm quyền hạn mới thành công!');
    }

    public function overrideUserPermission(Request $request)
    {
        $validated = $request->validate([
            'nguoi_dung_id' => 'required|exists:nguoi_dung,id',
            'quyen_han_id' => 'required|exists:quyen_han,id',
            'cho_phep' => 'required|in:1,0,-1', // 1: cho_phep, 0: tu_choi, -1: xoa_override (dung mac dinh theo vai tro)
        ]);

        $userId = $validated['nguoi_dung_id'];
        $permId = $validated['quyen_han_id'];
        $action = $validated['cho_phep'];

        if ($action === '-1') {
            NguoiDungQuyenHan::where('nguoi_dung_id', $userId)
                ->where('quyen_han_id', $permId)
                ->delete();

            return redirect()->back()->with('success', 'Đã xoá cấu hình override quyền cho người dùng.');
        }

        NguoiDungQuyenHan::updateOrCreate(
            ['nguoi_dung_id' => $userId, 'quyen_han_id' => $permId],
            ['id' => Str::uuid()->toString(), 'cho_phep' => (bool) $action]
        );

        $statusText = $action === '1' ? 'Cấp quyền (Cho phép)' : 'Tước quyền (Từ chối)';
        return redirect()->back()->with('success', "Đã cập nhật override: {$statusText} thành công!");
    }
}
