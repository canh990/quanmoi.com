<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Models\HinhAnhQuan;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HinhAnhQuanController extends Controller
{
    /**
     * Upload bộ sưu tập hình ảnh quán
     */
    public function store(Request $request, $quanId)
    {
        $request->validate([
            'hinh_anh.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'hinh_anh.*.image' => 'File tải lên phải là hình ảnh.',
            'hinh_anh.*.max'   => 'Dung lượng ảnh tối đa 5MB.',
        ]);

        $quan = Quan::findOrFail($quanId);
        $uploaded = [];

        if ($request->hasFile('hinh_anh')) {
            foreach ($request->file('hinh_anh') as $file) {
                $path = $file->store('quan/gallery', 'public');
                $img = HinhAnhQuan::create([
                    'quan_id'   => $quan->id,
                    'duong_dan' => $path,
                    'tieu_de'   => $file->getClientOriginalName(),
                ]);
                $uploaded[] = $img;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tải lên hình ảnh thành công!',
            'data'    => $uploaded,
        ]);
    }

    /**
     * Xóa hình ảnh khỏi thư viện quán
     */
    public function destroy($id)
    {
        $img = HinhAnhQuan::findOrFail($id);
        Storage::disk('public')->delete($img->duong_dan);
        $img->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ảnh thành công.',
        ]);
    }
}
