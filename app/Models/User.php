<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $table = 'nguoi_dung';

    const DELETED_AT = 'ngay_xoa';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'ho_ten',
        'email',
        'mat_khau',
        'da_xac_thuc',
        'ngay_xac_thuc',
        'vai_tro_id',
        'trang_thai',
        'anh_dai_dien',
        'anh_dai_dien_key',
        'gioi_tinh',
        'ngay_sinh',
        'so_dien_thoai',
        'dia_chi',
        'google_id',
    ];

    protected $hidden = [
        'mat_khau',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'da_xac_thuc' => 'boolean',
            'ngay_xac_thuc' => 'datetime',
            'mat_khau' => 'hashed',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->mat_khau;
    }

    public function vaiTro(): BelongsTo
    {
        return $this->belongsTo(VaiTro::class, 'vai_tro_id');
    }

    public function quan(): HasMany
    {
        return $this->hasMany(Quan::class, 'chu_quan_id');
    }

    public function savedQuan(): BelongsToMany
    {
        return $this->belongsToMany(Quan::class, 'quan_da_luu', 'nguoi_dung_id', 'quan_id')->withTimestamps();
    }

    public function blogModerationLogs(): HasMany
    {
        return $this->hasMany(BlogModerationLog::class, 'admin_id');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function hasRole(string $roleName): bool
    {
        return strtolower($this->vaiTro?->ten ?? '') === strtolower($roleName);
    }

    public function isActive(): bool
    {
        return $this->trang_thai === 'hoat_dong' && ! $this->trashed();
    }

    public function getTenVaiTroHienThiAttribute(): string
    {
        if ($this->isAdmin()) {
            return 'Quản trị viên';
        }

        return match (strtolower($this->vaiTro?->ten ?? '')) {
            'chu_quan' => 'Chủ quán',
            'nguoi_dung' => 'Thành viên',
            default => 'Thành viên',
        };
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    public function blogComments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function blogReports(): HasMany
    {
        return $this->hasMany(BlogReport::class);
    }

    public function blogRevisions(): HasMany
    {
        return $this->hasMany(BlogRevision::class);
    }
}
