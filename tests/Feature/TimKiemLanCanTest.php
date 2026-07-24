<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\DanhMucQuan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimKiemLanCanTest extends TestCase
{
    use RefreshDatabase;

    public function test_nearby_search_returns_venues_within_radius()
    {
        $danhMuc = DanhMucQuan::create([
            'ten_danh_muc' => 'Cà phê',
            'slug' => 'ca-phe',
        ]);

        $vaiTro = \App\Models\VaiTro::create([
            'ten' => 'owner'
        ]);

        $user = \App\Models\User::create([
            'ho_ten' => 'Chủ Quán Test',
            'email' => 'test_owner@quanmoi.com',
            'so_dien_thoai' => '0987654321',
            'mat_khau' => bcrypt('password'),
            'vai_tro_id' => $vaiTro->id,
        ]);

        // Tạo quán trong bán kính (khoảng 1km từ tọa độ test)
        $quanGan = Quan::create([
            'chu_quan_id' => $user->id,
            'ten_quan' => 'Quán Cà Phê Gần',
            'slug' => 'quan-ca-phe-gan',
            'vi_do' => 10.7769,
            'kinh_do' => 106.7009,
            'danh_muc_id' => $danhMuc->id,
            'trang_thai' => 'da_duyet',
            'dia_chi_chi_tiet' => '123 Nguyễn Huệ',
        ]);

        // Tạo quán xa bán kính (khoảng 15km)
        $quanXa = Quan::create([
            'chu_quan_id' => $user->id,
            'ten_quan' => 'Quán Cà Phê Xa',
            'slug' => 'quan-ca-phe-xa',
            'vi_do' => 10.9000,
            'kinh_do' => 106.8000,
            'danh_muc_id' => $danhMuc->id,
            'trang_thai' => 'da_duyet',
            'dia_chi_chi_tiet' => '456 Đại lộ Bình Dương',
        ]);

        // Tọa độ tìm kiếm (Bưu điện Trung tâm Sài Gòn)
        $response = $this->get(route('tim-kiem.index', [
            'type' => 'lan_can',
            'latitude' => 10.7798,
            'longitude' => 106.6990,
            'ban_kinh' => 3,
            'danh_muc_id' => $danhMuc->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Quán Cà Phê Gần');
        $response->assertDontSee('Quán Cà Phê Xa');
    }
}
