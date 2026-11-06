<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $res = App\Models\Quan::search('lẩu')->take(5)->get();
    echo "Elasticsearch found " . $res->count() . " quans for 'lẩu':\n";
    foreach ($res as $q) {
        echo " - ID: {$q->id} | {$q->ten_quan} | Địa chỉ: {$q->dia_chi_chi_tiet}\n";
    }
} catch (\Throwable $e) {
    echo "ES error: " . $e->getMessage() . "\n";
}
