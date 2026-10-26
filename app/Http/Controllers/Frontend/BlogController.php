<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogSubscriber;
use App\Models\BlogTag;
use App\Models\User;
use App\Services\BlogAiService;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

        // Gợi ý thông minh cho bạn trên trang Blog (AI Recommendation Engine)
        $aiRecommendations = $this->blogAiService->getSmartRecommendations(
            Auth::user(),
            null,
            $request->session()->getId(),
            $request->ip(),
            4
        );

        // Trợ lý AI Tìm kiếm & Phân tích ngữ nghĩa nếu có từ khóa
        $aiSearchData = null;
        if ($request->filled('search')) {
            $aiSearchData = $this->blogAiService->semanticSearch($request->search, 6);
        }

        return view('frontend.blog.index', compact('blogs', 'heroBlog', 'categories', 'tags', 'aiRecommendations', 'aiSearchData'));
    }

    public function show(Request $request, string $slug)
    {
        $blog = Blog::with([
            'user', 
            'category', 
            'tags', 
            'rootComments' => function($q) {
                $q->with([
                    'user', 
                    'likes',
                    'replies' => function($rq) {
                        $rq->with(['user', 'likes'])->oldest();
                    }
                ]);
            }
        ])->where('slug', $slug)->firstOrFail();

        // Ensure comment_count is strictly synchronized with real active comments
        $activeCommentCount = $blog->activeComments()->count();
        if ($blog->comment_count !== $activeCommentCount) {
            $blog->comment_count = $activeCommentCount;
            $blog->saveQuietly();
        }

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
            $blog->refresh();
        }

        $relatedBlogs = Blog::where('status', 'published')
            ->where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Hệ thống Gợi ý bài viết thông minh bằng AI (AI Recommendation Engine)
        $aiRecommendations = $this->blogAiService->getSmartRecommendations(
            Auth::user(),
            $blog,
            $request->session()->getId(),
            $request->ip(),
            4
        );
            
        $tags = BlogTag::has('blogs')->limit(15)->get();

        return view('frontend.blog.show', compact('blog', 'relatedBlogs', 'aiRecommendations', 'tags'));
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
     * Gửi bình luận / phản hồi và kiểm duyệt tự động bằng AI (AI Moderation)
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
            'parent_id' => 'nullable|integer|exists:blog_comments,id',
        ], [
            'content.required' => 'Vui lòng nhập nội dung bình luận.',
            'content.min' => 'Bình luận phải có ít nhất 2 ký tự.',
            'content.max' => 'Bình luận không được vượt quá 1000 ký tự.',
            'parent_id.exists' => 'Bình luận phản hồi không tồn tại.',
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

        // Validate parent_id belongs to this blog
        $parentId = null;
        if ($request->filled('parent_id')) {
            $parentComment = \App\Models\BlogComment::where('id', $request->parent_id)
                ->where('blog_id', $blog->id)
                ->where('status', 'active')
                ->first();

            if (! $parentComment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bình luận được trả lời không tồn tại hoặc đã bị xóa.',
                ], 404);
            }

            // Flatten replies to top-level parent if replying to a reply
            $parentId = $parentComment->parent_id ?: $parentComment->id;
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
            'parent_id' => $parentId,
            'user_id' => $user->id,
            'content' => trim($request->content),
            'status' => 'active',
            'likes_count' => 0,
        ]);

        $totalComments = $blog->activeComments()->count();
        $blog->update(['comment_count' => $totalComments]);

        // Gửi thông báo đến người được phản hồi nếu đây là reply
        if (!empty($parentComment) && $parentComment->user && $parentComment->user->id !== $user->id) {
            try {
                $parentComment->user->notify(new \App\Notifications\BlogCommentNotification($blog, $comment, $user, true));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Blog reply comment notification error: ' . $e->getMessage());
            }
        }

        // Gửi thông báo đến tác giả bài viết nếu người bình luận không phải là tác giả
        if ($blog->user && $blog->user->id !== $user->id && (empty($parentComment) || $parentComment->user_id !== $blog->user->id)) {
            try {
                $blog->user->notify(new \App\Notifications\BlogCommentNotification($blog, $comment, $user, false));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Blog author comment notification error: ' . $e->getMessage());
            }
        }

        // Ghi log hoạt động hệ thống cho Quản trị viên
        try {
            \App\Models\AdminAuditLog::create([
                'actor_id' => $user->id,
                'action' => 'blog.commented',
                'target_type' => \App\Models\Blog::class,
                'target_id' => (string) $blog->id,
                'metadata' => [
                    'blog_title' => $blog->title,
                    'blog_slug' => $blog->slug,
                    'user_name' => $user->ho_ten,
                    'content' => \Illuminate\Support\Str::limit($comment->content, 100),
                ],
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        $isAuthor = ($blog->user_id === $user->id);
        $isAdmin = $user->isAdmin();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $parentId 
                    ? 'Phản hồi đã được đăng thành công!' 
                    : 'Bình luận đã được đăng thành công!',
                'comment' => [
                    'id' => $comment->id,
                    'parent_id' => $comment->parent_id,
                    'user_id' => $user->id,
                    'user_name' => $user->ho_ten,
                    'user_avatar' => $user->anh_dai_dien_url,
                    'is_author' => $isAuthor,
                    'is_admin' => $isAdmin,
                    'content' => e($comment->content),
                    'created_at_human' => 'Vừa xong',
                    'likes_count' => 0,
                    'is_liked' => false,
                    'can_delete' => true,
                ],
                'comment_count' => $totalComments,
            ]);
        }

        return back()->with('success', 'Bình luận của bạn đã được đăng thành công!');
    }

    /**
     * Xóa bình luận (Dành cho người viết bình luận, tác giả bài viết, hoặc quản trị viên)
     */
    public function deleteComment(Request $request, string $slug, $commentId)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        $comment = \App\Models\BlogComment::where('id', $commentId)
            ->where('blog_id', $blog->id)
            ->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Bạn chưa đăng nhập.'], 401);
        }

        // Kiểm tra quyền xóa: người tạo bình luận, chủ bài viết, hoặc Admin
        $canDelete = ($user->id === $comment->user_id) || ($user->id === $blog->user_id) || $user->isAdmin();
        if (! $canDelete) {
            return response()->json([
                'success' => false, 
                'message' => 'Bạn không có quyền xóa bình luận này.'
            ], 403);
        }

        // Tính số lượng bình luận sẽ xóa (bình luận này + các phản hồi con nếu có)
        $deletedCount = 1 + $comment->replies()->count();

        $comment->delete();

        // Cập nhật lại comment_count chính xác cho blog
        $totalComments = $blog->activeComments()->count();
        $blog->update(['comment_count' => $totalComments]);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa bình luận thành công.',
            'comment_id' => (int) $commentId,
            'parent_id' => $comment->parent_id,
            'deleted_count' => $deletedCount,
            'comment_count' => $totalComments,
        ]);
    }

    /**
     * Thích / Bỏ thích bình luận
     */
    public function toggleLikeComment(Request $request, string $slug, $commentId)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        $comment = \App\Models\BlogComment::where('id', $commentId)
            ->where('blog_id', $blog->id)
            ->where('status', 'active')
            ->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false, 
                'message' => 'Vui lòng đăng nhập để thích bình luận.'
            ], 401);
        }

        $existingLike = \App\Models\BlogCommentLike::where('comment_id', $comment->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $comment->decrement('likes_count');
            $liked = false;
        } else {
            \App\Models\BlogCommentLike::create([
                'comment_id' => $comment->id,
                'user_id' => $user->id,
            ]);
            $comment->increment('likes_count');
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => max(0, (int) $comment->fresh()->likes_count),
        ]);
    }

    /**
     * AI Tóm tắt bài viết (AI Summarizer API):
     * Đọc toàn bộ nội dung và trả về 4 ý chính: Địa chỉ, Món nên thử, Mức giá, Ưu/Nhược điểm.
     */
    public function summarize(Request $request, string $slug)
    {
        $blog = Blog::with(['category'])->where('slug', $slug)->firstOrFail();
        $force = $request->boolean('refresh', false);
        $summary = $this->blogAiService->summarizeBlog($blog, $force);

        return response()->json($summary);
    }

    /**
     * Trợ lý AI Tìm kiếm & Ngữ nghĩa (Semantic Search API)
     */
    public function aiSearch(Request $request)
    {
        $query = $request->input('q') ?? $request->input('search') ?? '';
        $result = $this->blogAiService->semanticSearch($query, 6);

        return response()->json($result);
    }

    /**
     * Trợ lý AI Trò chuyện / Chatbot ẩm thực (Chat Assistant API)
     */
    public function aiChat(Request $request)
    {
        $message = trim($request->input('message', ''));
        if ($message === '') {
            return response()->json([
                'success' => false,
                'reply' => 'Vui lòng nhập câu hỏi để trò chuyện cùng trợ lý AI ẩm thực.',
            ], 422);
        }

        $history = $request->input('history', []);
        $result = app(\App\Services\QuanAiChatbotService::class)->chat($message, $history);

        return response()->json($result);
    }

    /**
     * Gợi ý bài viết thông minh (AI Recommendations API)
     */
    public function recommendations(Request $request)
    {
        $currentSlug = $request->input('current_slug');
        $currentBlog = $currentSlug ? Blog::where('slug', $currentSlug)->first() : null;

        $recommendations = $this->blogAiService->getSmartRecommendations(
            Auth::user(),
            $currentBlog,
            $request->session()->getId(),
            $request->ip(),
            (int) $request->input('limit', 4)
        );

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Đăng ký nhận tin ẩm thực Blog (Newsletter Subscription)
     */
    public function subscribeNewsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'source' => ['nullable', 'string', 'max:50'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email của bạn.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('email'),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $email = strtolower(trim($request->input('email')));
        $source = $request->input('source', 'blog_sidebar');
        $userId = Auth::id();
        $ip = $request->ip();

        $subscriber = BlogSubscriber::where('email', $email)->first();

        if ($subscriber) {
            if ($subscriber->status === 'active') {
                $msg = 'Email này đã đăng ký nhận tin ẩm thực từ Quán Mới rồi nhé! Cảm ơn bạn.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'already_subscribed' => true,
                        'message' => $msg,
                    ]);
                }
                return back()->with('newsletter_info', $msg);
            } else {
                $subscriber->status = 'active';
                $subscriber->source = $source;
                if ($userId) $subscriber->user_id = $userId;
                $subscriber->ip_address = $ip;
                $subscriber->save();

                $msg = 'Chào mừng bạn đã kích hoạt lại đăng ký nhận tin ẩm thực từ Quán Mới!';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $msg,
                    ]);
                }
                return back()->with('newsletter_success', $msg);
            }
        }

        BlogSubscriber::create([
            'email' => $email,
            'user_id' => $userId,
            'ip_address' => $ip,
            'source' => $source,
            'status' => 'active',
            'token' => Str::random(32),
        ]);

        $msg = 'Đăng ký nhận tin ẩm thực thành công! Quán Mới sẽ gửi đến bạn những review và ưu đãi mới nhất.';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return back()->with('newsletter_success', $msg);
    }

    /**
     * Xem thông tin tác giả và danh sách bài viết của tác giả (Author Profile)
     */
    public function author($id, Request $request)
    {
        $author = User::where('id', $id)->firstOrFail();

        $blogsQuery = Blog::with(['category', 'tags'])
            ->where('user_id', $author->id)
            ->where('status', 'published');

        if ($request->filled('category')) {
            $blogsQuery->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $blogsQuery->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        // Stats
        $totalBlogs = Blog::where('user_id', $author->id)->where('status', 'published')->count();
        $totalViews = Blog::where('user_id', $author->id)->where('status', 'published')->sum('view_count');
        $totalComments = Blog::where('user_id', $author->id)->where('status', 'published')->sum('comment_count');

        // Sorting
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'views':
                $blogsQuery->orderByDesc('view_count');
                break;
            case 'comments':
                $blogsQuery->orderByDesc('comment_count');
                break;
            case 'oldest':
                $blogsQuery->oldest('published_at');
                break;
            case 'latest':
            default:
                $blogsQuery->latest('published_at');
                break;
        }

        $blogs = $blogsQuery->paginate(9)->withQueryString();

        // Danh mục các bài viết tác giả đã viết
        $categories = BlogCategory::whereHas('blogs', function($q) use ($author) {
            $q->where('user_id', $author->id)->where('status', 'published');
        })->withCount(['blogs' => function($q) use ($author) {
            $q->where('user_id', $author->id)->where('status', 'published');
        }])->get();

        $topCategory = $categories->sortByDesc('blogs_count')->first();

        return view('frontend.blog.author', compact(
            'author',
            'blogs',
            'totalBlogs',
            'totalViews',
            'totalComments',
            'categories',
            'topCategory',
            'sort'
        ));
    }
}
