<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuanDaLuuController extends Controller
{
    /**
     * View saved places page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Load saved places with necessary relationships (if any, e.g. location or images)
        // Adjust pagination or get as needed
        $quanDaLuu = $user->savedQuan()->with(['hinhAnh' => function($q) {
            $q->limit(1); // get primary image
        }])->get();

        return view('nguoi-dung.quan-da-luu.index', compact('user', 'quanDaLuu'));
    }

    /**
     * Toggle saved place status via AJAX
     */
    public function toggle(Request $request, $quanId)
    {
        $user = Auth::user();
        $quan = Quan::findOrFail($quanId);

        if ($user->savedQuan()->where('quan_id', $quanId)->exists()) {
            // Unsave
            $user->savedQuan()->detach($quanId);
            return response()->json([
                'success' => true,
                'status'  => 'unsaved',
                'message' => 'Đã bỏ lưu quán.'
            ]);
        } else {
            // Save
            $user->savedQuan()->attach($quanId);
            return response()->json([
                'success' => true,
                'status'  => 'saved',
                'message' => 'Đã lưu quán thành công!'
            ]);
        }
    }
}
