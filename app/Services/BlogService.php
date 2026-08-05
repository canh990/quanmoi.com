<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\BlogView;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class BlogService
{
    /**
     * Increment view count for a blog post, preventing SPAM using session/IP.
     */
    public function incrementView(Blog $blog, string $sessionId, ?string $ipAddress): void
    {
        // Check if a view exists for this session/ip within the last hour
        $recentView = BlogView::where('blog_id', $blog->id)
            ->where(function ($query) use ($sessionId, $ipAddress) {
                if ($sessionId) {
                    $query->where('session_id', $sessionId);
                }
                if ($ipAddress) {
                    $query->orWhere('ip_address', $ipAddress);
                }
            })
            ->where('created_at', '>=', now()->subHour())
            ->first();

        if (!$recentView) {
            BlogView::create([
                'blog_id' => $blog->id,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
            ]);
        }
    }

    /**
     * Generate a unique slug for a blog post.
     */
    public function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Blog::where('slug', $slug)->where('id', '!=', $ignoreId)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Upload cover image and return the path.
     */
    public function uploadCoverImage(UploadedFile $file): string
    {
        // Upload lên Cloudflare R2 thông qua disk 'r2'
        return $file->store('blogs/covers', 'r2');
    }

    /**
     * Process tags for a blog post.
     */
    public function syncTags(Blog $blog, array $tagIds): void
    {
        $blog->tags()->sync($tagIds);
    }
}
