<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Blog extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'status',
        'published_at',
        'scheduled_at',
        'last_submitted_at',
        'view_count',
        'comment_count',
        'is_hero',
        'is_featured',
        'seo_title',
        'seo_description',
        'meta_keywords',
        'reading_time',
        'allow_comments',
        'canonical_url',
        'og_image',
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
     * Get the users who bookmarked this blog.
     */
    public function savedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'blog_da_luu', 'blog_id', 'user_id')
            ->withPivot(['da_doc', 'doc_luc'])
            ->withTimestamps();
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
     * Get only top-level (root) comments that are active.
     */
    public function rootComments(): HasMany
    {
        return $this->hasMany(BlogComment::class)
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get all active comments.
     */
    public function activeComments(): HasMany
    {
        return $this->hasMany(BlogComment::class)->where('status', 'active');
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
     * Get the food reactions for the blog.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(BlogReaction::class);
    }

    /**
     * Lấy cảm xúc của người dùng hiện tại đối với bài viết này.
     */
    public function userReaction(?User $user = null): ?BlogReaction
    {
        $userId = $user ? $user->id : auth()->id();
        if (! $userId) {
            return null;
        }

        try {
            return $this->reactions()->where('user_id', $userId)->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Thống kê tổng hợp số lượng và breakdown cảm xúc ẩm thực.
     */
    public function getReactionsSummaryAttribute(): array
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('blog_reactions')) {
                return ['total' => 0, 'breakdown' => [], 'top' => []];
            }

            $breakdown = $this->reactions()
                ->selectRaw('reaction, count(*) as count')
                ->groupBy('reaction')
                ->orderByRaw('count(*) DESC')
                ->pluck('count', 'reaction')
                ->toArray();

            $total = (int) array_sum($breakdown);
            arsort($breakdown);
            $top = array_slice(array_keys($breakdown), 0, 3);

            return [
                'total' => $total,
                'breakdown' => $breakdown,
                'top' => $top,
            ];
        } catch (\Throwable $e) {
            return ['total' => 0, 'breakdown' => [], 'top' => []];
        }
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

    /**
     * Get the full URL for the blog's cover image.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if (empty($this->cover_image)) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($this->cover_image, ['http://', 'https://'])) {
            return $this->cover_image;
        }

        $cleanPath = ltrim($this->cover_image, '/');
        if (\Illuminate\Support\Str::startsWith($cleanPath, 'uploads/')) {
            return '/' . $cleanPath;
        }
        if (is_file(public_path('uploads/' . $cleanPath))) {
            return '/uploads/' . $cleanPath;
        }
        if (\Illuminate\Support\Str::startsWith($cleanPath, 'storage/')) {
            return '/' . $cleanPath;
        }

        return '/storage/' . $cleanPath;
    }

    /**
     * Tự động làm sạch mã HTML của bài viết chống Stored XSS
     */
    public function setContentAttribute($value): void
    {
        $this->attributes['content'] = function_exists('clean')
            ? clean($value)
            : strip_tags((string) $value, '<h1><h2><h3><h4><h5><h6><p><br><strong><em><u><strike><s><del><ul><ol><li><blockquote><a><img><table><thead><tbody><tr><th><td><pre><code><hr>');
    }

    /**
     * Tùy biến tên index trong Elasticsearch
     */
    public function searchableAs(): string
    {
        return 'blogs';
    }

    /**
     * Tùy biến truy vấn khi import toàn bộ dữ liệu vào Scout.
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with(['user', 'category', 'tags']);
    }

    /**
     * Chỉ index các bài viết đã xuất bản và không bị xóa mềm.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->status === 'published' 
            && !empty($this->published_at) 
            && $this->published_at <= now() 
            && $this->deleted_at === null;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        // Danh sách tên tags
        $tags = [];
        $tagSlugs = [];
        if ($this->relationLoaded('tags')) {
            $tags = $this->tags->pluck('name')->filter()->values()->all();
            $tagSlugs = $this->tags->pluck('slug')->filter()->values()->all();
        } else {
            $tags = $this->tags()->pluck('name')->filter()->values()->all();
            $tagSlugs = $this->tags()->pluck('slug')->filter()->values()->all();
        }

        // Tác giả
        $authorName = '';
        if ($this->relationLoaded('user')) {
            $authorName = $this->user ? ($this->user->ho_ten ?: $this->user->name ?: '') : '';
        } else {
            $authorName = $this->user()->value('ho_ten') ?: '';
        }

        // Chuyên mục
        $categoryName = '';
        $categorySlug = '';
        if ($this->relationLoaded('category')) {
            $categoryName = $this->category ? $this->category->name : '';
            $categorySlug = $this->category ? $this->category->slug : '';
        } else {
            $cat = $this->category()->first(['name', 'slug']);
            if ($cat) {
                $categoryName = $cat->name;
                $categorySlug = $cat->slug;
            }
        }

        // Làm sạch nội dung HTML để index toàn văn (plain text)
        $cleanContent = trim(strip_tags(html_entity_decode((string) $this->content, ENT_QUOTES, 'UTF-8')));

        return [
            'id'             => (int) $this->id,
            'title'          => (string) $this->title,
            'slug'           => (string) $this->slug,
            'excerpt'        => (string) $this->excerpt,
            'content'        => $cleanContent,
            'category'       => $categoryName,
            'category_slug'  => $categorySlug,
            'category_id'    => (int) $this->category_id,
            'author'         => $authorName,
            'user_id'        => (int) $this->user_id,
            'tags'           => $tags,
            'tag_slugs'      => $tagSlugs,
            'status'         => (string) $this->status,
            'is_hero'        => (bool) $this->is_hero,
            'is_featured'    => (bool) $this->is_featured,
            'view_count'     => (int) ($this->view_count ?? 0),
            'comment_count'  => (int) ($this->comment_count ?? 0),
            'reading_time'   => (int) ($this->reading_time ?? 4),
            'cover_url'      => $this->cover_image_url,
            'published_at'   => $this->published_at ? $this->published_at->timestamp : null,
            'created_at'     => $this->created_at ? $this->created_at->timestamp : null,
            'updated_at'     => $this->updated_at ? $this->updated_at->timestamp : null,
        ];
    }
}
