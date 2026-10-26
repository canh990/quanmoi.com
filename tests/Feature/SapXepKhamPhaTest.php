<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\QuanDanhGia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SapXepKhamPhaTest extends TestCase
{
    use RefreshDatabase;

    public function test_hien_thi_day_du_cac_tieu_chi_sap_xep()
    {
        $response = $this->get(route('kham-pha'));

        $response->assertStatus(200);
        $response->assertSee('Độ liên quan / Mặc định');
        $response->assertSee('Gần tôi nhất (GPS)');
        $response->assertSee('Đánh giá cao nhất');
        $response->assertSee('Xem nhiều nhất');
        $response->assertSee('Giá: Thấp đến cao');
        $response->assertSee('Giá: Cao đến thấp');
        $response->assertSee('Mới đăng gần đây');
    }

    public function test_sap_xep_danh_gia_cao_nhat()
    {
        $owner = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Quán A: 5 sao
        $quanA = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán A Năm Sao',
            'trang_thai' => 'da_duyet',
        ]);
        QuanDanhGia::create([
            'quan_id' => $quanA->id,
            'nguoi_dung_id' => $user1->id,
            'so_sao' => 5,
            'binh_luan' => 'Tuyệt vời',
        ]);

        // Quán B: 2 sao
        $quanB = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán B Hai Sao',
            'trang_thai' => 'da_duyet',
        ]);
        QuanDanhGia::create([
            'quan_id' => $quanB->id,
            'nguoi_dung_id' => $user2->id,
            'so_sao' => 2,
            'binh_luan' => 'Bình thường',
        ]);

        $response = $this->get(route('kham-pha', ['sap_xep' => 'rating_desc']));
        $response->assertStatus(200);

        $quans = $response->viewData('quans');
        $this->assertEquals($quanA->id, $quans->first()->id);
    }

    public function test_sap_xep_gia_thap_den_cao()
    {
        $owner = User::factory()->create();

        $quanDat = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Đắt Tiền',
            'gia_nho_nhat' => 200000,
            'gia_lon_nhat' => 500000,
            'trang_thai' => 'da_duyet',
        ]);

        $quanRe = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Bình Dân',
            'gia_nho_nhat' => 25000,
            'gia_lon_nhat' => 50000,
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->get(route('kham-pha', ['sap_xep' => 'price_asc']));
        $response->assertStatus(200);

        $quans = $response->viewData('quans');
        $this->assertEquals($quanRe->id, $quans->first()->id);
    }

    public function test_sap_xep_gia_cao_den_thap()
    {
        $owner = User::factory()->create();

        $quanBinhDan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Bình Dân',
            'gia_nho_nhat' => 25000,
            'gia_lon_nhat' => 60000,
            'trang_thai' => 'da_duyet',
        ]);

        $quanSangTrong = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Nhà Hàng Sang Trọng',
            'gia_nho_nhat' => 300000,
            'gia_lon_nhat' => 1500000,
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->get(route('kham-pha', ['sap_xep' => 'price_desc']));
        $response->assertStatus(200);

        $quans = $response->viewData('quans');
        $this->assertEquals($quanSangTrong->id, $quans->first()->id);
    }

    public function test_sap_xep_xem_nhieu_nhat()
    {
        $owner = User::factory()->create();

        $quanItXem = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Ít Xem',
            'luot_xem' => 10,
            'trang_thai' => 'da_duyet',
        ]);

        $quanNhieuXem = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Triệu View',
            'luot_xem' => 9999,
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->get(route('kham-pha', ['sap_xep' => 'view_desc']));
        $response->assertStatus(200);

        $quans = $response->viewData('quans');
        $this->assertEquals($quanNhieuXem->id, $quans->first()->id);
    }

    public function test_sap_xep_moi_dang_gan_day()
    {
        $owner = User::factory()->create();

        $quanCu = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Cũ',
            'created_at' => now()->subMonths(3),
            'trang_thai' => 'da_duyet',
        ]);

        $quanMoi = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Mới Đăng',
            'created_at' => now(),
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->get(route('kham-pha', ['sap_xep' => 'created_desc']));
        $response->assertStatus(200);

        $quans = $response->viewData('quans');
        $this->assertEquals($quanMoi->id, $quans->first()->id);
    }
}
