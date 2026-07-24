<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['ho_ten', 'email', 'mat_khau', 'da_xac_thuc', 'ngay_xac_thuc', 'vai_tro_id', 'trang_thai', 'anh_dai_dien', 'gioi_tinh', 'ngay_sinh', 'so_dien_thoai', 'dia_chi'])]
#[Hidden(['mat_khau', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    protected $table = 'nguoi_dung';

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ngay_xac_thuc' => 'datetime',
            'mat_khau' => 'hashed',
        ];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->mat_khau;
    }
}

