<?php

namespace Database\Seeders;

use App\Models\Quan;
use App\Models\ThanhToan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ThanhToanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $quans = Quan::all();
        $packages = ['Gói VIP Tháng', 'Gói Nổi Bật 3 Tháng', 'Gói Doanh Nghiệp Năm', 'Gói Quảng Cáo Trang Chủ'];
        $prices = [299000, 599000, 1990000, 499000];

        for ($i = 0; $i < 25; $i++) {
            $date = now()->subDays(rand(1, 150));
            ThanhToan::create([
                'user_id' => $users->isNotEmpty() ? $users->random()->id : null,
                'quan_id' => $quans->isNotEmpty() ? $quans->random()->id : null,
                'ma_giao_dich' => 'PAY' . strtoupper(Str::random(8)),
                'ten_goi_dich_vu' => $packages[array_rand($packages)],
                'so_tien' => $prices[array_rand($prices)],
                'phuong_thuc' => ['vnpay', 'momo', 'chuyen_khoan'][array_rand(['vnpay', 'momo', 'chuyen_khoan'])],
                'trang_thai' => 'thanh_cong',
                'ghi_chu' => 'Thanh toán thành công gói dịch vụ',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
