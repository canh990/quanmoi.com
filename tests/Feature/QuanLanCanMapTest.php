<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuanLanCanMapTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create([
            'email' => 'secret-owner@example.com',
            'so_dien_thoai' => '0999888777',
        ]);
    }

    public function test_co_quan_gan_vi_tri_gps_va_sap_xep_khoang_cach_tang_dan(): void
    {
        // Tâm người dùng: Hồ Hoàn Kiếm, Hà Nội (21.0285, 105.8542)
        $quanGanNhat = Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Phở Hàng Trống (Cách ~0.3km)',
            'slug' => 'pho-hang-trong',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'dia_chi_chi_tiet' => '8 Hàng Trống',
            'ten_phuong_xa' => 'Phường Hàng Trống',
            'ten_quan_huyen' => 'Quận Hoàn Kiếm',
            'ten_tinh_thanh' => 'Thành phố Hà Nội',
            'vi_do' => 21.0295,
            'kinh_do' => 105.8515,
            'gia_nho_nhat' => 45000,
            'gia_lon_nhat' => 85000,
            'trang_thai' => 'da_duyet',
        ]);

        $quanXaHon = Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Bún Chả Hàng Mành (Cách ~0.8km)',
            'slug' => 'bun-cha-hang-manh',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'dia_chi_chi_tiet' => '1 Hàng Mành',
            'ten_phuong_xa' => 'Phường Hàng Gai',
            'ten_quan_huyen' => 'Quận Hoàn Kiếm',
            'ten_tinh_thanh' => 'Thành phố Hà Nội',
            'vi_do' => 21.0318,
            'kinh_do' => 105.8472,
            'gia_nho_nhat' => 50000,
            'gia_lon_nhat' => 90000,
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=3');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.radius_km', 3)
            ->assertJsonPath('meta.total', 2);

        $data = $response->json('data');
        $this->assertCount(2, $data);

        // Kiểm tra sắp xếp khoảng cách tăng dần
        $this->assertSame($quanGanNhat->id, $data[0]['id']);
        $this->assertSame($quanXaHon->id, $data[1]['id']);
        $this->assertLessThan($data[1]['khoang_cach_km'], $data[0]['khoang_cach_km']);

        // Kiểm tra đầy đủ các trường yêu cầu và không rò rỉ thông tin riêng tư của chủ quán
        $first = $data[0];
        $this->assertArrayHasKey('id', $first);
        $this->assertArrayHasKey('ten_quan', $first);
        $this->assertArrayHasKey('latitude', $first);
        $this->assertArrayHasKey('longitude', $first);
        $this->assertArrayHasKey('dia_chi', $first);
        $this->assertArrayHasKey('anh_dai_dien', $first);
        $this->assertArrayHasKey('khoang_cach_km', $first);
        $this->assertArrayHasKey('url', $first);
        $this->assertStringContainsString('/quan/pho-hang-trong', $first['url']);

        $this->assertArrayNotHasKey('chu_quan_id', $first);
        $this->assertArrayNotHasKey('email', $first);
    }

    public function test_khong_co_quan_trong_ban_kinh(): void
    {
        // Quán ở TP.HCM
        Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Cơm Tấm Sài Gòn',
            'slug' => 'com-tam-sai-gon',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'dia_chi_chi_tiet' => '100 Nguyễn Huệ',
            'ten_quan_huyen' => 'Quận 1',
            'ten_tinh_thanh' => 'TP. Hồ Chí Minh',
            'vi_do' => 10.7745,
            'kinh_do' => 106.7032,
            'trang_thai' => 'da_duyet',
        ]);

        // Người dùng đang ở Hà Nội quét bán kính 5 km
        $response = $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=5');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.total', 0)
            ->assertJsonCount(0, 'data');
    }

    public function test_loai_bo_quan_thieu_toa_do_hoac_chua_duyet_hoac_da_xoa(): void
    {
        // 1. Quán thiếu tọa độ
        Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Quán Thiếu Tọa Độ',
            'slug' => 'quan-thieu-toa-do',
            'dia_chi_chi_tiet' => '12 Hàng Khay',
            'vi_do' => null,
            'kinh_do' => null,
            'trang_thai' => 'da_duyet',
        ]);

        // 2. Quán chưa duyệt
        Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Quán Chờ Duyệt',
            'slug' => 'quan-cho-duyet',
            'dia_chi_chi_tiet' => '15 Hàng Khay',
            'vi_do' => 21.0285,
            'kinh_do' => 105.8542,
            'trang_thai' => 'chua_duyet',
        ]);

        // 3. Quán bị khóa
        Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Quán Bị Khóa',
            'slug' => 'quan-bi-khoa',
            'dia_chi_chi_tiet' => '18 Hàng Khay',
            'vi_do' => 21.0285,
            'kinh_do' => 105.8542,
            'trang_thai' => 'bi_khoa',
        ]);

        // 4. Quán đã xóa mềm
        $quanXoa = Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Quán Đã Xóa',
            'slug' => 'quan-da-xoa',
            'dia_chi_chi_tiet' => '20 Hàng Khay',
            'vi_do' => 21.0285,
            'kinh_do' => 105.8542,
            'trang_thai' => 'da_duyet',
        ]);
        $quanXoa->delete();

        $response = $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=5');

        $response->assertOk()
            ->assertJsonPath('meta.total', 0)
            ->assertJsonCount(0, 'data');
    }

    public function test_toa_do_hoac_ban_kinh_dau_vao_khong_hop_le(): void
    {
        // Thiếu tọa độ
        $this->getJson('/api/ban-do/quan-lan-can')
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        // Vĩ độ vượt quá [-90, 90]
        $this->getJson('/api/ban-do/quan-lan-can?lat=105.85&lng=21.02&radius=5')
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        // Bán kính không nằm trong danh sách cho phép [1, 3, 5, 10]
        $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=999')
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_thay_doi_ban_kinh_tim_kiem_1_3_5_10_km(): void
    {
        // Tâm: (21.0285, 105.8542)
        // Quán A cách ~0.5 km
        Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Quán Cách 0.5km',
            'slug' => 'quan-0-5-km',
            'dia_chi_chi_tiet' => 'Số 1 Đinh Tiên Hoàng',
            'vi_do' => 21.0310,
            'kinh_do' => 105.8530,
            'trang_thai' => 'da_duyet',
        ]);

        // Quán B cách ~2.3 km
        Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Quán Cách 2.3km',
            'slug' => 'quan-2-3-km',
            'dia_chi_chi_tiet' => 'Số 50 Kim Mã',
            'vi_do' => 21.0315,
            'kinh_do' => 105.8320,
            'trang_thai' => 'da_duyet',
        ]);

        // Quán C cách ~7.2 km
        Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Quán Cách 7.2km',
            'slug' => 'quan-7-2-km',
            'dia_chi_chi_tiet' => 'Cầu Giấy',
            'vi_do' => 21.0360,
            'kinh_do' => 105.7850,
            'trang_thai' => 'da_duyet',
        ]);

        $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=1')
            ->assertOk()
            ->assertJsonPath('meta.total', 1);

        $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=3')
            ->assertOk()
            ->assertJsonPath('meta.total', 2);

        $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=10')
            ->assertOk()
            ->assertJsonPath('meta.total', 3);
    }

    public function test_database_co_nhieu_ban_ghi_va_phan_trang(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            Quan::create([
                'chu_quan_id' => $this->owner->id,
                'ten_quan' => "Quán Lân Cận Số {$i}",
                'slug' => "quan-lan-can-so-{$i}",
                'dia_chi_chi_tiet' => "Số {$i} Phố Huế",
                'vi_do' => 21.0285 + ($i * 0.0005),
                'kinh_do' => 105.8542 + ($i * 0.0005),
                'trang_thai' => 'da_duyet',
            ]);
        }

        $resPage1 = $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=5&per_page=15&page=1');
        $resPage1->assertOk()
            ->assertJsonPath('meta.total', 30)
            ->assertJsonPath('meta.per_page', 15)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonCount(15, 'data');

        $resPage2 = $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=5&per_page=15&page=2');
        $resPage2->assertOk()
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonCount(15, 'data');
    }

    public function test_trang_kham_pha_hien_thi_chuc_nang_quet_quan_lan_can(): void
    {
        $response = $this->get('/kham-pha');

        $response->assertOk()
            ->assertSee('Bản đồ');
    }

    public function test_trang_ban_do_chuyen_dung_hoat_dong(): void
    {
        $response = $this->get('/ban-do');

        $response->assertOk()
            ->assertSee('Bản đồ Ẩm thực')
            ->assertSee('bando-leaflet-map');
    }

    public function test_loc_quan_lan_can_theo_danh_muc(): void
    {
        Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Cà Phê Hồ Gươm',
            'slug' => 'ca-phe-ho-guom',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'dia_chi_chi_tiet' => 'Số 10 Hàng Đào',
            'vi_do' => 21.0310,
            'kinh_do' => 105.8515,
            'trang_thai' => 'da_duyet',
        ]);

        Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Lẩu Nướng Phố Cổ',
            'slug' => 'lau-nuong-pho-co',
            'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
            'dia_chi_chi_tiet' => 'Số 20 Hàng Bài',
            'vi_do' => 21.0250,
            'kinh_do' => 105.8525,
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=5&danh_muc=' . urlencode('Cà phê & Trà'));

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.ten_quan', 'Cà Phê Hồ Gươm');
    }

    public function test_loc_quan_lan_can_theo_tu_khoa_ten_quan_hoac_mon_an(): void
    {
        $quanPho = Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Phở Thìn Bờ Hồ',
            'slug' => 'pho-thin-bo-ho',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'dia_chi_chi_tiet' => '61 Đinh Tiên Hoàng',
            'vi_do' => 21.0305,
            'kinh_do' => 105.8535,
            'trang_thai' => 'da_duyet',
        ]);

        $quanTraSua = Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Tiệm Trà Chanh Nhà Thờ',
            'slug' => 'tiem-tra-chanh-nha-tho',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'dia_chi_chi_tiet' => 'Số 2 Nhà Thờ',
            'vi_do' => 21.0288,
            'kinh_do' => 105.8495,
            'trang_thai' => 'da_duyet',
        ]);

        $danhMucMenu = \App\Models\DanhMucMenu::create([
            'quan_id' => $quanTraSua->id,
            'ten_danh_muc' => 'Đồ uống đặc biệt',
            'thu_tu' => 1,
        ]);

        \App\Models\MonTrongMenu::create([
            'danh_muc_id' => $danhMucMenu->id,
            'ten_mon' => 'Trà Ô Long Kem Cheese',
            'gia' => 35000,
            'trang_thai' => 'con_hang',
        ]);

        // 1. Tìm theo tên quán
        $resByName = $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=5&q=' . urlencode('Phở Thìn'));
        $resByName->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $quanPho->id);

        // 2. Tìm theo tên món ăn trong thực đơn
        $resByDish = $this->getJson('/api/ban-do/quan-lan-can?lat=21.0285&lng=105.8542&radius=5&q=' . urlencode('Kem Cheese'));
        $resByDish->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $quanTraSua->id);
    }

    public function test_geocode_tra_ve_goi_y_quan_an_trong_database_va_404_khi_khong_khop(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response([], 200),
        ]);

        Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Bánh Mì Hội An Đặc Biệt',
            'slug' => 'banh-mi-hoi-an-dac-biet',
            'loai_hinh_kinh_doanh' => 'Ăn vặt',
            'dia_chi_chi_tiet' => '25 Hàng Cá',
            'ten_quan_huyen' => 'Quận Hoàn Kiếm',
            'vi_do' => 21.035200,
            'kinh_do' => 105.848900,
            'trang_thai' => 'da_duyet',
        ]);

        // Khi tìm tên quán có trong DB (dù Nominatim trả rỗng) -> vẫn trả về quán khớp tọa độ thật
        $resMatch = $this->getJson('/api/ban-do/geocode?address=' . urlencode('Bánh Mì Hội An'));
        $resMatch->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('provider', 'database')
            ->assertJsonCount(1, 'venues')
            ->assertJsonPath('venues.0.ten_quan', 'Bánh Mì Hội An Đặc Biệt');

        // Khi địa chỉ không khớp cả Nominatim lẫn DB -> trả về 404 thay vì tọa độ Hồ Gươm giả
        $resNotFound = $this->getJson('/api/ban-do/geocode?address=' . urlencode('Địa chỉ hoàn toàn không tồn tại xyz987'));
        $resNotFound->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_chu_quan_co_the_cap_nhat_toa_do_vi_tri_quan(): void
    {
        $roleId = \Illuminate\Support\Facades\DB::table('vai_tro')->where('ten', 'chu_quan')->value('id');
        if (! $roleId) {
            $roleId = (string) \Illuminate\Support\Str::uuid();
            \Illuminate\Support\Facades\DB::table('vai_tro')->insert([
                'id' => $roleId,
                'ten' => 'chu_quan',
            ]);
        }
        $this->owner->forceFill(['vai_tro_id' => $roleId])->save();

        $quan = Quan::create([
            'chu_quan_id' => $this->owner->id,
            'ten_quan' => 'Quán Cơm Gia Đình',
            'slug' => 'quan-com-gia-dinh',
            'so_dien_thoai' => '0901234567',
            'dia_chi_chi_tiet' => 'Số 15 Lý Thường Kiệt',
            'gio_mo_cua' => '07:00',
            'gio_dong_cua' => '21:00',
            'vi_do' => 10.776900,
            'kinh_do' => 106.700900,
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->actingAs($this->owner)
            ->putJson("/chu-quan/quan/{$quan->slug}", [
                'so_dien_thoai' => '0909888777',
                'dia_chi_chi_tiet' => 'Số 88 Trần Hưng Đạo',
                'gio_mo_cua' => '08:00',
                'gio_dong_cua' => '22:00',
                'vi_do' => 21.024500,
                'kinh_do' => 105.841200,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $quan->refresh();
        $this->assertEqualsWithDelta(21.024500, (float) $quan->vi_do, 0.0001);
        $this->assertEqualsWithDelta(105.841200, (float) $quan->kinh_do, 0.0001);
        $this->assertSame('Số 88 Trần Hưng Đạo', $quan->dia_chi_chi_tiet);
    }
}
