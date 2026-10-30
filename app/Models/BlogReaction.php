<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogReaction extends Model
{
    use HasFactory;

    protected $table = 'blog_reactions';

    protected $fillable = [
        'blog_id',
        'user_id',
        'reaction',
    ];

    /**
     * Danh sách 6 cảm xúc ẩm thực đặc thù:
     * - like: Hữu ích (👍)
     * - love: Yêu thích (❤️)
     * - drool: Thèm quá (🤤)
     * - fire: Phải thử ngay (🔥)
     * - cry: Khóc (😢)
     * - angry: Phẫn nộ (😡)
     */
    public const TYPES = [
        'love' => [
            'key' => 'love',
            'name' => 'Yêu thích',
            'emoji' => '❤️',
            'color' => '#e11d48',
            'text_class' => 'text-rose-600',
            'bg_class' => 'bg-rose-50',
            'border_class' => 'border-rose-200',
            'hover_border' => 'hover:border-rose-400',
            'desc' => 'Bài viết cực kỳ hay và hợp gu ẩm thực của tôi!',
        ],
        'drool' => [
            'key' => 'drool',
            'name' => 'Thèm quá',
            'emoji' => '🤤',
            'color' => '#d97706',
            'text_class' => 'text-amber-600',
            'bg_class' => 'bg-amber-50',
            'border_class' => 'border-amber-200',
            'hover_border' => 'hover:border-amber-400',
            'desc' => 'Nhìn món ăn ngon chảy nước miếng!',
        ],
        'fire' => [
            'key' => 'fire',
            'name' => 'Phải thử ngay',
            'emoji' => '🔥',
            'color' => '#ea580c',
            'text_class' => 'text-orange-600',
            'bg_class' => 'bg-orange-50',
            'border_class' => 'border-orange-200',
            'hover_border' => 'hover:border-orange-400',
            'desc' => 'Quán đỉnh chóp, nhất định phải set kèo ghé thử!',
        ],
        'like' => [
            'key' => 'like',
            'name' => 'Hữu ích',
            'emoji' => '👍',
            'color' => '#2563eb',
            'text_class' => 'text-blue-600',
            'bg_class' => 'bg-blue-50',
            'border_class' => 'border-blue-200',
            'hover_border' => 'hover:border-blue-400',
            'desc' => 'Thông tin địa chỉ, giá cả rất chi tiết và hữu ích.',
        ],
        'cry' => [
            'key' => 'cry',
            'name' => 'Khóc',
            'emoji' => '😢',
            'color' => '#6366f1',
            'text_class' => 'text-indigo-600',
            'bg_class' => 'bg-indigo-50',
            'border_class' => 'border-indigo-200',
            'hover_border' => 'hover:border-indigo-400',
            'desc' => 'Tiếc nuối vì quán quá xa hoặc đã hết món!',
        ],
        'angry' => [
            'key' => 'angry',
            'name' => 'Phẫn nộ',
            'emoji' => '😡',
            'color' => '#dc2626',
            'text_class' => 'text-red-600',
            'bg_class' => 'bg-red-50',
            'border_class' => 'border-red-200',
            'hover_border' => 'hover:border-red-400',
            'desc' => 'Không hài lòng với trải nghiệm hoặc chất lượng món ăn.',
        ],
    ];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
