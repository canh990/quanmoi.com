<?php

namespace Database\Seeders;

use App\Models\Quan;
use App\Models\DanhMucMenu;
use App\Models\MonTrongMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Bắt đầu seed 10 món ăn Phở đặc sắc...');

        // Danh sách 10 món Phở chuẩn vị Việt Nam
        $phoDishes = [
            [
                'ten_mon' => 'Phở Bò Tái Nạm',
                'mo_ta' => 'Bánh phở tươi mềm mượt, thịt bò tái tươi ngon kết hợp nạm bò mềm ngậy, nước dùng ninh từ xương bò hầm 12 tiếng thơm lừng quế hồi thảo quả.',
                'gia' => 65000,
                'hinh_anh' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=600&q=80',
                'con_hang' => true,
            ],
            [
                'ten_mon' => 'Phở Bò Tái Lăn Hà Nội',
                'mo_ta' => 'Thịt bò bắp xào lăn nhanh trên lửa lớn cùng tỏi đập dập và hành hoa, dậy mùi thơm nức mũi, đậm đà béo ngậy đúng phong vị phố cổ Hà Nội.',
                'gia' => 70000,
                'hinh_anh' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
                'con_hang' => true,
            ],
            [
                'ten_mon' => 'Phở Bò Sốt Vang',
                'mo_ta' => 'Thịt dẻ sườn và nạm bò hầm sốt vang đỏ sánh mịn, màu đỏ nâu bắt mắt, vị cay nồng thơm lừng quế thảo quả hòa quyện nước phở đậm đà.',
                'gia' => 75000,
                'hinh_anh' => 'https://images.unsplash.com/photo-1576577445504-6af96477db52?auto=format&fit=crop&w=600&q=80',
                'con_hang' => true,
            ],
            [
                'ten_mon' => 'Phở Bò Gầu Bắp Hoa',
                'mo_ta' => 'Gầu bò giòn sần sật béo ngậy phối cùng bắp bò hoa thái mỏng mềm ngọt, rắc hành lá xanh non và hạt tiêu đen thơm phức.',
                'gia' => 75000,
                'hinh_anh' => 'https://images.unsplash.com/photo-1631709497146-a239ef373cf1?auto=format&fit=crop&w=600&q=80',
                'con_hang' => true,
            ],
            [
                'ten_mon' => 'Phở Gà Ta Chặt Miếng',
                'mo_ta' => 'Thịt gà ta thả vườn da vàng óng ả giòn dai, thịt ngọt lịm kèm lá chanh thái chỉ thơm ngát, nước dùng ninh xương gà thanh trong tự nhiên.',
                'gia' => 60000,
                'hinh_anh' => 'https://images.unsplash.com/photo-1552611052-33e04de081de?auto=format&fit=crop&w=600&q=80',
                'con_hang' => true,
            ],
            [
                'ten_mon' => 'Phở Đặc Biệt Thập Cẩm',
                'mo_ta' => 'Tô phở đại thượng hạng ngập tràn topping: bò tái, nạm, gầu giòn, gân dẻo trong veo và bò viên thơm dai, kèm trứng chần béo ngậy.',
                'gia' => 85000,
                'hinh_anh' => 'https://images.unsplash.com/photo-1594998893017-36147cbcae05?auto=format&fit=crop&w=600&q=80',
                'con_hang' => true,
            ],
            [
                'ten_mon' => 'Phở Cuốn Bò Nướng',
                'mo_ta' => 'Bánh phở tươi mềm mỏng cuộn chặt tay với thịt bò áp chảo thơm lừng, xà lách, rau thơm tươi sạch, chấm nước mắm chua ngọt chuẩn vị.',
                'gia' => 65000,
                'hinh_anh' => 'https://images.unsplash.com/photo-1617093727343-374698b1b08d?auto=format&fit=crop&w=600&q=80',
                'con_hang' => true,
            ],
            [
                'ten_mon' => 'Phở Chiên Phồng Bò Xào Cải',
                'mo_ta' => 'Từng miếng bánh phở cắt vuông chiên phồng xốp giòn rụm, phủ đều sốt thịt bò mềm xào cùng cải ngọt xanh giòn đậm đà nóng hổi.',
                'gia' => 80000,
                'hinh_anh' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=600&q=80',
                'con_hang' => true,
            ],
            [
                'ten_mon' => 'Phở Xào Giòn Thịt Bò',
                'mo_ta' => 'Sợi phở áp chảo xém cạnh giòn rụm bên ngoài dai mềm bên trong, rưới ngập sốt thịt bò xào hành tây, cần tây và cà chua thơm phức.',
                'gia' => 75000,
                'hinh_anh' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=600&q=80',
                'con_hang' => true,
            ],
            [
                'ten_mon' => 'Phở Trộn Gà Xé Sốt Chua Ngọt',
                'mo_ta' => 'Phở trộn khô thanh mát cùng thịt gà ta xé sợi, lạc rang giã dập, hành phi vàng ruộm hòa quyện nước sốt chua ngọt bí truyền.',
                'gia' => 60000,
                'hinh_anh' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=600&q=80',
                'con_hang' => true,
            ],
        ];

        // Tìm quán chuyên Phở (ví dụ quán có tên chứa 'phở') hoặc các nhà hàng
        $targetQuans = Quan::where('ten_quan', 'like', '%phở%')
            ->orWhere('ten_quan', 'like', '%pho%')
            ->get();

        // Nếu chưa có quán Phở nào, lấy thêm các quán Nhà hàng
        if ($targetQuans->isEmpty()) {
            $targetQuans = Quan::where('loai_hinh_kinh_doanh', 'Nhà hàng')->take(3)->get();
        }

        if ($targetQuans->isEmpty()) {
            $targetQuans = Quan::take(3)->get();
        }

        if ($targetQuans->isEmpty()) {
            $this->command->warn('Không tìm thấy Quán nào trong database để gán món ăn!');
            return;
        }

        $createdCount = 0;

        foreach ($targetQuans as $quan) {
            $this->command->info("Đang thêm thực đơn Phở cho quán: {$quan->ten_quan}");

            // Tạo hoặc tìm danh mục 'Phở Truyền Thống & Đặc Biệt'
            $danhMuc = DanhMucMenu::firstOrCreate(
                [
                    'quan_id' => $quan->id,
                    'ten_danh_muc' => 'Phở Truyền Thống & Đặc Biệt',
                ],
                [
                    'id' => (string) Str::uuid(),
                    'thu_tu' => 1,
                ]
            );

            foreach ($phoDishes as $dishData) {
                // Sử dụng updateOrCreate theo tên món trong danh mục để tránh trùng lặp
                MonTrongMenu::updateOrCreate(
                    [
                        'danh_muc_id' => $danhMuc->id,
                        'ten_mon' => $dishData['ten_mon'],
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'mo_ta' => $dishData['mo_ta'],
                        'gia' => $dishData['gia'],
                        'hinh_anh' => $dishData['hinh_anh'],
                        'con_hang' => $dishData['con_hang'],
                    ]
                );
                $createdCount++;
            }

            // Đồng bộ dữ liệu sang Elasticsearch
            if (method_exists($quan, 'searchable')) {
                $quan->searchable();
            }
        }

        $this->command->info("Đã seed thành công 10 món ăn Phở cho " . $targetQuans->count() . " quán ăn!");
    }
}
