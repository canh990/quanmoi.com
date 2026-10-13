<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'con_hang',
    ];

    public function danhMuc()
    {
        return $this->belongsTo(DanhMucMenu::class, 'danh_muc_id');
    }
}
