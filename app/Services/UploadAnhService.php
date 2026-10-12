<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UploadAnhService
{
    /**
     * Upload avatar image to R2, converting it to WebP.
     * Deletes the old image if oldKey is provided.
     *
     * @param UploadedFile $file
     * @param string $userId
     * @param string|null $oldKey
     * @return array ['url' => string, 'key' => string]
     */
    public function uploadAvatar(UploadedFile $file, string $userId, ?string $oldKey = null): array
    {
        $uploadDisk = config('filesystems.upload_disk', 'r2');
        $r2Configured = !empty(config('filesystems.disks.r2.key'))
            && !empty(config('filesystems.disks.r2.secret'))
            && !empty(config('filesystems.disks.r2.bucket'))
            && !empty(config('filesystems.disks.r2.endpoint'));

        if ($oldKey) {
            try {
                if (Str::startsWith($oldKey, 'local:')) {
                    $localKey = Str::after($oldKey, 'local:');
                    $disk = Storage::disk('uploads')->exists($localKey) ? 'uploads' : 'public';
                    Storage::disk($disk)->delete($localKey);
                } elseif ($r2Configured) {
                    Storage::disk('r2')->delete($oldKey);
                }
            } catch (\Exception $e) {
                Log::warning('Cannot delete old avatar: ' . $e->getMessage(), [
                    'user_id' => $userId,
                    'key'     => $oldKey,
                ]);
            }
        }

        $filename = Str::uuid() . '.webp';
        $manager = new ImageManager(new Driver());
        $image   = $manager->decode($file->getRealPath());
        $encoded = $image->encodeUsingFileExtension('webp', 80);

        $objectKey = 'avatars/' . $userId . '/' . $filename;
        $targetDisk = ($uploadDisk === 'r2' && ! $r2Configured) ? 'uploads' : $uploadDisk;

        try {
            Storage::disk($targetDisk)->put($objectKey, (string) $encoded);
            $url = ($targetDisk === 'uploads') ? '/uploads/' . $objectKey : Storage::disk($targetDisk)->url($objectKey);
            $key = ($targetDisk === 'uploads' || $targetDisk === 'public' || $targetDisk === 'local') ? 'local:' . $objectKey : $objectKey;

            return [
                'url' => $url,
                'key' => $key,
            ];
        } catch (\Throwable $e) {
            if ($targetDisk === 'r2') {
                Log::warning('Avatar upload to R2 failed; falling back to local uploads disk.', [
                    'user_id' => $userId,
                    'key' => $objectKey,
                    'error' => $e->getMessage(),
                ]);
                Storage::disk('uploads')->put($objectKey, (string) $encoded);
                return [
                    'url' => '/uploads/' . $objectKey,
                    'key' => 'local:' . $objectKey,
                ];
            }
            throw $e;
        }
    }
}
