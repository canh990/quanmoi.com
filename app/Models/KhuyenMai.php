<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'khuyen_mai';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'quan_id',
        'tieu_de',
        'ma_voucher',
        'loai_khuyen_mai',
        'gia_tri_giam',
        'slogan',
        'noi_dung',
        'the_le',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'mon_ap_dung',
        'trang_thai',
        'ai_provider',
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
    ];

    protected $appends = [
        'con_han',
        'loai_label',
    ];

    public function quan()
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }

    public function getConHanAttribute(): bool
    {
        if (! $this->trang_thai) {
            return false;
        }

        $today = now()->startOfDay();

        if ($this->ngay_ket_thuc && $today->gt($this->ngay_ket_thuc)) {
            return false;
        }

        return true;
    }

    public function getLoaiLabelAttribute(): string
    {
        return match ($this->loai_khuyen_mai) {
            'giam_phan_tram' => 'Giảm theo %',
            'giam_tien' => 'Giảm tiền trực tiếp',
            'mua_1_tang_1' => 'Mua 1 Tặng 1',
            'tang_mon' => 'Tặng món / đồ uống',
            'combo' => 'Combo ưu đãi',
            'voucher' => 'Voucher giảm giá',
            default => 'Ưu đãi đặc biệt',
        };
    }

    public function getNoiDungBaiDangAttribute(): ?string
    {
        return $this->noi_dung;
    }

    public function getDieuKienApDungAttribute(): ?string
    {
        return $this->the_le;
    }

    public function getGiaTriKhuyenMaiAttribute(): ?string
    {
        return $this->gia_tri_giam;
    }
}
