<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Quan;

echo "Total: " . Quan::search('')->whereIn('trang_thai', ['da_duyet'])->paginate(12)->total() . PHP_EOL;
echo "Tích xanh (is_xac_thuc): " . Quan::search('')->whereIn('trang_thai', ['da_duyet'])->whereIn('is_xac_thuc', [true])->paginate(12)->total() . PHP_EOL;
echo "ShopeeFood (co_shopeefood): " . Quan::search('')->whereIn('trang_thai', ['da_duyet'])->whereIn('co_shopeefood', [true])->paginate(12)->total() . PHP_EOL;
echo "Dưới 50k (duoi_50k): " . Quan::search('')->whereIn('trang_thai', ['da_duyet'])->whereIn('duoi_50k', [true])->paginate(12)->total() . PHP_EOL;
echo "Đang mở cửa (dang_mo_cua): " . Quan::search('')->whereIn('trang_thai', ['da_duyet'])->whereIn('dang_mo_cua', [true])->paginate(12)->total() . PHP_EOL;
echo "Lẩu & Nướng: " . Quan::search('')->whereIn('trang_thai', ['da_duyet'])->whereIn('loai_hinh_kinh_doanh.keyword', ['Lẩu & Nướng'])->paginate(12)->total() . PHP_EOL;
echo "Cà phê & Trà: " . Quan::search('')->whereIn('trang_thai', ['da_duyet'])->whereIn('loai_hinh_kinh_doanh.keyword', ['Cà phê & Trà'])->paginate(12)->total() . PHP_EOL;
