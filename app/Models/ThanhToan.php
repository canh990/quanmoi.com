<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThanhToan extends Model
{
    use HasFactory;

    protected $table = 'thanh_toan';

    protected $fillable = [
        'user_id',
        'quan_id',
        'ma_giao_dich',
        'sepay_order_id',
        'sepay_transaction_id',
        'ten_goi_dich_vu',
        'so_tien',
        'phuong_thuc',
        'trang_thai',
        'ghi_chu',
        'du_lieu_ipn',
    ];

    protected function casts(): array
    {
        return [
            'so_tien' => 'decimal:2',
            'du_lieu_ipn' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quan(): BelongsTo
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }
}
