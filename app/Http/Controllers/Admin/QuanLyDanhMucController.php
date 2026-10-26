<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\DanhMucQuan;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuanLyDanhMucController extends Controller
{
    private array $presetIcons = [
        ['icon' => 'restaurant', 'label' => 'Nhà hàng'],
        ['icon' => 'local_cafe', 'label' => 'Cà phê'],
        ['icon' => 'sports_esports', 'label' => 'Billiards / Giải trí'],
        ['icon' => 'fastfood', 'label' => 'Ăn vặt / Đồ ăn nhanh'],
        ['icon' => 'ramen_dining', 'label' => 'Lẩu / Nướng / Mì'],
        ['icon' => 'local_bar', 'label' => 'Quán nhậu / Bar / Pub'],
        ['icon' => 'emoji_food_beverage', 'label' => 'Trà sữa / Đồ uống'],
        ['icon' => 'bakery_dining', 'label' => 'Tiệm bánh'],
        ['icon' => 'lunch_dining', 'label' => 'Cơm bình dân / Cơm trưa'],
        ['icon' => 'icecream', 'label' => 'Kem / Tráng miệng'],
        ['icon' => 'set_meal', 'label' => 'Hải sản / Combo'],
        ['icon' => 'local_pizza', 'label' => 'Pizza / Món Âu'],
        ['icon' => 'soup_kitchen', 'label' => 'Bún / Phở / Cháo'],
        ['icon' => 'nightlife', 'label' => 'Quán đêm 24/7'],
        ['icon' => 'spa', 'label' => 'Ăn chay & Healthy'],
        ['icon' => 'storefront', 'label' => 'Quán xá chung'],
    ];

    private array $presetColors = [
        'rose'    => ['name' => 'Hồng Ruby', 'badge' => 'bg-rose-50 text-rose-700 border-rose-200', 'bg' => 'bg-rose-500'],
        'amber'   => ['name' => 'Vàng Hổ Phách', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200', 'bg' => 'bg-amber-500'],
        'indigo'  => ['name' => 'Chàm Tinh Tế', 'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'bg' => 'bg-indigo-500'],
        'emerald' => ['name' => 'Xanh Lục Bảo', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bg' => 'bg-emerald-500'],
        'orange'  => ['name' => 'Cam Nhiệt Đới', 'badge' => 'bg-orange-50 text-orange-700 border-orange-200', 'bg' => 'bg-orange-500'],
        'violet'  => ['name' => 'Tím Huyền Bí', 'badge' => 'bg-violet-50 text-violet-700 border-violet-200', 'bg' => 'bg-violet-500'],
        'pink'    => ['name' => 'Hồng Pastel', 'badge' => 'bg-pink-50 text-pink-700 border-pink-200', 'bg' => 'bg-pink-500'],
        'yellow'  => ['name' => 'Vàng Nắng', 'badge' => 'bg-yellow-50 text-yellow-800 border-yellow-200', 'bg' => 'bg-yellow-400'],
        'teal'    => ['name' => 'Xanh Mòng Két', 'badge' => 'bg-teal-50 text-teal-700 border-teal-200', 'bg' => 'bg-teal-500'],
        'sky'     => ['name' => 'Xanh Lam Nhạt', 'badge' => 'bg-sky-50 text-sky-700 border-sky-200', 'bg' => 'bg-sky-500'],
    ];

    public function index(Request $request)
    {
        $q = trim(strip_tags((string) $request->input('q', '')));

        $query = DanhMucQuan::withCount('quans');

        if (!empty($q)) {
            $query->where(function ($b) use ($q) {
                $b->where('ten_danh_muc', 'like', "%{$q}%")
                  ->orWhere('slug', 'like', "%{$q}%")
                  ->orWhere('mo_ta', 'like', "%{$q}%");
            });
        }

        $categories = $query->orderBy('thu_tu')->orderBy('id')->get();

        $stats = [
            'total'          => DanhMucQuan::count(),
            'active_count'   => DanhMucQuan::where('is_hien_thi', true)->count(),
            'featured_count' => DanhMucQuan::where('is_noi_bat', true)->count(),
            'mapped_quans'   => Quan::whereNotNull('danh_muc_id')->count(),
            'total_quans'    => Quan::count(),
        ];

        return view('admin.danh-muc.index', [
            'categories'   => $categories,
            'stats'        => $stats,
            'presetIcons'  => $this->presetIcons,
            'presetColors' => $this->presetColors,
            'searchQuery'  => $q,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_danh_muc' => ['required', 'string', 'max:100'],
            'slug'         => ['nullable', 'string', 'max:100', 'unique:danh_muc_quan,slug'],
            'mo_ta'        => ['nullable', 'string', 'max:255'],
            'icon'         => ['nullable', 'string', 'max:50'],
            'mau_sac'      => ['nullable', 'string', 'max:30'],
            'thu_tu'       => ['nullable', 'integer', 'min:0'],
            'is_hien_thi'  => ['nullable', 'boolean'],
            'is_noi_bat'   => ['nullable', 'boolean'],
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục.',
            'slug.unique'          => 'Đường dẫn (slug) này đã được sử dụng.',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['ten_danh_muc']);
        if (DanhMucQuan::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::random(4);
        }

        $category = DanhMucQuan::create([
            'ten_danh_muc' => trim($validated['ten_danh_muc']),
            'slug'         => $slug,
            'mo_ta'        => trim($validated['mo_ta'] ?? ''),
            'icon'         => $validated['icon'] ?? 'restaurant',
            'mau_sac'      => $validated['mau_sac'] ?? 'amber',
            'thu_tu'       => (int) ($validated['thu_tu'] ?? 0),
            'is_hien_thi'  => $request->boolean('is_hien_thi', true),
            'is_noi_bat'   => $request->boolean('is_noi_bat', false),
        ]);

        // Ghi Audit log
        try {
            AdminAuditLog::create([
                'actor_id'    => $request->user()->id,
                'action'      => 'category.created',
                'target_type' => DanhMucQuan::class,
                'target_id'   => (string) $category->id,
                'metadata'    => ['name' => $category->ten_danh_muc, 'slug' => $category->slug],
                'ip_address'  => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm danh mục mới thành công!',
                'data'    => $category,
            ]);
        }

        return redirect()->route('admin.danh-muc-quan.index')->with('success', 'Thêm danh mục "' . $category->ten_danh_muc . '" thành công!');
    }

    public function update(Request $request, int $id)
    {
        $category = DanhMucQuan::findOrFail($id);

        $validated = $request->validate([
            'ten_danh_muc' => ['required', 'string', 'max:100'],
            'slug'         => ['nullable', 'string', 'max:100', 'unique:danh_muc_quan,slug,' . $id],
            'mo_ta'        => ['nullable', 'string', 'max:255'],
            'icon'         => ['nullable', 'string', 'max:50'],
            'mau_sac'      => ['nullable', 'string', 'max:30'],
            'thu_tu'       => ['nullable', 'integer', 'min:0'],
            'is_hien_thi'  => ['nullable', 'boolean'],
            'is_noi_bat'   => ['nullable', 'boolean'],
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục.',
            'slug.unique'          => 'Đường dẫn (slug) này đã được sử dụng.',
        ]);

        $oldName = $category->ten_danh_muc;
        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['ten_danh_muc']);

        $category->update([
            'ten_danh_muc' => trim($validated['ten_danh_muc']),
            'slug'         => $slug,
            'mo_ta'        => trim($validated['mo_ta'] ?? ''),
            'icon'         => $validated['icon'] ?? $category->icon,
            'mau_sac'      => $validated['mau_sac'] ?? $category->mau_sac,
            'thu_tu'       => (int) ($validated['thu_tu'] ?? $category->thu_tu),
            'is_hien_thi'  => $request->boolean('is_hien_thi'),
            'is_noi_bat'   => $request->boolean('is_noi_bat'),
        ]);

        // Nếu đổi tên danh mục, cập nhật đồng bộ loai_hinh_kinh_doanh của quán để bảo toàn tương thích ngược
        if ($oldName !== $category->ten_danh_muc) {
            Quan::where('danh_muc_id', $category->id)->update([
                'loai_hinh_kinh_doanh' => $category->ten_danh_muc,
            ]);
        }

        try {
            AdminAuditLog::create([
                'actor_id'    => $request->user()->id,
                'action'      => 'category.updated',
                'target_type' => DanhMucQuan::class,
                'target_id'   => (string) $category->id,
                'metadata'    => ['old_name' => $oldName, 'new_name' => $category->ten_danh_muc],
                'ip_address'  => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh mục thành công!',
                'data'    => $category,
            ]);
        }

        return redirect()->route('admin.danh-muc-quan.index')->with('success', 'Đã cập nhật danh mục "' . $category->ten_danh_muc . '"!');
    }

    public function destroy(Request $request, int $id)
    {
        $category = DanhMucQuan::findOrFail($id);
        $name = $category->ten_danh_muc;

        // An toàn: gỡ liên kết các quán trước khi xóa danh mục
        Quan::where('danh_muc_id', $category->id)->update(['danh_muc_id' => null]);
        $category->delete();

        try {
            AdminAuditLog::create([
                'actor_id'    => $request->user()->id,
                'action'      => 'category.deleted',
                'target_type' => DanhMucQuan::class,
                'target_id'   => (string) $id,
                'metadata'    => ['name' => $name],
                'ip_address'  => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa danh mục "' . $name . '" thành công!',
            ]);
        }

        return redirect()->route('admin.danh-muc-quan.index')->with('success', 'Đã xóa danh mục "' . $name . '"!');
    }

    public function toggleHienThi(Request $request, int $id)
    {
        $category = DanhMucQuan::findOrFail($id);
        $category->is_hien_thi = !$category->is_hien_thi;
        $category->save();

        return response()->json([
            'success' => true,
            'is_hien_thi' => $category->is_hien_thi,
            'message' => ($category->is_hien_thi ? 'Đã bật hiển thị danh mục ' : 'Đã ẩn danh mục ') . $category->ten_danh_muc,
        ]);
    }

    public function toggleNoiBat(Request $request, int $id)
    {
        $category = DanhMucQuan::findOrFail($id);
        $category->is_noi_bat = !$category->is_noi_bat;
        $category->save();

        return response()->json([
            'success' => true,
            'is_noi_bat' => $category->is_noi_bat,
            'message' => ($category->is_noi_bat ? 'Đã ghim nổi bật Trang chủ: ' : 'Đã bỏ ghim Trang chủ: ') . $category->ten_danh_muc,
        ]);
    }
}
