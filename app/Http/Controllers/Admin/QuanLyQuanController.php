<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapNhatQuanRequest;
use App\Models\Quan;
use App\Models\VaiTro;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuanLyQuanController extends Controller
{
    public function __construct(private readonly AdminAuditService $adminAudit) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Quan::class);
        $query = Quan::with('chuQuan')->withTrashed();

        // Search keyword
        $rawQ = $request->input('q');
        if (! empty($rawQ) && ! is_array($rawQ)) {
            $q = (string) $rawQ;
            $query->where(function ($sub) use ($q) {
                $sub->where('ten_quan', 'like', "%{$q}%")
                    ->orWhere('dia_chi_chi_tiet', 'like', "%{$q}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$q}%");
            });
        }

        // Filter by status (chua_duyet, da_duyet, bi_khoa, soft_deleted)
        $rawStatus = $request->input('status');
        if (! is_array($rawStatus)) {
            if ($rawStatus === 'soft_deleted') {
                $query->onlyTrashed();
            } elseif (! empty($rawStatus)) {
                $query->whereNull('ngay_xoa')->where('trang_thai', (string) $rawStatus);
            }
        }

        // Filter by is_noi_bat
        if ($request->input('is_noi_bat') == 1) {
            $query->where('is_noi_bat', true);
        }

        // Filter by is_xac_thuc (Tick Xanh)
        $rawXacThuc = $request->input('is_xac_thuc');
        if (! is_array($rawXacThuc) && $rawXacThuc !== null && $rawXacThuc !== '') {
            $query->where('is_xac_thuc', (bool) $rawXacThuc);
        }

        $quanList = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.quan.index', compact('quanList'));
    }

    public function edit(string $id)
    {
        $quan = Quan::withTrashed()->with(['chuQuan', 'hinhAnh', 'danhMucMenu.monTrongMenu'])->findOrFail($id);
        $this->authorize('update', $quan);

        return view('admin.quan.edit', compact('quan'));
    }

    public function update(CapNhatQuanRequest $request, string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        $this->authorize('update', $quan);
        $validated = $request->validated();

        DB::transaction(function () use ($quan, $validated) {
            $quan->update($validated);

            if ($quan->trang_thai !== 'da_duyet') {
                return;
            }

            $owner = $quan->chuQuan;
            $ownerRole = VaiTro::where('ten', 'chu_quan')->first();

            if ($owner && $ownerRole && ! $owner->hasRole('admin')) {
                $owner->update(['vai_tro_id' => $ownerRole->id]);
            }
        });

        $this->adminAudit->record($request->user(), 'venue.updated', $quan, $request, [
            'status' => $quan->trang_thai,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin quán thành công!',
                'redirect_to' => route('admin.quan.index'),
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Cập nhật quán thành công!');
    }

    public function destroy(Request $request, string $id)
    {
        $quan = Quan::findOrFail($id);
        $this->authorize('delete', $quan);
        $quan->update(['trang_thai' => 'bi_khoa']);
        $quan->delete(); // Soft delete via ngay_xoa
        $this->adminAudit->record($request->user(), 'venue.deleted', $quan, $request);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã ẩn (xóa tạm) địa điểm quán thành công!',
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Đã xóa tạm quán!');
    }

    public function restore(Request $request, string $id)
    {
        $quan = Quan::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $quan);
        $quan->restore();
        $quan->update(['trang_thai' => 'da_duyet']);
        $this->adminAudit->record($request->user(), 'venue.restored', $quan, $request);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã khôi phục địa điểm quán thành công!',
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Đã khôi phục quán!');
    }

    public function forceDestroy(Request $request, string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $quan);

        $this->adminAudit->record($request->user(), 'venue.force_deleted', $quan, $request);
        $quan->forceDelete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa vĩnh viễn địa điểm quán thành công!',
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Đã xóa vĩnh viễn quán!');
    }

    public function toggleNoiBat(Request $request, string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        $this->authorize('update', $quan);
        $quan->is_noi_bat = ! $quan->is_noi_bat;
        $quan->save();
        $this->adminAudit->record($request->user(), 'venue.featured_toggled', $quan, $request, [
            'featured' => $quan->is_noi_bat,
        ]);

        $statusStr = $quan->is_noi_bat ? 'Đã thêm quán vào danh sách nổi bật' : 'Đã gỡ quán khỏi danh sách nổi bật';

        return back()->with('success', $statusStr);
    }

    public function toggleXacThuc(Request $request, string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        $this->authorize('update', $quan);
        $quan->is_xac_thuc = ! $quan->is_xac_thuc;
        $quan->save();
        $this->adminAudit->record($request->user(), 'venue.verification_toggled', $quan, $request, [
            'verified' => $quan->is_xac_thuc,
        ]);

        $statusStr = $quan->is_xac_thuc ? 'Đã cấp Tick Xanh cho quán' : 'Đã gỡ Tick Xanh của quán';

        return back()->with('success', $statusStr);
    }
}
