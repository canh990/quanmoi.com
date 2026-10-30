<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'parent_id',
        'user_id',
        'content',
        'likes_count',
        'status',
    ];

    protected $casts = [
        'likes_count' => 'integer',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saved(function (BlogComment $comment) {
            $comment->syncBlogCommentCount();
        });

        static::deleted(function (BlogComment $comment) {
            $comment->syncBlogCommentCount();
        });
    }

    /**
     * Synchronize the exact active comment count to the parent blog record.
     */
    public function syncBlogCommentCount(): void
    {
        if ($this->blog_id) {
            $count = static::where('blog_id', $this->blog_id)->where('status', 'active')->count();
            Blog::where('id', $this->blog_id)->update(['comment_count' => $count]);
        }
    }

    /**
     * Get the blog that owns the comment.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Get the user who wrote the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment (if this is a reply).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    /**
     * Get direct replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'parent_id')->where('status', 'active')->orderBy('created_at', 'asc');
    }

    /**
     * Get likes for this comment.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(BlogCommentLike::class, 'comment_id');
    }

    /**
     * Check if a specific user liked this comment.
     */
    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $user->id);
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Lấy cảm xúc của user hiện tại trên bình luận.
     */
    public function userReaction(?User $user = null): ?BlogCommentLike
    {
        $userId = $user ? $user->id : auth()->id();
        if (! $userId) {
            return null;
        }

        if ($this->relationLoaded('likes')) {
            return $this->likes->firstWhere('user_id', $userId);
        }

        return $this->likes()->where('user_id', $userId)->first();
    }

    /**
     * Thống kê tổng hợp số lượng và icon cảm xúc nhiều nhất trên bình luận.
     */
    public function getReactionsSummaryAttribute(): array
    {
        try {
            if ($this->relationLoaded('likes')) {
                $likesCollection = $this->likes;
                $total = $likesCollection->count();
                if ($total === 0) {
                    return ['total' => 0, 'breakdown' => [], 'top' => [], 'top_one' => null];
                }
                $breakdown = [];
                foreach ($likesCollection as $like) {
                    $r = !empty($like->reaction) ? $like->reaction : 'like';
                    $breakdown[$r] = ($breakdown[$r] ?? 0) + 1;
                }
            } else {
                $breakdown = $this->likes()
                    ->selectRaw("COALESCE(NULLIF(reaction, ''), 'like') as reaction_key, count(*) as count")
                    ->groupBy('reaction_key')
                    ->orderByRaw('count(*) DESC')
                    ->pluck('count', 'reaction_key')
                    ->toArray();

                $total = (int) array_sum($breakdown);
            }

            arsort($breakdown);
            $top = array_slice(array_keys($breakdown), 0, 3);
            $topOne = !empty($top) ? $top[0] : null;

            return [
                'total' => $total,
                'breakdown' => $breakdown,
                'top' => $top,
                'top_one' => $topOne,
            ];
        } catch (\Throwable $e) {
            return ['total' => 0, 'breakdown' => [], 'top' => [], 'top_one' => null];
        }
    }
}
