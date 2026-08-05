<?php

namespace App\Notifications;

use App\Models\Blog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BlogStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $blog;
    protected $action;
    protected $note;

    /**
     * Create a new notification instance.
     */
    public function __construct(Blog $blog, string $action, ?string $note = null)
    {
        $this->blog = $blog;
        $this->action = $action;
        $this->note = $note;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $messages = [
            'published' => 'Bài viết "' . $this->blog->title . '" của bạn đã được xuất bản.',
            'rejected' => 'Bài viết "' . $this->blog->title . '" đã bị từ chối duyệt.',
            'requested_revision' => 'Quản trị viên yêu cầu chỉnh sửa bài viết "' . $this->blog->title . '".',
            'hidden' => 'Bài viết "' . $this->blog->title . '" của bạn đã bị ẩn.',
        ];

        return [
            'blog_id' => $this->blog->id,
            'title' => $this->blog->title,
            'action' => $this->action,
            'message' => $messages[$this->action] ?? 'Trạng thái bài viết "' . $this->blog->title . '" đã thay đổi.',
            'note' => $this->note,
            'link' => route('nguoi-dung.blog.index')
        ];
    }
}
