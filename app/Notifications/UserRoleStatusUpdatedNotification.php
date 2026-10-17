<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserRoleStatusUpdatedNotification extends Notification
{
    use Queueable;

    public string $action;
    public string $message;
    public ?string $detail;
    public ?string $targetLink;

    public function __construct(string $action, string $message, ?string $detail = null, ?string $targetLink = null)
    {
        $this->action = $action;
        $this->message = $message;
        $this->detail = $detail;
        $this->targetLink = $targetLink;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $actionTitles = [
            'role_updated' => 'Thay đổi vai trò tài khoản',
            'verified' => 'Tài khoản đã được xác thực',
            'verification_rejected' => 'Yêu cầu xác thực chưa được duyệt',
            'permission_granted' => 'Cấp quyền truy cập mới',
            'permission_denied' => 'Thu hồi quyền truy cập',
            'permission_reset' => 'Cập nhật phân quyền mặc định',
        ];

        $statusLabels = [
            'role_updated' => 'Đổi vai trò',
            'verified' => 'Đã xác thực',
            'verification_rejected' => 'Từ chối xác thực',
            'permission_granted' => 'Được cấp quyền',
            'permission_denied' => 'Bị tước quyền',
            'permission_reset' => 'Quyền mặc định',
        ];

        $defaultLink = route('tai-khoan.index');
        if ($this->action === 'role_updated' && method_exists($notifiable, 'hasRole') && $notifiable->hasRole('chu_quan')) {
            $defaultLink = route('chu-quan.quan.index');
        }

        return [
            'title' => $actionTitles[$this->action] ?? 'Thông báo phân quyền tài khoản',
            'message' => $this->message,
            'note' => $this->detail,
            'action' => $this->action,
            'status' => $this->action,
            'status_label' => $statusLabels[$this->action] ?? 'Hệ thống',
            'link' => $this->targetLink ?? $defaultLink,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
