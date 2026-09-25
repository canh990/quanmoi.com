<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    use HasFactory;

    protected $table = 'thanh_toan';

    protected $fillable = [
        'user_id',
        'quan_id',
        'ma_giao_dich',
        'ten_goi_dich_vu',
        'so_tien',
        'phuong_thuc',
        'trang_thai',
        'ghi_chu',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quan()
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }
}
