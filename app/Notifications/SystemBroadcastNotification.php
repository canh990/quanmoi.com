<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemBroadcastNotification extends Notification
{
    use Queueable;

    public string $tieuDe;
    public string $noiDung;
    public string $doiTuong;
    public ?string $senderName;
    public ?string $targetLink;

    public function __construct(string $tieuDe, string $noiDung, string $doiTuong = 'tat_ca', ?string $senderName = null, ?string $targetLink = null)
    {
        $this->tieuDe = $tieuDe;
        $this->noiDung = $noiDung;
        $this->doiTuong = $doiTuong;
        $this->senderName = $senderName;
        $this->targetLink = $targetLink;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $defaultLink = route('tai-khoan.index');
        if (method_exists($notifiable, 'hasRole') && $notifiable->hasRole('chu_quan')) {
            $defaultLink = route('chu-quan.quan.index');
        }

        return [
            'title' => $this->tieuDe,
            'message' => $this->noiDung,
            'note' => 'Người gửi: ' . ($this->senderName ?? 'Ban quản trị Quán Mới'),
            'action' => 'broadcast',
            'status' => 'broadcast',
            'status_label' => 'Thông báo hệ thống',
            'doi_tuong' => $this->doiTuong,
            'sender' => $this->senderName ?? 'Ban quản trị',
            'link' => $this->targetLink ?? $defaultLink,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
