<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogTagController extends Controller
{
    public function index()
    {
        $tags = BlogTag::withCount('blogs')->latest()->paginate(20);
        return view('admin.blog.tags.index', compact('tags'));
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
