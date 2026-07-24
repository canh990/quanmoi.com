<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoData extends Model
{
    protected $table = 'seo_datas';

    protected $fillable = [
        'page_type',
        'name',
        'loai_hinh_kinh_doanh',
        'province_code',
        'district_code',
        'link',
        'meta_title',
        'meta_description',
        'is_active',
    ];
}
