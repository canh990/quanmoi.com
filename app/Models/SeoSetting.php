<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $table = 'seo_settings';

    protected $fillable = [
        'trang',
        'ten_trang',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
    ];

    /**
     * Get the SEO settings for a specific page slug.
     * Returns an empty model if not found (to avoid null errors in views).
     */
    public static function forPage(string $slug): self
    {
        return static::where('trang', $slug)->first() ?? new static();
    }
}
