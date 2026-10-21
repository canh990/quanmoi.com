<?php

namespace App\Notifications;

use App\Models\Quan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuanReviewNotification extends Notification
{
    use Queueable;

    public Quan $quan;
    public User $reviewer;
    public int $soSao;
    public string $binhLuan;

    public function __construct(Quan $quan, User $reviewer, int $soSao, string $binhLuan)
    {
        $this->quan = $quan;
        $this->reviewer = $reviewer;
        $this->soSao = $soSao;
        $this->binhLuan = $binhLuan;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $stars = str_repeat('⭐', max(1, min(5, $this->soSao)));
        $shortComment = \Illuminate\Support\Str::limit($this->binhLuan, 120);

        $isSelf = isset($notifiable->id) && (int) $notifiable->id === (int) $this->reviewer->id;
        $title = $isSelf
            ? ('Bạn đã đánh giá ' . $this->soSao . ' sao cho quán "' . $this->quan->ten_quan . '"')
            : ('Đánh giá mới (' . $this->soSao . ' sao) cho quán "' . $this->quan->ten_quan . '"');
        $message = $isSelf
            ? ('Cảm ơn bạn! Đánh giá ' . $this->soSao . ' sao của bạn đã được ghi nhận: "' . $shortComment . '"')
            : ('Khách hàng ' . ($this->reviewer->ho_ten ?? 'Người dùng') . ' vừa đánh giá ' . $this->soSao . ' sao: "' . $shortComment . '"');

        $targetLink = route('quan.detail', $this->quan->slug) . '#reviews-section';
        if (isset($notifiable->id) && (int) $this->quan->chu_quan_id === (int) $notifiable->id) {
            $targetLink = route('chu-quan.quan.show', $this->quan->slug) . '#owner-reviews-section';
        }

        return [
            'quan_id' => $this->quan->id,
            'slug' => $this->quan->slug,
            'title' => $title,
            'message' => $message,
            'note' => $this->binhLuan,
            'action' => 'new_review',
            'status' => 'new_review',
            'status_label' => $this->soSao . ' sao',
            'link' => $targetLink,
            'cover_image_url' => $this->quan->anh_bia_url,
            'reviewer_name' => $this->reviewer->ho_ten ?? 'Khách hàng',
            'reviewer_avatar' => $this->reviewer->anh_dai_dien ?? null,
            'stars' => $this->soSao,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
