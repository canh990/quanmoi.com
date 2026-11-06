<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $res = App\Models\Quan::search('cá voi xanh khổng lồ')->get();
    echo "Found: " . $res->count() . "\n";
} catch (\Throwable $e) {
    echo "ES error: " . $e->getMessage() . "\n";
}
