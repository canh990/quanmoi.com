<?php

namespace Database\Seeders;

use App\Models\Quan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuanGanToiSeeder extends Seeder
{
    /**
     * Seed danh sách các quán ăn có tọa độ thực tế tại Hà Nội, TP.HCM, Đà Nẵng
     * với đầy đủ cự ly (<1km, 1-3km, 3-5km, 5-10km) để test Bản đồ & Gần tôi.
     */
    public function run(): void
    {
        $owner = User::first() ?? User::factory()->create([
            'ho_ten' => 'Chủ Quán Mẫu',
            'email' => 'chuquan.mau@gmail.com',
            'so_dien_thoai' => '0988776655',
            'da_xac_thuc' => true,
        ]);

        $venues = [
            // ==========================================
            // KHU VỰC 1: HÀ NỘI - HOÀN KIẾM / PHỐ CỔ
            // Tâm: (21.0285, 105.8542) Hồ Hoàn Kiếm
            // ==========================================
            [
                'ten_quan' => 'Nộm Bò Khô Bờ Hồ Long Vi Dung',
                'loai_hinh_kinh_doanh' => 'Ăn vặt',
                'dia_chi_chi_tiet' => '23 Hồ Hoàn Kiếm',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '002',
                'ten_quan_huyen' => 'Quận Hoàn Kiếm',
                'phuong_xa_id' => '00040',
                'ten_phuong_xa' => 'Phường Hàng Bạc',
                'vi_do' => 21.0305,
                'kinh_do' => 105.8530,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 50000,
                'anh_bia' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Cà Phê Trứng Giảng 39 Nguyễn Hữu Huân',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
                'dia_chi_chi_tiet' => '39 Nguyễn Hữu Huân',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '002',
                'ten_quan_huyen' => 'Quận Hoàn Kiếm',
                'phuong_xa_id' => '00043',
                'ten_phuong_xa' => 'Phường Lý Thái Tổ',
                'vi_do' => 21.0315,
                'kinh_do' => 105.8550,
                'gia_nho_nhat' => 30000,
                'gia_lon_nhat' => 60000,
                'anh_bia' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Phở Gia Truyền Bát Đàn',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'dia_chi_chi_tiet' => '49 Bát Đàn',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '002',
                'ten_quan_huyen' => 'Quận Hoàn Kiếm',
                'phuong_xa_id' => '00046',
                'ten_phuong_xa' => 'Phường Cửa Đông',
                'vi_do' => 21.0336,
                'kinh_do' => 105.8475,
                'gia_nho_nhat' => 50000,
                'gia_lon_nhat' => 80000,
                'anh_bia' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Bia Hơi Phố Cổ Tạ Hiện',
                'loai_hinh_kinh_doanh' => 'Quán nhậu',
                'dia_chi_chi_tiet' => '18 Tạ Hiện',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '002',
                'ten_quan_huyen' => 'Quận Hoàn Kiếm',
                'phuong_xa_id' => '00049',
                'ten_phuong_xa' => 'Phường Hàng Buồm',
                'vi_do' => 21.0345,
                'kinh_do' => 105.8525,
                'gia_nho_nhat' => 80000,
                'gia_lon_nhat' => 250000,
                'anh_bia' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Bún Chả Hương Liên - Obama',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'dia_chi_chi_tiet' => '24 Lê Văn Hưu',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '007',
                'ten_quan_huyen' => 'Quận Hai Bà Trưng',
                'phuong_xa_id' => '00217',
                'ten_phuong_xa' => 'Phường Phạm Đình Hổ',
                'vi_do' => 21.0168,
                'kinh_do' => 105.8528,
                'gia_nho_nhat' => 50000,
                'gia_lon_nhat' => 120000,
                'anh_bia' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Lẩu Ếch Dũng Béo Lò Đúc',
                'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
                'dia_chi_chi_tiet' => '102 Lò Đúc',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '007',
                'ten_quan_huyen' => 'Quận Hai Bà Trưng',
                'phuong_xa_id' => '00220',
                'ten_phuong_xa' => 'Phường Đống Mác',
                'vi_do' => 21.0110,
                'kinh_do' => 105.8590,
                'gia_nho_nhat' => 150000,
                'gia_lon_nhat' => 350000,
                'anh_bia' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
            ],

            // ==========================================
            // KHU VỰC 2: HÀ NỘI - CẦU GIẤY / DUY TÂN
            // Tâm: (21.0310, 105.7820) Khu công nghệ Duy Tân
            // ==========================================
            [
                'ten_quan' => 'The Coffee House - Duy Tân',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
                'dia_chi_chi_tiet' => 'Số 1 Duy Tân',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '005',
                'ten_quan_huyen' => 'Quận Cầu Giấy',
                'phuong_xa_id' => '00169',
                'ten_phuong_xa' => 'Phường Dịch Vọng Hậu',
                'vi_do' => 21.0315,
                'kinh_do' => 105.7825,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 75000,
                'anh_bia' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Bún Bò Huế O Xuân - Trần Thái Tông',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'dia_chi_chi_tiet' => '68 Trần Thái Tông',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '005',
                'ten_quan_huyen' => 'Quận Cầu Giấy',
                'phuong_xa_id' => '00166',
                'ten_phuong_xa' => 'Phường Dịch Vọng',
                'vi_do' => 21.0335,
                'kinh_do' => 105.7870,
                'gia_nho_nhat' => 45000,
                'gia_lon_nhat' => 75000,
                'anh_bia' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Bánh Tráng Hoàng Bèo Cầu Giấy',
                'loai_hinh_kinh_doanh' => 'Ăn vặt',
                'dia_chi_chi_tiet' => '40 Duy Tân',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '005',
                'ten_quan_huyen' => 'Quận Cầu Giấy',
                'phuong_xa_id' => '00169',
                'ten_phuong_xa' => 'Phường Dịch Vọng Hậu',
                'vi_do' => 21.0300,
                'kinh_do' => 105.7845,
                'gia_nho_nhat' => 40000,
                'gia_lon_nhat' => 85000,
                'anh_bia' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Gogi House - Nướng Hàn Quốc Cầu Giấy',
                'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
                'dia_chi_chi_tiet' => '103 Xuân Thủy',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '005',
                'ten_quan_huyen' => 'Quận Cầu Giấy',
                'phuong_xa_id' => '00169',
                'ten_phuong_xa' => 'Phường Dịch Vọng Hậu',
                'vi_do' => 21.0365,
                'kinh_do' => 105.7885,
                'gia_nho_nhat' => 200000,
                'gia_lon_nhat' => 450000,
                'anh_bia' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
            ],

            // ==========================================
            // KHU VỰC 3: TP. HỒ CHÍ MINH - QUẬN 1 & QUẬN 3
            // Tâm: (10.7769, 106.7009) Phố đi bộ Nguyễn Huệ
            // ==========================================
            [
                'ten_quan' => 'Cơm Tấm Cali - Phố Đi Bộ Nguyễn Huệ',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'dia_chi_chi_tiet' => '48 Nguyễn Huệ',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '760',
                'ten_quan_huyen' => 'Quận 1',
                'phuong_xa_id' => '26734',
                'ten_phuong_xa' => 'Phường Bến Nghé',
                'vi_do' => 10.7745,
                'kinh_do' => 106.7032,
                'gia_nho_nhat' => 45000,
                'gia_lon_nhat' => 95000,
                'anh_bia' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Highlands Coffee - Bưu Điện Thành Phố',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
                'dia_chi_chi_tiet' => '2 Công Xã Paris',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '760',
                'ten_quan_huyen' => 'Quận 1',
                'phuong_xa_id' => '26734',
                'ten_phuong_xa' => 'Phường Bến Nghé',
                'vi_do' => 10.7798,
                'kinh_do' => 106.6998,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 79000,
                'anh_bia' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Bánh Mì Huỳnh Hoa Sài Gòn',
                'loai_hinh_kinh_doanh' => 'Ăn vặt',
                'dia_chi_chi_tiet' => '26 Lê Thị Riêng',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '760',
                'ten_quan_huyen' => 'Quận 1',
                'phuong_xa_id' => '26740',
                'ten_phuong_xa' => 'Phường Bến Thành',
                'vi_do' => 10.7715,
                'kinh_do' => 106.6912,
                'gia_nho_nhat' => 58000,
                'gia_lon_nhat' => 70000,
                'anh_bia' => 'https://images.unsplash.com/photo-1509722747041-616f39b57569?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Lẩu Bò Cô Thảo - Bến Nghé',
                'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
                'dia_chi_chi_tiet' => '84 Hồ Tùng Mậu',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '760',
                'ten_quan_huyen' => 'Quận 1',
                'phuong_xa_id' => '26734',
                'ten_phuong_xa' => 'Phường Bến Nghé',
                'vi_do' => 10.7720,
                'kinh_do' => 106.7045,
                'gia_nho_nhat' => 180000,
                'gia_lon_nhat' => 380000,
                'anh_bia' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Phở Lệ - Võ Văn Tần Quận 3',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'dia_chi_chi_tiet' => '303 Võ Văn Tần',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '770',
                'ten_quan_huyen' => 'Quận 3',
                'phuong_xa_id' => '26818',
                'ten_phuong_xa' => 'Phường 5',
                'vi_do' => 10.7735,
                'kinh_do' => 106.6855,
                'gia_nho_nhat' => 65000,
                'gia_lon_nhat' => 95000,
                'anh_bia' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=600&q=80',
            ],

            // ==========================================
            // KHU VỰC 4: ĐÀ NẴNG - HẢI CHÂU & SƠN TRÀ
            // Tâm: (16.0680, 108.2200) Cầu Rồng / Bạch Đằng
            // ==========================================
            [
                'ten_quan' => 'Mì Quảng Bà Mua - Trần Bình Trọng',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'dia_chi_chi_tiet' => '19 Trần Bình Trọng',
                'tinh_thanh_id' => '48',
                'ten_tinh_thanh' => 'Thành phố Đà Nẵng',
                'quan_huyen_id' => '492',
                'ten_quan_huyen' => 'Quận Hải Châu',
                'phuong_xa_id' => '20230',
                'ten_phuong_xa' => 'Phường Hải Châu 1',
                'vi_do' => 16.0682,
                'kinh_do' => 108.2185,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 65000,
                'anh_bia' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Cộng Cà Phê - Bạch Đằng Sông Hàn',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
                'dia_chi_chi_tiet' => '96 Bạch Đằng',
                'tinh_thanh_id' => '48',
                'ten_tinh_thanh' => 'Thành phố Đà Nẵng',
                'quan_huyen_id' => '492',
                'ten_quan_huyen' => 'Quận Hải Châu',
                'phuong_xa_id' => '20230',
                'ten_phuong_xa' => 'Phường Hải Châu 1',
                'vi_do' => 16.0675,
                'kinh_do' => 108.2240,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 69000,
                'anh_bia' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Bánh Xèo Bà Dưỡng Hoàng Diệu',
                'loai_hinh_kinh_doanh' => 'Ăn vặt',
                'dia_chi_chi_tiet' => '280/23 Hoàng Diệu',
                'tinh_thanh_id' => '48',
                'ten_tinh_thanh' => 'Thành phố Đà Nẵng',
                'quan_huyen_id' => '492',
                'ten_quan_huyen' => 'Quận Hải Châu',
                'phuong_xa_id' => '20239',
                'ten_phuong_xa' => 'Phường Phước Ninh',
                'vi_do' => 16.0610,
                'kinh_do' => 108.2170,
                'gia_nho_nhat' => 30000,
                'gia_lon_nhat' => 80000,
                'anh_bia' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Hải Sản Biển Đông - Võ Nguyên Giáp Mỹ Khê',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'dia_chi_chi_tiet' => '120 Võ Nguyên Giáp',
                'tinh_thanh_id' => '48',
                'ten_tinh_thanh' => 'Thành phố Đà Nẵng',
                'quan_huyen_id' => '493',
                'ten_quan_huyen' => 'Quận Sơn Trà',
                'phuong_xa_id' => '20250',
                'ten_phuong_xa' => 'Phường Phước Mỹ',
                'vi_do' => 16.0595,
                'kinh_do' => 108.2472,
                'gia_nho_nhat' => 150000,
                'gia_lon_nhat' => 650000,
                'anh_bia' => 'https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&fit=crop&w=600&q=80',
            ],
        ];

        $count = 0;
        foreach ($venues as $v) {
            $slug = Str::slug($v['ten_quan']);
            Quan::updateOrCreate(
                ['slug' => $slug],
                array_merge($v, [
                    'chu_quan_id' => $owner->id,
                    'mo_ta' => 'Quán ăn ngon nổi tiếng được người dân địa phương và du khách yêu thích đánh giá cao.',
                    'so_dien_thoai' => '09' . rand(10000000, 99999999),
                    'email' => Str::slug($v['ten_quan']) . '@quanmoi.vn',
                    'gio_mo_cua' => '07:30',
                    'gio_dong_cua' => '23:00',
                    'trang_thai' => 'da_duyet',
                    'is_noi_bat' => true,
                    'is_xac_thuc' => true,
                    'luot_xem' => rand(500, 8500),
                ])
            );
            $count++;
        }

        if ($this->command) {
            $this->command->info("Đã tạo thành công {$count} quán ăn thực tế có tọa độ GPS tại Hà Nội, TP.HCM và Đà Nẵng!");
        }
    }
}
