<?php

namespace Database\Seeders;

use App\Models\Quan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MultiRegionQuanSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        $venues = [
            // TP. Hồ Chí Minh
            [
                'ten_quan' => 'Cà phê Trứng Sài Gòn 1985',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
                'dia_chi_chi_tiet' => '12 Nguyễn Huệ',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '760',
                'ten_quan_huyen' => 'Quận 1',
                'phuong_xa_id' => '26734',
                'ten_phuong_xa' => 'Phường Bến Nghé',
                'kinh_do' => 106.7032,
                'vi_do' => 10.7745,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 85000,
                'anh_bia' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=600&q=80',
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
                'kinh_do' => 106.7045,
                'vi_do' => 10.7720,
                'gia_nho_nhat' => 150000,
                'gia_lon_nhat' => 350000,
                'anh_bia' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Cơm Tấm Ba Ghiền Bàn Cờ',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'dia_chi_chi_tiet' => '42 Nguyễn Thiện Thuật',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '770',
                'ten_quan_huyen' => 'Quận 3',
                'phuong_xa_id' => '26815',
                'ten_phuong_xa' => 'Phường 2',
                'kinh_do' => 106.6835,
                'vi_do' => 10.7680,
                'gia_nho_nhat' => 45000,
                'gia_lon_nhat' => 110000,
                'anh_bia' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Billiards Club 24/7 Landmark',
                'loai_hinh_kinh_doanh' => 'Billiards & Giải trí',
                'dia_chi_chi_tiet' => '208 Nguyễn Hữu Cảnh',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '765',
                'ten_quan_huyen' => 'Quận Bình Thạnh',
                'phuong_xa_id' => '26899',
                'ten_phuong_xa' => 'Phường 22',
                'kinh_do' => 106.7218,
                'vi_do' => 10.7950,
                'gia_nho_nhat' => 60000,
                'gia_lon_nhat' => 120000,
                'anh_bia' => 'https://images.unsplash.com/photo-1525610553991-2bede1a236e2?auto=format&fit=crop&w=600&q=80',
            ],
            // Đà Nẵng
            [
                'ten_quan' => 'Mì Quảng Bà Mua Sông Hàn',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'dia_chi_chi_tiet' => '19 Trần Bình Trọng',
                'tinh_thanh_id' => '48',
                'ten_tinh_thanh' => 'Thành phố Đà Nẵng',
                'quan_huyen_id' => '492',
                'ten_quan_huyen' => 'Quận Hải Châu',
                'phuong_xa_id' => '20230',
                'ten_phuong_xa' => 'Phường Hải Châu 1',
                'kinh_do' => 108.2185,
                'vi_do' => 16.0682,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 70000,
                'anh_bia' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'ten_quan' => 'Hải Sản Biển Đông Mỹ Khê',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'dia_chi_chi_tiet' => '120 Võ Nguyên Giáp',
                'tinh_thanh_id' => '48',
                'ten_tinh_thanh' => 'Thành phố Đà Nẵng',
                'quan_huyen_id' => '493',
                'ten_quan_huyen' => 'Quận Sơn Trà',
                'phuong_xa_id' => '20250',
                'ten_phuong_xa' => 'Phường Phước Mỹ',
                'kinh_do' => 108.2472,
                'vi_do' => 16.0595,
                'gia_nho_nhat' => 150000,
                'gia_lon_nhat' => 600000,
                'anh_bia' => 'https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&fit=crop&w=600&q=80',
            ],
        ];

        foreach ($venues as $v) {
            $slug = Str::slug($v['ten_quan']);
            Quan::updateOrCreate(
                ['slug' => $slug],
                array_merge($v, [
                    'chu_quan_id' => $user->id,
                    'mo_ta' => 'Địa điểm ẩm thực nổi bật chất lượng cao được yêu thích.',
                    'so_dien_thoai' => '0901234567',
                    'email' => Str::slug($v['ten_quan']) . '@gmail.com',
                    'gio_mo_cua' => '08:00',
                    'gio_dong_cua' => '23:00',
                    'trang_thai' => 'da_duyet',
                    'is_noi_bat' => true,
                    'is_xac_thuc' => true,
                    'luot_xem' => rand(500, 5000),
                ])
            );
        }
    }
}
