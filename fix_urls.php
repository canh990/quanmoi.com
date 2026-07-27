<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$oldDomain = 'https://cdn.quanmoi.com';
$newDomain = 'https://pub-f89cce68a1e54e32a13b532b105f41d9.r2.dev';

// Users
$users = App\Models\User::where('anh_dai_dien', 'LIKE', $oldDomain . '%')->get();
foreach($users as $user) {
    $user->update(['anh_dai_dien' => str_replace($oldDomain, $newDomain, $user->anh_dai_dien)]);
}

// Quan
$quans = App\Models\Quan::where('anh_bia', 'LIKE', $oldDomain . '%')->get();
foreach($quans as $quan) {
    $quan->update(['anh_bia' => str_replace($oldDomain, $newDomain, $quan->anh_bia)]);
}

// HinhAnhQuan
$hinhAnhs = App\Models\HinhAnhQuan::where('duong_dan', 'LIKE', $oldDomain . '%')->get();
foreach($hinhAnhs as $hinhAnh) {
    $hinhAnh->update(['duong_dan' => str_replace($oldDomain, $newDomain, $hinhAnh->duong_dan)]);
}

echo "Replaced " . $users->count() . " users, " . $quans->count() . " quans, " . $hinhAnhs->count() . " hinh_anhs.\n";
