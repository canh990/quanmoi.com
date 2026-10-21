<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogModerationLog;
use App\Notifications\BlogStatusUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total' => Blog::count(),
            'pending' => Blog::where('status', 'pending')->count(),
            'published' => Blog::where('status', 'published')->count(),
            'rejected' => Blog::where('status', 'rejected')->count(),
        ];
        
        // For now, redirect to posts index
        return redirect()->route('admin.blog.posts.index');
    }

    public function index(Request $request)
    {
        $query = Blog::with(['user', 'category']);
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        $blogs = $query->latest()->paginate(20)->withQueryString();
        
        $stats = [
            'total' => Blog::count(),
            'pending' => Blog::where('status', 'pending')->count(),
            'published' => Blog::where('status', 'published')->count(),
            'rejected' => Blog::where('status', 'rejected')->count(),
            'need_revision' => Blog::where('status', 'need_revision')->count(),
        ];
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'data' => $blogs,
            ]);
        }
        
        return view('admin.blog.index', compact('blogs', 'stats'));
    }
    
    public function pending(Request $request)
    {
        $request->merge(['status' => 'pending']);

        $admin = auth()->user();
        if ($admin) {
            $caiDat = $admin->cai_dat ?? [];
            if (is_string($caiDat)) {
                $caiDat = json_decode($caiDat, true) ?: [];
            }
            $readIds = array_map('strval', $caiDat['read_admin_notifs'] ?? []);
            $blogIds = Blog::where('status', 'pending')->pluck('id')->map(fn($id) => 'blog_' . $id)->all();
            $newReadIds = array_unique(array_merge($readIds, $blogIds));
            if (count($newReadIds) > 500) {
                $newReadIds = array_slice($newReadIds, -500);
            }
            if (count($newReadIds) !== count($readIds)) {
                $caiDat['read_admin_notifs'] = array_values($newReadIds);
                DB::table('nguoi_dung')
                    ->where('id', $admin->id)
                    ->update(['cai_dat' => json_encode($caiDat, JSON_UNESCAPED_UNICODE)]);
                $admin->cai_dat = $caiDat;
            }
        }

        return $this->index($request);
    }

    public function show(Blog $blog)
    {
        $this->authorize('view', $blog);
        $blog->load(['user', 'category', 'tags', 'moderationLogs.admin' => function($q) {
            $q->latest();
        }]);

        if (request()->expectsJson() || request()->ajax() || request()->wantsJson() || request()->query('ajax') == '1') {
            return response()->json([
                'success' => true,
                'blog' => [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'status' => $blog->status,
                    'excerpt' => $blog->excerpt,
                    'content' => $blog->content,
                    'cover_image_url' => $blog->cover_image_url,
                    'created_at_formatted' => $blog->created_at?->format('d/m/Y H:i'),
                    'updated_at_formatted' => $blog->updated_at?->format('d/m/Y H:i'),
                    'author_name' => $blog->user?->ho_ten ?? 'Ẩn danh',
                    'author_email' => $blog->user?->email ?? '',
                    'author_avatar' => $blog->user?->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($blog->user?->ho_ten ?? 'U'),
                    'category_name' => $blog->category?->name ?? 'Không phân loại',
                    'tags' => $blog->tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name])->values()->all(),
                    'is_hero' => (bool)$blog->is_hero,
                    'detail_url' => route('admin.blog.posts.show', $blog->id),
                    'public_url' => $blog->status === 'published' ? route('blog.show', $blog->slug) : null,
                    'latest_log' => $blog->moderationLogs->first() ? [
                        'admin_name' => $blog->moderationLogs->first()->admin?->ho_ten ?? 'Quản trị viên',
                        'action' => $blog->moderationLogs->first()->action,
                        'note' => $blog->moderationLogs->first()->note,
                        'created_at' => $blog->moderationLogs->first()->created_at?->format('d/m/Y H:i'),
                    ] : null,
                ],
            ]);
        }
        
        return view('admin.blog.show', compact('blog'));
    }

    private function markBlogAsRead(Request $request, int|string $blogId): void
    {
        $admin = $request->user();
        if (!$admin) return;
        $caiDat = $admin->cai_dat ?? [];
        if (is_string($caiDat)) {
            $caiDat = json_decode($caiDat, true) ?: [];
        }
        $readIds = array_map('strval', $caiDat['read_admin_notifs'] ?? []);
        $notifId = 'blog_' . $blogId;
        if (!in_array($notifId, $readIds, true)) {
            $readIds[] = $notifId;
            if (count($readIds) > 500) {
                $readIds = array_slice($readIds, -500);
            }
            $caiDat['read_admin_notifs'] = array_values($readIds);
            DB::table('nguoi_dung')
                ->where('id', $admin->id)
                ->update(['cai_dat' => json_encode($caiDat, JSON_UNESCAPED_UNICODE)]);
            $admin->cai_dat = $caiDat;
        }
    }

    public function updateStatus(Request $request, Blog $blog)
    {
        $this->authorize('update', $blog);
        $this->markBlogAsRead($request, $blog->id);
        $request->validate([
            'status' => 'required|in:published,rejected,need_revision,hidden,scheduled',
            'note' => 'nullable|string|max:1000',
            'expected_status' => 'nullable|string'
        ]);
        
        // Concurrency check: prevent conflicting updates if status already modified
        if ($request->filled('expected_status') && $blog->status !== $request->expected_status) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trạng thái bài viết đã được thay đổi bởi quản trị viên khác trước đó. Đang tải lại dữ liệu.',
                    'current_status' => $blog->status,
                ], 409);
            }
        }
        
        $oldStatus = $blog->status;
        $status = $request->status;
        
        // Handle Scheduled vs Published
        if ($status === 'published') {
            if ($blog->scheduled_at && $blog->scheduled_at > now()) {
                $status = 'scheduled';
            } else {
                if (!$blog->published_at) {
                    $blog->published_at = Carbon::now();
                }
                $blog->published_by = Auth::id();
                $blog->approved_by = Auth::id();
            }
        }
        
        $actionMap = [
            'published' => 'published',
            'scheduled' => 'approved',
            'rejected' => 'rejected',
            'need_revision' => 'requested_revision',
            'hidden' => 'hidden'
        ];
        
        $statusLabels = [
            'published' => 'Xuất bản',
            'rejected' => 'Từ chối',
            'need_revision' => 'Cần sửa',
            'hidden' => 'Đã ẩn',
            'scheduled' => 'Lên lịch',
            'draft' => 'Bản nháp',
            'pending' => 'Chờ duyệt',
        ];
        
        DB::transaction(function () use ($blog, $status, $oldStatus, $request, $actionMap) {
            $blog->status = $status;
            if ($status === 'published' && !$blog->published_at) {
                $blog->published_at = now();
            }
            $blog->save();
            
            BlogModerationLog::create([
                'blog_id' => $blog->id,
                'admin_id' => Auth::id(),
                'action' => $actionMap[$status] ?? 'updated',
                'from_status' => $oldStatus,
                'to_status' => $status,
                'note' => $request->note
            ]);
            
            if ($oldStatus !== $status && isset($actionMap[$status])) {
                try {
                    if ($blog->user) {
                        $blog->user->notify(new BlogStatusUpdatedNotification($blog, $actionMap[$status], $request->note));
                    }
                } catch (\Throwable $notiEx) {
                    \Illuminate\Support\Facades\Log::warning('BlogStatusUpdatedNotification error: ' . $notiEx->getMessage());
                }
            }
        });
        
        $msg = match ($status) {
            'published' => 'Đã phê duyệt và xuất bản bài viết thành công!',
            'rejected' => 'Đã từ chối duyệt bài viết thành công!',
            'need_revision' => 'Đã gửi yêu cầu chỉnh sửa đến tác giả!',
            'hidden' => 'Đã ẩn bài viết thành công!',
            default => 'Trạng thái bài viết đã được cập nhật thành công.'
        };
        
        $freshStats = [
            'total' => Blog::count(),
            'pending' => Blog::where('status', 'pending')->count(),
            'published' => Blog::where('status', 'published')->count(),
            'rejected' => Blog::where('status', 'rejected')->count(),
            'need_revision' => Blog::where('status', 'need_revision')->count(),
        ];
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'status' => $blog->status,
                'status_label' => $statusLabels[$blog->status] ?? $blog->status,
                'stats' => $freshStats,
                'blog' => [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'status' => $blog->status,
                    'published_at' => $blog->published_at ? $blog->published_at->format('d/m/Y H:i') : null,
                    'updated_at' => $blog->updated_at ? $blog->updated_at->format('d/m/Y H:i') : null,
                ],
                'log' => [
                    'admin_name' => Auth::user()?->ho_ten ?? 'Hệ thống',
                    'action' => $actionMap[$status] ?? 'updated',
                    'action_label' => match($status) {
                        'published' => 'phê duyệt và xuất bản',
                        'rejected' => 'từ chối duyệt',
                        'need_revision' => 'yêu cầu chỉnh sửa',
                        'hidden' => 'ẩn',
                        default => 'cập nhật'
                    },
                    'note' => $request->note,
                    'created_at' => now()->format('d/m/Y H:i'),
                ]
            ]);
        }
        
        return back()->with('success', $msg);
    }
    
    public function toggleHero(Request $request, Blog $blog)
    {
        $this->authorize('update', $blog);
        DB::transaction(function () use ($blog) {
            // Unset any existing hero
            Blog::where('is_hero', true)->update(['is_hero' => false]);
            
            $blog->is_hero = true;
            $blog->save();

            if ($request->user()) {
                \App\Models\AdminAuditLog::create([
                    'actor_id' => $request->user()->id,
                    'action' => 'blog.hero_toggled',
                    'target_type' => Blog::class,
                    'target_id' => (string) $blog->id,
                    'metadata' => ['title' => $blog->title],
                    'ip_address' => $request->ip(),
                ]);
            }

            try {
                if ($blog->user) {
                    $blog->user->notify(new BlogStatusUpdatedNotification($blog, 'hero'));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Blog hero notification error: ' . $e->getMessage());
            }
        });
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã đặt bài viết thành Hero thành công.',
                'is_hero' => true,
            ]);
        }
        
        return back()->with('success', 'Đã đặt bài viết thành Hero.');
    }
    
    public function destroy(Request $request, Blog $blog)
    {
        if ($request->has('force') && $request->force) {
            $this->authorize('forceDelete', $blog);
            if ($blog->cover_image) {
                try {
                    $uploadDisk = config('filesystems.upload_disk', 'uploads');
                    $r2Configured = !empty(config('filesystems.disks.r2.key'))
                        && !empty(config('filesystems.disks.r2.secret'))
                        && !empty(config('filesystems.disks.r2.bucket'))
                        && !empty(config('filesystems.disks.r2.endpoint'));
                    $targetDisk = ($uploadDisk === 'r2' && ! $r2Configured) ? 'uploads' : $uploadDisk;
                    if (Storage::disk($targetDisk)->exists($blog->cover_image)) {
                        Storage::disk($targetDisk)->delete($blog->cover_image);
                    } elseif ($targetDisk !== 'public' && Storage::disk('public')->exists($blog->cover_image)) {
                        Storage::disk('public')->delete($blog->cover_image);
                    }
                } catch (\Throwable $th) {
                    // Ignore storage deletion errors
                }
            }
            $blogUser = $blog->user;
            $blogTitle = $blog->title;

            if ($request->user()) {
                \App\Models\AdminAuditLog::create([
                    'actor_id' => $request->user()->id,
                    'action' => 'blog.deleted',
                    'target_type' => Blog::class,
                    'target_id' => (string) $blog->id,
                    'metadata' => ['title' => $blogTitle, 'type' => 'force_delete'],
                    'ip_address' => $request->ip(),
                ]);
            }

            try {
                if ($blogUser) {
                    $blogUser->notify(new BlogStatusUpdatedNotification($blog, 'deleted'));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Blog force delete notification error: ' . $e->getMessage());
            }

            $blog->forceDelete();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bài viết đã bị xoá vĩnh viễn.',
                    'stats' => [
                        'total' => Blog::count(),
                        'pending' => Blog::where('status', 'pending')->count(),
                        'published' => Blog::where('status', 'published')->count(),
                        'rejected' => Blog::where('status', 'rejected')->count(),
                        'need_revision' => Blog::where('status', 'need_revision')->count(),
                    ]
                ]);
            }
            return redirect()->route('admin.blog.posts.index')->with('success', 'Bài viết đã bị xoá vĩnh viễn.');
        }

        $this->authorize('delete', $blog);
        
        if ($request->user()) {
            \App\Models\AdminAuditLog::create([
                'actor_id' => $request->user()->id,
                'action' => 'blog.deleted',
                'target_type' => Blog::class,
                'target_id' => (string) $blog->id,
                'metadata' => ['title' => $blog->title, 'type' => 'soft_delete'],
                'ip_address' => $request->ip(),
            ]);
        }

        try {
            if ($blog->user) {
                $blog->user->notify(new BlogStatusUpdatedNotification($blog, 'deleted'));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Blog delete notification error: ' . $e->getMessage());
        }

        $blog->delete();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bài viết đã được chuyển vào thùng rác thành công.',
                'stats' => [
                    'total' => Blog::count(),
                    'pending' => Blog::where('status', 'pending')->count(),
                    'published' => Blog::where('status', 'published')->count(),
                    'rejected' => Blog::where('status', 'rejected')->count(),
                    'need_revision' => Blog::where('status', 'need_revision')->count(),
                ]
            ]);
        }
        return redirect()->route('admin.blog.posts.index')->with('success', 'Bài viết đã bị xoá tạm thời.');
    }
}
