<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuanDaLuu extends Model
{
    use HasFactory;

    protected $table = 'quan_da_luu';

    protected $fillable = [
        'nguoi_dung_id',
        'quan_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function quan()
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }
}
