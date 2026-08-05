<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogModerationLog;
use App\Notifications\BlogStatusUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $blogs = $query->latest()->paginate(20);
        
        $stats = [
            'total' => Blog::count(),
            'pending' => Blog::where('status', 'pending')->count(),
            'published' => Blog::where('status', 'published')->count(),
        ];
        
        return view('admin.blog.index', compact('blogs', 'stats'));
    }
    
    public function pending(Request $request)
    {
        $request->merge(['status' => 'pending']);
        return $this->index($request);
    }

    public function show(Blog $blog)
    {
        $blog->load(['user', 'category', 'tags', 'moderationLogs.admin' => function($q) {
            $q->latest();
        }]);
        
        return view('admin.blog.show', compact('blog'));
    }

    public function updateStatus(Request $request, Blog $blog)
    {
        $request->validate([
            'status' => 'required|in:published,rejected,need_revision,hidden,scheduled',
            'note' => 'nullable|string'
        ]);
        
        $oldStatus = $blog->status;
        $status = $request->status;
        
        // Handle Scheduled vs Published
        if ($status === 'published') {
            if ($blog->scheduled_at && $blog->scheduled_at > now()) {
                $status = 'scheduled';
            } else {
                if (!$blog->published_at) {
                    $blog->published_at = now();
                }
                $blog->published_by = Auth::id();
                $blog->approved_by = Auth::id();
            }
        }
        
        $blog->status = $status;
        $blog->save();
        
        // Log Moderation
        $actionMap = [
            'published' => 'published',
            'scheduled' => 'approved', // Conceptually approved but waiting
            'rejected' => 'rejected',
            'need_revision' => 'requested_revision',
            'hidden' => 'hidden'
        ];
        
        BlogModerationLog::create([
            'blog_id' => $blog->id,
            'admin_id' => Auth::id(),
            'action' => $actionMap[$status] ?? 'updated',
            'from_status' => $oldStatus,
            'to_status' => $status,
            'note' => $request->note
        ]);
        
        if ($oldStatus !== $status && isset($actionMap[$status])) {
            $blog->user->notify(new BlogStatusUpdatedNotification($blog, $actionMap[$status], $request->note));
        }
        
        return back()->with('success', 'Trạng thái bài viết đã được cập nhật.');
    }
    
    public function toggleHero(Blog $blog)
    {
        Blog::where('is_hero', true)->update(['is_hero' => false]);
        
        $blog->is_hero = true;
        $blog->save();
        
        return back()->with('success', 'Đã đặt bài viết thành Hero.');
    }
    
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blog.posts.index')->with('success', 'Bài viết đã bị xoá.');
    }
}
