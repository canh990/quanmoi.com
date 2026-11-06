<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Blog;

$localFiles = glob(storage_path('app/public/blogs/covers/*.*'));
$fileNames = array_map('basename', $localFiles);

echo "Found " . count($fileNames) . " local cover files.\n";

$blogs = Blog::where('status', 'published')->get();

$i = 0;
foreach ($blogs as $blog) {
    // Kiểm tra xem cover_image có hợp lệ không
    $needsUpdate = false;
    $currentCover = $blog->cover_image;

    if (empty($currentCover)) {
        $needsUpdate = true;
    } elseif (str_starts_with($currentCover, 'http')) {
        // Test URL
        $ch = curl_init($currentCover);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200) {
            $needsUpdate = true;
            echo "Blog #{$blog->id} has broken URL: {$currentCover} (Code: {$code})\n";
        }
    } else {
        $cleanPath = ltrim($currentCover, '/');
        $cleanPath = str_replace('storage/', '', $cleanPath);
        if (!file_exists(storage_path('app/public/' . $cleanPath))) {
            $needsUpdate = true;
            echo "Blog #{$blog->id} missing local file: {$currentCover}\n";
        }
    }

    if ($needsUpdate && !empty($fileNames)) {
        // Gán 1 ảnh local chắc chắn hoạt động
        $picked = $fileNames[$i % count($fileNames)];
        $blog->update(['cover_image' => 'blogs/covers/' . $picked]);
        echo "--> Updated Blog #{$blog->id} to: blogs/covers/{$picked}\n";
        $i++;
    }
}

echo "Image check and fix completed!\n";
