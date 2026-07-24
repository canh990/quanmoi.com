<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoData;
use Illuminate\Http\Request;

class SeoDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SeoData::query();

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('link', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('page_type')) {
            $query->where('page_type', $request->page_type);
        }

        if ($request->filled('loai_hinh_kinh_doanh')) {
            $query->where('loai_hinh_kinh_doanh', $request->loai_hinh_kinh_doanh);
        }

        if ($request->filled('province_code')) {
            $query->where('province_code', $request->province_code);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $seoDatas = $query->latest()->paginate(25)->withQueryString();

        return view('admin.seo-datas.index', compact('seoDatas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.seo-datas.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_type' => 'required|string',
            'name' => 'required|string|max:255',
            'loai_hinh_kinh_doanh' => 'nullable|string|max:255',
            'province_code' => 'nullable|string|max:255',
            'district_code' => 'nullable|string|max:255',
            'link' => 'required|string|max:255',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        SeoData::create($validated);

        return redirect()->route('admin.seo-datas.index')->with('success', 'Thêm SEO Data thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeoData $seoData)
    {
        return view('admin.seo-datas.form', compact('seoData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeoData $seoData)
    {
        $validated = $request->validate([
            'page_type' => 'required|string',
            'name' => 'required|string|max:255',
            'loai_hinh_kinh_doanh' => 'nullable|string|max:255',
            'province_code' => 'nullable|string|max:255',
            'district_code' => 'nullable|string|max:255',
            'link' => 'required|string|max:255',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $seoData->update($validated);

        return redirect()->route('admin.seo-datas.index')->with('success', 'Cập nhật SEO Data thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SeoData $seoData)
    {
        $seoData->delete();
        return redirect()->route('admin.seo-datas.index')->with('success', 'Xóa SEO Data thành công.');
    }
}
