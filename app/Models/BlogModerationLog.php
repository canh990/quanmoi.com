<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogModerationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'admin_id',
        'action',
        'from_status',
        'to_status',
        'note',
    ];

    /**
     * Get the blog this log belongs to.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Get the admin/user who performed the action.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
