<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = AdminAuditLog::with('admin')->latest()->paginate(20);
        return view('admin.audit-log.index', compact('logs'));
    }
}
