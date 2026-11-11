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

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $pendingQuery = Quan::where('trang_thai', 'chua_duyet')->with(['chuQuan', 'hinhAnh']);
        $rejectedQuery = Quan::where('trang_thai', 'bi_khoa')->with(['chuQuan', 'hinhAnh']);
        $approvedQuery = Quan::where('trang_thai', 'da_duyet')->with(['chuQuan', 'hinhAnh']);
        $unverifiedQuery = User::where('da_xac_thuc', false);

        if ($q !== '') {
            $esService = app(\App\Services\ElasticAdminSearchService::class);
            $tokens = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [$q];

            // 1. Tìm kiếm Quán ăn bằng Elasticsearch (1 query duy nhất cho toàn bộ trạng thái)
            $allQuanIds = $esService->searchQuan($q);

            if ($allQuanIds !== null && ! empty($allQuanIds)) {
                $escaped = implode("','", array_map('addslashes', $allQuanIds));
                $orderByField = (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') ? "FIELD(id, '{$escaped}')" : null;

                $pendingQuery->whereIn('id', $allQuanIds);
                $rejectedQuery->whereIn('id', $allQuanIds);
                $approvedQuery->whereIn('id', $allQuanIds);

                if ($orderByField) {
                    $pendingQuery->orderByRaw($orderByField);
                    $rejectedQuery->orderByRaw($orderByField);
                    $approvedQuery->orderByRaw($orderByField);
                }
            } else {
                // Fallback tìm kiếm trực tiếp trong cơ sở dữ liệu nếu ES offline hoặc không có ID
                $applyQuanDbSearch = function ($query) use ($q, $tokens) {
                    $query->where(function ($sub) use ($q, $tokens) {
                        $sub->where('ten_quan', 'like', "%{$q}%")
                            ->orWhere('dia_chi_chi_tiet', 'like', "%{$q}%")
                            ->orWhere('so_dien_thoai', 'like', "%{$q}%")
                            ->orWhereHas('chuQuan', function ($u) use ($q) {
                                $u->where('ho_ten', 'like', "%{$q}%")
                                    ->orWhere('email', 'like', "%{$q}%");
                            });

                        if (count($tokens) > 1) {
                            $sub->orWhere(function ($tokSub) use ($tokens) {
                                foreach ($tokens as $token) {
                                    $tokSub->where(function ($w) use ($token) {
                                        $w->where('ten_quan', 'like', "%{$token}%")
                                          ->orWhere('dia_chi_chi_tiet', 'like', "%{$token}%");
                                    });
                                }
                            });
                        }
                    });
                };

                $applyQuanDbSearch($pendingQuery);
                $applyQuanDbSearch($rejectedQuery);
                $applyQuanDbSearch($approvedQuery);
            }

            // 2. Tìm kiếm người dùng chưa xác thực bằng Elasticsearch
            $userIds = $esService->searchUser($q, ['da_xac_thuc' => false]);
            if ($userIds !== null && ! empty($userIds)) {
                $unverifiedQuery->whereIn('id', $userIds);
                if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
                    $escaped = implode("','", array_map('addslashes', $userIds));
                    $unverifiedQuery->orderByRaw("FIELD(id, '{$escaped}')");
                }
            } else {
                $unverifiedQuery->where(function ($sub) use ($q, $tokens) {
                    $sub->where('ho_ten', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('so_dien_thoai', 'like', "%{$q}%");

                    if (count($tokens) > 1) {
                        $sub->orWhere(function ($tokSub) use ($tokens) {
                            foreach ($tokens as $token) {
                                $tokSub->where(function ($w) use ($token) {
                                    $w->where('ho_ten', 'like', "%{$token}%")
                                      ->orWhere('email', 'like', "%{$token}%");
                                });
                            }
                        });
                    }
                });
            }
        }

        $pendingQuan = $pendingQuery->latest()
            ->paginate(10, ['*'], 'pending_page')
            ->withQueryString();

        $rejectedQuan = $rejectedQuery->latest()
            ->paginate(10, ['*'], 'rejected_page')
            ->withQueryString();

        $recentApproved = $approvedQuery->latest('updated_at')
            ->limit(20)
            ->get();

        $unverifiedUsers = $unverifiedQuery->latest()
            ->paginate(10, ['*'], 'users_page')
            ->withQueryString();

        if ($q !== '') {
            $stats = [
                'pending_quan' => $pendingQuan->total(),
                'rejected_quan' => $rejectedQuan->total(),
                'approved_quan' => $recentApproved->count(),
                'unverified_users' => $unverifiedUsers->total(),
            ];
        } else {
            $kpi = Quan::selectRaw('
                SUM(CASE WHEN trang_thai = "chua_duyet" THEN 1 ELSE 0 END) as pending_cnt,
                SUM(CASE WHEN trang_thai = "bi_khoa" THEN 1 ELSE 0 END) as rejected_cnt,
                SUM(CASE WHEN trang_thai = "da_duyet" THEN 1 ELSE 0 END) as approved_cnt
            ')->first();
            $stats = [
                'pending_quan' => (int) ($kpi->pending_cnt ?? 0),
                'rejected_quan' => (int) ($kpi->rejected_cnt ?? 0),
                'approved_quan' => (int) ($kpi->approved_cnt ?? 0),
                'unverified_users' => User::where('da_xac_thuc', false)->count(),
            ];
        }

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
            'stats',
            'q'
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
        $this->checkPermissionQuan($request);

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
        $this->checkPermissionQuan($request);

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
        $this->checkPermissionQuan($request);

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
        $this->checkPermissionUser($request);

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
        $this->checkPermissionUser($request);

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

    private function checkPermissionQuan(Request $request): void
    {
        $user = $request->user();
        if ($user) {
            if (method_exists($user, 'hasPermissionExplicitlyDenied') && $user->hasPermissionExplicitlyDenied('quan_ly_quan')) {
                abort(403, 'Bạn không có quyền quản lý duyệt quán.');
            }
            if (! $user->hasPermissionTo('quan_ly_quan')) {
                abort(403, 'Bạn không có quyền quản lý duyệt quán.');
            }
        }
    }

    private function checkPermissionUser(Request $request): void
    {
        $user = $request->user();
        if ($user) {
            if (method_exists($user, 'hasPermissionExplicitlyDenied') && $user->hasPermissionExplicitlyDenied('quan_ly_nguoi_dung')) {
                abort(403, 'Bạn không có quyền quản lý xác thực người dùng.');
            }
            if (! $user->hasPermissionTo('quan_ly_nguoi_dung')) {
                abort(403, 'Bạn không có quyền quản lý xác thực người dùng.');
            }
        }
    }
}
