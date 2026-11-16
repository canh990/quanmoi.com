<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucQuan extends Model
{
    use HasFactory;

    protected $table = 'danh_muc_quan';

    protected $fillable = [
        'ten_danh_muc',
        'slug',
        'mo_ta',
        'icon',
        'mau_sac',
        'hinh_anh',
        'thu_tu',
        'is_hien_thi',
        'is_noi_bat',
    ];

    protected $casts = [
        'is_hien_thi' => 'boolean',
        'is_noi_bat'  => 'boolean',
        'thu_tu'      => 'integer',
    ];

    public static array $presetColors = [
        'rose'    => ['name' => 'Hồng Ruby', 'badge' => 'bg-rose-50 text-rose-700 border-rose-200', 'bg' => 'bg-rose-500', 'hex' => '#f43f5e', 'light' => '#fff1f2', 'border' => '#fecdd3', 'text' => '#be123c'],
        'amber'   => ['name' => 'Vàng Hổ Phách', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200', 'bg' => 'bg-amber-500', 'hex' => '#f59e0b', 'light' => '#fffbeb', 'border' => '#fde68a', 'text' => '#b45309'],
        'indigo'  => ['name' => 'Chàm Tinh Tế', 'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'bg' => 'bg-indigo-500', 'hex' => '#6366f1', 'light' => '#eef2ff', 'border' => '#c7d2fe', 'text' => '#4338ca'],
        'emerald' => ['name' => 'Xanh Lục Bảo', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bg' => 'bg-emerald-500', 'hex' => '#10b981', 'light' => '#ecfdf5', 'border' => '#a7f3d0', 'text' => '#047857'],
        'orange'  => ['name' => 'Cam Nhiệt Đới', 'badge' => 'bg-orange-50 text-orange-700 border-orange-200', 'bg' => 'bg-orange-500', 'hex' => '#f97316', 'light' => '#fff7ed', 'border' => '#fed7aa', 'text' => '#c2410c'],
        'violet'  => ['name' => 'Tím Huyền Bí', 'badge' => 'bg-violet-50 text-violet-700 border-violet-200', 'bg' => 'bg-violet-500', 'hex' => '#8b5cf6', 'light' => '#f5f3ff', 'border' => '#ddd6fe', 'text' => '#6d28d9'],
        'pink'    => ['name' => 'Hồng Pastel', 'badge' => 'bg-pink-50 text-pink-700 border-pink-200', 'bg' => 'bg-pink-500', 'hex' => '#ec4899', 'light' => '#fdf2f8', 'border' => '#fbcfe8', 'text' => '#be185d'],
        'yellow'  => ['name' => 'Vàng Nắng', 'badge' => 'bg-yellow-50 text-yellow-800 border-yellow-200', 'bg' => 'bg-yellow-400', 'hex' => '#eab308', 'light' => '#fefce8', 'border' => '#fef08a', 'text' => '#854d0e'],
        'teal'    => ['name' => 'Xanh Mòng Két', 'badge' => 'bg-teal-50 text-teal-700 border-teal-200', 'bg' => 'bg-teal-500', 'hex' => '#14b8a6', 'light' => '#f0fdfa', 'border' => '#99f6e4', 'text' => '#0f766e'],
        'sky'     => ['name' => 'Xanh Lam Nhạt', 'badge' => 'bg-sky-50 text-sky-700 border-sky-200', 'bg' => 'bg-sky-500', 'hex' => '#0ea5e9', 'light' => '#f0f9ff', 'border' => '#bae6fd', 'text' => '#0369a1'],
    ];

    public function getColorInfoAttribute(): array
    {
        $key = $this->mau_sac ?: 'amber';
        return self::$presetColors[$key] ?? self::$presetColors['amber'];
    }

    public function quans()
    {
        return $this->hasMany(Quan::class, 'danh_muc_id');
    }

    public function scopeHienThi($query)
    {
        return $query->where('is_hien_thi', true);
    }

    public function scopeNoiBat($query)
    {
        return $query->where('is_noi_bat', true);
    }

    public function scopeSapXep($query)
    {
        return $query->orderByDesc('is_noi_bat')->orderBy('thu_tu')->orderBy('id');
    }
}
