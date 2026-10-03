<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AiToolsController extends Controller
{
    public function index()
    {
        return view('admin.ai-tools.index');
    }

    public function generateSeoDescription(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        return response()->json([
            'success' => true,
            'result' => "AI Suggested Content based on '{$request->prompt}': Khám phá các địa điểm ăn uống độc đáo và hấp dẫn nhất cùng Quán Mới!",
        ]);
    }
}
