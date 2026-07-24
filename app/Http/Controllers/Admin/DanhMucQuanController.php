<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMucQuan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DanhMucQuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $danhMucs = DanhMucQuan::latest()->paginate(15);
        return view('admin.danh-muc.index', compact('danhMucs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.danh-muc.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:255|unique:danh_muc_quans,ten_danh_muc',
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục.',
            'ten_danh_muc.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        DanhMucQuan::create([
            'ten_danh_muc' => $request->ten_danh_muc,
            'slug' => Str::slug($request->ten_danh_muc),
        ]);

        return redirect()->route('admin.danh-muc.index')->with('success', 'Thêm danh mục thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DanhMucQuan $danhMuc)
    {
        return view('admin.danh-muc.form', compact('danhMuc'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DanhMucQuan $danhMuc)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:255|unique:danh_muc_quans,ten_danh_muc,' . $danhMuc->id,
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục.',
            'ten_danh_muc.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        $danhMuc->update([
            'ten_danh_muc' => $request->ten_danh_muc,
            'slug' => Str::slug($request->ten_danh_muc),
        ]);

        return redirect()->route('admin.danh-muc.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DanhMucQuan $danhMuc)
    {
        $danhMuc->delete();
        return redirect()->route('admin.danh-muc.index')->with('success', 'Xóa danh mục thành công.');
    }
}
