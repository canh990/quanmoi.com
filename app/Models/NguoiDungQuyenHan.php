<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NguoiDungQuyenHan extends Pivot
{
    use HasFactory, HasUuids;

    protected $table = 'nguoi_dung_quyen_han';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nguoi_dung_id',
        'quyen_han_id',
        'cho_phep',
    ];

    protected function casts(): array
    {
        return [
            'cho_phep' => 'boolean',
        ];
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function quyenHan(): BelongsTo
    {
        return $this->belongsTo(QuyenHan::class, 'quyen_han_id');
    }
}
