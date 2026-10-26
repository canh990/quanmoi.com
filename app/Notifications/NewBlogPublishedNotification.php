<?php

namespace App\Notifications;

use App\Models\Blog;
use App\Models\BlogSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewBlogPublishedNotification extends Notification
{
    use Queueable;

    public Blog $blog;
    public BlogSubscriber $subscriber;

    public function __construct(Blog $blog, BlogSubscriber $subscriber)
    {
        $this->blog = $blog;
        $this->subscriber = $subscriber;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'Quán Mới');
        $baseUrl = rtrim(config('app.url', 'http://127.0.0.1:8002'), '/');
        $postUrl = $baseUrl . '/blog/' . $this->blog->slug;
        $unsubscribeUrl = $baseUrl . '/blog/huy-dang-ky-nhan-tin/' . $this->subscriber->token;

        $categoryName = $this->blog->category ? $this->blog->category->name : 'Ẩm thực';
        $authorName = $this->blog->user ? $this->blog->user->ho_ten : 'Quán Mới';
        $excerpt = $this->blog->excerpt ?: Str::limit(strip_tags($this->blog->content), 160);

        return (new MailMessage)
            ->subject("🍜 Bài viết mới: {$this->blog->title} - {$appName}")
            ->greeting('Xin chào bạn yêu ẩm thực!')
            ->line("{$appName} vừa xuất bản một bài viết mới về chủ đề **{$categoryName}**:")
            ->line("### {$this->blog->title}")
            ->line("✍️ *Tác giả:* **{$authorName}**")
            ->line("📝 *Tóm tắt:* {$excerpt}")
            ->action('👉 Đọc bài viết ngay', $postUrl)
            ->line('Hy vọng bài viết sẽ mang lại cho bạn những gợi ý quán ăn và món ngon ưng ý!')
            ->salutation("Thân ái,\nĐội ngũ {$appName}")
            ->line("Bạn nhận được email này vì đã đăng ký nhận tin ẩm thực trên {$appName}. Để dừng nhận thông báo, bạn có thể [hủy đăng ký tại đây]({$unsubscribeUrl}).");
    }
}
