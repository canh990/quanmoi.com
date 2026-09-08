<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Services\BlogService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function index(Request $request)
    {
        $query = Blog::with(['user', 'category'])
            ->where('status', 'published');

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'like', "%{$searchTerm}%");
            });
        }

        $heroBlog = Blog::with(['user', 'category'])
            ->where('status', 'published')
            ->where('is_hero', true)
            ->latest('published_at')
            ->first();

        // Exclude hero blog from main list if exists
        if ($heroBlog) {
            $query->where('id', '!=', $heroBlog->id);
        }

        $blogs = $query->latest('published_at')->paginate(12);
        
        $categories = BlogCategory::where('status', true)->get();
        
        $tags = BlogTag::has('blogs')->limit(15)->get();

        return view('frontend.blog.index', compact('blogs', 'heroBlog', 'categories', 'tags'));
    }

    public function show(Request $request, string $slug)
    {
        $blog = Blog::with(['user', 'category', 'tags', 'comments' => function($q) {
            $q->where('status', 'active')->with('user')->latest();
        }])->where('slug', $slug)->firstOrFail();

        // If not published, only owner or admin can view
        if ($blog->status !== 'published') {
            if (!auth()->check() || (!auth()->user()->isAdmin() && auth()->id() !== $blog->user_id)) {
                abort(404);
            }
        } else {
            // Increment views for published blogs only
            $this->blogService->incrementView(
                $blog, 
                $request->session()->getId(), 
                $request->ip()
            );
        }

        $relatedBlogs = Blog::where('status', 'published')
            ->where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->latest('published_at')
            ->limit(3)
            ->get();
            
        $tags = BlogTag::has('blogs')->limit(15)->get();

        return view('frontend.blog.show', compact('blog', 'relatedBlogs', 'tags'));
    }
}
