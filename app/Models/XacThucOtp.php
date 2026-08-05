<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XacThucOtp extends Model
{
    protected $table = 'xac_thuc_otp';

    protected $fillable = [
        'email',
        'otp',
        'otp_hash',
        'expires_at',
        'attempts',
        'locked_until',
        'last_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'locked_until' => 'datetime',
            'last_sent_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }
}
