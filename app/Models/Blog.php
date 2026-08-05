<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'status',
        'seo_title',
        'seo_description',
        'meta_keywords',
        'is_hero',
        'published_at',
        'approved_by',
        'published_by',
        'reading_time',
        'allow_comments',
        'is_featured',
        'view_count',
        'comment_count',
        'canonical_url',
        'og_image',
        'scheduled_at',
        'last_submitted_at',
    ];

    protected $casts = [
        'is_hero' => 'boolean',
        'published_at' => 'datetime',
        'allow_comments' => 'boolean',
        'is_featured' => 'boolean',
        'scheduled_at' => 'datetime',
        'last_submitted_at' => 'datetime',
    ];

    /**
     * Get the author of the blog.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the blog.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Get the tags for the blog.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_tag_items', 'blog_id', 'blog_tag_id')->withTimestamps();
    }

    /**
     * Get the comments for the blog.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    /**
     * Get the views for the blog.
     */
    public function views(): HasMany
    {
        return $this->hasMany(BlogView::class);
    }

    /**
     * Get the reports for the blog.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(BlogReport::class);
    }

    /**
     * Get the moderation logs for the blog.
     */
    public function moderationLogs(): HasMany
    {
        return $this->hasMany(BlogModerationLog::class);
    }
    
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
