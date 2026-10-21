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
        $actionTitles = [
            'approved' => 'Hồ sơ quán "' . $this->quan->ten_quan . '" đã được phê duyệt!',
            'rejected' => 'Hồ sơ quán "' . $this->quan->ten_quan . '" chưa được phê duyệt',
            'submitted' => 'Hồ sơ quán "' . $this->quan->ten_quan . '" đã gửi duyệt thành công',
            'created' => 'Cơ sở ẩm thực "' . $this->quan->ten_quan . '" vừa được khởi tạo',
            'updated' => 'Thông tin quán "' . $this->quan->ten_quan . '" đã được cập nhật',
            'deleted' => 'Quán "' . $this->quan->ten_quan . '" đã bị tạm ẩn khỏi hệ thống',
            'restored' => 'Quán "' . $this->quan->ten_quan . '" đã được khôi phục hoạt động',
            'force_deleted' => 'Quán "' . $this->quan->ten_quan . '" đã bị xóa vĩnh viễn',
            'featured_granted' => 'Quán "' . $this->quan->ten_quan . '" được gắn nhãn Quán Nổi Bật!',
            'featured_revoked' => 'Quán "' . $this->quan->ten_quan . '" đã gỡ khỏi danh sách Nổi Bật',
            'verified_granted' => 'Quán "' . $this->quan->ten_quan . '" đã được cấp Tick Xanh xác thực!',
            'verified_revoked' => 'Tick Xanh xác thực của quán "' . $this->quan->ten_quan . '" đã bị gỡ',
            'menu_updated' => 'Thực đơn quán "' . $this->quan->ten_quan . '" đã được cập nhật thành công',
        ];

        $actionMessages = [
            'approved' => 'Chúc mừng bạn! Cơ sở ẩm thực "' . $this->quan->ten_quan . '" đã được Ban quản trị phê duyệt và chính thức hiển thị công khai trên QuanMoi.',
            'rejected' => 'Hồ sơ quán "' . $this->quan->ten_quan . '" đã bị từ chối duyệt.' . ($this->note ? ' Ghi chú: ' . $this->note : ' Vui lòng bổ sung hình ảnh và thông tin xác thực để gửi duyệt lại.'),
            'submitted' => 'Hồ sơ cơ sở ẩm thực "' . $this->quan->ten_quan . '" đã được gửi thành công và đang chờ ban quản trị kiểm duyệt.',
            'created' => 'Cơ sở ẩm thực "' . $this->quan->ten_quan . '" vừa được Ban quản trị khởi tạo và liên kết với tài khoản của bạn.',
            'updated' => 'Thông tin cơ sở ẩm thực "' . $this->quan->ten_quan . '" vừa được cập nhật thành công.',
            'deleted' => 'Cơ sở ẩm thực "' . $this->quan->ten_quan . '" của bạn đã bị quản trị viên tạm ẩn/khóa trên hệ thống.',
            'restored' => 'Cơ sở ẩm thực "' . $this->quan->ten_quan . '" của bạn đã được khôi phục và tiếp tục hoạt động.',
            'force_deleted' => 'Cơ sở ẩm thực "' . $this->quan->ten_quan . '" đã bị xóa vĩnh viễn khỏi cơ sở dữ liệu.',
            'featured_granted' => 'Chúc mừng! Quán "' . $this->quan->ten_quan . '" của bạn đã được Admin đưa vào danh sách Quán Nổi Bật trên trang chủ.',
            'featured_revoked' => 'Quán "' . $this->quan->ten_quan . '" của bạn đã được gỡ khỏi danh sách Quán Nổi Bật.',
            'verified_granted' => 'Chúc mừng! Quán "' . $this->quan->ten_quan . '" của bạn đã được cấp huy hiệu Tick Xanh xác thực chính chủ uy tín.',
            'verified_revoked' => 'Huy hiệu Tick Xanh xác thực của cơ sở "' . $this->quan->ten_quan . '" đã bị thu hồi.',
            'menu_updated' => 'Danh sách món ăn và thực đơn của quán "' . $this->quan->ten_quan . '" đã được lưu thành công vào hệ thống.',
        ];

        $statusLabels = [
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối duyệt',
            'submitted' => 'Chờ duyệt',
            'created' => 'Đã tạo mới',
            'updated' => 'Đã cập nhật',
            'deleted' => 'Đã tạm ẩn',
            'restored' => 'Đã khôi phục',
            'force_deleted' => 'Đã xóa vĩnh viễn',
            'featured_granted' => 'Quán nổi bật',
            'featured_revoked' => 'Hết nổi bật',
            'verified_granted' => 'Tick Xanh',
            'verified_revoked' => 'Gỡ Tick Xanh',
            'menu_updated' => 'Thực đơn',
        ];

        $targetLink = route('chu-quan.quan.index');
        if ($this->quan->slug) {
            if ($this->action === 'menu_updated') {
                $targetLink = route('chu-quan.quan.menu.edit', $this->quan->slug);
            } elseif (in_array($this->action, ['approved', 'featured_granted', 'featured_revoked', 'verified_granted', 'verified_revoked']) && $this->quan->trang_thai === 'da_duyet') {
                $targetLink = route('quan.detail', $this->quan->slug);
            } else {
                $targetLink = route('chu-quan.quan.show', $this->quan->slug);
            }
        }

        return [
            'quan_id' => $this->quan->id,
            'title' => $actionTitles[$this->action] ?? ('Cập nhật cơ sở: ' . $this->quan->ten_quan),
            'message' => $actionMessages[$this->action] ?? ('Thông tin cơ sở "' . $this->quan->ten_quan . '" đã có sự thay đổi.'),
            'note' => $this->note,
            'action' => $this->action,
            'slug' => $this->quan->slug,
            'status' => $this->quan->trang_thai,
            'status_label' => $statusLabels[$this->action] ?? 'Hệ thống',
            'link' => $targetLink,
            'cover_image_url' => $this->quan->anh_bia_url,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
