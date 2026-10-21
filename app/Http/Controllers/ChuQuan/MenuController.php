<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Models\DanhMucMenu;
use App\Models\MonTrongMenu;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MenuController extends Controller
{
    /**
     * Hien thi form xay dung thuc don
     */
    public function edit(Request $request, $slug)
    {
        $quan = Quan::where('slug', $slug)
            ->where('chu_quan_id', $request->user()->id)
            ->whereIn('trang_thai', ['chua_duyet', 'da_duyet'])
            ->firstOrFail();
        $this->authorize('update', $quan);

        // Eager load danh muc va mon an
        $quan->load(['danhMucMenu' => function ($q) {
            $q->orderBy('thu_tu', 'asc');
        }, 'danhMucMenu.monAn']);

        return view('chu-quan.thuc-don', compact('quan'));
    }

    /**
     * Cap nhat thuc don
     */
    public function update(Request $request, $slug)
    {
        $quan = Quan::where('slug', $slug)
            ->where('chu_quan_id', $request->user()->id)
            ->whereIn('trang_thai', ['chua_duyet', 'da_duyet'])
            ->firstOrFail();
        $this->authorize('update', $quan);

        // Decode JSON trước để validate cấu trúc mảng
        $menuCategories = json_decode($request->menu_data, true);

        if (! is_array($menuCategories)) {
            return back()->with('error', 'Dữ liệu thực đơn không hợp lệ.');
        }

        // Validate nghiêm ngặt bằng Laravel Validator
        $request->merge(['menu_categories_array' => $menuCategories]);

        $validatedData = $request->validate([
            'menu_categories_array' => 'array',
            'menu_categories_array.*.name' => 'required|string|max:255',
            'menu_categories_array.*.db_id' => 'nullable|string',
            'menu_categories_array.*.items' => 'nullable|array',
            'menu_categories_array.*.items.*.name' => 'required|string|max:255',
            'menu_categories_array.*.items.*.db_id' => 'nullable|string',
            'menu_categories_array.*.items.*.tmp_id' => 'nullable|integer',
            'menu_categories_array.*.items.*.price' => 'nullable|numeric|min:0|max:999999999',
            'menu_categories_array.*.items.*.description' => 'nullable|string|max:2000',
            'menu_categories_array.*.items.*.shopeefood_url' => 'nullable|url:http,https|max:2048',
        ], [
            'menu_categories_array.*.name.required' => 'Tên danh mục thực đơn không được để trống.',
            'menu_categories_array.*.items.*.name.required' => 'Tên món ăn không được để trống.',
            'menu_categories_array.*.items.*.price.numeric' => 'Giá món ăn phải là định dạng số.',
            'menu_categories_array.*.items.*.price.min' => 'Giá món ăn phải lớn hơn hoặc bằng 0.',
            'menu_categories_array.*.items.*.shopeefood_url.url' => 'Đường dẫn ShopeeFood của món ăn không hợp lệ.',
        ]);

        $safeMenuCategories = $validatedData['menu_categories_array'];

        try {
            DB::transaction(function () use ($quan, $safeMenuCategories, $request) {
                $existingCategoryIds = $quan->danhMucMenu()->pluck('id')->toArray();
                $keptCategoryIds = [];
                $keptItemIds = [];

                foreach ($safeMenuCategories as $catIndex => $catData) {
                    // Chống XSS bằng strip_tags
                    $catName = strip_tags(trim($catData['name']));
                    if (empty($catName)) {
                        continue;
                    }

                    // Update or create category
                    $danhMucId = $catData['db_id'] ?? null;

                    if ($danhMucId && in_array($danhMucId, $existingCategoryIds)) {
                        $danhMuc = DanhMucMenu::find($danhMucId);
                        $danhMuc->update([
                            'ten_danh_muc' => $catName,
                            'thu_tu' => $catIndex,
                        ]);
                    } else {
                        $danhMuc = DanhMucMenu::create([
                            'quan_id' => $quan->id,
                            'ten_danh_muc' => $catName,
                            'thu_tu' => $catIndex,
                        ]);
                    }

                    $keptCategoryIds[] = $danhMuc->id;

                    if (! empty($catData['items']) && is_array($catData['items'])) {
                        foreach ($catData['items'] as $itemData) {
                            $itemName = strip_tags(trim($itemData['name']));
                            if (empty($itemName)) {
                                continue;
                            }

                            // Ép kiểu float và chống XSS cho description
                            $price = isset($itemData['price']) ? (float) $itemData['price'] : 0;
                            $description = isset($itemData['description']) ? strip_tags(trim($itemData['description'])) : null;
                            $shopeefoodUrl = isset($itemData['shopeefood_url']) ? trim($itemData['shopeefood_url']) : null;

                            $itemId = $itemData['db_id'] ?? null;
                            $tmpId = $itemData['tmp_id'] ?? null;
                            $imagePath = null;

                            // Upload dish photos to Cloudflare R2
                            if ($tmpId && $request->hasFile("item_image_{$tmpId}")) {
                                $file = $request->file("item_image_{$tmpId}");
                                $manager = new ImageManager(new Driver);
                                $image = $manager->decode($file->getRealPath());
                                $image->scaleDown(width: 800);
                                $encoded = $image->encodeUsingFileExtension('webp', 80);
                                $fileName = 'quan/menu/'.bin2hex(random_bytes(16)).'.webp';
                                $imageContents = $encoded->toString();
                                
                                $uploadDisk = config('filesystems.upload_disk', 'r2');
                                $r2Configured = !empty(config('filesystems.disks.r2.key'))
                                    && !empty(config('filesystems.disks.r2.secret'))
                                    && !empty(config('filesystems.disks.r2.bucket'))
                                    && !empty(config('filesystems.disks.r2.endpoint'));

                                $targetDisk = ($uploadDisk === 'r2' && ! $r2Configured) ? 'uploads' : $uploadDisk;

                                try {
                                    $saved = Storage::disk($uploadDisk)->put($fileName, $imageContents);
                                    if (! $saved) {
                                        throw new \RuntimeException("Disk {$uploadDisk} returned false on put.");
                                    }
                                    $imagePath = Storage::disk($uploadDisk)->url($fileName);
                                } catch (\Throwable $e) {
                                    \Illuminate\Support\Facades\Log::warning('Menu image upload failed on primary disk, falling back to local.', [
                                        'disk' => $uploadDisk,
                                        'key' => $fileName,
                                        'error' => $e->getMessage(),
                                        'previous' => $e->getPrevious()?->getMessage(),
                                    ]);

                                    if ($uploadDisk !== 'uploads') {
                                        try {
                                            Storage::disk('uploads')->put($fileName, $imageContents);
                                            $imagePath = Storage::disk('uploads')->url($fileName);
                                        } catch (\Throwable $fallbackEx) {
                                            \Illuminate\Support\Facades\Log::error('Menu image local fallback also failed.', [
                                                'error' => $fallbackEx->getMessage(),
                                            ]);
                                            throw new \RuntimeException('Lỗi lưu ảnh món ăn. Vui lòng thử lại sau.');
                                        }
                                    } else {
                                        throw new \RuntimeException('Lỗi lưu ảnh món ăn lên Cloud Storage. Vui lòng thử lại sau.');
                                    }
                                }
                            }

                            if ($itemId && MonTrongMenu::where('id', $itemId)->where('danh_muc_id', $danhMuc->id)->exists()) {
                                $mon = MonTrongMenu::find($itemId);
                                $updateData = [
                                    'ten_mon' => $itemName,
                                    'gia' => $price,
                                    'mo_ta' => $description,
                                    'shopeefood_url' => $shopeefoodUrl ?: null,
                                ];
                                if ($imagePath) {
                                    $updateData['hinh_anh'] = $imagePath;
                                }
                                $mon->update($updateData);
                            } else {
                                $mon = MonTrongMenu::create([
                                    'danh_muc_id' => $danhMuc->id,
                                    'ten_mon' => $itemName,
                                    'gia' => $price,
                                    'mo_ta' => $description,
                                    'shopeefood_url' => $shopeefoodUrl ?: null,
                                    'hinh_anh' => $imagePath,
                                ]);
                            }
                            $keptItemIds[] = $mon->id;
                        }
                    }
                }

                // Delete items and categories not in the kept lists
                MonTrongMenu::whereIn('danh_muc_id', $existingCategoryIds)->whereNotIn('id', $keptItemIds)->delete();
                DanhMucMenu::where('quan_id', $quan->id)->whereNotIn('id', $keptCategoryIds)->delete();
            });

            try {
                $request->user()->notify(new \App\Notifications\QuanStatusUpdatedNotification($quan, 'menu_updated'));
            } catch (\Throwable $e) {
                Log::warning('Menu update notification error: ' . $e->getMessage());
            }

            try {
                \App\Models\AdminAuditLog::create([
                    'actor_id' => $request->user()->id,
                    'action' => 'venue.menu_updated',
                    'target_type' => Quan::class,
                    'target_id' => (string) $quan->id,
                    'metadata' => [
                        'ten_quan' => $quan->ten_quan,
                        'chu_quan' => $request->user()->ho_ten,
                    ],
                    'ip_address' => $request->ip(),
                ]);
            } catch (\Throwable $e) {}

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật thực đơn thành công!',
                    'redirect_to' => route('chu-quan.quan.show', ['slug' => $quan->slug]),
                ]);
            }

            return redirect()
                ->route('chu-quan.quan.show', ['slug' => $quan->slug])
                ->with('success', 'Cập nhật thực đơn thành công!');

        } catch (\Exception $e) {
            Log::error('Venue menu update failed.', [
                'quan_id' => $quan->id,
                'user_id' => $request->user()->id,
                'exception' => $e,
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể cập nhật thực đơn lúc này. Vui lòng thử lại sau.',
                ], 500);
            }

            return back()->with('error', 'Không thể cập nhật thực đơn lúc này. Vui lòng thử lại sau.');
        }
    }
}
