<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\DanhMucQuan;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class QuanLyDanhMucController extends Controller
{
    private const CATEGORY_ALIAS_MAP = [
        'nha-hang'           => ['Nhà hàng'],
        'ca-phe-tra'         => ['Cà phê & Trà', 'Cà phê'],
        'billiards-giai-tri' => ['Billiards & Giải trí', 'Billiards', 'Bida'],
        'do-an-vat'          => ['Đồ ăn vặt', 'Ăn vặt', 'Món ăn vặt'],
        'lau-nuong'          => ['Lẩu & Nướng', 'Lẩu', 'Nướng'],
        'quan-nhau'          => ['Quán nhậu & Bar', 'Quán nhậu', 'Quán Đêm 24/7', 'Bar', 'Pub'],
        'tra-sua-nuoc-ep'    => ['Trà sữa & Nước ép', 'Trà sữa', 'Nước ép'],
        'tiem-banh'          => ['Tiệm bánh', 'Bánh ngọt'],
        'quan-an-binh-dan'   => ['Quán ăn bình dân', 'Cơm bình dân', 'Bình dân'],
    ];
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
        'rose'    => ['name' => 'Hồng Ruby', 'badge' => 'bg-rose-50 text-rose-700 border-rose-200', 'bg' => 'bg-rose-500', 'hex' => '#f43f5e', 'light' => '#fff1f2', 'border' => '#fecdd3', 'text' => '#be123c'],
        'amber'   => ['name' => 'Vàng Hổ Phách', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200', 'bg' => 'bg-amber-500', 'hex' => '#f59e0b', 'light' => '#fffbeb', 'border' => '#fde68a', 'text' => '#b45309'],
        'indigo'  => ['name' => 'Chàm Tinh Tế', 'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'bg' => 'bg-indigo-500', 'hex' => '#6366f1', 'light' => '#eef2ff', 'border' => '#c7d2fe', 'text' => '#4338ca'],
        'emerald' => ['name' => 'Xanh Lục Bảo', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bg' => 'bg-emerald-500', 'hex' => '#10b981', 'light' => '#ecfdf5', 'border' => '#a7f3d0', 'text' => '#047857'],
        'orange'  => ['name' => 'Cam Nhiệt Đới', 'badge' => 'bg-orange-50 text-orange-700 border-orange-200', 'bg' => 'bg-orange-500', 'hex' => '#f97316', 'light' => '#fff7ed', 'border' => '#fed7aa', 'text' => '#c2410c'],
        'violet'  => ['name' => 'Tím Huyền Bí', 'badge' => 'bg-violet-50 text-violet-700 border-violet-200', 'bg' => 'bg-violet-500', 'hex' => '#8b5cf6', 'light' => '#f5f3ff', 'border' => '#ddd6fe', 'text' => '#6d28d9'],
        'pink'    => ['name' => 'Hồng Pastel', 'badge' => 'bg-pink-50 text-pink-700 border-pink-200', 'bg' => 'bg-pink-500', 'hex' => '#ec4899', 'light' => '#fdf2f8', 'border' => '#fbcfe8', 'text' => '#be185d'],
        'yellow'  => ['name' => 'Vàng Nắng', 'badge' => 'bg-yellow-50 text-yellow-800 border-yellow-200', 'bg' => 'bg-yellow-400', 'hex' => '#eab308', 'light' => '#fefce8', 'border' => '#fef08a', 'text' => '#854d0e'],
        'teal'    => ['name' => 'Xanh Mòng Két', 'badge' => 'bg-teal-50 text-teal-700 border-teal-200', 'bg' => 'bg-teal-500', 'hex' => '#14b8a6', 'light' => '#f0fdfa', 'border' => '#99f6e4', 'text' => '#0f766e'],
        'sky'     => ['name' => 'Xanh Lam Nhạt', 'badge' => 'bg-sky-50 text-sky-700 border-sky-200', 'bg' => 'bg-sky-500', 'hex' => '#0ea5e9', 'light' => '#f0f9ff', 'border' => '#bae6fd', 'text' => '#0369a1'],
    ];

    public function index(Request $request)
    {
        $this->syncUnmappedQuans();

        $q = DanhMucQuan::cleanName(strip_tags((string) $request->input('q', '')));
        if (mb_strlen($q, 'UTF-8') > 100) {
            $q = mb_substr($q, 0, 100, 'UTF-8');
        }

        $query = DanhMucQuan::withCount('quans');

        if (!empty($q)) {
            $esService = app(\App\Services\ElasticAdminSearchService::class);
            $esCatIds = $esService->searchDanhMuc($q);

            if ($esCatIds !== null && ! empty($esCatIds)) {
                $query->whereIn('id', $esCatIds);
                if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
                    $escapedIds = implode(',', array_map('intval', $esCatIds));
                    $query->orderByRaw("FIELD(id, {$escapedIds})");
                }
            } else {
                $tokens = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [$q];
                $query->where(function ($b) use ($q, $tokens) {
                    $b->where('ten_danh_muc', 'like', "%{$q}%")
                      ->orWhere('slug', 'like', "%{$q}%")
                      ->orWhere('mo_ta', 'like', "%{$q}%");

                    if (count($tokens) > 1) {
                        $b->orWhere(function ($tokSub) use ($tokens) {
                            foreach ($tokens as $token) {
                                $tokSub->where('ten_danh_muc', 'like', "%{$token}%");
                            }
                        });
                    }
                });
            }
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
            'categories'    => $categories,
            'allCategories' => DanhMucQuan::orderBy('id')->get(['id', 'ten_danh_muc', 'slug']),
            'stats'         => $stats,
            'presetIcons'   => $this->presetIcons,
            'presetColors'  => DanhMucQuan::$presetColors,
            'searchQuery'   => $q,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategoryInput($request);

        $category = DB::transaction(function () use ($validated, $request) {
            return DanhMucQuan::create([
                'ten_danh_muc' => $validated['ten_danh_muc'],
                'slug'         => $validated['slug'],
                'mo_ta'        => DanhMucQuan::cleanName((string) ($validated['mo_ta'] ?? '')),
                'icon'         => $validated['icon'] ?? 'restaurant',
                'mau_sac'      => $validated['mau_sac'] ?? 'amber',
                'thu_tu'       => (int) ($validated['thu_tu'] ?? 0),
                'is_hien_thi'  => $request->boolean('is_hien_thi', true),
                'is_noi_bat'   => $request->boolean('is_noi_bat', false),
            ]);
        });

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

        Cache::forget('bando_categories_v2');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm danh mục mới thành công!',
                'data'    => $category,
            ]);
        }

        return redirect()->route('admin.danh-muc-quan.index')->with('success', 'Thêm danh mục "' . $category->ten_danh_muc . '" thành công!');
    }

    public function update(Request $request, $id)
    {
        if (! is_numeric($id) || (int) $id <= 0 || ! ($category = DanhMucQuan::find((int) $id))) {
            $notFoundMsg = 'Cập nhật không hợp lệ: Danh mục này không tồn tại hoặc đã bị xóa ở tab khác. Vui lòng tải lại trang.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $notFoundMsg], 404);
            }

            return redirect()->route('admin.danh-muc-quan.index')->with('error', $notFoundMsg);
        }

        // Khóa lạc quan (Optimistic Locking): Chặn cập nhật trùng lặp khi nhân đôi tab
        $lastUpdatedAt = $request->input('last_updated_at');
        if (! empty($lastUpdatedAt) && $category->updated_at) {
            try {
                $clientTime = \Carbon\Carbon::parse((string) $lastUpdatedAt);
                if ($category->updated_at->timestamp > $clientTime->timestamp) {
                    $conflictMsg = 'Dữ liệu danh mục đã được cập nhật ở tab khác. Vui lòng tải lại trang trước khi cập nhật!';
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $conflictMsg], 409);
                    }

                    return redirect()->route('admin.danh-muc-quan.index')->with('error', $conflictMsg);
                }
            } catch (\Throwable $e) {
            }
        }

        $validated = $this->validateCategoryInput($request, $category);

        $oldName = $category->ten_danh_muc;

        DB::transaction(function () use ($category, $validated, $request, $oldName) {
            $nextUpdatedAt = ($category->updated_at && $category->updated_at->timestamp >= now()->timestamp)
                ? $category->updated_at->copy()->addSecond()
                : now();

            $category->fill([
                'ten_danh_muc' => $validated['ten_danh_muc'],
                'slug'         => $validated['slug'],
                'mo_ta'        => DanhMucQuan::cleanName((string) ($validated['mo_ta'] ?? '')),
                'icon'         => $validated['icon'] ?? $category->icon,
                'mau_sac'      => $validated['mau_sac'] ?? $category->mau_sac,
                'thu_tu'       => (int) ($validated['thu_tu'] ?? $category->thu_tu),
                'is_hien_thi'  => $request->boolean('is_hien_thi'),
                'is_noi_bat'   => $request->boolean('is_noi_bat'),
            ]);
            $category->timestamps = false;
            $category->updated_at = $nextUpdatedAt;
            $category->save();
            $category->timestamps = true;

            // Nếu đổi tên danh mục, cập nhật đồng bộ loai_hinh_kinh_doanh của quán để bảo toàn tương thích ngược
            if ($oldName !== $category->ten_danh_muc) {
                Quan::where('danh_muc_id', $category->id)->update([
                    'loai_hinh_kinh_doanh' => $category->ten_danh_muc,
                ]);
            }
        });

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

        Cache::forget('bando_categories_v2');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh mục thành công!',
                'data'    => $category,
            ]);
        }

        return redirect()->route('admin.danh-muc-quan.index')->with('success', 'Đã cập nhật danh mục "' . $category->ten_danh_muc . '"!');
    }

    /**
     * Kiểm tra dữ liệu tạo/sửa danh mục dùng chung: chặn trùng tên (hoa/thường, khoảng trắng) và chuẩn hóa slug.
     */
    private function validateCategoryInput(Request $request, ?DanhMucQuan $existing = null): array
    {
        $ignoreId = $existing?->id;
        $cleanName = DanhMucQuan::cleanName((string) $request->input('ten_danh_muc', ''));
        $rawSlugInput = DanhMucQuan::cleanName((string) $request->input('slug', ''));
        $hasCustomSlug = $rawSlugInput !== '';
        $normalizedCustomSlug = $hasCustomSlug ? Str::slug($rawSlugInput) : null;

        $rawThuTu = $request->input('thu_tu');
        if (is_string($rawThuTu)) {
            $rawThuTu = DanhMucQuan::cleanName($rawThuTu);
        }

        $defaultThuTu = $existing?->thu_tu ?? 0;
        $input = $request->all();
        $input['ten_danh_muc'] = $cleanName;
        $input['slug'] = $normalizedCustomSlug;
        $input['mo_ta'] = $request->has('mo_ta') ? DanhMucQuan::cleanName((string) $request->input('mo_ta', '')) : null;
        $input['thu_tu'] = ($rawThuTu === '' || $rawThuTu === null) ? $defaultThuTu : $rawThuTu;

        $slugRules = ['nullable', 'string', 'max:100'];
        if ($hasCustomSlug) {
            $slugRules = [
                'required',
                'string',
                'max:100',
                Rule::unique('danh_muc_quan', 'slug')->ignore($ignoreId),
            ];
        }

        $allowedIcons = array_column($this->presetIcons, 'icon');
        $allowedColors = array_keys(DanhMucQuan::$presetColors);

        $validated = Validator::make($input, [
            'ten_danh_muc' => [
                'required',
                'string',
                'max:100',
                function (string $attribute, mixed $value, \Closure $fail) use ($ignoreId) {
                    $duplicate = DanhMucQuan::findDuplicateByName((string) $value, $ignoreId);
                    if ($duplicate) {
                        $fail('Tên danh mục "' . $duplicate->ten_danh_muc . '" đã tồn tại (không phân biệt hoa/thường và khoảng trắng).');
                    }
                },
            ],
            'slug'        => $slugRules,
            'mo_ta'       => ['nullable', 'string', 'max:255'],
            'icon'        => ['nullable', 'string', 'max:50', Rule::in($allowedIcons)],
            'mau_sac'     => ['nullable', 'string', 'max:30', Rule::in($allowedColors)],
            'thu_tu'      => ['nullable', 'integer', 'min:0', 'max:999999'],
            'is_hien_thi' => ['nullable', 'boolean'],
            'is_noi_bat'  => ['nullable', 'boolean'],
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục (không được chỉ nhập khoảng trắng).',
            'ten_danh_muc.max'      => 'Tên danh mục không được vượt quá 100 ký tự.',
            'slug.required'         => 'Đường dẫn (slug) không hợp lệ.',
            'slug.max'              => 'Đường dẫn (slug) không được vượt quá 100 ký tự.',
            'slug.unique'           => 'Đường dẫn (slug) này đã được sử dụng.',
            'mo_ta.max'             => 'Mô tả ngắn không được vượt quá 255 ký tự.',
            'icon.in'               => 'Biểu tượng (icon) đã chọn không hợp lệ.',
            'mau_sac.in'            => 'Màu sắc đại diện đã chọn không hợp lệ.',
            'thu_tu.integer'        => 'Thứ tự sắp xếp phải là chữ số chuẩn (0-9), không chấp nhận chữ số full-width hoặc ký tự chữ.',
            'thu_tu.min'            => 'Thứ tự sắp xếp không được nhỏ hơn 0.',
            'thu_tu.max'            => 'Thứ tự sắp xếp không được vượt quá 999.999.',
        ])->validate();

        $baseSlug = $hasCustomSlug
            ? $validated['slug']
            : (Str::slug($cleanName) ?: 'danh-muc');

        $slug = $baseSlug;
        if (! $hasCustomSlug) {
            $suffix = 2;
            while (
                DanhMucQuan::where('slug', $slug)
                    ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
                    ->exists()
            ) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }
        }

        $validated['ten_danh_muc'] = $cleanName;
        $validated['slug'] = $slug;

        return $validated;
    }

    public function destroy(Request $request, $id)
    {
        if (! is_numeric($id) || (int) $id <= 0 || ! ($category = DanhMucQuan::find((int) $id))) {
            $invalidDeleteMsg = 'Xóa không hợp lệ: Danh mục này không tồn tại hoặc đã bị xóa ở tab khác.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $invalidDeleteMsg,
                ], 400);
            }

            return redirect()->route('admin.danh-muc-quan.index')->with('error', $invalidDeleteMsg);
        }

        $name = $category->ten_danh_muc;

        // An toàn: gỡ liên kết và chuyển toàn bộ quán thuộc danh mục (kể cả đã xóa mềm hoặc chưa gán FK theo alias) về Chưa phân loại
        $affectedQuans = DB::transaction(function () use ($category, $name) {
            $otherCategoryNames = DanhMucQuan::where('id', '!=', $category->id)
                ->pluck('ten_danh_muc')
                ->all();

            $matchingNames = array_values(array_diff(
                array_unique(array_merge([$name], self::CATEGORY_ALIAS_MAP[$category->slug] ?? [])),
                $otherCategoryNames
            ));

            $affectedQuery = Quan::withTrashed()->where(function ($query) use ($category, $matchingNames) {
                $query->where('danh_muc_id', $category->id)
                    ->orWhere(function ($unmapped) use ($matchingNames) {
                        $unmapped->whereNull('danh_muc_id')
                            ->whereIn('loai_hinh_kinh_doanh', $matchingNames);
                    });
            });

            $quans = (clone $affectedQuery)->get();

            $affectedQuery->update([
                'danh_muc_id'          => null,
                'loai_hinh_kinh_doanh' => 'Chưa phân loại',
            ]);

            $category->delete();

            return $quans;
        });

        // Đồng bộ lại Elasticsearch cho các quán còn hoạt động/chưa xóa mềm vừa chuyển sang "Chưa phân loại"
        $nonTrashedQuans = $affectedQuans->filter(fn (Quan $quan) => ! $quan->trashed());
        if ($nonTrashedQuans->isNotEmpty()) {
            try {
                $nonTrashedQuans->each(function (Quan $quan) {
                    $quan->danh_muc_id = null;
                    $quan->loai_hinh_kinh_doanh = 'Chưa phân loại';
                });
                $nonTrashedQuans->searchable();
            } catch (\Throwable $e) {
            }
        }

        try {
            AdminAuditLog::create([
                'actor_id'    => $request->user()->id,
                'action'      => 'category.deleted',
                'target_type' => DanhMucQuan::class,
                'target_id'   => (string) $id,
                'metadata'    => [
                    'name'                 => $name,
                    'affected_quans_count' => $affectedQuans->count(),
                ],
                'ip_address'  => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        Cache::forget('bando_categories_v2');
        Cache::forget('bando_total_v1');

        $affectedCount = $affectedQuans->count();
        $message = $affectedCount > 0
            ? 'Đã xóa danh mục "' . $name . '" và chuyển ' . $affectedCount . ' quán về Chưa phân loại!'
            : 'Đã xóa danh mục "' . $name . '" thành công!';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.danh-muc-quan.index')->with('success', $message);
    }

    public function toggleHienThi(Request $request, $id)
    {
        if (! is_numeric($id) || (int) $id <= 0 || ! ($category = DanhMucQuan::find((int) $id))) {
            return response()->json([
                'success' => false,
                'message' => 'Thao tác không hợp lệ: Danh mục không tồn tại hoặc đã bị xóa ở tab khác. Vui lòng tải lại trang.',
            ], 404);
        }

        $category->is_hien_thi = !$category->is_hien_thi;
        $category->save();

        Cache::forget('bando_categories_v2');

        return response()->json([
            'success' => true,
            'is_hien_thi' => $category->is_hien_thi,
            'message' => ($category->is_hien_thi ? 'Đã bật hiển thị danh mục ' : 'Đã ẩn danh mục ') . $category->ten_danh_muc,
        ]);
    }

    public function toggleNoiBat(Request $request, $id)
    {
        if (! is_numeric($id) || (int) $id <= 0 || ! ($category = DanhMucQuan::find((int) $id))) {
            return response()->json([
                'success' => false,
                'message' => 'Thao tác không hợp lệ: Danh mục không tồn tại hoặc đã bị xóa ở tab khác. Vui lòng tải lại trang.',
            ], 404);
        }

        $category->is_noi_bat = !$category->is_noi_bat;
        $category->save();

        Cache::forget('bando_categories_v2');

        return response()->json([
            'success' => true,
            'is_noi_bat' => $category->is_noi_bat,
            'message' => ($category->is_noi_bat ? 'Đã ghim nổi bật Trang chủ: ' : 'Đã bỏ ghim Trang chủ: ') . $category->ten_danh_muc,
        ]);
    }

    /**
     * Tự động đồng bộ các quán chưa gán danh_muc_id nếu có
     */
    protected function syncUnmappedQuans(): void
    {
        if (!Quan::whereNull('danh_muc_id')->exists()) {
            return;
        }

        $categories = DanhMucQuan::all()->keyBy('slug');

        foreach (self::CATEGORY_ALIAS_MAP as $slug => $types) {
            if (isset($categories[$slug])) {
                Quan::whereNull('danh_muc_id')
                    ->whereIn('loai_hinh_kinh_doanh', $types)
                    ->update(['danh_muc_id' => $categories[$slug]->id]);
            }
        }

        foreach ($categories as $cat) {
            Quan::whereNull('danh_muc_id')
                ->where('loai_hinh_kinh_doanh', $cat->ten_danh_muc)
                ->update(['danh_muc_id' => $cat->id]);
        }
    }
}
