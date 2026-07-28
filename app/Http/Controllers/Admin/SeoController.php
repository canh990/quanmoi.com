<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function index()
    {
        $seoList = SeoSetting::orderBy('id')->get();
        return view('admin.seo.index', compact('seoList'));
    }

    public function edit(string $trang)
    {
        $seo = SeoSetting::where('trang', $trang)->firstOrFail();
        return view('admin.seo.edit', compact('seo'));
    }

    public function update(Request $request, string $trang)
    {
        $request->validate([
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'og_title'         => 'nullable|string|max:255',
            'og_description'   => 'nullable|string|max:500',
            'og_image'         => 'nullable|url|max:500',
        ]);

        SeoSetting::where('trang', $trang)->update([
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
            'og_title'         => $request->og_title,
            'og_description'   => $request->og_description,
            'og_image'         => $request->og_image,
        ]);

        return redirect()->route('admin.seo.index')->with('success', 'Đã cập nhật SEO cho trang "' . $trang . '" thành công!');
    }
}
