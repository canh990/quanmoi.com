<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;

class DuyetXacThucController extends Controller
{
    public function __construct(private readonly ?AdminAuditService $adminAudit = null) {}

    public function index()
    {
        $pendingQuan = Quan::where('trang_thai', 'chua_duyet')
            ->with(['chuQuan', 'hinhAnh'])
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        $rejectedQuan = Quan::where('trang_thai', 'bi_khoa')
            ->with(['chuQuan', 'hinhAnh'])
            ->latest()
            ->paginate(10, ['*'], 'rejected_page');

        $recentApproved = Quan::where('trang_thai', 'da_duyet')
            ->with(['chuQuan', 'hinhAnh'])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        $unverifiedUsers = User::where('da_xac_thuc', false)
            ->latest()
            ->paginate(10, ['*'], 'users_page');

        $stats = [
            'pending_quan' => Quan::where('trang_thai', 'chua_duyet')->count(),
            'rejected_quan' => Quan::where('trang_thai', 'bi_khoa')->count(),
            'approved_quan' => Quan::where('trang_thai', 'da_duyet')->count(),
            'unverified_users' => User::where('da_xac_thuc', false)->count(),
        ];

        // Tự động đánh dấu các thông báo quán chờ duyệt và user chưa xác thực đã được admin xem qua
        $admin = auth()->user();
        if ($admin) {
            $caiDat = $admin->cai_dat ?? [];
            if (is_string($caiDat)) {
                $caiDat = json_decode($caiDat, true) ?: [];
            }
            $readIds = array_map('strval', $caiDat['read_admin_notifs'] ?? []);
            $venueIds = $pendingQuan->pluck('id')->map(fn($id) => 'quan_' . $id)->all();
            $userIds = $unverifiedUsers->pluck('id')->map(fn($id) => 'user_' . $id)->all();
            $newReadIds = array_unique(array_merge($readIds, $venueIds, $userIds));
            if (count($newReadIds) > 500) {
                $newReadIds = array_slice($newReadIds, -500);
            }
            if (count($newReadIds) !== count($readIds)) {
                $caiDat['read_admin_notifs'] = array_values($newReadIds);
                \Illuminate\Support\Facades\DB::table('nguoi_dung')
                    ->where('id', $admin->id)
                    ->update(['cai_dat' => json_encode($caiDat, JSON_UNESCAPED_UNICODE)]);
                $admin->cai_dat = $caiDat;
            }
        }

        return view('admin.duyet-xac-thuc.index', compact(
            'pendingQuan',
            'rejectedQuan',
            'recentApproved',
            'unverifiedUsers',
            'stats'
        ));
    }

    private function getStats(): array
    {
        $realtime = ThongBaoAdminController::buildRealtimeNotificationData(auth()->user());

        return [
            'pending_quan' => Quan::where('trang_thai', 'chua_duyet')->count(),
            'rejected_quan' => Quan::where('trang_thai', 'bi_khoa')->count(),
            'approved_quan' => Quan::where('trang_thai', 'da_duyet')->count(),
            'unverified_users' => User::where('da_xac_thuc', false)->count(),
            'unread_count' => $realtime['unread_count'] ?? 0,
        ];
    }

    private function markItemAsRead(Request $request, string $notifId): void
    {
        $admin = $request->user();
        if (!$admin) return;
        $caiDat = $admin->cai_dat ?? [];
        if (is_string($caiDat)) {
            $caiDat = json_decode($caiDat, true) ?: [];
        }
        $readIds = array_map('strval', $caiDat['read_admin_notifs'] ?? []);
        if (!in_array($notifId, $readIds, true)) {
            $readIds[] = $notifId;
            if (count($readIds) > 500) {
                $readIds = array_slice($readIds, -500);
            }
            $caiDat['read_admin_notifs'] = array_values($readIds);
            \Illuminate\Support\Facades\DB::table('nguoi_dung')
                ->where('id', $admin->id)
                ->update(['cai_dat' => json_encode($caiDat, JSON_UNESCAPED_UNICODE)]);
            $admin->cai_dat = $caiDat;
        }
    }

