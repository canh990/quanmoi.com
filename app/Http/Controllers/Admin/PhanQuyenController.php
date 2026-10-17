<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NguoiDungQuyenHan;
use App\Models\QuyenHan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PhanQuyenController extends Controller
{
    public function index(Request $request)
    {
        $permissions = QuyenHan::withCount('vaiTro')->orderBy('ten')->get();
        $roles = VaiTro::with('quyenHan')->orderBy('ten')->get();

        $query = User::with(['vaiTro', 'quyenHanOverrides']);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('ho_ten', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('vai_tro_id', $request->input('role'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.phan-quyen.index', compact('permissions', 'roles', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_\-]+$/', 'unique:quyen_han,ten'],
            'mo_ta' => 'nullable|string|max:500',
        ], [
            'ten.required' => 'Mã quyền hạn không được để trống.',
            'ten.unique' => 'Mã quyền hạn này đã tồn tại trong hệ thống.',
            'ten.regex' => 'Mã quyền hạn chỉ được chứa chữ cái, số và dấu gạch dưới (ví dụ: quan_ly_bai_viet).',
            'ten.max' => 'Mã quyền hạn không được vượt quá 50 ký tự.',
        ]);

        $permission = QuyenHan::create([
            'ten' => strtolower(trim($validated['ten'])),
            'mo_ta' => $validated['mo_ta'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Thêm quyền hạn '{$permission->ten}' thành công!",
                'permission' => $permission,
            ]);
        }

        return redirect()->back()->with('success', 'Thêm quyền hạn mới thành công!');
    }

    public function overrideUserPermission(Request $request)
    {
        $validated = $request->validate([
            'nguoi_dung_id' => 'required|exists:nguoi_dung,id',
            'quyen_han_id' => 'required|exists:quyen_han,id',
            'cho_phep' => 'required|in:1,0,-1', // 1: cho_phep, 0: tu_choi, -1: xoa_override (dung mac dinh theo vai tro)
        ]);

        $userId = $validated['nguoi_dung_id'];
        $permId = $validated['quyen_han_id'];
        $action = $validated['cho_phep'];

        $targetUser = User::findOrFail($userId);
        $permission = QuyenHan::findOrFail($permId);

        // Security check: cannot revoke all permissions from the last active admin
        if ($targetUser->isAdmin() && $action === '0') {
            $activeAdminCount = User::whereHas('vaiTro', fn($q) => $q->where('ten', 'admin'))
                ->where('trang_thai', 'hoat_dong')
                ->count();
            if ($activeAdminCount <= 1 && $request->user()->id === $targetUser->id) {
                $msg = 'Không thể tự tước quyền của Quản trị viên đang hoạt động duy nhất.';
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return redirect()->back()->with('error', $msg);
            }
        }

        if ($action === '-1') {
            NguoiDungQuyenHan::where('nguoi_dung_id', $userId)
                ->where('quyen_han_id', $permId)
                ->delete();

            $targetUser->clearPermissionCache();

            // Real-time notification to user
            try {
                $targetUser->notify(new \App\Notifications\UserRoleStatusUpdatedNotification(
                    'permission_reset',
                    "Cấu hình phân quyền cho chức năng '{$permission->ten}' của bạn đã được đưa về mặc định theo vai trò."
                ));
            } catch (\Throwable $th) {}

            $msg = 'Đã xoá cấu hình override quyền cho người dùng.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'action' => '-1',
                    'permission_name' => $permission->ten,
                ]);
            }

            return redirect()->back()->with('success', $msg);
        }

        NguoiDungQuyenHan::updateOrCreate(
            ['nguoi_dung_id' => $userId, 'quyen_han_id' => $permId],
            ['id' => Str::uuid()->toString(), 'cho_phep' => (bool) $action]
        );

        $targetUser->clearPermissionCache();

        $statusText = $action === '1' ? 'Cấp quyền (Cho phép)' : 'Tước quyền (Từ chối)';
        $notifAction = $action === '1' ? 'permission_granted' : 'permission_denied';
        $notifMsg = $action === '1'
            ? "Tài khoản của bạn vừa được cấp quyền đặc biệt: '{$permission->ten}'."
            : "Quyền truy cập chức năng '{$permission->ten}' của bạn đã bị từ chối.";

        try {
            $targetUser->notify(new \App\Notifications\UserRoleStatusUpdatedNotification(
                $notifAction,
                $notifMsg
            ));
        } catch (\Throwable $th) {}

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật override: {$statusText} thành công!",
                'action' => $action,
                'permission_name' => $permission->ten,
            ]);
        }

        return redirect()->back()->with('success', "Đã cập nhật override: {$statusText} thành công!");
    }

    public function destroy(Request $request, $id)
    {
        $permission = QuyenHan::findOrFail($id);

        $rolesCount = $permission->vaiTro()->count();
        $overridesCount = NguoiDungQuyenHan::where('quyen_han_id', $permission->id)->count();

        if ($rolesCount > 0 || $overridesCount > 0) {
            $msg = "Không thể xóa quyền hạn '{$permission->ten}' vì đang được gán cho {$rolesCount} vai trò và {$overridesCount} người dùng.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $permission->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xóa quyền hạn '{$permission->ten}' thành công!",
            ]);
        }

        return redirect()->back()->with('success', "Đã xóa quyền hạn '{$permission->ten}' thành công!");
    }
}
