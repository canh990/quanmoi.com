<?php

namespace App\Notifications;

use App\Models\BlogSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterWelcomeNotification extends Notification
{
    use Queueable;

    public BlogSubscriber $subscriber;

    public function __construct(BlogSubscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'Quán Mới');
        $baseUrl = rtrim(config('app.url', 'http://127.0.0.1:8002'), '/');
        $blogUrl = $baseUrl . '/blog';
        $unsubscribeUrl = $baseUrl . '/blog/huy-dang-ky-nhan-tin/' . $this->subscriber->token;

        return (new MailMessage)
            ->subject("🎉 Chào mừng bạn đến với Bản tin Ẩm thực {$appName}!")
            ->greeting('Xin chào bạn yêu ẩm thực!')
            ->line("Cảm ơn bạn đã đăng ký nhận tin từ **{$appName}**.")
            ->line('Từ nay, mỗi khi có bài viết review quán ăn mới, món ngon độc lạ hoặc các chương trình ưu đãi ẩm thực hấp dẫn, chúng tôi sẽ gửi thông báo sớm nhất đến hộp thư này của bạn.')
            ->action('Khám phá Blog Ẩm thực ngay', $blogUrl)
            ->line('Chúc bạn luôn có những trải nghiệm ẩm thực trọn vẹn và ngon miệng!')
            ->salutation("Trân trọng,\nĐội ngũ {$appName}")
            ->line("Nếu bạn không muốn tiếp tục nhận email này, bạn có thể [hủy đăng ký tại đây]({$unsubscribeUrl}).");
    }
}
