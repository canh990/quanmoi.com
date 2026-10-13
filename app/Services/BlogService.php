<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\BlogView;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BlogService
{
    /**
     * Ghi nhận mỗi lượt vào xem bài viết blog.
     * Mỗi lần người dùng vào/tải bài viết sẽ được cộng thêm 1 lượt xem.
     */
    public function incrementView(Blog $blog, ?string $sessionId, ?string $ipAddress): bool
    {
        // Chỉ ghi nhận cho bài viết đã xuất bản
        if ($blog->status !== 'published') {
            return false;
        }

        return DB::transaction(function () use ($blog, $sessionId, $ipAddress) {
            BlogView::create([
                'blog_id' => $blog->id,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
            ]);

            $blog->increment('view_count');
            $blog->refresh();

            return true;
        });
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
        $uploadDisk = config('filesystems.upload_disk', 'r2');
        $r2Configured = !empty(config('filesystems.disks.r2.key'))
            && !empty(config('filesystems.disks.r2.secret'))
            && !empty(config('filesystems.disks.r2.bucket'))
            && !empty(config('filesystems.disks.r2.endpoint'));

        $targetDisk = ($uploadDisk === 'r2' && ! $r2Configured) ? 'uploads' : $uploadDisk;

        try {
            return $file->store('blogs/covers', $targetDisk);
        } catch (\Throwable $e) {
            if ($targetDisk === 'r2') {
                \Illuminate\Support\Facades\Log::warning('Blog cover upload to R2 failed; falling back to local uploads disk.', [
                    'error' => $e->getMessage(),
                ]);
                try {
                    return $file->store('blogs/covers', 'uploads');
                } catch (\Throwable $uploadsEx) {
                    \Illuminate\Support\Facades\Log::warning('Blog cover fallback to uploads disk failed; attempting public disk.', [
                        'error' => $uploadsEx->getMessage(),
                    ]);
                }
            }

            \Illuminate\Support\Facades\Log::error('Blog cover image upload failed.', [
                'disk' => $targetDisk,
                'error' => $e->getMessage(),
            ]);

            if ($targetDisk !== 'public') {
                try {
                    return $file->store('blogs/covers', 'public');
                } catch (\Throwable $fallbackEx) {
                    \Illuminate\Support\Facades\Log::error('Blog cover image fallback to public disk also failed.', [
                        'error' => $fallbackEx->getMessage(),
                    ]);
                }
            }

            throw new \RuntimeException('Lỗi lưu ảnh bìa bài viết. Vui lòng thử lại sau.');
        }
    }

    /**
     * Process tags for a blog post.
     */
    public function syncTags(Blog $blog, array $tagIds): void
    {
        $blog->tags()->sync($tagIds);
    }
}
