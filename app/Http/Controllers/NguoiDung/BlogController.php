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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    protected BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function index(Request $request)
    {
        $query = Blog::with(['category'])
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $blogs = $query->latest()->paginate(10);

        $statusCounts = Blog::where('user_id', Auth::id())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalCount = array_sum($statusCounts);
            
        return view('nguoi-dung.blog.index', compact('blogs', 'statusCounts', 'totalCount'));
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
        
        try {
            $blog = new Blog();
            $blog->user_id = Auth::id();
            $blog->title = $data['title'];
            $blog->slug = $this->blogService->generateUniqueSlug($data['title']);

            // Handle typed Category Name or selected Category ID
            $catName = trim($request->input('category_name', $request->input('new_category', '')));
            if (!empty($catName)) {
                $category = BlogCategory::firstOrCreate(
                    ['slug' => \Illuminate\Support\Str::slug($catName)],
                    ['name' => $catName, 'status' => true]
                );
                $blog->category_id = $category->id;
            } elseif (!empty($data['category_id'])) {
                if (is_numeric($data['category_id'])) {
                    $blog->category_id = $data['category_id'];
                } else {
                    $catName = trim($data['category_id']);
                    $category = BlogCategory::firstOrCreate(
                        ['slug' => \Illuminate\Support\Str::slug($catName)],
                        ['name' => $catName, 'status' => true]
                    );
                    $blog->category_id = $category->id;
                }
            }

            $blog->excerpt = $data['excerpt'] ?? null;
            $blog->content = clean($data['content']);
            $blog->status = (isset($data['action']) && $data['action'] === 'pending') ? 'pending' : 'draft';
            
            if ($request->hasFile('cover_image')) {
                $blog->cover_image = $this->blogService->uploadCoverImage($request->file('cover_image'));
            }
            
            $blog->save();
            
            // Handle Tags (Selected existing tag IDs + custom typed new tags string)
            $tagIds = [];
            if (!empty($data['tags']) && is_array($data['tags'])) {
                $tagIds = array_merge($tagIds, $data['tags']);
            }

            if (!empty($request->input('custom_tags'))) {
                $rawTags = explode(',', $request->input('custom_tags'));
                foreach ($rawTags as $rawTag) {
                    $tagName = trim($rawTag);
                    if ($tagName !== '') {
                        $newTag = BlogTag::firstOrCreate(
                            ['slug' => \Illuminate\Support\Str::slug($tagName)],
                            ['name' => $tagName]
                        );
                        $tagIds[] = $newTag->id;
                    }
                }
            }

            if (!empty($tagIds)) {
                $this->blogService->syncTags($blog, array_unique($tagIds));
            }
            
            if ($blog->status === 'pending') {
                $blog->last_submitted_at = now();
                $blog->save();
                try {
                    BlogModerationLog::create([
                        'blog_id' => $blog->id,
                        'admin_id' => Auth::id(),
                        'action' => 'submitted',
                        'from_status' => null,
                        'to_status' => 'pending',
                        'note' => 'Gửi bài viết để duyệt'
                    ]);
                } catch (\Throwable $logEx) {
                    \Illuminate\Support\Facades\Log::error('BlogModerationLog error: ' . $logEx->getMessage());
                }
            }
            
            $msg = $blog->status === 'pending'
                ? 'Đã gửi bài viết thành công! Bài viết của bạn đang được duyệt.'
                : 'Đã lưu bản nháp thành công!';
            
            if ($request->filled('redirect_to')) {
                return redirect($request->input('redirect_to'))->with('success', $msg);
            }

            return redirect()->route('nguoi-dung.blog.index')->with('success', $msg);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Blog store error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
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
        
        // Handle typed Category Name or selected Category ID
        $catName = trim($request->input('category_name', $request->input('new_category', '')));
        if (!empty($catName)) {
            $category = BlogCategory::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($catName)],
                ['name' => $catName, 'status' => true]
            );
            $blog->category_id = $category->id;
        } elseif (!empty($data['category_id'])) {
            if (is_numeric($data['category_id'])) {
                $blog->category_id = $data['category_id'];
            } else {
                $catName = trim($data['category_id']);
                $category = BlogCategory::firstOrCreate(
                    ['slug' => \Illuminate\Support\Str::slug($catName)],
                    ['name' => $catName, 'status' => true]
                );
                $blog->category_id = $category->id;
            }
        }

        $blog->excerpt = $data['excerpt'] ?? null;
        $blog->content = clean($data['content']);
        $blog->status = $data['action'] === 'pending' ? 'pending' : 'draft';
        
        if ($request->hasFile('cover_image')) {
            try {
                if ($blog->cover_image) {
                    Storage::disk('r2')->delete($blog->cover_image);
                }
            } catch (\Throwable $th) {
                // Ignore storage deletion errors
            }
            $blog->cover_image = $this->blogService->uploadCoverImage($request->file('cover_image'));
        }
        
        $blog->save();
        
        // Handle Tags (Selected existing tag IDs + custom typed new tags string)
        $tagIds = [];
        if (!empty($data['tags']) && is_array($data['tags'])) {
            $tagIds = array_merge($tagIds, $data['tags']);
        }

        if (!empty($request->input('custom_tags'))) {
            $rawTags = explode(',', $request->input('custom_tags'));
            foreach ($rawTags as $rawTag) {
                $tagName = trim($rawTag);
                if ($tagName !== '') {
                    $newTag = BlogTag::firstOrCreate(
                        ['slug' => \Illuminate\Support\Str::slug($tagName)],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $newTag->id;
                }
            }
        }

        if (!empty($tagIds)) {
            $this->blogService->syncTags($blog, array_unique($tagIds));
        }
        
        if ($blog->status === 'pending' && $oldStatus !== 'pending') {
            $blog->last_submitted_at = Carbon::now();
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
        
        // When user soft deletes, we don't necessarily delete the R2 image yet.
        // It will be deleted if they forceDelete later.
        
        $blog->delete();
        return back()->with('success', 'Đã xóa bài viết.');
    }
}
