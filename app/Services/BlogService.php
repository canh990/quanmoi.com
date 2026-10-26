<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\BlogView;
use App\Models\BlogSubscriber;
use App\Notifications\NewBlogPublishedNotification;
use App\Notifications\NewsletterWelcomeNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

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
            \Illuminate\Support\Facades\Log::warning('Blog cover image upload failed on primary disk, falling back to local.', [
                'disk' => $uploadDisk,
                'error' => $e->getMessage(),
                'previous' => $e->getPrevious()?->getMessage(),
            ]);

            if ($uploadDisk !== 'uploads') {
                try {
                    return $file->store('blogs/covers', 'uploads');
                } catch (\Throwable $fallbackEx) {
                    \Illuminate\Support\Facades\Log::error('Blog cover image local fallback also failed.', [
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

    /**
     * Gửi email chào mừng khi độc giả đăng ký nhận tin ẩm thực thành công.
     */
    public function sendNewsletterWelcome(BlogSubscriber $subscriber): bool
    {
        try {
            Notification::route('mail', $subscriber->email)
                ->notify(new NewsletterWelcomeNotification($subscriber));
            return true;
        } catch (\Throwable $e) {
            Log::warning('Không thể gửi email chào mừng nhận tin ẩm thực: ' . $e->getMessage(), [
                'email' => $subscriber->email,
            ]);
            return false;
        }
    }

    /**
     * Tự động gửi thông báo qua Gmail tới toàn bộ độc giả đã đăng ký khi có bài viết blog mới được xuất bản.
     */
    public function notifySubscribersAboutNewPost(Blog $blog): int
    {
        if ($blog->status !== 'published') {
            return 0;
        }

        // Đảm bảo load đủ thông tin cần thiết cho email
        $blog->loadMissing(['category', 'user']);

        $subscribers = BlogSubscriber::where('status', 'active')->get();
        if ($subscribers->isEmpty()) {
            return 0;
        }

        $sentCount = 0;
        foreach ($subscribers as $subscriber) {
            try {
                if (filter_var($subscriber->email, FILTER_VALIDATE_EMAIL)) {
                    Notification::route('mail', $subscriber->email)
                        ->notify(new NewBlogPublishedNotification($blog, $subscriber));
                    $sentCount++;
                }
            } catch (\Throwable $e) {
                Log::warning("Lỗi gửi thông báo bài viết mới tới email {$subscriber->email}: " . $e->getMessage());
            }
        }

        Log::info("Đã gửi thông báo bài viết blog mới [{$blog->title}] tới {$sentCount}/{$subscribers->count()} độc giả nhận tin ẩm thực.");
        return $sentCount;
    }
}
