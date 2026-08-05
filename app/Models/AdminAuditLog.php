<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    use HasUuids;

    protected $table = 'admin_audit_logs';

    protected $fillable = [
        'actor_id',
        'action',
        'target_type',
        'target_id',
        'metadata',
        'ip_address',
    ];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
