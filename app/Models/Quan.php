<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'quan';
    const DELETED_AT = 'ngay_xoa';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'chu_quan_id',
        'ten_quan',
        'loai_hinh_kinh_doanh',
        'slug',
        'mo_ta',
        'so_dien_thoai',
        'email',
        'dia_chi_chi_tiet',
        'tinh_thanh_id',
        'ten_tinh_thanh',
        'quan_huyen_id',
        'ten_quan_huyen',
        'phuong_xa_id',
        'ten_phuong_xa',
        'kinh_do',
        'vi_do',
        'gio_mo_cua',
        'gio_dong_cua',
        'gia_nho_nhat',
        'gia_lon_nhat',
        'anh_bia',
        'anh_bia_key',
        'trang_thai',
        'is_noi_bat',
        'is_xac_thuc',
        'luot_xem',
    ];

    protected $casts = [
        'is_noi_bat' => 'boolean',
        'is_xac_thuc' => 'boolean',
    ];

    public function chuQuan()
    {
        return $this->belongsTo(User::class, 'chu_quan_id');
    }

    public function hinhAnh()
    {
        return $this->hasMany(HinhAnhQuan::class, 'quan_id');
    }

    public function danhMucMenu()
    {
        return $this->hasMany(DanhMucMenu::class, 'quan_id')->orderBy('thu_tu', 'asc');
    }

    public function savedByUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'quan_da_luu', 'quan_id', 'nguoi_dung_id')->withTimestamps();
    }
}
