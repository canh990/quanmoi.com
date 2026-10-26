<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucQuan extends Model
{
    use HasFactory;

    protected $table = 'danh_muc_quan';

    protected $fillable = [
        'ten_danh_muc',
        'slug',
        'mo_ta',
        'icon',
        'mau_sac',
        'hinh_anh',
        'thu_tu',
        'is_hien_thi',
        'is_noi_bat',
    ];

    protected $casts = [
        'is_hien_thi' => 'boolean',
        'is_noi_bat'  => 'boolean',
        'thu_tu'      => 'integer',
    ];

    public function quans()
    {
        return $this->hasMany(Quan::class, 'danh_muc_id');
    }

    public function scopeHienThi($query)
    {
        return $query->where('is_hien_thi', true);
    }

    public function scopeNoiBat($query)
    {
        return $query->where('is_noi_bat', true);
    }

    public function scopeSapXep($query)
    {
        return $query->orderByDesc('is_noi_bat')->orderBy('thu_tu')->orderBy('id');
    }
}
