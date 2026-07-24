<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucMenu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'danh_muc_menu';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'quan_id',
        'ten_danh_muc',
        'thu_tu',
    ];

    public function quan()
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }

    public function monAn()
    {
        return $this->hasMany(MonTrongMenu::class, 'danh_muc_id');
    }
}
