<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Services\BlogService;
use App\Models\BlogModerationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BlogController extends Controller
{
    protected BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function index(Request $request)
    {
        $blogs = Blog::with(['category'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
            
        return view('nguoi-dung.blog.index', compact('blogs'));
    }

    public function create()
    {
        Gate::authorize('create', Blog::class);
        $categories = BlogCategory::where('status', true)->get();
        $tags = BlogTag::all();
        
        return view('nguoi-dung.blog.create', compact('categories', 'tags'));
    }

    public function store(StoreBlogRequest $request)
    {
        $data = $request->validated();
        
        $blog = new Blog();
        $blog->user_id = Auth::id();
        $blog->title = $data['title'];
        $blog->slug = $this->blogService->generateUniqueSlug($data['title']);
        $blog->category_id = $data['category_id'];
        $blog->excerpt = $data['excerpt'];
        $blog->content = $data['content'];
        $blog->status = $data['action'] === 'pending' ? 'pending' : 'draft';
        
        if ($request->hasFile('cover_image')) {
            $blog->cover_image = $this->blogService->uploadCoverImage($request->file('cover_image'));
        }
        
        $blog->save();
        
        if (isset($data['tags'])) {
            $this->blogService->syncTags($blog, $data['tags']);
        }
        
        if ($blog->status === 'pending') {
            $blog->last_submitted_at = now();
            $blog->save();
            BlogModerationLog::create([
                'blog_id' => $blog->id,
                'user_id' => Auth::id(),
                'action' => 'submitted',
                'from_status' => null,
                'to_status' => 'pending',
                'note' => 'Gửi bài viết để duyệt'
            ]);
        }
        
        return redirect()->route('nguoi-dung.blog.index')->with('success', 'Đã lưu bài viết thành công.');
    }

    public function edit(Blog $blog)
    {
        Gate::authorize('update', $blog);
        
        $categories = BlogCategory::where('status', true)->get();
        $tags = BlogTag::all();
        
        return view('nguoi-dung.blog.edit', compact('blog', 'categories', 'tags'));
    }

    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $data = $request->validated();
        
        $oldStatus = $blog->status;
        
        $blog->title = $data['title'];
        // Optional: Do not change slug to prevent breaking links, or update it
        // $blog->slug = $this->blogService->generateUniqueSlug($data['title'], $blog->id);
        $blog->category_id = $data['category_id'];
        $blog->excerpt = $data['excerpt'];
        $blog->content = $data['content'];
        $blog->status = $data['action'] === 'pending' ? 'pending' : 'draft';
        
        if ($request->hasFile('cover_image')) {
            $blog->cover_image = $this->blogService->uploadCoverImage($request->file('cover_image'));
        }
        
        $blog->save();
        
        if (isset($data['tags'])) {
            $this->blogService->syncTags($blog, $data['tags']);
        }
        
        if ($blog->status === 'pending' && $oldStatus !== 'pending') {
            $blog->last_submitted_at = now();
            $blog->save();
            BlogModerationLog::create([
                'blog_id' => $blog->id,
                'admin_id' => Auth::id(), // We map it to admin_id field as per user req
                'action' => 'submitted',
                'from_status' => $oldStatus,
                'to_status' => 'pending',
                'note' => 'Gửi lại bài viết để duyệt'
            ]);
        }
        
        return redirect()->route('nguoi-dung.blog.index')->with('success', 'Đã cập nhật bài viết thành công.');
    }

    public function destroy(Blog $blog)
    {
        Gate::authorize('delete', $blog);
        $blog->delete();
        return back()->with('success', 'Đã xóa bài viết.');
    }
}
