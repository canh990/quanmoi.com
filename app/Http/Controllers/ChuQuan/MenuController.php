<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use App\Models\DanhMucMenu;
use App\Models\MonTrongMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Hien thi form xay dung thuc don
     */
    public function edit(Request $request, $slug)
    {
        $quan = $request->user()->quan()->where('slug', $slug)->firstOrFail();
        
        // Eager load danh muc va mon an
        $quan->load(['danhMucMenu' => function($q) {
            $q->orderBy('thu_tu', 'asc');
        }, 'danhMucMenu.monTrongMenu']);

        return view('chu-quan.thuc-don', compact('quan'));
    }

    /**
     * Cap nhat thuc don
     */
    public function update(Request $request, $slug)
    {
        $quan = $request->user()->quan()->where('slug', $slug)->firstOrFail();

        $request->validate([
            'menu_data' => 'required|string',
        ]);

        $menuCategories = json_decode($request->menu_data, true);
        
        if (!is_array($menuCategories)) {
            return back()->with('error', 'Dữ liệu thực đơn không hợp lệ.');
        }

        try {
            DB::transaction(function () use ($quan, $menuCategories) {
                // To keep it simple, we delete old menu items and recreate them. 
                // Or we can sync. For a menu builder, deleting and recreating is often safest 
                // unless we need to preserve IDs for foreign keys (like order items).
                // Let's preserve IDs where possible.
                
                $existingCategoryIds = $quan->danhMucMenu()->pluck('id')->toArray();
                $keptCategoryIds = [];
                $keptItemIds = [];

                foreach ($menuCategories as $catIndex => $catData) {
                    if (empty(trim($catData['name']))) continue;

                    // Update or create category
                    $danhMucId = $catData['db_id'] ?? null;
                    
                    if ($danhMucId && in_array($danhMucId, $existingCategoryIds)) {
                        $danhMuc = DanhMucMenu::find($danhMucId);
                        $danhMuc->update([
                            'ten_danh_muc' => trim($catData['name']),
                            'thu_tu' => $catIndex,
                        ]);
                    } else {
                        $danhMuc = DanhMucMenu::create([
                            'quan_id' => $quan->id,
                            'ten_danh_muc' => trim($catData['name']),
                            'thu_tu' => $catIndex,
                        ]);
                    }
                    
                    $keptCategoryIds[] = $danhMuc->id;

                    if (!empty($catData['items']) && is_array($catData['items'])) {
                        foreach ($catData['items'] as $itemData) {
                            if (empty(trim($itemData['name']))) continue;

                            $itemId = $itemData['db_id'] ?? null;
                            if ($itemId && MonTrongMenu::where('id', $itemId)->where('danh_muc_id', $danhMuc->id)->exists()) {
                                $mon = MonTrongMenu::find($itemId);
                                $mon->update([
                                    'ten_mon' => trim($itemData['name']),
                                    'gia' => !empty($itemData['price']) ? $itemData['price'] : 0,
                                    'mo_ta' => $itemData['description'] ?? null,
                                ]);
                            } else {
                                $mon = MonTrongMenu::create([
                                    'danh_muc_id' => $danhMuc->id,
                                    'ten_mon' => trim($itemData['name']),
                                    'gia' => !empty($itemData['price']) ? $itemData['price'] : 0,
                                    'mo_ta' => $itemData['description'] ?? null,
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
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}
