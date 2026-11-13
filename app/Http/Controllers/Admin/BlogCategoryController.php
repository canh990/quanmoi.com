<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $query = BlogCategory::withCount('blogs');

        if ($q !== '') {
            $tokens = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [$q];
            $query->where(function ($sub) use ($q, $tokens) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");

                if (count($tokens) > 1) {
                    $sub->orWhere(function ($tokSub) use ($tokens) {
                        foreach ($tokens as $token) {
                            $tokSub->where('name', 'like', "%{$token}%");
                        }
                    });
                }
            });
        }

        $categories = $query->latest()->paginate(15)->withQueryString();
        return view('admin.blog.categories.index', compact('categories', 'q'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);
        
        BlogCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        
        return back()->with('success', 'Thêm danh mục thành công.');
    }
}
