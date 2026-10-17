<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\Request;

class QuanNoiBatController extends Controller
{
    public function index()
    {
        $featuredQuan = Quan::where('is_noi_bat', true)->with('chuQuan')->latest()->paginate(15);
        $allQuan = Quan::where('is_noi_bat', false)->where('trang_thai', 'da_duyet')->take(50)->get();

        return view('admin.quan-noi-bat.index', compact('featuredQuan', 'allQuan'));
    }

    public function toggle(Request $request, $id)
    {
        $quan = Quan::findOrFail($id);
        $quan->update([
            'is_noi_bat' => ! $quan->is_noi_bat,
        ]);

        $status = $quan->is_noi_bat ? 'Đã bật' : 'Đã tắt';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_noi_bat' => $quan->is_noi_bat,
                'message' => "{$status} trạng thái nổi bật cho quán '{$quan->ten_quan}'!",
            ]);
        }

        return redirect()->back()->with('success', "{$status} trạng thái nổi bật cho quán '{$quan->ten_quan}'!");
    }
}
