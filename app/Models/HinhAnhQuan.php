<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function quan()
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }
}
