<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tìm kiếm theo khu vực dùng mã hành chính của provinces.open-api.vn (v1):
 * Hà Nội = 1, TP.HCM = 79, Quận 1 = 760, Quận Ba Đình = 1.
 */
class TimKiemKhuVucVaGanBanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\VaiTroSeeder::class);
        $this->seed(\Database\Seeders\MultiRegionQuanSeeder::class);
        \App\Models\Quan::factory()->count(5)->create();

        $this->mock(\App\Services\DiaChiService::class, function ($mock) {
            $mock->shouldReceive('getTinhThanh')->andReturn([
                ['name' => 'Thành phố Hà Nội', 'code' => 1],
                ['name' => 'Thành phố Hồ Chí Minh', 'code' => 79],
                ['name' => 'Thành phố Đà Nẵng', 'code' => 48],
            ]);
            $mock->shouldReceive('getQuanHuyen')->andReturnUsing(function ($code) {
                if ((int) $code === 79) {
                    return [
                        ['name' => 'Quận 1', 'code' => 760],
                        ['name' => 'Quận 3', 'code' => 770],
                    ];
                }
                if ((int) $code === 1) {
                    return [
                        ['name' => 'Quận Ba Đình', 'code' => 1],
                    ];
                }
                return [];
            });
        });
    }

    public function test_tim_kiem_theo_ma_tinh_ha_noi_khop_ca_ma_co_so_0(): void
    {
        // API trả mã 1, dữ liệu mẫu lưu '01' -> vẫn phải khớp
        $response = $this->get('/kham-pha?tinh_thanh_id=1');

        $response->assertStatus(200);
        $response->assertSee('Thành phố Hà Nội');
        $this->assertGreaterThan(0, $response->viewData('quans')->total());
    }

    public function test_tim_kiem_theo_ma_tinh_ho_chi_minh(): void
    {
        $response = $this->get('/kham-pha?tinh_thanh_id=79');

        $response->assertStatus(200);
        $response->assertSee('Thành phố Hồ Chí Minh');

        $quans = $response->viewData('quans');
        $this->assertGreaterThan(0, $quans->total());
        foreach ($quans as $quan) {
            $this->assertSame(79, (int) $quan->tinh_thanh_id);
        }
    }

    public function test_tim_kiem_theo_ma_quan_huyen(): void
    {
        $response = $this->get('/kham-pha?tinh_thanh_id=79&quan_huyen_id=760');

        $response->assertStatus(200);
        $response->assertSee('Quận 1');

        $quans = $response->viewData('quans');
        $this->assertGreaterThan(0, $quans->total());
        foreach ($quans as $quan) {
            $this->assertSame(760, (int) $quan->quan_huyen_id);
        }
    }

    public function test_link_cu_dung_ma_viet_tat_van_hoat_dong(): void
    {
        $response = $this->get('/kham-pha?tinh_thanh_id=HCM');

        $response->assertStatus(200);
        $this->assertSame('79', $response->viewData('tinhThanhId'));
        $this->assertGreaterThan(0, $response->viewData('quans')->total());
    }

    public function test_tim_kiem_gan_ban_sap_xep_near_me(): void
    {
        // Phố đi bộ Nguyễn Huệ, Quận 1
        $response = $this->get('/kham-pha?lat=10.7745&lon=106.7032&sap_xep=near_me');

        $response->assertStatus(200);
        $response->assertSee('Gần bạn');

        $quans = $response->viewData('quans');
        $this->assertNotEmpty($quans);

        $distances = $quans->getCollection()->pluck('khoang_cach_km')->filter(fn ($d) => $d !== null)->values();
        $this->assertNotEmpty($distances);
        $this->assertLessThanOrEqual(5.0, (float) $distances->first());
        $this->assertSame($distances->sort()->values()->all(), $distances->all(), 'Kết quả phải sắp xếp gần -> xa');
    }

    public function test_tim_kiem_gan_ban_theo_ban_kinh(): void
    {
        $response = $this->get('/kham-pha?lat=10.7745&lon=106.7032&ban_kinh=3&sap_xep=near_me');

        $response->assertStatus(200);

        $quans = $response->viewData('quans');
        $this->assertNotEmpty($quans);
        foreach ($quans as $quan) {
            $this->assertLessThanOrEqual(3.05, (float) $quan->khoang_cach_km);
        }
    }
}
