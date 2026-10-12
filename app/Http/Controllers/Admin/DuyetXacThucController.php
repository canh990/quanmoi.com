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
        $pendingQuan = Quan::where('trang_thai', 'chua_duyet')->with('chuQuan')->latest()->paginate(10);
        $unverifiedUsers = User::where('da_xac_thuc', false)->latest()->paginate(10, ['*'], 'users_page');

        return view('admin.duyet-xac-thuc.index', compact('pendingQuan', 'unverifiedUsers'));
    }

    public function approveQuan(Request $request, $id)
    {
        if ($request->input('action') === 'reject' || $request->input('action') === 'khong_duyet') {
            return $this->rejectQuan($request, $id);
        }

        $quan = Quan::findOrFail($id);
        $quan->update([
            'trang_thai' => 'da_duyet',
            'is_xac_thuc' => true,
        ]);

        $owner = $quan->chuQuan;
        $ownerRole = VaiTro::where('ten', 'chu_quan')->first();
        if ($owner && $ownerRole && ! $owner->hasRole('admin')) {
            $owner->update(['vai_tro_id' => $ownerRole->id]);
        }

        if ($this->adminAudit && $request->user()) {
            $this->adminAudit->record($request->user(), 'venue.approved', $quan, $request);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã duyệt quán '{$quan->ten_quan}' thành công!",
            ]);
        }

        return redirect()->back()->with('success', "Đã duyệt quán '{$quan->ten_quan}' thành công!");
    }

    public function rejectQuan(Request $request, $id)
    {
        $quan = Quan::findOrFail($id);
        $quan->update([
            'trang_thai' => 'bi_khoa',
            'is_xac_thuc' => false,
        ]);

        if ($this->adminAudit && $request->user()) {
            $this->adminAudit->record($request->user(), 'venue.rejected', $quan, $request);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã từ chối duyệt quán '{$quan->ten_quan}' thành công!",
            ]);
        }

        return redirect()->back()->with('success', "Đã từ chối duyệt quán '{$quan->ten_quan}' thành công!");
    }

    public function verifyUser(Request $request, $id)
    {
        if ($request->input('action') === 'reject' || $request->input('action') === 'khong_duyet') {
            return $this->rejectUser($request, $id);
        }

        $user = User::findOrFail($id);
        $user->update([
            'da_xac_thuc' => true,
            'ngay_xac_thuc' => now(),
        ]);

        if ($this->adminAudit && $request->user()) {
            $this->adminAudit->record($request->user(), 'user.verified', $user, $request);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xác thực tài khoản người dùng '{$user->ho_ten}' thành công!",
            ]);
        }

        return redirect()->back()->with('success', "Đã xác thực tài khoản người dùng '{$user->ho_ten}' thành công!");
    }

    public function rejectUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'da_xac_thuc' => false,
            'ngay_xac_thuc' => null,
        ]);

        if ($this->adminAudit && $request->user()) {
            $this->adminAudit->record($request->user(), 'user.rejected', $user, $request);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã từ chối xác thực tài khoản người dùng '{$user->ho_ten}'!",
            ]);
        }

        return redirect()->back()->with('success', "Đã từ chối xác thực tài khoản người dùng '{$user->ho_ten}'!");
    }
}
