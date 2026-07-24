<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaiTro extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'vai_tro';

    protected $fillable = [
        'ten',
    ];

    public function quyenHan(): BelongsToMany
    {
        return $this->belongsToMany(QuyenHan::class, 'vai_tro_quyen_han', 'vai_tro_id', 'quyen_han_id');
    }

    public function nguoiDung(): HasMany
    {
        return $this->hasMany(User::class, 'vai_tro_id');
    }
}
