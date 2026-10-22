<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuyenHan;
use App\Models\VaiTro;
use Illuminate\Http\Request;

class VaiTroController extends Controller
{
    public function index()
    {
        $roles = VaiTro::with('quyenHan')->withCount(['nguoiDung', 'quyenHan'])->get();
        $permissions = QuyenHan::all();

        return view('admin.vai-tro.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_\-]+$/', 'unique:vai_tro,ten'],
        ], [
            'ten.required' => 'Tên vai trò không được để trống.',
            'ten.unique' => 'Mã vai trò này đã tồn tại trong hệ thống.',
            'ten.regex' => 'Mã vai trò chỉ được chứa chữ cái, số và dấu gạch dưới (ví dụ: bien_tap_vien).',
            'ten.max' => 'Tên vai trò không được vượt quá 50 ký tự.',
        ]);

        $role = VaiTro::create([
            'ten' => strtolower(trim($validated['ten'])),
        ]);

        if ($request->user()) {
            \App\Models\AdminAuditLog::create([
                'actor_id' => $request->user()->id,
                'action' => 'role.created',
                'target_type' => VaiTro::class,
                'target_id' => (string) $role->id,
                'metadata' => ['ten' => $role->ten],
                'ip_address' => $request->ip(),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Tạo vai trò '{$role->ten}' thành công!",
                'role' => $role,
            ]);
        }

        return redirect()->back()->with('success', "Tạo vai trò '{$role->ten}' thành công!");
    }

    public function updatePermissions(Request $request, $id)
    {
        $role = VaiTro::findOrFail($id);
        $permissionIds = $request->input('quyen_han', []);

        if (!is_array($permissionIds)) {
            $permissionIds = [];
        }

        // Xóa tất cả quyền cũ trước, sau đó gán lại để tránh lỗi UUID primary key với sync()
        \Illuminate\Support\Facades\DB::table('vai_tro_quyen_han')
            ->where('vai_tro_id', $role->id)
            ->delete();

        $now = now();
        $insertRows = [];
        foreach ($permissionIds as $permId) {
            $perm = \App\Models\QuyenHan::find($permId);
            if ($perm) {
                $insertRows[] = [
                    'id'           => (string) \Illuminate\Support\Str::uuid(),
                    'vai_tro_id'   => $role->id,
                    'quyen_han_id' => $perm->id,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
        }

        if (!empty($insertRows)) {
            \Illuminate\Support\Facades\DB::table('vai_tro_quyen_han')->insert($insertRows);
        }

        // Reload relation để reflection đúng
        $role->unsetRelation('quyenHan');

        if ($request->user()) {
            \App\Models\AdminAuditLog::create([
                'actor_id' => $request->user()->id,
                'action' => 'role.updated_permissions',
                'target_type' => VaiTro::class,
                'target_id' => (string) $role->id,
                'metadata' => ['ten' => $role->ten, 'permissions_count' => count($permissionIds)],
                'ip_address' => $request->ip(),
            ]);
        }

        // Notify users with this role
        try {
            $usersWithRole = $role->nguoiDung()->get();
            foreach ($usersWithRole as $targetUser) {
                $targetUser->clearPermissionCache();
                $targetUser->notify(new \App\Notifications\UserRoleStatusUpdatedNotification(
                    'permission_reset',
                    "Cơ cấu phân quyền cho vai trò '{$role->ten}' của bạn vừa được cập nhật."
                ));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Role update notification error: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Cập nhật quyền hạn cho vai trò '{$role->ten}' thành công!",
                'role_id' => $role->id,
                'permissions_count' => count($permissionIds),
            ]);
        }

        return redirect()->back()->with('success', "Cập nhật quyền hạn cho vai trò '{$role->ten}' thành công!");
    }

    public function destroy(Request $request, $id)
    {
        $role = VaiTro::findOrFail($id);

        $protectedRoles = ['admin', 'chu_quan', 'nguoi_dung'];
        if (in_array(strtolower($role->ten), $protectedRoles)) {
            $msg = "Vai trò hệ thống '{$role->ten}' là mặc định, không thể xóa.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($role->nguoiDung()->count() > 0) {
            $count = $role->nguoiDung()->count();
            $msg = "Không thể xóa vai trò '{$role->ten}' vì còn {$count} người dùng đang thuộc vai trò này.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($request->user()) {
            \App\Models\AdminAuditLog::create([
                'actor_id' => $request->user()->id,
                'action' => 'role.deleted',
                'target_type' => VaiTro::class,
                'target_id' => (string) $role->id,
                'metadata' => ['ten' => $role->ten],
                'ip_address' => $request->ip(),
            ]);
        }

        $role->quyenHan()->detach();
        $role->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xóa vai trò '{$role->ten}' thành công!",
            ]);
        }

        return redirect()->back()->with('success', "Đã xóa vai trò '{$role->ten}' thành công!");
    }
}
