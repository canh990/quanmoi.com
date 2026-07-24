<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids, SoftDeletes;

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
        'gioi_tinh',
        'ngay_sinh',
        'so_dien_thoai',
        'dia_chi'
    ];

    protected $hidden = [
        'mat_khau',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
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

    public function isAdmin(): bool
    {
        return $this->vaiTro?->ten === 'admin' || strtolower($this->email) === 'admin@quanmoi.com';
    }

    public function hasRole(string $roleName): bool
    {
        if ($roleName === 'admin' && strtolower($this->email) === 'admin@quanmoi.com') {
            return true;
        }
        return strtolower($this->vaiTro?->ten ?? '') === strtolower($roleName);
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
}
