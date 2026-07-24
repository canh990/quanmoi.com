<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Models\DanhMucMenu;
use App\Models\MonTrongMenu;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuQuanController extends Controller
{
    public function storeCategory(Request $request, string $slug)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
            'thu_tu' => 'nullable|integer|min:0',
        ]);

        $quan = Quan::where('slug', $slug)->firstOrFail();
        $this->authorize('update', $quan);

        DanhMucMenu::create([
            'quan_id' => $quan->id,
            'ten_danh_muc' => trim(strip_tags($request->input('ten_danh_muc'))),
            'thu_tu' => $request->integer('thu_tu', 0),
        ]);

        return redirect()
            ->route('chu-quan.quan.show', ['slug' => $quan->slug])
            ->with('success', 'Da them danh muc menu moi.');
    }

    public function destroyCategory(string $id)
    {
        $category = DanhMucMenu::with('quan')->findOrFail($id);
        $this->authorize('update', $category->quan);

        $slug = $category->quan->slug;
        $category->delete();

        return redirect()
            ->route('chu-quan.quan.show', ['slug' => $slug])
            ->with('success', 'Da xoa danh muc menu.');
    }

    public function storeItem(Request $request, string $id)
    {
        $request->validate([
            'ten_mon' => 'required|string|max:255',
            'mo_ta' => 'nullable|string|max:1000',
            'gia' => 'required|numeric|min:0',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'con_hang' => 'nullable|boolean',
        ]);

        $category = DanhMucMenu::with('quan')->findOrFail($id);
        $this->authorize('update', $category->quan);

        $hinhAnhPath = null;
        if ($request->hasFile('hinh_anh')) {
            $hinhAnhPath = $request->file('hinh_anh')->store('quan/menu', 'public');
        }

        MonTrongMenu::create([
            'danh_muc_id' => $category->id,
            'ten_mon' => trim(strip_tags($request->input('ten_mon'))),
            'mo_ta' => $request->filled('mo_ta') ? trim(strip_tags($request->input('mo_ta'))) : null,
            'gia' => $request->input('gia'),
            'hinh_anh' => $hinhAnhPath,
            'con_hang' => $request->boolean('con_hang', true),
        ]);

        return redirect()
            ->route('chu-quan.quan.show', ['slug' => $category->quan->slug])
            ->with('success', 'Da them mon moi vao menu.');
    }

    public function destroyItem(string $id)
    {
        $item = MonTrongMenu::with('danhMuc.quan')->findOrFail($id);
        $this->authorize('update', $item->danhMuc->quan);

        if ($item->hinh_anh) {
            Storage::disk('public')->delete($item->hinh_anh);
        }

        $slug = $item->danhMuc->quan->slug;
        $item->delete();

        return redirect()
            ->route('chu-quan.quan.show', ['slug' => $slug])
            ->with('success', 'Da xoa mon khoi menu.');
    }
}
