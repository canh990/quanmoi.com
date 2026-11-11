<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogTagController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $query = BlogTag::withCount('blogs');

        if ($q !== '') {
            $tokens = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [$q];
            $query->where(function ($sub) use ($q, $tokens) {
                $sub->where('name', 'like', "%{$q}%");

                if (count($tokens) > 1) {
                    $sub->orWhere(function ($tokSub) use ($tokens) {
                        foreach ($tokens as $token) {
                            $tokSub->where('name', 'like', "%{$token}%");
                        }
                    });
                }
            });
        }

        $tags = $query->latest()->paginate(20)->withQueryString();
        return view('admin.blog.tags.index', compact('tags', 'q'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        BlogTag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);
        
        return back()->with('success', 'Thêm thẻ thành công.');
    }
}
