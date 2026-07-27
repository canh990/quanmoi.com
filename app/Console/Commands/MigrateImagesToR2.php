<?php

namespace App\Console\Commands;

use App\Models\HinhAnhQuan;
use App\Models\Quan;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

#[Signature('app:migrate-images-to-r2')]
#[Description('Migrate all old images to Cloudflare R2')]
class MigrateImagesToR2 extends Command
{
    public function handle()
    {
        $manager = new ImageManager(new Driver());

        $this->info("1. Migrate User Avatars...");
        $users = User::whereNotNull('anh_dai_dien')
                     ->whereNull('anh_dai_dien_key')
                     ->get();
        
        foreach ($users as $user) {
            $path = $user->anh_dai_dien;
            if (Str::startsWith($path, 'http')) {
                continue;
            }
            
            $absPath = Storage::disk('public')->path($path);
            if (!file_exists($absPath)) {
                $this->warn("Missing local file: " . $absPath);
                continue;
            }

            try {
                $image = $manager->decode($absPath);
                $encoded = $image->encodeUsingFileExtension('webp', 80);
                $filename = Str::uuid() . '.webp';
                $objectKey = 'avatars/' . $user->id . '/' . $filename;
                
                Storage::disk('r2')->put($objectKey, (string) $encoded);
                
                $user->update([
                    'anh_dai_dien' => Storage::disk('r2')->url($objectKey),
                    'anh_dai_dien_key' => $objectKey,
                ]);
                $this->line("Migrated avatar for user " . $user->id);
            } catch (\Exception $e) {
                $this->error("Failed to migrate avatar for user {$user->id}: " . $e->getMessage());
            }
        }

        $this->info("2. Migrate Quan Cover Images...");
        $quans = Quan::whereNotNull('anh_bia')
                     ->whereNull('anh_bia_key')
                     ->get();

        foreach ($quans as $quan) {
            $path = $quan->anh_bia;
            if (Str::startsWith($path, 'http')) continue;

            $absPath = Storage::disk('public')->path($path);
            if (!file_exists($absPath)) {
                $this->warn("Missing local file: " . $absPath);
                continue;
            }

            try {
                $image = $manager->decode($absPath);
                $encoded = $image->encodeUsingFileExtension('webp', 80);
                $filename = Str::uuid() . '.webp';
                $objectKey = 'quan/anh-bia/' . $filename;
                
                Storage::disk('r2')->put($objectKey, (string) $encoded);
                
                $quan->update([
                    'anh_bia' => Storage::disk('r2')->url($objectKey),
                    'anh_bia_key' => $objectKey,
                ]);
                $this->line("Migrated cover for quan " . $quan->id);
            } catch (\Exception $e) {
                $this->error("Failed to migrate cover for quan {$quan->id}: " . $e->getMessage());
            }
        }

        $this->info("3. Migrate Quan Gallery Images...");
        $gallery = HinhAnhQuan::whereNotNull('duong_dan')
                              ->whereNull('object_key')
                              ->get();

        foreach ($gallery as $img) {
            $path = $img->duong_dan;
            if (Str::startsWith($path, 'http')) continue;

            $absPath = Storage::disk('public')->path($path);
            if (!file_exists($absPath)) {
                $this->warn("Missing local file: " . $absPath);
                continue;
            }

            try {
                $image = $manager->decode($absPath);
                $encoded = $image->encodeUsingFileExtension('webp', 80);
                $filename = Str::uuid() . '.webp';
                $objectKey = 'quan/gallery/' . $img->quan_id . '/' . $filename;
                
                Storage::disk('r2')->put($objectKey, (string) $encoded);
                
                $img->update([
                    'duong_dan' => Storage::disk('r2')->url($objectKey),
                    'object_key' => $objectKey,
                ]);
                $this->line("Migrated gallery image " . $img->id);
            } catch (\Exception $e) {
                $this->error("Failed to migrate gallery image {$img->id}: " . $e->getMessage());
            }
        }

        $this->info("Migration to Cloudflare R2 Complete!");
    }
}
