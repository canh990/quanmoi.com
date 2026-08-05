<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoShort extends Model
{
    use HasFactory;

    protected $fillable = [
        'quan_id',
        'tieu_de',
        'video_id',
        'video_url',
        'thumbnail_url',
        'nguoi_dang',
        'avatar_nguoi_dang',
        'luot_xem',
        'luot_thich',
        'trang_thai',
    ];

    public function quan()
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }
}
