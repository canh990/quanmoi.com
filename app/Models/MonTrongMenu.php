<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MonTrongMenu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'mon_trong_menu';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'danh_muc_id',
        'ten_mon',
        'mo_ta',
        'gia',
        'hinh_anh',
        'shopeefood_url',
        'con_hang',
    ];

    protected $appends = ['hinh_anh_url'];

    public function getHinhAnhUrlAttribute(): ?string
    {
        $value = trim((string) $this->getRawOriginal('hinh_anh'));

        if ($value === '') {
            return null;
        }

        $path = Str::startsWith($value, ['http://', 'https://'])
            ? (string) parse_url($value, PHP_URL_PATH)
            : $value;

        if (Str::startsWith($path, '/storage/')) {
            $relativePath = Str::after($path, '/storage/');

            if (is_file(public_path('uploads/'.$relativePath))) {
                return asset('uploads/'.$relativePath);
            }

            if (Str::startsWith($value, ['http://', 'https://'])) {
                return $value;
            }

            return asset(ltrim($path, '/'));
        }

        if (Str::startsWith($path, '/uploads/')) {
            return asset(ltrim($path, '/'));
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        $relativePath = ltrim($value, '/');

        if (is_file(public_path('uploads/'.$relativePath))) {
            return asset('uploads/'.$relativePath);
        }

        $r2Base = config('filesystems.disks.r2.url') ?: (
            config('filesystems.disks.r2.endpoint') && config('filesystems.disks.r2.bucket')
                ? config('filesystems.disks.r2.endpoint').'/'.config('filesystems.disks.r2.bucket')
                : ''
        );

        return $r2Base ? rtrim($r2Base, '/').'/'.$relativePath : null;
    }

    public function danhMuc()
    {
        return $this->belongsTo(DanhMucMenu::class, 'danh_muc_id');
    }
}
