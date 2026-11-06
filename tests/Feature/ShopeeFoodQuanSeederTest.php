<?php

namespace Tests\Feature;

use App\Models\DanhMucQuan;
use App\Models\Quan;
use App\Models\User;
use Database\Seeders\ShopeeFoodQuanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopeeFoodQuanSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Chuẩn bị danh mục quán mẫu
        DanhMucQuan::firstOrCreate(
            ['slug' => 'nha-hang'],
            ['ten_danh_muc' => 'Nhà hàng', 'is_hien_thi' => true, 'is_noi_bat' => true]
        );
        DanhMucQuan::firstOrCreate(
            ['slug' => 'ca-phe-tra'],
            ['ten_danh_muc' => 'Cà phê & Trà', 'is_hien_thi' => true, 'is_noi_bat' => true]
        );
        DanhMucQuan::firstOrCreate(
            ['slug' => 'tra-sua-nuoc-ep'],
            ['ten_danh_muc' => 'Trà sữa & Nước ép', 'is_hien_thi' => true, 'is_noi_bat' => true]
        );
        DanhMucQuan::firstOrCreate(
            ['slug' => 'quan-an-binh-dan'],
            ['ten_danh_muc' => 'Quán ăn bình dân', 'is_hien_thi' => true, 'is_noi_bat' => true]
        );
    }

    public function test_shopeefood_quan_seeder_creates_quans_and_dishes_with_shopeefood_urls(): void
    {
        // Chạy seeder
        $this->seed(ShopeeFoodQuanSeeder::class);

        // Kiểm tra có các quán có shopeefood_url
        $shopeeQuans = Quan::whereNotNull('shopeefood_url')
            ->where('shopeefood_url', '!=', '')
            ->get();

        $this->assertGreaterThanOrEqual(10, $shopeeQuans->count());

        // Kiểm tra quán Cơm Tấm Phúc Lộc Thọ
        $comTam = Quan::where('slug', 'com-tam-phuc-loc-tho-dinh-tien-hoang')->first();
        $this->assertNotNull($comTam);
        $this->assertEquals('https://shopeefood.vn/ho-chi-minh/com-tam-phuc-loc-tho-dinh-tien-hoang', $comTam->shopeefood_url);
        $this->assertTrue($comTam->is_noi_bat);
        $this->assertTrue($comTam->is_xac_thuc);

        // Kiểm tra thực đơn của quán có món ăn kèm link ShopeeFood
        $this->assertTrue($comTam->danhMucMenu()->exists());
        $firstDish = $comTam->danhMucMenu()->first()->monAn()->first();
        $this->assertNotNull($firstDish);
        $this->assertNotEmpty($firstDish->shopeefood_url);
        $this->assertStringContainsString('shopeefood.vn', $firstDish->shopeefood_url);
    }

    public function test_shopeefood_quan_seeder_is_idempotent(): void
    {
        // Chạy lần 1
        $this->seed(ShopeeFoodQuanSeeder::class);
        $countFirst = Quan::count();

        // Chạy lần 2 - không được sinh bản ghi trùng lặp
        $this->seed(ShopeeFoodQuanSeeder::class);
        $countSecond = Quan::count();

        $this->assertEquals($countFirst, $countSecond);
    }

    public function test_kham_pha_filter_co_shopeefood_shows_seeded_venues(): void
    {
        $this->seed(ShopeeFoodQuanSeeder::class);

        $response = $this->get(route('kham-pha', ['co_shopeefood' => 1]));

        $response->assertOk();
        $response->assertSee('ShopeeFood');

        // Lấy danh sách quán có ShopeeFood được tạo
        $firstQuan = Quan::where('slug', 'com-tam-phuc-loc-tho-dinh-tien-hoang')->first();
        $this->assertNotNull($firstQuan);
        $this->assertNotEmpty($firstQuan->shopeefood_url);
    }

    public function test_venue_detail_page_displays_shopeefood_order_button(): void
    {
        $this->seed(ShopeeFoodQuanSeeder::class);

        $comTam = Quan::where('slug', 'com-tam-phuc-loc-tho-dinh-tien-hoang')->firstOrFail();

        $response = $this->get('/quan/' . $comTam->slug);

        $response->assertOk();
        $response->assertSee('Đặt ngay trên ShopeeFood');
        $response->assertSee($comTam->shopeefood_url, false);
    }

    public function test_venue_card_displays_delivery_icon_and_badge_when_shopeefood_is_present(): void
    {
        $this->seed(ShopeeFoodQuanSeeder::class);

        $comTam = Quan::where('slug', 'com-tam-phuc-loc-tho-dinh-tien-hoang')->firstOrFail();

        $view = $this->blade('<x-venue-card :quan="$quan" />', ['quan' => $comTam]);

        $view->assertSee('Giao hàng');
        $view->assertSee('ShopeeFood');
        $view->assertSee('<svg', false);
    }
}
