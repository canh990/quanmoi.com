<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Models\HinhAnhQuan;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class HinhAnhQuanController extends Controller
{
    /** Serve locally stored public venue images without relying on a cross-platform storage symlink. */
    public function localImage(string $path)
    {
        abort_unless(
            Str::startsWith($path, ['quan/gallery/', 'quan/anh-bia/']) && ! str_contains($path, '..'),
            404
        );

        $disk = Storage::disk('uploads')->exists($path) ? 'uploads' : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->response($path, basename($path), [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

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
            'hinh_anh.*.max' => 'Dung lượng ảnh tối đa 5MB.',
        ]);

        $quan = Quan::where('id', $quanId)
            ->where('chu_quan_id', $request->user()->id)
            ->where('trang_thai', 'da_duyet')
            ->firstOrFail();
        $this->authorize('update', $quan);
        $uploaded = [];

        if ($request->hasFile('hinh_anh')) {
            $manager = new ImageManager(new Driver);

            foreach ($request->file('hinh_anh') as $file) {
                // Tên file: quan/gallery/{quan_id}/{uuid}.webp
                $filename = Str::uuid().'.webp';

                // Convert sang WebP với quality 80
                $image = $manager->decode($file->getRealPath());
                $encoded = $image->encodeUsingFileExtension('webp', 80);

                $objectKey = 'quan/gallery/'.$quan->id.'/'.$filename;
                $storedKey = $objectKey;
                $r2Configured = config('filesystems.disks.r2.key')
                    && config('filesystems.disks.r2.secret')
                    && config('filesystems.disks.r2.bucket')
                    && config('filesystems.disks.r2.endpoint');

                if ($r2Configured) {
                    try {
                        Storage::disk('r2')->put($objectKey, (string) $encoded);
                        $imageUrl = Storage::disk('r2')->url($objectKey);
                    } catch (\Throwable $e) {
                        Log::warning('Venue gallery upload to R2 failed; using local storage.', [
                            'key' => $objectKey,
                            'error' => $e->getMessage(),
                        ]);
                        $imageUrl = $this->storeLocally($objectKey, (string) $encoded);
                        $storedKey = 'local:'.$objectKey;
                    }
                } else {
                    $imageUrl = $this->storeLocally($objectKey, (string) $encoded);
                    $storedKey = 'local:'.$objectKey;
                }

                $img = HinhAnhQuan::create([
                    'quan_id' => $quan->id,
                    'duong_dan' => $imageUrl,
                    'object_key' => $storedKey,
                    'tieu_de' => $file->getClientOriginalName(),
                ]);

                $uploaded[] = [
                    'id' => $img->id,
                    'duong_dan' => $img->duong_dan,
                    'tieu_de' => $img->tieu_de,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tải lên hình ảnh thành công!',
            'data' => $uploaded,
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
                $query->where('chu_quan_id', $request->user()->id)
                    ->where('trang_thai', 'da_duyet');
            })
            ->firstOrFail();
        $this->authorize('update', $img->quan);

        // Xóa file trên R2 (nếu có object_key)
        if ($img->object_key && Str::startsWith($img->object_key, 'local:quan/gallery/')) {
            $path = Str::after($img->object_key, 'local:');
            $disk = Storage::disk('uploads')->exists($path) ? 'uploads' : 'public';
            Storage::disk($disk)->delete($path);
        } elseif ($img->object_key && Str::startsWith($img->object_key, 'quan/gallery/')) {
            try {
                Storage::disk('r2')->delete($img->object_key);
            } catch (\Exception $e) {
                // Ghi log nhưng không chặn việc xóa bản ghi DB
                Log::warning('Không thể xóa ảnh quán trên R2: '.$e->getMessage(), [
                    'hinh_anh_id' => $img->id,
                    'object_key' => $img->object_key,
                ]);
            }
        } elseif ($img->object_key) {
            Log::warning('Refusing to delete a venue image with an invalid R2 key prefix.', [
                'hinh_anh_id' => $img->id,
            ]);
        }

        $img->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ảnh thành công.',
        ]);
    }

    private function storeLocally(string $path, string $contents): string
    {
        if (! Storage::disk('uploads')->put($path, $contents)) {
            throw new \RuntimeException('Unable to save venue gallery image to local storage.');
        }

        return '/media/venue/'.$path;
    }
}
