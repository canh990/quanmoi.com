<?php

namespace App\Notifications;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class BlogCommentNotification extends Notification
{
    use Queueable;

    public Blog $blog;
    public BlogComment $comment;
    public User $commenter;
    public bool $isReply;

    public function __construct(Blog $blog, BlogComment $comment, User $commenter, bool $isReply = false)
    {
        $this->blog = $blog;
        $this->comment = $comment;
        $this->commenter = $commenter;
        $this->isReply = $isReply;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $authorName = $this->commenter->ho_ten ?? 'Người dùng';
        $shortComment = Str::limit($this->comment->content, 120);

        $title = $this->isReply
            ? ($authorName . ' đã trả lời bình luận của bạn')
            : ($authorName . ' đã bình luận bài viết "' . $this->blog->title . '"');

        $message = $this->isReply
            ? ($authorName . ' trả lời: "' . $shortComment . '"')
            : ($authorName . ' bình luận: "' . $shortComment . '"');

        $link = route('blog.show', $this->blog->slug ?? $this->blog->id) . '#comment-' . $this->comment->id;

        return [
            'blog_id' => $this->blog->id,
            'comment_id' => $this->comment->id,
            'title' => $title,
            'message' => $message,
            'note' => $this->comment->content,
            'action' => $this->isReply ? 'comment_reply' : 'blog_comment',
            'status' => 'comment',
            'status_label' => $this->isReply ? 'Phản hồi' : 'Bình luận',
            'link' => $link,
            'slug' => $this->blog->slug,
            'cover_image_url' => $this->blog->cover_image_url,
            'commenter_name' => $authorName,
            'commenter_avatar' => $this->commenter->anh_dai_dien ?? null,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
