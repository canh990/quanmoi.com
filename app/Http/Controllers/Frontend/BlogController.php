<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use App\Services\BlogAiService;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    protected BlogService $blogService;
    protected BlogAiService $blogAiService;

    public function __construct(BlogService $blogService, BlogAiService $blogAiService)
    {
        $this->blogService = $blogService;
        $this->blogAiService = $blogAiService;
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

        if ($request->has('suggest') || ($request->ajax() && $request->has('search'))) {
            $searchTerm = trim($request->input('search', ''));
            if ($searchTerm === '') {
                return response()->json([]);
            }
            $suggestions = Blog::with(['category'])
                ->where('status', 'published')
                ->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                      ->orWhereHas('category', function ($catQ) use ($searchTerm) {
                          $catQ->where('name', 'like', "%{$searchTerm}%");
                      })
                      ->orWhereHas('tags', function ($tagQ) use ($searchTerm) {
                          $tagQ->where('name', 'like', "%{$searchTerm}%");
                      });
                })
                ->latest('published_at')
                ->limit(5)
                ->get()
                ->map(function ($b) {
                    return [
                        'id' => $b->id,
                        'title' => $b->title,
                        'slug' => $b->slug,
                        'url' => route('blog.show', $b->slug),
                        'category' => $b->category ? $b->category->name : null,
                        'cover_url' => $b->cover_image_url ?? 'https://placehold.co/100x70/png',
                        'published_date' => $b->published_at ? $b->published_at->format('d/m/Y') : '',
                    ];
                });

            return response()->json($suggestions);
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
                  })
                  ->orWhereHas('user', function ($userQ) use ($searchTerm) {
                      $userQ->where('ho_ten', 'like', "%{$searchTerm}%");
                  });
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

        $blogs = $query->latest('published_at')->paginate(12)->withQueryString();
        
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
            $user = Auth::user();
            if (! $user instanceof User || (! $user->isAdmin() && Auth::id() !== $blog->user_id)) {
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

    /**
     * Ghi nhận lượt xem cho bài viết qua AJAX / beacon.
     */
    public function recordView(Request $request, string $slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        if ($blog->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Bài viết chưa được xuất bản.',
            ], 404);
        }

        $incremented = $this->blogService->incrementView(
            $blog,
            $request->session()->getId(),
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'incremented' => $incremented,
            'view_count' => (int) $blog->view_count,
            'formatted_view_count' => number_format((int) $blog->view_count),
        ]);
    }

    /**
     * Gửi bình luận và kiểm duyệt tự động bằng AI (AI Moderation)
     */
    public function storeComment(Request $request, string $slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        if (! $blog->allow_comments) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài viết này hiện đã tắt tính năng bình luận.',
                ], 403);
            }
            return back()->with('error', 'Bài viết này hiện đã tắt tính năng bình luận.');
        }

        $request->validate([
            'content' => 'required|string|min:2|max:1000',
        ], [
            'content.required' => 'Vui lòng nhập nội dung bình luận.',
            'content.min' => 'Bình luận phải có ít nhất 2 ký tự.',
            'content.max' => 'Bình luận không được vượt quá 1000 ký tự.',
        ]);

        $user = Auth::user();
        if (! $user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để gửi bình luận.',
                ], 401);
            }
            return redirect()->route('login');
        }

        // Kiểm duyệt bình luận bằng AI
        $moderation = $this->blogAiService->moderateComment($request->content, $user);

        if (! $moderation['is_safe']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'moderated' => true,
                    'flag' => $moderation['flag'],
                    'message' => $moderation['reason'],
                ], 422);
            }
            return back()->withInput()->with('error', $moderation['reason']);
        }

        $comment = \App\Models\BlogComment::create([
            'blog_id' => $blog->id,
            'user_id' => $user->id,
            'content' => trim($request->content),
            'status' => 'active',
        ]);

        $blog->increment('comment_count');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã được đăng thành công và kiểm duyệt an toàn bởi AI!',
                'comment' => [
                    'id' => $comment->id,
                    'user_name' => $user->ho_ten,
                    'user_avatar' => $user->anh_dai_dien_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($user->ho_ten) . '&background=random'),
                    'content' => e($comment->content),
                    'created_at_human' => 'Vừa xong',
                    'is_verified_ai' => true,
                ],
                'comment_count' => $blog->fresh()->comment_count,
            ]);
        }

        return back()->with('success', 'Bình luận của bạn đã được đăng thành công!');
    }
}
