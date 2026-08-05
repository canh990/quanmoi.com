<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'user_id',
        'content_before',
        'content_after',
    ];

    /**
     * Get the blog that was revised.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Get the user who made the revision.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
