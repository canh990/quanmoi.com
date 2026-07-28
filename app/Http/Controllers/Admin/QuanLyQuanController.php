<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapNhatQuanRequest;
use App\Models\Quan;
use Illuminate\Http\Request;

class QuanLyQuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Quan::with('chuQuan')->withTrashed();

        // Search keyword
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('ten_quan', 'like', "%{$q}%")
                    ->orWhere('dia_chi_chi_tiet', 'like', "%{$q}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$q}%");
            });
        }

        // Filter by status (chua_duyet, da_duyet, bi_khoa, soft_deleted)
        if ($request->input('status') === 'soft_deleted') {
            $query->onlyTrashed();
        } elseif ($request->filled('status')) {
            $query->whereNull('ngay_xoa')->where('trang_thai', $request->input('status'));
        }

        // Filter by is_noi_bat
        if ($request->input('is_noi_bat') == 1) {
            $query->where('is_noi_bat', true);
        }

        $quanList = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.quan.index', compact('quanList'));
    }

    public function edit(string $id)
    {
        $quan = Quan::withTrashed()->with(['chuQuan', 'hinhAnh', 'danhMucMenu.monTrongMenu'])->findOrFail($id);

        return view('admin.quan.edit', compact('quan'));
    }

    public function update(CapNhatQuanRequest $request, string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        $validated = $request->validated();

        $quan->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin quán thành công!',
                'redirect_to' => route('admin.quan.index')
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Cập nhật quán thành công!');
    }

    public function destroy(string $id)
    {
        $quan = Quan::findOrFail($id);
        $quan->update(['trang_thai' => 'bi_khoa']);
        $quan->delete(); // Soft delete via ngay_xoa

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã ẩn (xóa tạm) địa điểm quán thành công!'
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Đã xóa tạm quán!');
    }

    public function restore(string $id)
    {
        $quan = Quan::onlyTrashed()->findOrFail($id);
        $quan->restore();
        $quan->update(['trang_thai' => 'da_duyet']);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã khôi phục địa điểm quán thành công!'
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Đã khôi phục quán!');
    }

    public function forceDestroy(string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        
        // Force delete
        $quan->forceDelete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa vĩnh viễn địa điểm quán thành công!'
            ]);
        }

        return redirect()->route('admin.quan.index')->with('success', 'Đã xóa vĩnh viễn quán!');
    }

    public function toggleNoiBat(string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        $quan->is_noi_bat = !$quan->is_noi_bat;
        $quan->save();

        $statusStr = $quan->is_noi_bat ? 'Đã thêm quán vào danh sách nổi bật' : 'Đã gỡ quán khỏi danh sách nổi bật';
        return back()->with('success', $statusStr);
    }
    public function toggleXacThuc(string $id)
    {
        $quan = Quan::withTrashed()->findOrFail($id);
        $quan->is_xac_thuc = !$quan->is_xac_thuc;
        $quan->save();

        $statusStr = $quan->is_xac_thuc ? 'Đã cấp Tick Xanh cho quán' : 'Đã gỡ Tick Xanh của quán';
        return back()->with('success', $statusStr);
    }
}
