<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Quan;

// 1. Gán shopeefood_url cho ~18 quán
$shopeeSlugs = [
    'quan-an-ngon-ha-noi',
    'pho-bo-gia-truyen-bat-dan',
    'bun-cha-huong-lien',
    'pizza-4ps-phan-ke-binh',
    'the-coffee-house-thai-ha',
    'highlands-coffee-hoan-kiem',
    'lau-phan-thai-ha',
    'haidilao-hotpot-vincom-pham-ngoc-thach',
    'king-bbq-buffet-artemis',
    'tocotoco-bubble-tea',
    'phuc-long-coffee-tea',
    'tra-sua-gong-cha-ho-guom',
];

$quans = Quan::where('trang_thai', 'da_duyet')->get();

$i = 0;
foreach ($quans as $quan) {
    // 15 quán có ShopeeFood URL
    if ($i < 18) {
        $slug = \Illuminate\Support\Str::slug($quan->ten_quan);
        $quan->shopeefood_url = "https://shopeefood.vn/ha-noi/{$slug}";
    }

    // 12 quán có giá sàn trên 50k (ví dụ 80k - 250k: buffet, lẩu cao cấp)
    if ($i >= 38) {
        $quan->gia_nho_nhat = 99000;
        $quan->gia_lon_nhat = 399000;
    }

    // 6 quán mở đêm (20:00 - 04:00, đang đóng cửa vào ban ngày)
    if ($i >= 44) {
        $quan->gio_mo_cua = '20:00';
        $quan->gio_dong_cua = '04:00';
    }

    $quan->saveQuietly();
    $i++;
}

echo "Updated data for {$quans->count()} restaurants." . PHP_EOL;
