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
        $query = Blog::with(['category', 'moderationLogs' => function($q) {
            $q->latest();
        }])->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($catQ) use ($searchTerm) {
                      $catQ->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('tags', function ($tagQ) use ($searchTerm) {
                      $tagQ->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $blogs = $query->latest()->paginate(10)->withQueryString();

        $statusCounts = Blog::where('user_id', Auth::id())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalCount = array_sum($statusCounts);
            
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'total_count' => $totalCount,
                'status_counts' => $statusCounts,
                'blogs' => $blogs,
            ]);
        }

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
                    ['slug' => \Illuminate\Support\Str::slug($catName) ?: 'danh-muc-' . time()],
                    ['name' => $catName, 'status' => true]
                );
                $blog->category_id = $category->id;
            } elseif (!empty($data['category_id'])) {
                if (is_numeric($data['category_id'])) {
                    $blog->category_id = $data['category_id'];
                } else {
                    $catName = trim($data['category_id']);
                    $category = BlogCategory::firstOrCreate(
                        ['slug' => \Illuminate\Support\Str::slug($catName) ?: 'danh-muc-' . time()],
                        ['name' => $catName, 'status' => true]
                    );
                    $blog->category_id = $category->id;
                }
            }

            $blog->excerpt = $data['excerpt'] ?? null;
            $blog->content = function_exists('clean') ? clean($data['content']) : $data['content'];
            $blog->status = (isset($data['action']) && $data['action'] === 'pending') ? 'pending' : 'draft';
            $blog->allow_comments = $request->boolean('allow_comments', true);
            
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
                    $tagName = ltrim(trim($rawTag), '#');
                    if ($tagName !== '') {
                        $newTag = BlogTag::firstOrCreate(
                            ['slug' => \Illuminate\Support\Str::slug($tagName) ?: 'the-' . time() . '-' . rand(1, 999)],
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

                try {
                    $blog->user->notify(new \App\Notifications\BlogStatusUpdatedNotification($blog, 'submitted'));
                } catch (\Throwable $notiEx) {
                    \Illuminate\Support\Facades\Log::warning('Blog submission notification error: ' . $notiEx->getMessage());
                }

                try {
                    \App\Models\AdminAuditLog::create([
                        'actor_id' => Auth::id(),
                        'action' => 'blog.submitted',
                        'target_type' => Blog::class,
                        'target_id' => (string) $blog->id,
                        'metadata' => ['title' => $blog->title, 'author' => Auth::user()?->ho_ten],
                        'ip_address' => $request->ip(),
                    ]);
                } catch (\Throwable $auditEx) {
                    \Illuminate\Support\Facades\Log::warning('Blog submit audit log error: ' . $auditEx->getMessage());
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
                    ['slug' => \Illuminate\Support\Str::slug($catName) ?: 'danh-muc-' . time()],
                    ['name' => $catName, 'status' => true]
                );
                $blog->category_id = $category->id;
            }
        }

        $blog->excerpt = $data['excerpt'] ?? null;
        $blog->content = function_exists('clean') ? clean($data['content']) : $data['content'];
        $blog->allow_comments = $request->boolean('allow_comments', true);
        if ($oldStatus === 'published') {
            $blog->status = ($data['action'] === 'draft') ? 'draft' : 'published';
        } else {
            $blog->status = ($data['action'] === 'pending') ? 'pending' : 'draft';
        }
        
        if ($request->hasFile('cover_image')) {
            try {
                if ($blog->cover_image) {
                    $disk = config('filesystems.upload_disk', 'public');
                    if ($disk === 'r2' && empty(config('filesystems.disks.r2.bucket'))) {
                        $disk = 'public';
                    }
                    if (Storage::disk($disk)->exists($blog->cover_image)) {
                        Storage::disk($disk)->delete($blog->cover_image);
                    } elseif ($disk !== 'public' && Storage::disk('public')->exists($blog->cover_image)) {
                        Storage::disk('public')->delete($blog->cover_image);
                    }
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
                $tagName = ltrim(trim($rawTag), '#');
                if ($tagName !== '') {
                    $newTag = BlogTag::firstOrCreate(
                        ['slug' => \Illuminate\Support\Str::slug($tagName) ?: 'the-' . time() . '-' . rand(1, 999)],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $newTag->id;
                }
            }
        }

        $this->blogService->syncTags($blog, array_unique($tagIds));
        
        if ($blog->status === 'pending' && $oldStatus !== 'pending') {
            $blog->last_submitted_at = Carbon::now();
            $blog->save();
            try {
                BlogModerationLog::create([
                    'blog_id' => $blog->id,
                    'admin_id' => Auth::id(),
                    'action' => 'submitted',
                    'from_status' => $oldStatus,
                    'to_status' => 'pending',
                    'note' => 'Gửi lại bài viết để duyệt'
                ]);
            } catch (\Throwable $logEx) {
                \Illuminate\Support\Facades\Log::warning('BlogModerationLog update error: ' . $logEx->getMessage());
            }

            try {
                $blog->user->notify(new \App\Notifications\BlogStatusUpdatedNotification($blog, 'submitted'));
            } catch (\Throwable $notiEx) {}

            try {
                \App\Models\AdminAuditLog::create([
                    'actor_id' => Auth::id(),
                    'action' => 'blog.submitted',
                    'target_type' => Blog::class,
                    'target_id' => (string) $blog->id,
                    'metadata' => ['title' => $blog->title, 'author' => Auth::user()?->ho_ten],
                    'ip_address' => $request->ip(),
                ]);
            } catch (\Throwable $auditEx) {}
        } else {
            try {
                $blog->user->notify(new \App\Notifications\BlogStatusUpdatedNotification($blog, 'updated'));
            } catch (\Throwable $notiEx) {}
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

    /**
     * Danh sách bài viết đã lưu (Bookmark / Đọc sau) của người dùng
     */
    public function savedIndex(Request $request)
    {
        $user = Auth::user();

        $query = $user->savedBlogs()
            ->with(['user', 'category'])
            ->where('blogs.status', 'published');

        if ($request->filled('tab')) {
            if ($request->tab === 'unread') {
                $query->wherePivot('da_doc', false);
            } elseif ($request->tab === 'read') {
                $query->wherePivot('da_doc', true);
            }
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($catQ) use ($searchTerm) {
                      $catQ->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('user', function ($userQ) use ($searchTerm) {
                      $userQ->where('ho_ten', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $blogs = $query->orderBy('blog_da_luu.created_at', 'desc')
            ->paginate(9)
            ->withQueryString();

        $totalCount = $user->savedBlogs()->where('blogs.status', 'published')->count();
        $unreadCount = $user->unreadSavedBlogsCount();
        $readCount = $totalCount - $unreadCount;
        $categories = BlogCategory::where('status', true)->get();

        return view('nguoi-dung.blog.saved', compact('blogs', 'totalCount', 'unreadCount', 'readCount', 'categories'));
    }

    /**
     * Bật / tắt bookmark bài viết cho người dùng đã đăng nhập qua AJAX
     */
    public function toggleBookmark(Request $request, $blogId)
    {
        $user = Auth::user();
        $blog = Blog::findOrFail($blogId);

        $exists = $user->savedBlogs()->where('blog_id', $blog->id)->exists();

        if ($exists) {
            $user->savedBlogs()->detach($blog->id);
            $status = 'unbookmarked';
            $message = 'Đã bỏ lưu bài viết.';
        } else {
            $user->savedBlogs()->attach($blog->id, ['da_doc' => false]);
            $status = 'bookmarked';
            $message = 'Đã lưu bài viết thành công.';
        }

        $unreadCount = $user->unreadSavedBlogsCount();

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message,
            'count' => $unreadCount,
            'unread_count' => $unreadCount,
            'total_count' => $user->savedBlogs()->where('blogs.status', 'published')->count(),
        ]);
    }

    /**
     * Đánh dấu bài viết đã lưu là đã đọc hoặc chưa đọc
     */
    public function toggleRead(Request $request, $blogId)
    {
        $user = Auth::user();
        $record = $user->savedBlogs()->where('blog_id', $blogId)->first();

        if ($record) {
            $currentIsRead = !empty($record->pivot->da_doc);
            $newIsRead = !$currentIsRead;

            $user->savedBlogs()->updateExistingPivot($blogId, [
                'da_doc' => $newIsRead,
                'doc_luc' => $newIsRead ? now() : null,
            ]);

            $unreadCount = $user->unreadSavedBlogsCount();
            $totalCount = $user->savedBlogs()->where('blogs.status', 'published')->count();

            return response()->json([
                'success' => true,
                'da_doc' => $newIsRead,
                'unread_count' => $unreadCount,
                'total_count' => $totalCount,
                'read_count' => $totalCount - $unreadCount,
                'message' => $newIsRead ? 'Đã đánh dấu là đã đọc.' : 'Đã đánh dấu là chưa đọc.',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Bài viết chưa được lưu.'], 404);
    }

    /**
     * Đánh dấu bài viết đã lưu là đã đọc (khi click xem) mà không cần tải lại trang
     */
    public function markRead(Request $request, $blogId)
    {
        $user = Auth::user();
        $record = $user->savedBlogs()->where('blog_id', $blogId)->first();

        if ($record && empty($record->pivot->da_doc)) {
            $user->savedBlogs()->updateExistingPivot($blogId, [
                'da_doc' => true,
                'doc_luc' => now(),
            ]);
        }

        $unreadCount = $user->unreadSavedBlogsCount();
        $totalCount = $user->savedBlogs()->where('blogs.status', 'published')->count();

        return response()->json([
            'success' => true,
            'da_doc' => true,
            'unread_count' => $unreadCount,
            'total_count' => $totalCount,
            'read_count' => $totalCount - $unreadCount,
            'message' => 'Đã đánh dấu là đã đọc.',
        ]);
    }
}
