<?php

namespace App\Notifications;

use App\Models\Quan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuanStatusUpdatedNotification extends Notification
{
    use Queueable;

    public $quan;
    public $action;
    public $note;

    public function __construct(Quan $quan, string $action, ?string $note = null)
    {
        $this->quan = $quan;
        $this->action = $action;
        $this->note = $note;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isApproved = ($this->action === 'approved');
        $title = $isApproved 
            ? 'Hồ sơ quán "' . $this->quan->ten_quan . '" đã được phê duyệt!'
            : 'Hồ sơ quán "' . $this->quan->ten_quan . '" chưa được phê duyệt';

        $message = $isApproved
            ? 'Chúc mừng bạn! Cơ sở ẩm thực "' . $this->quan->ten_quan . '" đã được Ban quản trị phê duyệt và chính thức hiển thị công khai trên QuanMoi.'
            : 'Hồ sơ quán "' . $this->quan->ten_quan . '" đã bị từ chối duyệt.' . ($this->note ? ' Ghi chú: ' . $this->note : ' Vui lòng bổ sung hình ảnh và thông tin xác thực để gửi duyệt lại.');

        $targetLink = $isApproved && $this->quan->slug
            ? route('quan.detail', $this->quan->slug)
            : route('chu-quan.quan.index');

        return [
            'quan_id' => $this->quan->id,
            'title' => $title,
            'message' => $message,
            'note' => $this->note,
            'action' => $this->action,
            'status' => $this->quan->trang_thai,
            'status_label' => $isApproved ? 'Đã duyệt' : 'Từ chối / Khóa',
            'link' => $targetLink,
            'cover_image_url' => $this->quan->anh_bia_url,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
