<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuyenHan;
use App\Models\VaiTro;
use Illuminate\Http\Request;

class VaiTroController extends Controller
{
    public function index()
    {
        $roles = VaiTro::withCount(['nguoiDung', 'quyenHan'])->get();
        $permissions = QuyenHan::all();

        return view('admin.vai-tro.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten' => 'required|string|unique:vai_tro,ten|max:255',
        ]);

        VaiTro::create($validated);

        return redirect()->back()->with('success', 'Tạo vai trò thành công!');
    }

    public function updatePermissions(Request $request, $id)
    {
        $role = VaiTro::findOrFail($id);
        $permissionIds = $request->input('quyen_han', []);

        $role->quyenHan()->sync($permissionIds);

        return redirect()->back()->with('success', "Cập nhật quyền hạn cho vai trò '{$role->ten}' thành công!");
    }
}
