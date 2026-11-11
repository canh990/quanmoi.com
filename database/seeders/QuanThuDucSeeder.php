<?php

namespace Database\Seeders;

use App\Models\DanhMucMenu;
use App\Models\MonTrongMenu;
use App\Models\Quan;
use App\Models\QuanDanhGia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuanThuDucSeeder extends Seeder
{
    /**
     * Tạo danh sách các quán ăn thực tế có tọa độ GPS bao quanh điểm
     * (10.852161, 106.757911) tại TP. Thủ Đức, TP. Hồ Chí Minh
     * với cự ly từ 150m đến 1.5km để test Bản đồ & Gần tôi.
     */
    public function run(): void
    {
        $owner = User::first() ?? User::factory()->create([
            'ho_ten' => 'Chủ Quán Thủ Đức',
            'email' => 'chuquan.thuduc@gmail.com',
            'so_dien_thoai' => '0912345678',
            'da_xac_thuc' => true,
        ]);

        $users = User::take(5)->get();

        $venues = [
            // --- CỰ LY < 500M QUANH (10.852161, 106.757911) ---
            [
                'ten_quan' => 'Bún Đậu Mắm Tôm Mạc Văn Khoa - Thủ Đức',
                'loai_hinh_kinh_doanh' => 'Quán ăn bình dân',
                'dia_chi_chi_tiet' => '108 Tô Vĩnh Diện',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26830',
                'ten_phuong_xa' => 'Phường Linh Chiểu',
                'vi_do' => 10.853200,
                'kinh_do' => 106.758200,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 85000,
                'anh_bia' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Quán bún đậu mắm tôm chuẩn vị Bắc nổi tiếng tại làng đại học Thủ Đức, mẹt thập cẩm giòn rụm.',
                'menu' => [
                    ['ten_mon' => 'Mẹt Bún Đậu Thập Cẩm', 'gia' => 65000, 'mo_ta' => 'Đậu hũ chiên giòn, chả cốm, dồi sụn, nem chua rán, thịt bắp giò'],
                    ['ten_mon' => 'Chả Cốm Hà Nội Chiên Nóng', 'gia' => 30000, 'mo_ta' => 'Chả cốm dẻo thơm hạt sen'],
                    ['ten_mon' => 'Nước Mơ Chùa Hương', 'gia' => 18000, 'mo_ta' => 'Chua ngọt thanh mát giải nhiệt'],
                ],
            ],
            [
                'ten_quan' => 'Phở Huỳnh Thư - Đặng Văn Bi',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'dia_chi_chi_tiet' => '125 Đặng Văn Bi',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26839',
                'ten_phuong_xa' => 'Phường Trường Thọ',
                'vi_do' => 10.851500,
                'kinh_do' => 106.756800,
                'gia_nho_nhat' => 45000,
                'gia_lon_nhat' => 90000,
                'anh_bia' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Phở bò gia truyền nước dùng hầm xương tủy thanh ngọt tự nhiên, bánh phở mềm dẻo.',
                'menu' => [
                    ['ten_mon' => 'Phở Bò Tái Nạm Gầu', 'gia' => 60000, 'mo_ta' => 'Bò tái mềm, nạm giòn, gầu béo ngậy'],
                    ['ten_mon' => 'Phở Đặc Biệt Thố Đá', 'gia' => 85000, 'mo_ta' => 'Sôi sùng sục giữ nhiệt cực lâu với đuôi bò và tủy'],
                    ['ten_mon' => 'Quẩy Giòn Hà Nội', 'gia' => 10000, 'mo_ta' => 'Quẩy giòn rụm chấm nước phở'],
                ],
            ],
            [
                'ten_quan' => 'Bánh Tráng Nướng & Trứng Nướng Cô Ba - Chợ Thủ Đức',
                'loai_hinh_kinh_doanh' => 'Ăn vặt',
                'dia_chi_chi_tiet' => '18 Đoàn Công Hớn, Chợ Thủ Đức',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26836',
                'ten_phuong_xa' => 'Phường Linh Tây',
                'vi_do' => 10.853800,
                'kinh_do' => 106.753500,
                'gia_nho_nhat' => 15000,
                'gia_lon_nhat' => 45000,
                'anh_bia' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Thiên đường ăn vặt sinh viên giá rẻ, bánh tráng nướng mỡ hành phô mai bò băm thơm nức mũi.',
                'menu' => [
                    ['ten_mon' => 'Bánh Tráng Nướng Trứng Bò Khô Phô Mai', 'gia' => 25000, 'mo_ta' => 'Đầy ắp topping xúc xích, bò khô, phô mai kéo sợi'],
                    ['ten_mon' => 'Trứng Cút Nướng Chén Phô Mai', 'gia' => 20000, 'mo_ta' => 'Set 4 chén béo ngậy hành phi'],
                    ['ten_mon' => 'Trà Tắc Xí Muội Khổng Lồ', 'gia' => 15000, 'mo_ta' => 'Ly 700ml giải ngấy tuyệt hảo'],
                ],
            ],

            // --- CỰ LY 500M - 1.2KM ---
            [
                'ten_quan' => 'The Coffee House - Võ Văn Ngân Thủ Đức',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
                'dia_chi_chi_tiet' => '114 Võ Văn Ngân',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26833',
                'ten_phuong_xa' => 'Phường Bình Thọ',
                'vi_do' => 10.850800,
                'kinh_do' => 106.766500,
                'gia_nho_nhat' => 39000,
                'gia_lon_nhat' => 75000,
                'anh_bia' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Không gian hiện đại 3 tầng thoáng đãng, máy lạnh mát rượi thích hợp học tập và làm việc chạy deadline.',
                'menu' => [
                    ['ten_mon' => 'Trà Đào Cam Sả', 'gia' => 55000, 'mo_ta' => 'Đậm vị trà đen hòa quyện hương sả và miếng đào giòn'],
                    ['ten_mon' => 'Cà Phê Sữa Đá Đậm Đà', 'gia' => 39000, 'mo_ta' => 'Hạt robusta Đắk Lắk nguyên chất'],
                    ['ten_mon' => 'Bánh Mì Chà Bông Phô Mai', 'gia' => 35000, 'mo_ta' => 'Bánh mềm xốp nhân phô mai tan chảy'],
                ],
            ],
            [
                'ten_quan' => 'Katinat Saigon Kafe - Võ Văn Ngân',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
                'dia_chi_chi_tiet' => '206 Võ Văn Ngân',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26833',
                'ten_phuong_xa' => 'Phường Bình Thọ',
                'vi_do' => 10.851900,
                'kinh_do' => 106.769500,
                'gia_nho_nhat' => 45000,
                'gia_lon_nhat' => 85000,
                'anh_bia' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Kiến trúc phong cách cổ điển retro check-in sang chảnh, menu đồ uống trendy bắt mắt.',
                'menu' => [
                    ['ten_mon' => 'Trà Sữa Chôm Chôm Katinat', 'gia' => 60000, 'mo_ta' => 'Món signature ngọt thanh với topping chôm chôm tươi'],
                    ['ten_mon' => 'Cà Phê Muối Biển', 'gia' => 45000, 'mo_ta' => 'Lớp kem muối béo mặn kết hợp cà phê phin truyền thống'],
                    ['ten_mon' => 'Trà Đào Hồng Đài Loan', 'gia' => 55000, 'mo_ta' => 'Trà hoa quả chua thanh ngọt dịu'],
                ],
            ],
            [
                'ten_quan' => 'Phúc Long Coffee & Tea - Vincom Thủ Đức',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
                'dia_chi_chi_tiet' => '216 Võ Văn Ngân, Vincom Plaza',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26833',
                'ten_phuong_xa' => 'Phường Bình Thọ',
                'vi_do' => 10.852400,
                'kinh_do' => 106.771200,
                'gia_nho_nhat' => 45000,
                'gia_lon_nhat' => 85000,
                'anh_bia' => 'https://images.unsplash.com/photo-1579954115545-a95591f28bfc?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Thương hiệu trà và cà phê Bảo Lộc đậm vị trứ danh, phục vụ nhanh chóng và chuyên nghiệp.',
                'menu' => [
                    ['ten_mon' => 'Trà Sữa Phúc Long Truyền Thống', 'gia' => 55000, 'mo_ta' => 'Trà ô long nguyên chất đậm đà quyến rũ'],
                    ['ten_mon' => 'Trà Lài Hạt Đác', 'gia' => 55000, 'mo_ta' => 'Hương lài tinh tế cùng hạt đác dẻo giòn ngào đường phèn'],
                ],
            ],
            [
                'ten_quan' => 'Trà Sữa KOI Thé - Vincom Thủ Đức',
                'loai_hinh_kinh_doanh' => 'Trà sữa & Nước ép',
                'dia_chi_chi_tiet' => 'Tầng trệt Vincom Thủ Đức, 216 Võ Văn Ngân',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26833',
                'ten_phuong_xa' => 'Phường Bình Thọ',
                'vi_do' => 10.852500,
                'kinh_do' => 106.771500,
                'gia_nho_nhat' => 42000,
                'gia_lon_nhat' => 89000,
                'anh_bia' => 'https://images.unsplash.com/photo-1558857563-b37fe3160a22?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Thương hiệu trà sữa chuẩn Đài Loan nổi tiếng với trân châu hoàng kim và lớp macchiato sóng sánh.',
                'menu' => [
                    ['ten_mon' => 'Lục Trà Macchiato', 'gia' => 50000, 'mo_ta' => 'Lớp váng sữa phô mai mặn béo ngậy'],
                    ['ten_mon' => 'Hồng Trà Sữa Trân Châu Hoàng Kim', 'gia' => 55000, 'mo_ta' => 'Trân châu vàng ươm dẻo thơm'],
                ],
            ],
            [
                'ten_quan' => 'Gogi House - Quán Thịt Nướng Hàn Quốc Thủ Đức',
                'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
                'dia_chi_chi_tiet' => 'Tầng 3 Vincom Thủ Đức, 216 Võ Văn Ngân',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26833',
                'ten_phuong_xa' => 'Phường Bình Thọ',
                'vi_do' => 10.852600,
                'kinh_do' => 106.771100,
                'gia_nho_nhat' => 150000,
                'gia_lon_nhat' => 450000,
                'anh_bia' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Thưởng thức đại tiệc thịt nướng chuẩn Seoul không khói, panchan refill thoải mái không giới hạn.',
                'menu' => [
                    ['ten_mon' => 'Ba Chỉ Bò Mỹ Sốt Obathan', 'gia' => 189000, 'mo_ta' => 'Thịt bò vân mỡ đan xen sốt đậm đà'],
                    ['ten_mon' => 'Dẻ Sườn Bò Nướng Tảng', 'gia' => 269000, 'mo_ta' => 'Mềm ngọt thơm nức mũi'],
                    ['ten_mon' => 'Lẩu Kim Chi Hải Sản', 'gia' => 219000, 'mo_ta' => 'Cay nồng đậm vị cay Hàn Quốc'],
                ],
            ],
            [
                'ten_quan' => 'Billiards Club 68 - Hoàng Diệu 2',
                'loai_hinh_kinh_doanh' => 'Billiards & Giải trí',
                'dia_chi_chi_tiet' => '88 Hoàng Diệu 2',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26830',
                'ten_phuong_xa' => 'Phường Linh Chiểu',
                'vi_do' => 10.856500,
                'kinh_do' => 106.764500,
                'gia_nho_nhat' => 50000,
                'gia_lon_nhat' => 120000,
                'anh_bia' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Câu lạc bộ bida máy lạnh bàn Aileex và Min tiêu chuẩn thi đấu quốc tế, cơ bida chuẩn carbon.',
                'menu' => [
                    ['ten_mon' => 'Giờ Chơi Bàn Lỗ Aileex VIP', 'gia' => 70000, 'mo_ta' => 'Tính theo giờ, miễn phí trà đá'],
                    ['ten_mon' => 'Bia Heineken Bạc Ướp Lạnh', 'gia' => 28000, 'mo_ta' => 'Lon 330ml mát lạnh'],
                ],
            ],
            [
                'ten_quan' => 'Lẩu Dê Đồng Quê - Kha Vạn Cân',
                'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
                'dia_chi_chi_tiet' => '955 Kha Vạn Cân',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26836',
                'ten_phuong_xa' => 'Phường Linh Tây',
                'vi_do' => 10.854500,
                'kinh_do' => 106.751500,
                'gia_nho_nhat' => 90000,
                'gia_lon_nhat' => 320000,
                'anh_bia' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Chuyên các món thịt dê tươi ré Ninh Bình chế biến trong ngày: dê nướng mọi, dê hấp tía tô, lẩu dê thuốc bắc.',
                'menu' => [
                    ['ten_mon' => 'Lẩu Dê Thuốc Bắc Thập Cẩm', 'gia' => 220000, 'mo_ta' => 'Nước lẩu ngọt thanh bổ dưỡng từ thảo mộc'],
                    ['ten_mon' => 'Dê Nướng Mọi Muối Ớt', 'gia' => 160000, 'mo_ta' => 'Thịt dê mềm ngọt chấm chao béo ngậy'],
                ],
            ],
            [
                'ten_quan' => 'Quán Nướng & Nhậu Đêm 24/7 - Tô Ngọc Vân',
                'loai_hinh_kinh_doanh' => 'Quán Đêm 24/7',
                'dia_chi_chi_tiet' => '15 Tô Ngọc Vân',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26836',
                'ten_phuong_xa' => 'Phường Linh Tây',
                'vi_do' => 10.855000,
                'kinh_do' => 106.754800,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 250000,
                'anh_bia' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Mở cửa xuyên đêm 24/7 phục vụ các cú đêm Thủ Đức với chân gà nướng, ốc xào me và các món lẩu nóng hổi.',
                'menu' => [
                    ['ten_mon' => 'Chân Gà Nướng Mật Ong', 'gia' => 45000, 'mo_ta' => 'Dĩa 5 cặp chân gà nướng vàng rộm'],
                    ['ten_mon' => 'Ốc Móng Tay Xào Rau Muống', 'gia' => 65000, 'mo_ta' => 'Ốc tươi giòn ngọt tỏi phi thơm lừng'],
                    ['ten_mon' => 'Bia Tiger Bạc', 'gia' => 25000, 'mo_ta' => 'Bia ướp lạnh cực đã'],
                ],
            ],
            [
                'ten_quan' => 'Tiệm Bánh Tươi BreadTalk - Thủ Đức',
                'loai_hinh_kinh_doanh' => 'Tiệm bánh',
                'dia_chi_chi_tiet' => '216 Võ Văn Ngân',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '769',
                'ten_quan_huyen' => 'Thành phố Thủ Đức',
                'phuong_xa_id' => '26833',
                'ten_phuong_xa' => 'Phường Bình Thọ',
                'vi_do' => 10.852700,
                'kinh_do' => 106.771000,
                'gia_nho_nhat' => 22000,
                'gia_lon_nhat' => 150000,
                'anh_bia' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=600&q=80',
                'mo_ta' => 'Bánh mì nướng tươi mỗi ngày theo công thức Singapore, bánh chà bông cay signature nức tiếng.',
                'menu' => [
                    ['ten_mon' => 'Bánh Mì Chà Bông Cay Flosss', 'gia' => 38000, 'mo_ta' => 'Lớp sốt kem trứng béo ngậy phủ ngập chà bông cay'],
                    ['ten_mon' => 'Bánh Sừng Bò Phô Mai Croissant', 'gia' => 32000, 'mo_ta' => 'Vỏ bánh ngàn lớp giòn rụm'],
                ],
            ],
        ];

        $count = 0;
        foreach ($venues as $v) {
            $menuItems = $v['menu'] ?? [];
            unset($v['menu']);

            $slug = Str::slug($v['ten_quan']);
            $quan = Quan::updateOrCreate(
                ['slug' => $slug],
                array_merge($v, [
                    'chu_quan_id' => $owner->id,
                    'so_dien_thoai' => '09' . rand(10000000, 99999999),
                    'email' => Str::slug($v['ten_quan']) . '@quanmoi.vn',
                    'gio_mo_cua' => '07:00',
                    'gio_dong_cua' => '23:30',
                    'trang_thai' => 'da_duyet',
                    'is_noi_bat' => true,
                    'is_xac_thuc' => true,
                    'luot_xem' => rand(800, 9500),
                ])
            );

            // Tạo thực đơn món ăn cho quán
            if (!empty($menuItems)) {
                $danhMucMenu = DanhMucMenu::firstOrCreate(
                    [
                        'quan_id' => $quan->id,
                        'ten_danh_muc' => 'Món nổi bật',
                    ],
                    ['thu_tu' => 1]
                );

                foreach ($menuItems as $idx => $m) {
                    MonTrongMenu::updateOrCreate(
                        [
                            'danh_muc_id' => $danhMucMenu->id,
                            'ten_mon' => $m['ten_mon'],
                        ],
                        [
                            'gia' => $m['gia'],
                            'mo_ta' => $m['mo_ta'] ?? null,
                            'con_hang' => true,
                        ]
                    );
                }
            }

            // Tạo 2-3 đánh giá mẫu sao 4.8 - 5.0
            if ($users->isNotEmpty()) {
                foreach ($users->take(3) as $u) {
                    QuanDanhGia::updateOrCreate(
                        [
                            'quan_id' => $quan->id,
                            'nguoi_dung_id' => $u->id,
                        ],
                        [
                            'so_sao' => rand(4, 5),
                            'binh_luan' => 'Quán ở Thủ Đức này rất tiện đường, đồ ăn thức uống ngon, phục vụ nhanh.',
                        ]
                    );
                }
            }

            $count++;
        }

        if ($this->command) {
            $this->command->info("Đã tạo thành công {$count} quán ăn thực tế có tọa độ GPS tại Thủ Đức quanh vị trí (10.852161, 106.757911)!");
        }
    }
}
