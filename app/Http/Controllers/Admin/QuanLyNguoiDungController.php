<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapNhatNguoiDungRequest;
use App\Models\User;
use App\Models\VaiTro;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class QuanLyNguoiDungController extends Controller
{
    public function __construct(private readonly AdminAuditService $adminAudit) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        $query = User::with('vaiTro')->withTrashed();

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

        if ($request->input('status') === 'soft_deleted') {
            $query->onlyTrashed();
        } elseif ($request->filled('status')) {
            $query->whereNull('ngay_xoa')->where('trang_thai', $request->input('status'));
        }

        $rawXacThuc = $request->input('da_xac_thuc');
        if (! is_array($rawXacThuc) && $rawXacThuc !== null && $rawXacThuc !== '') {
            $query->where('da_xac_thuc', (bool) $rawXacThuc);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $roles = VaiTro::all();

        return view('admin.nguoi-dung.index', compact('users', 'roles'));
    }

    public function edit(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('update', $user);

        return view('admin.nguoi-dung.edit', ['user' => $user, 'roles' => VaiTro::all()]);
    }

    public function update(CapNhatNguoiDungRequest $request, string $id)
    {
        $validated = $request->validated();
        $actor = $request->user();

        DB::transaction(function () use ($id, $validated, $actor, $request) {
            $user = User::withTrashed()->whereKey($id)->lockForUpdate()->firstOrFail();
            $user->load('vaiTro');
            $this->authorize('update', $user);

            $updateData = [
                'ho_ten' => $validated['ho_ten'],
                'email' => $validated['email'],
                'so_dien_thoai' => $validated['so_dien_thoai'] ?? null,
                'vai_tro_id' => $validated['vai_tro_id'] ?? null,
                'trang_thai' => $validated['trang_thai'],
                'da_xac_thuc' => $validated['da_xac_thuc'] ?? false,
            ];

            $removesActiveAdminAccess = $user->isAdmin()
                && ($updateData['vai_tro_id'] !== $user->vai_tro_id || $updateData['trang_thai'] !== 'hoat_dong');
            $this->ensureAdministrativeAccessRemains($actor, $user, $removesActiveAdminAccess);

            if ($updateData['da_xac_thuc'] && ! $user->da_xac_thuc) {
                $updateData['ngay_xac_thuc'] = now();
            } elseif (! $updateData['da_xac_thuc']) {
                $updateData['ngay_xac_thuc'] = null;
            }

            if (! empty($validated['mat_khau'])) {
                $updateData['mat_khau'] = Hash::make($validated['mat_khau']);
            }

            $user->update($updateData);
            $this->adminAudit->record($actor, 'user.updated', $user, $request, [
                'role_changed' => $user->wasChanged('vai_tro_id'),
                'status_changed' => $user->wasChanged('trang_thai'),
                'verification_changed' => $user->wasChanged('da_xac_thuc'),
                'password_changed' => array_key_exists('mat_khau', $updateData),
            ]);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin người dùng thành công!',
                'redirect_to' => route('admin.nguoi-dung.index'),
            ]);
        }

        return redirect()->route('admin.nguoi-dung.index')->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy(Request $request, string $id)
    {
        $actor = $request->user();
        DB::transaction(function () use ($id, $actor, $request) {
            $user = User::whereKey($id)->lockForUpdate()->firstOrFail();
            $user->load('vaiTro');
            $this->authorize('delete', $user);
            $this->ensureAdministrativeAccessRemains($actor, $user, $user->isAdmin());

            $venueCount = 0;
            foreach ($user->quan as $quan) {
                $quan->update(['trang_thai' => 'bi_khoa']);
                $quan->delete();
                $venueCount++;
            }

            $user->delete();
            $this->adminAudit->record($actor, 'user.deleted', $user, $request, ['soft_deleted_venues' => $venueCount]);
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa tạm thời người dùng và khóa các quán liên quan.']);
        }

        return redirect()->route('admin.nguoi-dung.index')->with('success', 'Đã xóa tạm người dùng thành công!');
    }

    public function restore(Request $request, string $id)
    {
        $actor = $request->user();
        DB::transaction(function () use ($id, $actor, $request) {
            $user = User::onlyTrashed()->whereKey($id)->lockForUpdate()->firstOrFail();
            $this->authorize('restore', $user);
            $user->restore();

            $venueCount = 0;
            foreach ($user->quan()->onlyTrashed()->get() as $quan) {
                $quan->restore();
                $quan->update(['trang_thai' => 'da_duyet']);
                $venueCount++;
            }

            $this->adminAudit->record($actor, 'user.restored', $user, $request, ['restored_venues' => $venueCount]);
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Khôi phục tài khoản và các quán liên quan thành công.']);
        }

        return redirect()->route('admin.nguoi-dung.index')->with('success', 'Đã khôi phục người dùng!');
    }

    public function forceDestroy(Request $request, string $id)
    {
        $actor = $request->user();
        DB::transaction(function () use ($id, $actor, $request) {
            $user = User::withTrashed()->whereKey($id)->lockForUpdate()->firstOrFail();
            $user->load('vaiTro');
            $this->authorize('forceDelete', $user);
            $this->ensureAdministrativeAccessRemains($actor, $user, $user->isAdmin() && ! $user->trashed());

            $venueCount = 0;
            foreach ($user->quan()->withTrashed()->get() as $quan) {
                $quan->forceDelete();
                $venueCount++;
            }

            $this->adminAudit->record($actor, 'user.force_deleted', $user, $request, ['force_deleted_venues' => $venueCount]);
            $user->forceDelete();
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa vĩnh viễn người dùng và các quán liên quan.']);
        }

        return redirect()->route('admin.nguoi-dung.index')->with('success', 'Đã xóa vĩnh viễn người dùng!');
    }

    public function toggleXacThuc(Request $request, string $id)
    {
        $actor = $request->user();
        $verified = DB::transaction(function () use ($id, $actor, $request) {
            $user = User::withTrashed()->whereKey($id)->lockForUpdate()->firstOrFail();
            $this->authorize('update', $user);
            $user->da_xac_thuc = ! $user->da_xac_thuc;
            $user->ngay_xac_thuc = $user->da_xac_thuc ? now() : null;
            $user->save();

            $this->adminAudit->record($actor, 'user.verification_toggled', $user, $request, ['verified' => $user->da_xac_thuc]);

            return $user->da_xac_thuc;
        });

        return back()->with('success', $verified ? 'Đã cấp Tick Xanh cho người dùng.' : 'Đã gỡ Tick Xanh của người dùng.');
    }

    private function ensureAdministrativeAccessRemains(User $actor, User $target, bool $removesActiveAdminAccess): void
    {
        if (! $removesActiveAdminAccess) {
            return;
        }

        if ($actor->is($target)) {
            throw ValidationException::withMessages([
                'trang_thai' => 'Không thể tự hạ quyền, khóa hoặc xóa tài khoản quản trị đang đăng nhập.',
            ]);
        }

        $activeAdminCount = User::query()
            ->where('vai_tro_id', $target->vai_tro_id)
            ->where('trang_thai', 'hoat_dong')
            ->lockForUpdate()
            ->count();

        if ($activeAdminCount <= 1) {
            throw ValidationException::withMessages([
                'vai_tro_id' => 'Không thể thay đổi, khóa hoặc xóa Admin đang hoạt động cuối cùng.',
            ]);
        }
    }
}
