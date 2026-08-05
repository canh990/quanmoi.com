<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'user_id',
        'reason',
        'status',
    ];

    /**
     * Get the blog that was reported.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Get the user who reported the blog.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
