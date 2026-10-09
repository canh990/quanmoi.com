<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = \Illuminate\Http\Request::create('/kham-pha?tinh_thanh_id=HN&danh_muc=C%C3%A0+ph%C3%AA&is_xac_thuc=1', 'GET');
app()->instance('request', $request);

$urlWithoutXacThuc = $request->fullUrlWithQuery(['is_xac_thuc' => null]);
echo "Without xac_thuc: " . $urlWithoutXacThuc . PHP_EOL;

$urlWithoutAll = route('kham-pha');
echo "Without all: " . $urlWithoutAll . PHP_EOL;
