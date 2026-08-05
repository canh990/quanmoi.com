<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Models\HinhAnhQuan;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class HinhAnhQuanController extends Controller
{
    /**
     * Upload bộ sưu tập hình ảnh quán lên Cloudflare R2.
     * Lưu CDN URL vào MySQL (duong_dan) và object key để xóa sau này (object_key).
     */
    public function store(Request $request, $quanId)
    {
        $request->validate([
            'hinh_anh.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'hinh_anh.*.image' => 'File tải lên phải là hình ảnh.',
            'hinh_anh.*.max'   => 'Dung lượng ảnh tối đa 5MB.',
        ]);

        $quan = Quan::where('id', $quanId)
            ->where('chu_quan_id', $request->user()->id)
            ->firstOrFail();
        $uploaded = [];

        if ($request->hasFile('hinh_anh')) {
            $manager = new ImageManager(new Driver());

            foreach ($request->file('hinh_anh') as $file) {
                // Tên file: quan/gallery/{quan_id}/{uuid}.webp
                $filename  = Str::uuid() . '.webp';
                
                // Convert sang WebP với quality 80
                $image   = $manager->decode($file->getRealPath());
                $encoded = $image->encodeUsingFileExtension('webp', 80);

                $objectKey = 'quan/gallery/' . $quan->id . '/' . $filename;
                Storage::disk('r2')->put($objectKey, (string) $encoded);

                $img = HinhAnhQuan::create([
                    'quan_id'    => $quan->id,
                    'duong_dan'  => Storage::disk('r2')->url($objectKey), // CDN URL
                    'object_key' => $objectKey,                           // R2 key để xóa
                    'tieu_de'    => $file->getClientOriginalName(),
                ]);

                $uploaded[] = [
                    'id'        => $img->id,
                    'duong_dan' => $img->duong_dan,
                    'tieu_de'   => $img->tieu_de,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tải lên hình ảnh thành công!',
            'data'    => $uploaded,
        ]);
    }

    /**
     * Xóa hình ảnh khỏi thư viện quán.
     * Xóa file trên R2 trước, sau đó xóa bản ghi trong database.
     */
    public function destroy(Request $request, $id)
    {
        $img = HinhAnhQuan::where('id', $id)
            ->whereHas('quan', function ($query) use ($request) {
                $query->where('chu_quan_id', $request->user()->id);
            })
            ->firstOrFail();

        // Xóa file trên R2 (nếu có object_key)
        if ($img->object_key) {
            try {
                Storage::disk('r2')->delete($img->object_key);
            } catch (\Exception $e) {
                // Ghi log nhưng không chặn việc xóa bản ghi DB
                Log::warning('Không thể xóa ảnh quán trên R2: ' . $e->getMessage(), [
                    'hinh_anh_id' => $img->id,
                    'object_key'  => $img->object_key,
                ]);
            }
        }

        $img->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ảnh thành công.',
        ]);
    }
}
