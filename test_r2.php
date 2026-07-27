<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "=== R2 Debug ===" . PHP_EOL;
echo "Endpoint : " . config('filesystems.disks.r2.endpoint') . PHP_EOL;
echo "Bucket   : " . config('filesystems.disks.r2.bucket') . PHP_EOL;
echo "Region   : " . config('filesystems.disks.r2.region') . PHP_EOL;
echo "Key      : " . substr(config('filesystems.disks.r2.key'), 0, 8) . "..." . PHP_EOL;
echo "PathStyle: " . (config('filesystems.disks.r2.use_path_style_endpoint') ? 'true' : 'false') . PHP_EOL;
echo PHP_EOL;

try {
    Storage::disk('r2')->put('test/connection.txt', 'R2 test');
    echo "WRITE: OK" . PHP_EOL;
} catch (Exception $e) {
    echo "ERROR CLASS: " . get_class($e) . PHP_EOL;
    echo "ERROR MSG  : " . $e->getMessage() . PHP_EOL;

    $prev = $e->getPrevious();
    while ($prev) {
        echo "CAUSE      : " . $prev->getMessage() . PHP_EOL;
        $prev = $prev->getPrevious();
    }
}
