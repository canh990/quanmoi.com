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
        if ($oldKey) {
            try {
                Storage::disk('r2')->delete($oldKey);
            } catch (\Exception $e) {
                Log::warning('Cannot delete old avatar on R2: ' . $e->getMessage(), [
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
        Storage::disk('r2')->put($objectKey, (string) $encoded);

        return [
            'url' => Storage::disk('r2')->url($objectKey),
            'key' => $objectKey,
        ];
    }
}
