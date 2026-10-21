<?php

namespace App\Console\Commands;

use App\Models\Quan;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedNearMeCommand extends Command
{
    /**
     * Tên lệnh và chữ ký. Cho phép người dùng truyền tọa độ bất kỳ để tạo quán ngay quanh họ!
     */
    protected $signature = 'quanmoi:seed-near-me 
                            {lat? : Vĩ độ (Latitude) của bạn} 
                            {lng? : Kinh độ (Longitude) của bạn} 
                            {--count=15 : Số lượng quán muốn tạo}';

    protected $description = 'Seed nhanh danh sách các quán ăn mẫu xung quanh tọa độ GPS bất kỳ để kiểm thử bản đồ';

    public function handle(): int
    {
        $lat = $this->argument('lat');
        $lng = $this->argument('lng');
        $count = (int) $this->option('count');

        // Nếu không truyền tọa độ -> Chạy Seeder mẫu toàn quốc
        if (!$lat || !$lng) {
            $this->info("Chưa truyền tọa độ cụ thể. Đang chạy seeder mẫu (Hà Nội, Sài Gòn, Đà Nẵng)...");
            $this->call('db:seed', ['--class' => 'QuanGanToiSeeder']);
            return Command::SUCCESS;
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            $this->error("Tọa độ không hợp lệ. Vui lòng nhập vĩ độ (-90 đến 90) và kinh độ (-180 đến 180).");
            return Command::FAILURE;
        }

        $owner = User::first() ?? User::factory()->create([
            'ho_ten' => 'Chủ Quán GPS',
            'email' => 'chuquan.gps@gmail.com',
            'da_xac_thuc' => true,
        ]);

        $samples = [
            ['Tên' => 'Phở Bò Gia Truyền Phố Mới', 'Loại' => 'Nhà hàng', 'Giá' => [40000, 75000], 'Img' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=600&q=80'],
            ['Tên' => 'Cà Phê Muối & Trà Sữa Đậm Vị', 'Loại' => 'Cà phê & Trà', 'Giá' => [28000, 55000], 'Img' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=600&q=80'],
            ['Tên' => 'Bánh Mì Kẹp Thịt Nướng Giòn Cay', 'Loại' => 'Ăn vặt', 'Giá' => [25000, 45000], 'Img' => 'https://images.unsplash.com/photo-1509722747041-616f39b57569?auto=format&fit=crop&w=600&q=80'],
            ['Tên' => 'Lẩu Nướng Không Khói Buffet', 'Loại' => 'Lẩu & Nướng', 'Giá' => [169000, 329000], 'Img' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80'],
            ['Tên' => 'Bún Đậu Mắm Tôm Thập Cẩm', 'Loại' => 'Nhà hàng', 'Giá' => [35000, 65000], 'Img' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=600&q=80'],
            ['Tên' => 'Ốc Đêm & Bia Hơi Sôi Động', 'Loại' => 'Quán nhậu', 'Giá' => [80000, 250000], 'Img' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=600&q=80'],
            ['Tên' => 'Cơm Gà Xối Mỡ Da Giòn', 'Loại' => 'Nhà hàng', 'Giá' => [45000, 80000], 'Img' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80'],
            ['Tên' => 'Trà Chanh & Ăn Vặt Sinh Viên', 'Loại' => 'Ăn vặt', 'Giá' => [15000, 35000], 'Img' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=600&q=80'],
        ];

        $created = 0;
        for ($i = 0; $i < $count; $i++) {
            $sample = $samples[$i % count($samples)];
            
            // Random cự ly từ 0.2 km đến 4.8 km
            $distanceKm = 0.2 + ($i * (4.5 / max(1, $count - 1)));
            $angle = ($i * (360 / max(1, $count))) * (M_PI / 180);
            
            $offsetLat = ($distanceKm / 111.0) * cos($angle);
            $offsetLng = ($distanceKm / (111.0 * max(0.1, cos(deg2rad($lat))))) * sin($angle);
            
            $venueLat = round($lat + $offsetLat, 6);
            $venueLng = round($lng + $offsetLng, 6);
            
            $name = $sample['Tên'] . " (Gần Bạn #" . ($i + 1) . ")";
            $slug = Str::slug($name) . '-' . Str::random(5);

            Quan::create([
                'id' => Str::uuid()->toString(),
                'chu_quan_id' => $owner->id,
                'ten_quan' => $name,
                'slug' => $slug,
                'loai_hinh_kinh_doanh' => $sample['Loại'],
                'mo_ta' => 'Quán ăn phục vụ đồ tươi ngon nóng hổi, nhận đặt bàn và giao hàng nhanh chóng.',
                'so_dien_thoai' => '09' . rand(10000000, 99999999),
                'email' => $slug . '@quanmoi.vn',
                'dia_chi_chi_tiet' => "Số " . ($i * 12 + 8) . " Đường Gần Bạn",
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Khu vực thử nghiệm',
                'quan_huyen_id' => '001',
                'ten_quan_huyen' => 'Quận mẫu',
                'phuong_xa_id' => '00001',
                'ten_phuong_xa' => 'Phường mẫu',
                'vi_do' => $venueLat,
                'kinh_do' => $venueLng,
                'gio_mo_cua' => '08:00',
                'gio_dong_cua' => '22:30',
                'gia_nho_nhat' => $sample['Giá'][0],
                'gia_lon_nhat' => $sample['Giá'][1],
                'anh_bia' => $sample['Img'],
                'trang_thai' => 'da_duyet',
                'is_noi_bat' => $i < 3,
                'is_xac_thuc' => true,
                'luot_xem' => rand(200, 3500),
            ]);
            $created++;
        }

        $this->info("✓ Đã tạo thành công {$created} quán ăn xung quanh tọa độ ({$lat}, {$lng}) trong bán kính 0.2 km -> 4.8 km!");
        return Command::SUCCESS;
    }
}
