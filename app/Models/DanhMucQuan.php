<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucQuan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'danh_muc_quans';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ten_danh_muc',
        'slug',
    ];

    public function quans()
    {
        return $this->hasMany(Quan::class, 'danh_muc_id');
    }
}
