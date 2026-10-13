<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangKyQuanRequest;
use App\Models\HinhAnhQuan;
use App\Models\Quan;
use App\Models\VideoShort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

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

        if (! $user) {
            abort(403, 'Bạn chưa đăng nhập hoặc phiên làm việc đã hết hạn.');
        }

        $anhBiaPath = null;
        $anhBiaKey = null;
        if ($request->hasFile('anh_bia')) {
            $file = $request->file('anh_bia');
            $filename = Str::uuid().'.webp';

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file->getRealPath());
            $encoded = $image->encodeUsingFileExtension('webp', 80);

            $objectKey = 'quan/anh-bia/'.$filename;
            Storage::disk('r2')->put($objectKey, (string) $encoded);

            $anhBiaPath = Storage::disk('r2')->url($objectKey);
            $anhBiaKey = $objectKey;
        }

        $galleryKeys = [];
        $galleryPaths = [];
        if ($request->hasFile('danh_sach_anh')) {
            $manager = new ImageManager(new Driver);
            foreach ($request->file('danh_sach_anh') as $file) {
                $filename = Str::uuid().'.webp';

                $image = $manager->decode($file->getRealPath());
                $encoded = $image->encodeUsingFileExtension('webp', 80);

                $objectKey = 'quan/gallery/'.$filename;
                Storage::disk('r2')->put($objectKey, (string) $encoded);

                $galleryPaths[] = Storage::disk('r2')->url($objectKey);
                $galleryKeys[] = $objectKey;
            }
        }

        $baseSlug = Str::slug($validated['ten_quan']);
        $slug = $baseSlug;
        $counter = 1;

        while (Quan::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        $chuQuanId = $user->id;

        try {
            $quan = DB::transaction(function () use ($validated, $chuQuanId, $slug, $anhBiaPath, $anhBiaKey, $galleryKeys, $galleryPaths) {
                $q = Quan::create([
                    'chu_quan_id' => $chuQuanId,
                    'ten_quan' => $validated['ten_quan'],
                    'loai_hinh_kinh_doanh' => $validated['loai_hinh_kinh_doanh'] ?? 'Quan an',
                    'slug' => $slug,
                    'mo_ta' => $validated['mo_ta'] ?? null,
                    'so_dien_thoai' => $validated['so_dien_thoai'],
                    'email' => $validated['email'] ?? null,
                    'dia_chi_chi_tiet' => $validated['dia_chi_chi_tiet'],
                    'tinh_thanh_id' => $validated['tinh_thanh_id'] ?? null,
                    'ten_tinh_thanh' => $validated['ten_tinh_thanh'],
                    'quan_huyen_id' => $validated['quan_huyen_id'] ?? null,
                    'ten_quan_huyen' => $validated['ten_quan_huyen'],
                    'phuong_xa_id' => $validated['phuong_xa_id'] ?? null,
                    'ten_phuong_xa' => $validated['ten_phuong_xa'] ?? null,
                    'kinh_do' => $validated['kinh_do'] ?? 10.7769,
                    'vi_do' => $validated['vi_do'] ?? 106.7009,
                    'gio_mo_cua' => $validated['gio_mo_cua'],
                    'gio_dong_cua' => $validated['gio_dong_cua'],
                    'gia_nho_nhat' => $validated['gia_nho_nhat'] ?? 0,
                    'gia_lon_nhat' => $validated['gia_lon_nhat'] ?? 0,
                    'anh_bia' => $anhBiaPath,
                    'anh_bia_key' => $anhBiaKey,
                    'tiktok_url' => $validated['tiktok_url'] ?? null,
                    'trang_thai' => 'chua_duyet',
                ]);

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
                'message' => 'Hồ sơ quán đã được gửi và đang chờ duyệt.',
                'redirect_to' => route('home'),
                'data' => $quan,
            ]);
        }

        return redirect()
            ->route('home')
            ->with('success', 'Hồ sơ quán đã được gửi và đang chờ duyệt.');
    }

    /**
     * Danh sach quan cua chu quan
     */
    public function ownerIndex(Request $request)
    {
        $quanList = $request->user()
            ->quan()
            ->where('trang_thai', 'da_duyet')
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
            ->where('chu_quan_id', Auth::id())
            ->where('trang_thai', 'da_duyet')
            ->with(['hinhAnh', 'danhMucMenu.monAn'])
            ->firstOrFail();
        $this->authorize('update', $quan);

        $luotLuu = $quan->savedByUsers()->count();
        $luotVideo = VideoShort::where('quan_id', $quan->id)->count();

        return view('chu-quan.chi-tiet-quan', compact('quan', 'luotLuu', 'luotVideo'));
    }

    /**
     * Cap nhat thong tin quan (AJAX)
     */
    public function update(Request $request, $slug)
    {
        $quan = Quan::where('slug', $slug)
            ->where('chu_quan_id', Auth::id())
            ->where('trang_thai', 'da_duyet')
            ->firstOrFail();
        $this->authorize('update', $quan);

        $validated = $request->validate([
            'so_dien_thoai' => 'required|string|max:20',
            'mo_ta' => 'nullable|string|max:2000',
            'gio_mo_cua' => 'required|string',
            'gio_dong_cua' => 'required|string',
            'gia_nho_nhat' => 'nullable|numeric|min:0',
            'gia_lon_nhat' => 'nullable|numeric|min:0',
            'tiktok_url' => 'nullable|url|max:255',
            'anh_bia' => 'nullable|image|max:5120',
        ]);

        // Upload ảnh bìa mới nếu có
        if ($request->hasFile('anh_bia')) {
            // Xóa ảnh cũ
            if ($quan->anh_bia_key && Str::startsWith($quan->anh_bia_key, 'quan/anh-bia/')) {
                Storage::disk('r2')->delete($quan->anh_bia_key);
            }

            $file = $request->file('anh_bia');
            $filename = Str::uuid().'.webp';
            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file->getRealPath());
            $encoded = $image->encodeUsingFileExtension('webp', 85);

            $objectKey = 'quan/anh-bia/'.$filename;
            Storage::disk('r2')->put($objectKey, (string) $encoded);

            $validated['anh_bia'] = Storage::disk('r2')->url($objectKey);
            $validated['anh_bia_key'] = $objectKey;
        }

        $quan->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin quán thành công!',
            ]);
        }

        return back()->with('success', 'Cập nhật thông tin quán thành công!');
    }
}
