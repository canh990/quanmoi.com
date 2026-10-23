<?php

namespace App\Notifications;

use App\Models\VideoShort;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VideoStatusNotification extends Notification
{
    use Queueable;

    public $videoData;
    public $action;
    public $note;

    public function __construct(array $videoData, string $action, ?string $note = null)
    {
        $this->videoData = $videoData;
        $this->action = $action;
        $this->note = $note;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->videoData['tieu_de'] ?? 'Video review';
        $quanName = $this->videoData['ten_quan'] ?? 'quán';

        $slug = $this->videoData['quan_slug'] ?? '';
        $link = !empty($slug)
            ? (route('chu-quan.quan.show', ['slug' => $slug]) . '#owner-videos-section')
            : route('chu-quan.quan.index');

        if ($this->action === 'video_approved') {
            return [
                'title' => 'Video review cho quán "' . $quanName . '" đã được phê duyệt!',
                'message' => 'Video review "' . $title . '" đã được Ban quản trị phê duyệt và hiển thị công khai trên QuanMoi.',
                'action' => 'video_approved',
                'link' => $link,
                'video_id' => $this->videoData['id'] ?? null,
                'quan_id' => $this->videoData['quan_id'] ?? null,
                'quan_name' => $quanName,
                'note' => $this->note,
            ];
        }

        return [
            'title' => 'Video review cho quán "' . $quanName . '" bị từ chối duyệt',
            'message' => 'Video review "' . $title . '" đã bị từ chối duyệt và xóa khỏi quán.' . ($this->note ? ' Lý do: ' . $this->note : ''),
            'action' => 'video_rejected',
            'link' => $link,
            'video_id' => $this->videoData['id'] ?? null,
            'quan_id' => $this->videoData['quan_id'] ?? null,
            'quan_name' => $quanName,
            'note' => $this->note,
        ];
    }
}
