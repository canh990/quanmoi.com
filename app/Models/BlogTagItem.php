<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BlogTagItem extends Pivot
{
    protected $table = 'blog_tag_items';

    protected $fillable = [
        'blog_id',
        'blog_tag_id',
    ];
}
