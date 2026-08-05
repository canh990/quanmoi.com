<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapNhatNguoiDungRequest;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class QuanLyNguoiDungController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('vaiTro')->withTrashed();

        // Search keyword (ho_ten, email, so_dien_thoai)
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('ho_ten', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$q}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('vai_tro_id', $request->input('role'));
        }

        // Filter by status (active, locked, soft_deleted)
        if ($request->input('status') === 'soft_deleted') {
            $query->onlyTrashed();
        } elseif ($request->filled('status')) {
            $query->whereNull('ngay_xoa')->where('trang_thai', $request->input('status'));
        }

        // Filter by tick xanh
        $rawXacThuc = $request->input('da_xac_thuc');
        if (!is_array($rawXacThuc) && $rawXacThuc !== null && $rawXacThuc !== '') {
            $query->where('da_xac_thuc', (bool)$rawXacThuc);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $roles = VaiTro::all();

        return view('admin.nguoi-dung.index', compact('users', 'roles'));
    }

    public function edit(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $roles = VaiTro::all();

        return view('admin.nguoi-dung.edit', compact('user', 'roles'));
    }

    public function update(CapNhatNguoiDungRequest $request, string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $validated = $request->validated();

        $updateData = [
            'ho_ten'       => $validated['ho_ten'],
            'email'        => $validated['email'],
            'so_dien_thoai' => $validated['so_dien_thoai'] ?? null,
            'vai_tro_id'   => $validated['vai_tro_id'] ?? null,
            'trang_thai'   => $validated['trang_thai'],
            'da_xac_thuc'  => $validated['da_xac_thuc'] ?? 0,
        ];

        // Cập nhật ngày xác thực nếu thay đổi trạng thái tick xanh
        if ($updateData['da_xac_thuc'] && !$user->da_xac_thuc) {
            $updateData['ngay_xac_thuc'] = now();
        } elseif (!$updateData['da_xac_thuc']) {
            $updateData['ngay_xac_thuc'] = null;
        }

        if (!empty($validated['mat_khau'])) {
            $updateData['mat_khau'] = Hash::make($validated['mat_khau']);
        }

        $user->update($updateData);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin người dùng thành công!',
                'redirect_to' => route('admin.nguoi-dung.index')
            ]);
        }

        return redirect()->route('admin.nguoi-dung.index')->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Soft-delete linked venues owned by this user
        foreach ($user->quan as $quan) {
            $quan->update(['trang_thai' => 'bi_khoa']);
            $quan->delete(); // Soft-delete venue via ngay_xoa column
        }

        // Soft delete user via ngay_xoa column
        $user->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa tạm thời người dùng và tạm khóa các quán liên quan.'
            ]);
        }

        return redirect()->route('admin.nguoi-dung.index')->with('success', 'Đã xóa tạm người dùng thành công!');
    }

    public function restore(string $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        // Restore soft-deleted venues owned by user
        foreach ($user->quan()->onlyTrashed()->get() as $quan) {
            $quan->restore();
            $quan->update(['trang_thai' => 'da_duyet']);
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Khôi phục tài khoản và các quán của người dùng thành công!'
            ]);
        }

        return redirect()->route('admin.nguoi-dung.index')->with('success', 'Đã khôi phục người dùng!');
    }

    public function forceDestroy(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        // Force delete linked venues owned by this user
        foreach ($user->quan()->withTrashed()->get() as $quan) {
            $quan->forceDelete();
        }

        // Force delete user
        $user->forceDelete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa vĩnh viễn người dùng và các quán liên quan.'
            ]);
        }

        return redirect()->route('admin.nguoi-dung.index')->with('success', 'Đã xóa vĩnh viễn người dùng!');
    }

    public function toggleXacThuc(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->da_xac_thuc = !$user->da_xac_thuc;
        $user->ngay_xac_thuc = $user->da_xac_thuc ? now() : null;
        $user->save();

        $statusStr = $user->da_xac_thuc ? 'Đã cấp Tick Xanh cho người dùng' : 'Đã gỡ Tick Xanh của người dùng';
        return back()->with('success', $statusStr);
    }
}
