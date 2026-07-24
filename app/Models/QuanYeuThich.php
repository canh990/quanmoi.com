<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuanYeuThich extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'quan_yeu_thich';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'quan_id',
        'nguoi_dung_id',
    ];

    public function quan()
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
