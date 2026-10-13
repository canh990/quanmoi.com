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
            $localPath = Str::after($objectKey, 'local:');
            if (is_file(public_path('uploads/'.$localPath))) {
                return '/uploads/'.$localPath;
            }
            return '/media/venue/'.$localPath;
        }

        if (is_string($value) && (Str::startsWith($value, 'http://') || Str::startsWith($value, 'https://'))) {
            $host = strtolower((string) parse_url($value, PHP_URL_HOST));
            if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
                return parse_url($value, PHP_URL_PATH) ?: $value;
            }
        }

        return $value;
    }

    public function quan()
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }
}
