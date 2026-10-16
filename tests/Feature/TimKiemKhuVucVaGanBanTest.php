<?php

namespace Tests\Feature;

use App\Models\DanhMucMenu;
use App\Models\MonTrongMenu;
use App\Models\Quan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $owner = User::factory()->create();

        // Quán ở Hà Nội (mã lưu '01' có số 0 ở đầu)
        $quanHaNoi = Quan::create([
            'chu_quan_id'          => $owner->id,
            'ten_quan'             => 'Bún Đậu Hàng Khay',
            'slug'                 => 'bun-dau-hang-khay',
            'loai_hinh_kinh_doanh' => 'Đồ ăn vặt',
            'dia_chi_chi_tiet'     => '10 Hàng Khay',
            'tinh_thanh_id'        => '01',
            'ten_tinh_thanh'       => 'Thành phố Hà Nội',
            'quan_huyen_id'        => '001',
            'ten_quan_huyen'       => 'Quận Ba Đình',
            'vi_do'                => 21.0285,
            'kinh_do'              => 105.8542,
            'gia_nho_nhat'         => 35000,
            'gia_lon_nhat'         => 80000,
            'trang_thai'           => 'da_duyet',
        ]);

        $dmHaNoi = DanhMucMenu::create([
            'quan_id'      => $quanHaNoi->id,
            'ten_danh_muc' => 'Món chính',
            'thu_tu'       => 1,
        ]);

        MonTrongMenu::create([
            'danh_muc_id' => $dmHaNoi->id,
            'ten_mon'     => 'Bún đậu mắm tôm đặc biệt',
            'gia'         => 55000,
            'hinh_anh'    => 'https://images.unsplash.com/photo-1564834724105-918b73d1b9e0',
            'con_hang'    => true,
        ]);

        // Quán 1 ở Quận 1, TP.HCM (cách Phố đi bộ Nguyễn Huệ ~0.2km)
        Quan::create([
            'chu_quan_id'          => $owner->id,
            'ten_quan'             => 'Cà Phê Nguyễn Huệ',
            'slug'                 => 'ca-phe-nguyen-hue',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'dia_chi_chi_tiet'     => '68 Nguyễn Huệ',
            'tinh_thanh_id'        => '79',
            'ten_tinh_thanh'       => 'Thành phố Hồ Chí Minh',
            'quan_huyen_id'        => '760',
            'ten_quan_huyen'       => 'Quận 1',
            'vi_do'                => 10.7748,
            'kinh_do'              => 106.7035,
            'gia_nho_nhat'         => 40000,
            'gia_lon_nhat'         => 90000,
            'trang_thai'           => 'da_duyet',
        ]);

        // Quán 2 ở Quận 3, TP.HCM (cách Phố đi bộ Nguyễn Huệ ~2.2km)
        Quan::create([
            'chu_quan_id'          => $owner->id,
            'ten_quan'             => 'Nhà Hàng Võ Văn Tần',
            'slug'                 => 'nha-hang-vo-van-tan',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'dia_chi_chi_tiet'     => '120 Võ Văn Tần',
            'tinh_thanh_id'        => '79',
            'ten_tinh_thanh'       => 'Thành phố Hồ Chí Minh',
            'quan_huyen_id'        => '770',
            'ten_quan_huyen'       => 'Quận 3',
            'vi_do'                => 10.7765,
            'kinh_do'              => 106.6860,
            'gia_nho_nhat'         => 100000,
            'gia_lon_nhat'         => 300000,
            'trang_thai'           => 'da_duyet',
        ]);
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

    public function test_loc_danh_muc_an_vat_va_do_an_vat_dong_bo(): void
    {
        $res1 = $this->get('/kham-pha?danh_muc=' . urlencode('Ăn vặt'));
        $res1->assertStatus(200);
        $this->assertSame(1, $res1->viewData('quans')->total());

        $res2 = $this->get('/kham-pha?danh_muc=' . urlencode('Đồ ăn vặt'));
        $res2->assertStatus(200);
        $this->assertSame(1, $res2->viewData('quans')->total());
    }

    public function test_goi_y_tim_kiem_tra_ve_hinh_anh_mon_an_va_loc_theo_tinh(): void
    {
        $res = $this->getJson('/api/search/suggest?q=' . urlencode('Bún đậu') . '&tinh_thanh_id=1');
        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.quans')
            ->assertJsonCount(1, 'data.mons');

        $mon = $res->json('data.mons.0');
        $this->assertNotEmpty($mon['hinh_anh']);
        $this->assertSame('bun-dau-hang-khay', $mon['quan_slug']);
    }
}
