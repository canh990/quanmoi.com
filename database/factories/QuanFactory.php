<?php

namespace Database\Factories;

use App\Models\Quan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class QuanFactory extends Factory
{
    protected $model = Quan::class;

    public function definition(): array
    {
        $tenQuan = 'Quán ' . $this->faker->company();
        $loaiHinh = $this->faker->randomElement(['Nhà hàng', 'Cà phê & Trà', 'Billiards & Giải trí', 'Lẩu & Nướng', 'Ăn vặt', 'Quán Đêm 24/7']);
        
        return [
            'id' => Str::uuid()->toString(),
            'chu_quan_id' => User::factory(),
            'ten_quan' => $tenQuan,
            'loai_hinh_kinh_doanh' => $loaiHinh,
            'slug' => Str::slug($tenQuan . '-' . uniqid()),
            'mo_ta' => $this->faker->paragraph(),
            'so_dien_thoai' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'dia_chi_chi_tiet' => $this->faker->streetAddress(),
            'tinh_thanh_id' => '01',
            'ten_tinh_thanh' => 'Thành phố Hà Nội',
            'quan_huyen_id' => '001',
            'ten_quan_huyen' => 'Quận Ba Đình',
            'phuong_xa_id' => '00001',
            'ten_phuong_xa' => 'Phường Phúc Xá',
            'kinh_do' => $this->faker->longitude(105.7, 105.9),
            'vi_do' => $this->faker->latitude(20.9, 21.1),
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
            'gia_nho_nhat' => $this->faker->numberBetween(20000, 50000),
            'gia_lon_nhat' => $this->faker->numberBetween(100000, 500000),
            'anh_bia' => $this->faker->randomElement([
                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1525610553991-2bede1a236e2?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1551632436-cbf8dd35adfa?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1549488344-c4b9f0460c38?auto=format&fit=crop&w=600&q=80'
            ]),
            'anh_bia_key' => null,
            'trang_thai' => 'da_duyet',
        ];
    }
}