    public function approveQuan(Request $request, $id)
    {
        if ($request->input('action') === 'reject' || $request->input('action') === 'khong_duyet') {
            return $this->rejectQuan($request, $id);
        }

        $this->markItemAsRead($request, 'quan_' . $id);

        $quan = Quan::findOrFail($id);
        $quan->update([
            'trang_thai' => 'da_duyet',
            'is_xac_thuc' => true,
        ]);

        $owner = $quan->chuQuan;
        $ownerRole = VaiTro::where('ten', 'chu_quan')->first();
        if ($owner && $ownerRole && ! $owner->hasRole('admin')) {
            $oldRoleId = $owner->vai_tro_id;
            $owner->update(['vai_tro_id' => $ownerRole->id]);
            $owner->clearPermissionCache();
            if ($oldRoleId !== $ownerRole->id) {
                try {
                    $owner->notify(new \App\Notifications\UserRoleStatusUpdatedNotification(
                        'role_updated',
                        "Bạn đã được nâng cấp vai trò thành 'Chủ Quán' sau khi cơ sở '{$quan->ten_quan}' được duyệt thành công!"
                    ));
                } catch (\Throwable $notifEx) {
                    \Illuminate\Support\Facades\Log::warning('Owner role update notification error: ' . $notifEx->getMessage());
                }
            }
        }

        if ($owner) {
            try {
                $owner->notify(new \App\Notifications\QuanStatusUpdatedNotification($quan, 'approved'));
            } catch (\Throwable $notifEx) {
                \Illuminate\Support\Facades\Log::warning('Quan approval notification error: ' . $notifEx->getMessage());
            }
        }

        if ($this->adminAudit && $request->user()) {
            $this->adminAudit->record($request->user(), 'venue.approved', $quan, $request);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã duyệt quán '{$quan->ten_quan}' thành công!",
                'stats' => $this->getStats(),
                'item' => [
                    'id' => $quan->id,
                    'ten_quan' => $quan->ten_quan,
                    'trang_thai' => 'da_duyet',
                    'anh_bia_url' => $quan->anh_bia_url,
                ],
            ]);
        }

        return redirect()->back()->with('success', "Đã duyệt quán '{$quan->ten_quan}' thành công!");
    }

    public function rejectQuan(Request $request, $id)
    {
        $this->markItemAsRead($request, 'quan_' . $id);

        $quan = Quan::findOrFail($id);
        $quan->update([
            'trang_thai' => 'bi_khoa',
            'is_xac_thuc' => false,
        ]);

        $owner = $quan->chuQuan;
        if ($owner) {
            try {
                $owner->notify(new \App\Notifications\QuanStatusUpdatedNotification($quan, 'rejected', $request->input('note')));
            } catch (\Throwable $notifEx) {
                \Illuminate\Support\Facades\Log::warning('Quan rejection notification error: ' . $notifEx->getMessage());
            }
        }

        if ($this->adminAudit && $request->user()) {
            $this->adminAudit->record($request->user(), 'venue.rejected', $quan, $request);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã từ chối duyệt quán '{$quan->ten_quan}' thành công!",
                'stats' => $this->getStats(),
                'item' => [
                    'id' => $quan->id,
                    'ten_quan' => $quan->ten_quan,
                    'trang_thai' => 'bi_khoa',
                    'anh_bia_url' => $quan->anh_bia_url,
                ],
            ]);
        }

        return redirect()->back()->with('success', "Đã từ chối duyệt quán '{$quan->ten_quan}' thành công!");
    }

    public function destroyQuan(Request $request, $id)
    {
        $this->markItemAsRead($request, 'quan_' . $id);

        $quan = Quan::findOrFail($id);
        $quan->update(['trang_thai' => 'bi_khoa']);
        $quan->delete(); // Soft delete qua ngay_xoa

        if ($this->adminAudit && $request->user()) {
            $this->adminAudit->record($request->user(), 'venue.deleted', $quan, $request);
        }

        $owner = $quan->chuQuan;
        if ($owner) {
            try {
                $owner->notify(new \App\Notifications\QuanStatusUpdatedNotification($quan, 'deleted'));
            } catch (\Throwable $notifEx) {
                \Illuminate\Support\Facades\Log::warning('Quan deletion notification error: ' . $notifEx->getMessage());
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xóa hồ sơ quán '{$quan->ten_quan}' thành công!",
                'stats' => $this->getStats(),
                'item' => [
                    'id' => $quan->id,
                    'ten_quan' => $quan->ten_quan,
                ],
            ]);
        }

        return redirect()->back()->with('success', "Đã xóa hồ sơ quán '{$quan->ten_quan}' thành công!");
    }

    public function verifyUser(Request $request, $id)
    {
        if ($request->input('action') === 'reject' || $request->input('action') === 'khong_duyet') {
            return $this->rejectUser($request, $id);
        }

        $this->markItemAsRead($request, 'user_' . $id);

        $user = User::findOrFail($id);
        $settings = $user->cai_dat ?? [];
        if (isset($settings['yeu_cau_xac_thuc'])) {
            $settings['yeu_cau_xac_thuc']['trang_thai'] = 'da_duyet';
            $settings['yeu_cau_xac_thuc']['ngay_duyet'] = now()->toIso8601String();
        }
        $user->update([
            'da_xac_thuc' => true,
            'ngay_xac_thuc' => now(),
            'cai_dat' => $settings,
        ]);
        $user->clearPermissionCache();

        if ($this->adminAudit && $request->user()) {
            $this->adminAudit->record($request->user(), 'user.verified', $user, $request);
        }

        try {
            $user->notify(new \App\Notifications\UserRoleStatusUpdatedNotification(
                'verified',
                'Tài khoản của bạn đã được quản trị viên duyệt xác thực danh tính thành công.'
            ));
        } catch (\Throwable $notifEx) {
            \Illuminate\Support\Facades\Log::warning('User verification notification error: ' . $notifEx->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xác thực tài khoản người dùng '{$user->ho_ten}' thành công!",
                'stats' => $this->getStats(),
                'item' => [
                    'id' => $user->id,
                    'ho_ten' => $user->ho_ten,
                    'da_xac_thuc' => true,
                ],
            ]);
        }

        return redirect()->back()->with('success', "Đã xác thực tài khoản người dùng '{$user->ho_ten}' thành công!");
    }

    public function rejectUser(Request $request, $id)
    {
        $this->markItemAsRead($request, 'user_' . $id);

        $user = User::findOrFail($id);
        $settings = $user->cai_dat ?? [];
        if (isset($settings['yeu_cau_xac_thuc'])) {
            $settings['yeu_cau_xac_thuc']['trang_thai'] = 'tu_choi';
            $settings['yeu_cau_xac_thuc']['ngay_tu_choi'] = now()->toIso8601String();
            if ($request->filled('note')) {
                $settings['yeu_cau_xac_thuc']['ly_do_tu_choi'] = $request->input('note');
            }
        }
        $user->update([
            'da_xac_thuc' => false,
            'ngay_xac_thuc' => null,
            'cai_dat' => $settings,
        ]);
        $user->clearPermissionCache();

        if ($this->adminAudit && $request->user()) {
            $this->adminAudit->record($request->user(), 'user.rejected', $user, $request);
        }

        try {
            $user->notify(new \App\Notifications\UserRoleStatusUpdatedNotification(
                'verification_rejected',
                'Yêu cầu xác thực tài khoản của bạn đã bị từ chối hoặc thu hồi.',
                $request->input('note')
            ));
        } catch (\Throwable $notifEx) {
            \Illuminate\Support\Facades\Log::warning('User rejection notification error: ' . $notifEx->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã từ chối xác thực tài khoản người dùng '{$user->ho_ten}'!",
                'stats' => $this->getStats(),
                'item' => [
                    'id' => $user->id,
                    'ho_ten' => $user->ho_ten,
                    'da_xac_thuc' => false,
                ],
            ]);
        }

        return redirect()->back()->with('success', "Đã từ chối xác thực tài khoản người dùng '{$user->ho_ten}'!");
    }
}
