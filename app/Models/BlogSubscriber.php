<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogSubscriber extends Model
{
    use HasFactory;

    protected $table = 'blog_subscribers';

    protected $fillable = [
        'email',
        'user_id',
        'ip_address',
        'source',
        'status',
        'token',
    ];

    /**
     * Người dùng liên kết (nếu có đăng nhập)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
