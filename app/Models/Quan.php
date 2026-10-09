<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Quan extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Searchable;

    protected $table = 'quan';
    const DELETED_AT = 'ngay_xoa';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'chu_quan_id',
        'ten_quan',
        'loai_hinh_kinh_doanh',
        'slug',
        'mo_ta',
        'so_dien_thoai',
        'email',
        'dia_chi_chi_tiet',
        'tinh_thanh_id',
        'ten_tinh_thanh',
        'quan_huyen_id',
        'ten_quan_huyen',
        'phuong_xa_id',
        'ten_phuong_xa',
        'kinh_do',
        'vi_do',
        'gio_mo_cua',
        'gio_dong_cua',
        'gia_nho_nhat',
        'gia_lon_nhat',
        'tiktok_url',
        'shopeefood_url',
        'anh_bia',
        'anh_bia_key',
        'trang_thai',
        'is_noi_bat',
        'is_xac_thuc',
        'luot_xem',
    ];

    /**
     * Tùy biến truy vấn khi import toàn bộ dữ liệu vào Scout.
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with([
            'danhMucMenu.monAn:id,danh_muc_id,ten_mon,gia'
        ]);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        // Lấy danh sách tên món ăn từ thực đơn của quán
        $danhSachMon = [];
        if ($this->relationLoaded('danhMucMenu')) {
            $danhSachMon = $this->danhMucMenu
                ->pluck('monAn')
                ->flatten()
                ->pluck('ten_mon')
                ->filter()
                ->unique()
                ->values()
                ->all();
        } else {
            $danhSachMon = $this->danhMucMenu()
                ->with('monAn:id,danh_muc_id,ten_mon')
                ->get()
                ->pluck('monAn')
                ->flatten()
                ->pluck('ten_mon')
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $document = [
            'id'                   => (string) $this->id,
            'ten_quan'             => $this->ten_quan,
            'loai_hinh_kinh_doanh' => $this->loai_hinh_kinh_doanh,
            'mo_ta'                => $this->mo_ta,
            'dia_chi'              => trim($this->dia_chi_chi_tiet . ', ' . $this->ten_phuong_xa . ', ' . $this->ten_quan_huyen . ', ' . $this->ten_tinh_thanh),
            'tinh_thanh_id'        => (string) $this->tinh_thanh_id,
            'quan_huyen_id'        => (string) $this->quan_huyen_id,
            'phuong_xa_id'         => (string) $this->phuong_xa_id,
            'gia_nho_nhat'         => (float) $this->gia_nho_nhat,
            'gia_lon_nhat'         => (float) $this->gia_lon_nhat,
            'gio_mo_cua'           => $this->gio_mo_cua,
            'gio_dong_cua'         => $this->gio_dong_cua,
            'luot_xem'             => (int) $this->luot_xem,
            'trang_thai'           => $this->trang_thai,
            'is_noi_bat'           => (bool) $this->is_noi_bat,
            'is_xac_thuc'          => (bool) $this->is_xac_thuc,
            'mon_an'               => $danhSachMon,
            'created_at'           => $this->created_at ? $this->created_at->timestamp : null,
            'updated_at'           => $this->updated_at ? $this->updated_at->timestamp : null,
        ];

        // Chỉ index location nếu quán có đủ tọa độ GPS hợp lệ
        if (!empty($this->vi_do) && !empty($this->kinh_do) && is_numeric($this->vi_do) && is_numeric($this->kinh_do)) {
            $document['location'] = [
                'lat' => (float) $this->vi_do,
                'lon' => (float) $this->kinh_do,
            ];
        }

        return $document;
    }

    protected $casts = [
        'is_noi_bat' => 'boolean',
        'is_xac_thuc' => 'boolean',
    ];

    protected $appends = ['anh_bia_url'];

    /** Resolve old storage URLs to project-local copies usable in Docker. */
    public function getAnhBiaUrlAttribute(): ?string
    {
        $key = $this->attributes['anh_bia_key'] ?? null;
        if (is_string($key) && str_starts_with($key, 'local:quan/anh-bia/')) {
            $localPath = substr($key, strlen('local:'));
            if (is_file(public_path('uploads/'.$localPath))) {
                return '/uploads/'.$localPath;
            }
        }

        $value = $this->attributes['anh_bia'] ?? null;
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $path = parse_url($value, PHP_URL_PATH) ?: $value;
        $path = ltrim(str_replace('\\', '/', $path), '/');
        $path = preg_replace('#^(?:storage|public)/#', '', $path);

        // New local uploads are stored under public/uploads, not storage/app/public.
        // Normalize /uploads/foo, uploads/foo, and absolute localhost URLs consistently.
        $isUploadsPath = str_starts_with($path, 'uploads/');
        if ($isUploadsPath) {
            $path = substr($path, strlen('uploads/'));
        }
        if (is_file(public_path('uploads/'.$path))) {
            return '/uploads/'.$path;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            $host = strtolower((string) parse_url($value, PHP_URL_HOST));
            if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
                return ($isUploadsPath ? '/uploads/' : '/storage/').$path;
            }

            return $value;
        }

        return '/storage/'.$path;
    }

    public function delete()
    {
        if ($this->trashed()) {
            return false;
        }

        $deletedAt = now();
        $result = parent::delete();

        if ($result) {
            $this->newQuery()->withoutGlobalScopes()->whereKey($this->getKey())->update([
                'ngay_xoa' => $deletedAt,
                'deleted_at' => $deletedAt,
            ]);
            $this->ngay_xoa = $deletedAt;
            $this->deleted_at = $deletedAt;
        }

        return $result;
    }

    public function restore()
    {
        if (! $this->trashed()) {
            return false;
        }

        $restored = parent::restore();

        if ($restored) {
            $this->newQuery()->withoutGlobalScopes()->whereKey($this->getKey())->update([
                'ngay_xoa' => null,
                'deleted_at' => null,
            ]);
            $this->ngay_xoa = null;
            $this->deleted_at = null;
        }

        return $restored;
    }

    public function chuQuan()
    {
        return $this->belongsTo(User::class, 'chu_quan_id');
    }

    public function hinhAnh()
    {
        return $this->hasMany(HinhAnhQuan::class, 'quan_id');
    }

    public function danhMucMenu()
    {
        return $this->hasMany(DanhMucMenu::class, 'quan_id')->orderBy('thu_tu', 'asc');
    }

    public function savedByUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'quan_da_luu', 'quan_id', 'nguoi_dung_id')->withTimestamps();
    }

    public function videos()
    {
        return $this->hasMany(VideoShort::class, 'quan_id');
    }

    public function danhGia()
    {
        return $this->hasMany(QuanDanhGia::class, 'quan_id');
    }
}
