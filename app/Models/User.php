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
        'cai_dat',
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
            'cai_dat' => 'array',
        ];
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

    public function quyenHanOverrides(): BelongsToMany
    {
        return $this->belongsToMany(QuyenHan::class, 'nguoi_dung_quyen_han', 'nguoi_dung_id', 'quyen_han_id')
            ->using(NguoiDungQuyenHan::class)
            ->withPivot('cho_phep')
            ->withTimestamps();
    }

    protected ?array $cachedPermissionOverrides = null;
    protected ?array $cachedRolePermissions = null;

    public function clearPermissionCache(): void
    {
        $this->cachedPermissionOverrides = null;
        $this->cachedRolePermissions = null;
        $this->unsetRelation('quyenHanOverrides');
        $this->unsetRelation('vaiTro');
    }

    public function hasPermissionTo(string $permissionName): bool
    {
        // 1. Check user-level override in nguoi_dung_quyen_han
        if ($this->cachedPermissionOverrides === null) {
            $this->cachedPermissionOverrides = [];
            $overrides = $this->relationLoaded('quyenHanOverrides')
                ? $this->quyenHanOverrides
                : $this->quyenHanOverrides()->get();

            foreach ($overrides as $ov) {
                $this->cachedPermissionOverrides[$ov->ten] = (bool) $ov->pivot->cho_phep;
                $this->cachedPermissionOverrides[$ov->id] = (bool) $ov->pivot->cho_phep;
            }
        }

        if (array_key_exists($permissionName, $this->cachedPermissionOverrides)) {
            return $this->cachedPermissionOverrides[$permissionName];
        }

        // 2. Check role permissions via vai_tro_quyen_han
        if ($this->vai_tro_id) {
            if ($this->cachedRolePermissions === null) {
                $this->cachedRolePermissions = [];
                $role = $this->relationLoaded('vaiTro')
                    ? $this->vaiTro
                    : $this->vaiTro()->with('quyenHan')->first();

                if ($role) {
                    $perms = $role->relationLoaded('quyenHan') ? $role->quyenHan : $role->quyenHan()->get();
                    foreach ($perms as $p) {
                        $this->cachedRolePermissions[$p->ten] = true;
                        $this->cachedRolePermissions[$p->id] = true;
                    }
                }
            }

            if (!empty($this->cachedRolePermissions[$permissionName])) {
                return true;
            }
        }

        // 3. Fallback for admin role if no explicit user-level override exists
        if ($this->isAdmin()) {
            return true;
        }

        return false;
    }

    public function hasPermissionExplicitlyDenied(string $permissionName): bool
    {
        if ($this->cachedPermissionOverrides === null) {
            $this->hasPermissionTo($permissionName);
        }

        return array_key_exists($permissionName, $this->cachedPermissionOverrides ?? [])
            && $this->cachedPermissionOverrides[$permissionName] === false;
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

    public function getAnhDaiDienUrlAttribute(): string
    {
        if (!empty($this->anh_dai_dien)) {
            if (\Illuminate\Support\Str::startsWith($this->anh_dai_dien, ['http://', 'https://'])) {
                return $this->anh_dai_dien;
            }
            return asset('storage/' . $this->anh_dai_dien);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->ho_ten ?? 'User') . '&background=ffdbcc&color=a04100&bold=true&size=128';
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

    /**
     * Lấy giá trị cài đặt người dùng hoặc trả về mặc định
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->cai_dat ?? [];
        return data_get($settings, $key, $default);
    }
}
