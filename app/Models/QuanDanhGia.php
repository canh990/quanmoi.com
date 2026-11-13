<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuanDanhGia extends Model
{
    protected $table = 'quan_danh_gia';

    protected $fillable = ['quan_id', 'nguoi_dung_id', 'so_sao', 'binh_luan', 'phan_hoi', 'phan_hoi_luc'];

    protected $casts = [
        'phan_hoi_luc' => 'datetime',
    ];

    /**
     * Đồng bộ lại điểm đánh giá trung bình của quán lên Elasticsearch khi có đánh giá mới/sửa/xóa.
     */
    protected static function booted(): void
    {
        $sync = static function (QuanDanhGia $danhGia): void {
            $quan = $danhGia->quan;
            if ($quan && method_exists($quan, 'shouldBeSearchable') && $quan->shouldBeSearchable()) {
                try {
                    $quan->searchable();
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Sync Quan Elastic after QuanDanhGia change failed: ' . $e->getMessage());
                }
            }
        };

        static::saved($sync);
        static::deleted($sync);
    }

    public function quan(): BelongsTo
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
