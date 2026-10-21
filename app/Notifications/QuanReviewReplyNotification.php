<?php

namespace App\Notifications;

use App\Models\Quan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class QuanReviewReplyNotification extends Notification
{
    use Queueable;

    public Quan $quan;
    public User $owner;
    public string $replyContent;

    public function __construct(Quan $quan, User $owner, string $replyContent)
    {
        $this->quan = $quan;
        $this->owner = $owner;
        $this->replyContent = $replyContent;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $shortReply = Str::limit($this->replyContent, 120);

        return [
            'quan_id' => $this->quan->id,
            'slug' => $this->quan->slug,
            'title' => 'Chủ quán "' . $this->quan->ten_quan . '" đã phản hồi đánh giá của bạn',
            'message' => 'Chủ quán vừa phản hồi: "' . $shortReply . '"',
            'note' => $this->replyContent,
            'action' => 'review_reply',
            'status' => 'review_reply',
            'status_label' => 'Đã phản hồi',
            'link' => route('quan.detail', $this->quan->slug) . '#reviews-section',
            'cover_image_url' => $this->quan->anh_bia_url,
            'owner_name' => $this->owner->ho_ten ?? 'Chủ quán',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
