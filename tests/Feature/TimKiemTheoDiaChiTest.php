<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\DanhMucQuan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimKiemTheoDiaChiTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_search_returns_venues_matching_locality()
    {
        $danhMuc = DanhMucQuan::create([
            'ten_danh_muc' => 'Nhà hàng',
            'slug' => 'nha-hang',
        ]);

        $vaiTro = \App\Models\VaiTro::create([
            'ten' => 'owner'
        ]);

        $user = \App\Models\User::create([
            'ho_ten' => 'Chủ Quán Test 2',
            'email' => 'test_owner2@quanmoi.com',
            'so_dien_thoai' => '0987654322',
            'mat_khau' => bcrypt('password'),
            'vai_tro_id' => $vaiTro->id,
        ]);

        $quanSaiGon = Quan::create([
            'chu_quan_id' => $user->id,
            'ten_quan' => 'Quán Ăn Sài Gòn',
            'slug' => 'quan-an-sai-gon',
            'tinh_thanh_id' => '79', // TP. HCM
            'quan_huyen_id' => '760', // Quận 1
            'danh_muc_id' => $danhMuc->id,
            'trang_thai' => 'da_duyet',
            'dia_chi_chi_tiet' => '123 Nguyễn Huệ',
        ]);

        $quanHaNoi = Quan::create([
            'chu_quan_id' => $user->id,
            'ten_quan' => 'Quán Ăn Hà Nội',
            'slug' => 'quan-an-ha-noi',
            'tinh_thanh_id' => '01', // Hà Nội
            'quan_huyen_id' => '001', // Ba Đình
            'danh_muc_id' => $danhMuc->id,
            'trang_thai' => 'da_duyet',
            'dia_chi_chi_tiet' => '456 Hàng Bạc',
        ]);

        $response = $this->get(route('tim-kiem.index', [
            'type' => 'dia_chi',
            'tinh_thanh_id' => '79',
            'quan_huyen_id' => '760',
            'danh_muc_id' => $danhMuc->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Quán Ăn Sài Gòn');
        $response->assertDontSee('Quán Ăn Hà Nội');
    }
}
