<?php

namespace App\Models;

use App\Traits\SafeSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model
{
    use HasFactory, HasUuids, SafeSearchable;

    protected $table = 'khuyen_mai';

    public $incrementing = false;
    protected $keyType = 'string';

    public function searchableAs(): string
    {
        return 'khuyen_mai';
    }

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with(['quan:id,ten_quan']);
    }

    public function toSearchableArray(): array
    {
        $quan = $this->relationLoaded('quan')
            ? $this->quan
            : $this->quan()->select('id', 'ten_quan')->first();

        return [
            'id' => (string) $this->id,
            'quan_id' => (string) $this->quan_id,
            'tieu_de' => $this->tieu_de,
            'ma_voucher' => $this->ma_voucher ?? '',
            'ten_quan' => $quan?->ten_quan ?? '',
            'mon_ap_dung' => $this->mon_ap_dung ?? '',
            'slogan' => $this->slogan ?? '',
            'noi_dung' => $this->noi_dung ?? '',
            'loai_khuyen_mai' => $this->loai_khuyen_mai,
            'gia_tri_giam' => $this->gia_tri_giam ?? '',
            'trang_thai_duyet' => $this->trang_thai_duyet ?? 'cho_duyet',
            'trang_thai' => (bool) $this->trang_thai,
            'created_at' => $this->created_at ? $this->created_at->timestamp : null,
        ];
    }

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
        'trang_thai_duyet',
        'ly_do_tu_choi',
        'nguoi_duyet_id',
        'ngay_duyet',
        'ai_provider',
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'ngay_duyet' => 'datetime',
    ];

    protected $appends = [
        'con_han',
        'loai_label',
        'trang_thai_duyet_label',
    ];

    public function quan()
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }

    public function nguoiDuyet()
    {
        return $this->belongsTo(User::class, 'nguoi_duyet_id');
    }

    public function scopeDaDuyet($query)
    {
        return $query->where('trang_thai_duyet', 'da_duyet');
    }

    public function scopeChoDuyet($query)
    {
        return $query->where('trang_thai_duyet', 'cho_duyet');
    }

    public function scopeTuChoi($query)
    {
        return $query->where('trang_thai_duyet', 'tu_choi');
    }

    public function getConHanAttribute(): bool
    {
        if (! $this->trang_thai || $this->trang_thai_duyet !== 'da_duyet') {
            return false;
        }

        $today = now()->startOfDay();

        if ($this->ngay_ket_thuc && $today->gt($this->ngay_ket_thuc)) {
            return false;
        }

        return true;
    }

    public function getTrangThaiDuyetLabelAttribute(): string
    {
        return match ($this->trang_thai_duyet) {
            'cho_duyet' => 'Chờ duyệt',
            'da_duyet' => 'Đã duyệt',
            'tu_choi' => 'Bị từ chối',
            default => 'Chờ duyệt',
        };
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

    /**
     * Tìm kiếm voucher/khuyến mãi qua Elasticsearch (mã voucher, tiêu đề, món áp dụng, tên quán),
     * tự động fallback sang truy vấn SQL khi Elasticsearch không khả dụng.
     */
    public static function applyElasticSearch(Builder $query, string $keyword, ?string $statusFilter = null): void
    {
        $search = trim($keyword);
        if ($search === '') {
            return;
        }

        $filters = ($statusFilter && in_array($statusFilter, ['cho_duyet', 'da_duyet', 'tu_choi']))
            ? ['trang_thai_duyet' => $statusFilter]
            : [];

        if (static::applyElasticIdsToQuery($query, $search, $filters)) {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('tieu_de', 'like', "%{$search}%")
                ->orWhere('ma_voucher', 'like', "%{$search}%")
                ->orWhere('mon_ap_dung', 'like', "%{$search}%")
                ->orWhere('slogan', 'like', "%{$search}%")
                ->orWhereHas('quan', fn ($r) => $r->where('ten_quan', 'like', "%{$search}%"));
        });
    }
}
