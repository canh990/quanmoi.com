<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class VaiTroQuyenHan extends Pivot
{
    use HasUuids;

    protected $table = 'vai_tro_quyen_han';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'vai_tro_id',
        'quyen_han_id',
    ];
}
