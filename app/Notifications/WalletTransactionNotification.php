<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WalletTransactionNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $message;
    public string $action;
    public string $statusLabel;
    public ?string $link;
    public array $extra;

    public function __construct(
        string $title,
        string $message,
        string $action = 'wallet_deposit',
        string $statusLabel = 'Ví & Nạp tiền',
        ?string $link = null,
        array $extra = []
    ) {
        $this->title = $title;
        $this->message = $message;
        $this->action = $action;
        $this->statusLabel = $statusLabel;
        $this->link = $link;
        $this->extra = $extra;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return array_merge([
            'title' => $this->title,
            'message' => $this->message,
            'action' => $this->action,
            'status' => 'approved',
            'status_label' => $this->statusLabel,
            'link' => $this->link ?? route('tai-khoan.vi-tien'),
            'created_at' => now()->toIso8601String(),
        ], $this->extra);
    }
}
