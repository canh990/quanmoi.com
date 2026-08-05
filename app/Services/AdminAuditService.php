<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AdminAuditService
{
    /** Record privileged actions without retaining passwords or other secret values. */
    public function record(User $actor, string $action, Model $target, Request $request, array $metadata = []): void
    {
        AdminAuditLog::create([
            'actor_id' => $actor->getKey(),
            'action' => $action,
            'target_type' => $target::class,
            'target_id' => (string) $target->getKey(),
            'metadata' => $metadata,
            'ip_address' => $request->ip(),
        ]);
    }
}
