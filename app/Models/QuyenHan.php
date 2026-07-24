<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QuyenHan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'quyen_han';

    protected $fillable = [
        'ten',
        'mo_ta',
    ];

    public function vaiTro(): BelongsToMany
    {
        return $this->belongsToMany(VaiTro::class, 'vai_tro_quyen_han', 'quyen_han_id', 'vai_tro_id');
    }
}
