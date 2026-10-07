<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use Database\Seeders\VaiTroSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(VaiTroSeeder::class);
    }

    public function test_owner_can_add_a_menu_item_with_a_shopeefood_link_and_customers_can_open_it(): void
    {
        $owner = $this->owner();
        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'shopeefood_url' => 'https://shopeefood.vn/example/restaurant',
        ]);
        $shopeefoodUrl = 'https://shopeefood.vn/example/pho-bo';

        $this->actingAs($owner)
            ->get(route('chu-quan.quan.menu.edit', $quan->slug))
            ->assertOk()
            ->assertSee('thuc-don-form', false)
            ->assertSee('Link ShopeeFood của món', false)
            ->assertSee("image.replace(/^\\/+/, '')", false);

        $this->actingAs($owner)
            ->postJson(route('chu-quan.quan.menu.update', $quan->slug), [
                'menu_data' => json_encode([
                    [
                        'name' => 'Món chính',
                        'items' => [[
                            'tmp_id' => 1,
                            'name' => 'Phở bò',
                            'price' => 50000,
                            'description' => 'Phở bò tái',
                            'shopeefood_url' => $shopeefoodUrl,
                        ]],
                    ],
                ]),
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('mon_trong_menu', [
            'ten_mon' => 'Phở bò',
            'shopeefood_url' => $shopeefoodUrl,
        ]);

        $this->get(route('quan.detail', $quan->slug))
            ->assertOk()
            ->assertSee('Giao hàng tận nơi')
            ->assertSee('Đặt ngay trên ShopeeFood')
            ->assertSee('href="#menu-section"', false)
            ->assertSee('href="#gallery-section"', false)
            ->assertSee('href="'.$shopeefoodUrl.'"', false)
            ->assertSee('aria-label="Đặt Phở bò trên ShopeeFood"', false);
    }

    public function test_owner_can_update_the_venue_shopeefood_link_below_tiktok(): void
    {
        $owner = $this->owner();
        $quan = Quan::factory()->create(['chu_quan_id' => $owner->id]);
        $shopeefoodUrl = 'https://shopeefood.vn/example/restaurant';

        $this->actingAs($owner)
            ->putJson(route('chu-quan.quan.update', $quan->slug), [
                'so_dien_thoai' => $quan->so_dien_thoai,
                'gio_mo_cua' => $quan->gio_mo_cua,
                'gio_dong_cua' => $quan->gio_dong_cua,
                'shopeefood_url' => $shopeefoodUrl,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('quan', [
            'id' => $quan->id,
            'shopeefood_url' => $shopeefoodUrl,
        ]);

        $this->get(route('chu-quan.quan.show', $quan->slug))
            ->assertOk()
            ->assertSee('id="edit-shopeefood"', false)
            ->assertSee('value="'.$shopeefoodUrl.'"', false);
    }

    private function owner(): User
    {
        return User::factory()->create([
            'vai_tro_id' => VaiTro::where('ten', 'chu_quan')->value('id'),
            'da_xac_thuc' => true,
            'trang_thai' => 'hoat_dong',
        ]);
    }
}
