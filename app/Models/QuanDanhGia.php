<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuanDanhGia extends Model
{
    protected $table = 'quan_danh_gia';

    protected $fillable = ['quan_id', 'nguoi_dung_id', 'so_sao', 'binh_luan'];

    public function quan(): BelongsTo
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
