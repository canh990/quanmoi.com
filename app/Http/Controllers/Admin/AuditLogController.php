<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminAuditLog::with('admin');

        $q = trim((string) $request->input('q', ''));
        if ($q !== '') {
            $esService = app(\App\Services\ElasticAdminSearchService::class);
            $esUserIds = $esService->searchUser($q);

            $escaped = addcslashes($q, '%_');
            $query->where(function ($sub) use ($escaped, $esUserIds) {
                $sub->where('action', 'like', "%{$escaped}%")
                    ->orWhere('target_id', 'like', "%{$escaped}%")
                    ->orWhere('target_type', 'like', "%{$escaped}%")
                    ->orWhere('ip_address', 'like', "%{$escaped}%");

                if (! empty($esUserIds)) {
                    $sub->orWhereIn('actor_id', $esUserIds);
                } else {
                    $sub->orWhereHas('admin', function ($u) use ($escaped) {
                        $u->where('ho_ten', 'like', "%{$escaped}%")
                          ->orWhere('email', 'like', "%{$escaped}%");
                    });
                }
            });
        }

        $logs = $query->latest()->paginate(20)->withQueryString();
        return view('admin.audit-log.index', compact('logs', 'q'));
    }
}
