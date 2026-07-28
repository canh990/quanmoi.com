<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangKyQuanRequest;
use App\Models\Quan;
use App\Models\VaiTro;
use App\Models\DanhMucMenu;
use App\Models\MonTrongMenu;
use App\Models\HinhAnhQuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class QuanController extends Controller
{
    /**
     * Form dang quan
     */
    public function create()
    {
        return view('chu-quan.dang-quan');
    }

    /**
     * Xu ly luu quan moi vao database
     */
    public function store(DangKyQuanRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();

        if ($user && !$user->isAdmin() && !$user->hasRole('chu_quan')) {
            $vaiTroChuQuan = VaiTro::where('ten', 'chu_quan')->first();

            if ($vaiTroChuQuan) {
                $user->update(['vai_tro_id' => $vaiTroChuQuan->id]);
            }
        }

        $anhBiaPath = null;
        $anhBiaKey  = null;
        if ($request->hasFile('anh_bia')) {
            $file = $request->file('anh_bia');
            $filename = Str::uuid() . '.webp';
            
            $manager = new ImageManager(new Driver());
            $image   = $manager->decode($file->getRealPath());
            $encoded = $image->encodeUsingFileExtension('webp', 80);

            $objectKey = 'quan/anh-bia/' . $filename;
            Storage::disk('r2')->put($objectKey, (string) $encoded);

            $anhBiaPath = Storage::disk('r2')->url($objectKey);
            $anhBiaKey  = $objectKey;
        }

        $galleryKeys = [];
        $galleryPaths = [];
        if ($request->hasFile('danh_sach_anh')) {
            $manager = new ImageManager(new Driver());
            foreach ($request->file('danh_sach_anh') as $file) {
                $filename = Str::uuid() . '.webp';
                
                $image   = $manager->decode($file->getRealPath());
                $encoded = $image->encodeUsingFileExtension('webp', 80);

                $objectKey = 'quan/gallery/' . $filename;
                Storage::disk('r2')->put($objectKey, (string) $encoded);

                $galleryPaths[] = Storage::disk('r2')->url($objectKey);
                $galleryKeys[]  = $objectKey;
            }
        }

        $baseSlug = Str::slug($validated['ten_quan']);
        $slug = $baseSlug;
        $counter = 1;

        while (Quan::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $chuQuanId = $user?->id ?? (Auth::check() ? Auth::id() : DB::table('nguoi_dung')->first()?->id);

        try {
            $quan = DB::transaction(function () use ($validated, $request, $chuQuanId, $slug, $anhBiaPath, $anhBiaKey) {
                $q = Quan::create([
                    'chu_quan_id' => $chuQuanId,
                    'ten_quan' => $validated['ten_quan'],
                    'loai_hinh_kinh_doanh' => $validated['loai_hinh_kinh_doanh'] ?? 'Quan an',
                    'slug' => $slug,
                    'mo_ta' => $validated['mo_ta'] ?? null,
                    'so_dien_thoai' => $validated['so_dien_thoai'],
                    'email' => $validated['email'] ?? null,
                    'dia_chi_chi_tiet' => $validated['dia_chi_chi_tiet'],
                    'tinh_thanh_id' => $request->tinh_thanh_id ?? null,
                    'ten_tinh_thanh' => $validated['ten_tinh_thanh'],
                    'quan_huyen_id' => $request->quan_huyen_id ?? null,
                    'ten_quan_huyen' => $validated['ten_quan_huyen'],
                    'phuong_xa_id' => $request->phuong_xa_id ?? null,
                    'ten_phuong_xa' => $validated['ten_phuong_xa'] ?? null,
                    'kinh_do' => $request->kinh_do ?? 10.7769,
                    'vi_do' => $request->vi_do ?? 106.7009,
                    'gio_mo_cua' => $validated['gio_mo_cua'],
                    'gio_dong_cua' => $validated['gio_dong_cua'],
                    'gia_nho_nhat' => $validated['gia_nho_nhat'] ?? 0,
                    'gia_lon_nhat' => $validated['gia_lon_nhat'] ?? 0,
                    'anh_bia' => $anhBiaPath,
                    'anh_bia_key' => $anhBiaKey,
                    'trang_thai' => 'chua_duyet',
                ]);

                // Save Menu Data
                if ($request->filled('menu_data')) {
                    $menuCategories = json_decode($request->menu_data, true);
                    if (is_array($menuCategories)) {
                        foreach ($menuCategories as $catIndex => $catData) {
                            if (empty(trim($catData['name']))) continue;

                            $danhMuc = DanhMucMenu::create([
                                'quan_id' => $q->id,
                                'ten_danh_muc' => trim($catData['name']),
                                'thu_tu' => $catIndex,
                            ]);

                            if (!empty($catData['items']) && is_array($catData['items'])) {
                                foreach ($catData['items'] as $itemData) {
                                    if (empty(trim($itemData['name']))) continue;

                                    MonTrongMenu::create([
                                        'danh_muc_id' => $danhMuc->id,
                                        'ten_mon' => trim($itemData['name']),
                                        'gia' => !empty($itemData['price']) ? $itemData['price'] : 0,
                                        'mo_ta' => $itemData['description'] ?? null,
                                    ]);
                                }
                            }
                        }
                    }
                }

                // Save Gallery Images
                foreach ($galleryKeys as $index => $key) {
                    HinhAnhQuan::create([
                        'quan_id' => $q->id,
                        'duong_dan' => $galleryPaths[$index],
                        'object_key' => $key,
                        'tieu_de' => 'Hình ảnh quán',
                    ]);
                }

                return $q;
            });
        } catch (\Exception $e) {
            // Rollback uploaded image on R2 if transaction fails
            if ($anhBiaKey) {
                Storage::disk('r2')->delete($anhBiaKey);
            }
            foreach ($galleryKeys as $key) {
                Storage::disk('r2')->delete($key);
            }
            throw $e;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dang quan thanh cong!',
                'redirect_to' => route('chu-quan.quan.show', ['slug' => $quan->slug]),
                'data' => $quan,
            ]);
        }

        return redirect()
            ->route('chu-quan.quan.show', ['slug' => $quan->slug])
            ->with('success', 'Dang quan moi thanh cong!');
    }

    /**
     * Danh sach quan cua chu quan
     */
    public function ownerIndex(Request $request)
    {
        $quanList = $request->user()
            ->quan()
            ->latest()
            ->get();

        return view('chu-quan.index', compact('quanList'));
    }

    /**
     * Xem chi tiet quan danh cho chu quan
     */
    public function show($slug)
    {
        $quan = Quan::where('slug', $slug)
            ->with(['hinhAnh', 'danhMucMenu.monAn'])
            ->firstOrFail();

        return view('chu-quan.chi-tiet-quan', compact('quan'));
    }
}
