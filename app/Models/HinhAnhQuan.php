<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HinhAnhQuan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'hinh_anh_quan';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'quan_id',
        'duong_dan',    // CDN URL — https://cdn.quanmoi.com/quan/gallery/...
        'object_key',   // R2 object key — dùng để xóa file khi cần
        'tieu_de',
    ];

    /** Return local uploads as same-origin URLs, even if APP_URL was localhost at upload time. */
    public function getDuongDanAttribute($value): ?string
    {
        $objectKey = $this->attributes['object_key'] ?? null;
        if (Str::startsWith($objectKey, 'local:quan/gallery/')) {
            return '/media/venue/'.Str::after($objectKey, 'local:');
        }

        return $value;
    }

    public function quan()
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }
}
