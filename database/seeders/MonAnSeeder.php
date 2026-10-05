<?php

namespace Database\Seeders;

use App\Models\Quan;
use App\Models\DanhMucMenu;
use App\Models\MonTrongMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MonAnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quans = Quan::all();

        if ($quans->isEmpty()) {
            $this->command->warn('Không có Quán nào trong database. Vui lòng seed Quán trước!');
            return;
        }

        $this->command->info('Đang tạo thực đơn và món ăn mẫu cho các quán...');

        $menuData = [
            'Ăn vặt' => [
                'Bún đậu mắm tôm', 'Nem chua rán', 'Bánh tráng trộn', 'Bánh tráng nướng', 'Cút lộn xào me', 'Xúc xích Đức', 'Khoai tây chiên'
            ],
            'Nhà hàng' => [
                'Phở bò tái nạm', 'Phở gà ta', 'Cơm tấm sườn bì chả', 'Gà nướng muối ớt', 'Bò tơ nướng tảng', 'Lẩu thái hải sản', 'Gỏi ngó sen tôm thịt'
            ],
            'Cà phê & Trà' => [
                'Cà phê sữa đá', 'Bạc xỉu', 'Trà sữa Phúc Long', 'Trà đào cam sả', 'Trà vải', 'Americano', 'Latte', 'Matcha đá xay'
            ],
            'Lẩu & Nướng' => [
                'Ba chỉ bò Mỹ nướng', 'Nầm lợn nướng', 'Bạch tuộc sa tế', 'Lẩu tứ xuyên', 'Lẩu nấm kim châm', 'Salad lườn ngỗng'
            ]
        ];

        foreach ($quans as $quan) {
            $loaiHinh = $quan->loai_hinh_kinh_doanh ?? 'Nhà hàng';
            
            // Tìm mảng món ăn tương ứng, nếu không có thì lấy mặc định
            $monAnList = $menuData[$loaiHinh] ?? $menuData['Nhà hàng'];
            
            // Lấy ngẫu nhiên 3 đến 5 món
            $monAnQuan = collect($monAnList)->random(rand(3, 5));

            // Tạo danh mục Món Chính
            $danhMucChinh = DanhMucMenu::create([
                'id' => Str::uuid()->toString(),
                'quan_id' => $quan->id,
                'ten_danh_muc' => 'Món Chính',
                'thu_tu' => 1,
            ]);

            // Thêm các món vào danh mục
            foreach ($monAnQuan as $tenMon) {
                MonTrongMenu::create([
                    'id' => Str::uuid()->toString(),
                    'danh_muc_id' => $danhMucChinh->id,
                    'ten_mon' => $tenMon,
                    'mo_ta' => 'Hương vị thơm ngon, chuẩn vị truyền thống.',
                    'gia' => rand(15, 150) * 1000, // Giá từ 15k đến 150k
                    'hinh_anh' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                    'con_hang' => true,
                ]);
            }
            
            // Random tạo thêm Danh mục Nước uống
            if (rand(0, 1)) {
                $danhMucNuoc = DanhMucMenu::create([
                    'id' => Str::uuid()->toString(),
                    'quan_id' => $quan->id,
                    'ten_danh_muc' => 'Nước uống giải khát',
                    'thu_tu' => 2,
                ]);
                
                $nuocUongList = ['Coca Cola', 'Pepsi', 'Trà đá', 'Bia Heineken', 'Bia Tiger'];
                $nuocQuan = collect($nuocUongList)->random(rand(1, 3));
                
                foreach ($nuocQuan as $tenMon) {
                    MonTrongMenu::create([
                        'id' => Str::uuid()->toString(),
                        'danh_muc_id' => $danhMucNuoc->id,
                        'ten_mon' => $tenMon,
                        'mo_ta' => 'Nước uống mát lạnh.',
                        'gia' => rand(10, 30) * 1000,
                        'hinh_anh' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=600&q=80',
                        'con_hang' => true,
                    ]);
                }
            }
        }

        $this->command->info('Seed thực đơn và món ăn hoàn tất!');
    }
}
