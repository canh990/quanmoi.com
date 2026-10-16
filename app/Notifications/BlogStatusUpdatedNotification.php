<?php

namespace App\Notifications;

use App\Models\Blog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BlogStatusUpdatedNotification extends Notification
{
    use Queueable;

    public $blog;
    public $action;
    public $note;

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
        $statusLabels = [
            'published' => 'Đã xuất bản',
            'rejected' => 'Bị từ chối',
            'requested_revision' => 'Yêu cầu chỉnh sửa',
            'hidden' => 'Đã ẩn',
        ];

        $defaultMessages = [
            'published' => 'Bài viết "' . $this->blog->title . '" của bạn đã được quản trị viên duyệt và xuất bản thành công.',
            'rejected' => 'Bài viết "' . $this->blog->title . '" đã bị từ chối duyệt.' . ($this->note ? ' Lý do: ' . $this->note : ''),
            'requested_revision' => 'Quản trị viên yêu cầu chỉnh sửa bài viết "' . $this->blog->title . '".' . ($this->note ? ' Ghi chú: ' . $this->note : ''),
            'hidden' => 'Bài viết "' . $this->blog->title . '" của bạn đã bị ẩn.',
        ];

        $targetLink = match ($this->action) {
            'published' => route('blog.show', $this->blog->slug ?? $this->blog->id),
            'rejected', 'requested_revision' => route('nguoi-dung.blog.edit', $this->blog->id),
            default => route('nguoi-dung.blog.index'),
        };

        return [
            'blog_id' => $this->blog->id,
            'title' => $this->blog->title,
            'slug' => $this->blog->slug,
            'cover_image_url' => $this->blog->cover_image_url,
            'action' => $this->action,
            'status' => $this->blog->status,
            'status_label' => $statusLabels[$this->action] ?? 'Đã cập nhật',
            'message' => $defaultMessages[$this->action] ?? 'Trạng thái bài viết "' . $this->blog->title . '" đã được cập nhật.',
            'note' => $this->note,
            'link' => $targetLink,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
