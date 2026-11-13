<?php

namespace Tests\Feature;

use App\Models\DanhMucQuan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DangQuanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_opening_dang_quan_page(): void
    {
        $response = $this->get('/chu-quan/dang-quan');

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_create_new_quan(): void
    {
        $response = $this->post('/chu-quan/dang-quan', []);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_dang_quan_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/chu-quan/dang-quan');

        $response->assertStatus(200);
        $response->assertSee('dang-quan-form', false);
        $response->assertSee('shopeefood_url', false);
    }

    public function test_user_can_create_new_quan_successfully(): void
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
        $roleNguoiDung = VaiTro::create(['ten' => 'nguoi_dung']);
        $roleChuQuan = VaiTro::create(['ten' => 'chu_quan']);
        $user = User::factory()->create([
            'vai_tro_id' => $roleNguoiDung->id,
        ]);

        $cat = DanhMucQuan::firstOrCreate(
            ['slug' => 'quan-an-binh-dan'],
            [
                'ten_danh_muc' => 'Quan an binh dan',
                'icon' => 'lunch_dining',
                'is_hien_thi' => true,
            ]
        );

        $data = [
            'ten_quan' => 'Pho Thin Ha Noi',
            'loai_hinh_kinh_doanh' => 'Quan an binh dan',
            'danh_muc_id' => $cat->id,
            'so_dien_thoai' => '0912345678',
            'email' => 'phothin@example.com',
            'dia_chi_chi_tiet' => '13 Lo Duc',
            'tinh_thanh_id' => '01',
            'ten_tinh_thanh' => 'TP. Ha Noi',
            'quan_huyen_id' => '001',
            'ten_quan_huyen' => 'Quan Hai Ba Trung',
            'phuong_xa_id' => '00001',
            'ten_phuong_xa' => 'Phuong Pham Dinh Ho',
            'mo_ta' => 'Pho Thin Lo Duc truyen thong',
            'gio_mo_cua' => '06:00',
            'gio_dong_cua' => '22:00',
            'gia_nho_nhat' => 40000,
            'gia_lon_nhat' => 90000,
            'shopeefood_url' => 'https://shopeefood.vn/pho-thin',
        ];

        $response = $this->actingAs($user)->postJson('/chu-quan/dang-quan', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonPath('redirect_to', route('chu-quan.quan.show', ['slug' => 'pho-thin-ha-noi']));

        $this->assertDatabaseHas('quan', [
            'ten_quan' => 'Pho Thin Ha Noi',
            'so_dien_thoai' => '0912345678',
            'tinh_thanh_id' => '01',
            'ten_tinh_thanh' => 'TP. Ha Noi',
            'quan_huyen_id' => '001',
            'phuong_xa_id' => '00001',
            'shopeefood_url' => 'https://shopeefood.vn/pho-thin',
            'chu_quan_id' => $user->id,
            'danh_muc_id' => $cat->id,
        ]);

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'vai_tro_id' => $roleChuQuan->id,
        ]);

        $this->actingAs($user->fresh())
            ->get(route('chu-quan.quan.show', ['slug' => 'pho-thin-ha-noi']))
            ->assertOk();
    }

    public function test_dang_quan_page_displays_categories_dynamically(): void
    {
        DanhMucQuan::firstOrCreate(
            ['slug' => 'danh-muc-moi-test'],
            [
                'icon' => 'restaurant',
                'ten_danh_muc' => 'Danh Mục Mới Test',
                'is_hien_thi' => true,
            ]
        );

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/chu-quan/dang-quan');

        $response->assertStatus(200);
        $response->assertSee('Danh Mục Mới Test');
    }

    public function test_dang_quan_page_shows_xem_them_button_when_more_than_six_categories(): void
    {
        DanhMucQuan::query()->delete();

        for ($i = 1; $i <= 8; $i++) {
            DanhMucQuan::create([
                'slug' => 'danh-muc-test-' . $i,
                'ten_danh_muc' => 'Danh Muc ' . $i,
                'icon' => 'restaurant',
                'thu_tu' => $i,
                'is_hien_thi' => true,
            ]);
        }

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/chu-quan/dang-quan');

        $response->assertStatus(200);
        $response->assertSee('id="btnToggleBizTypes"', false);
        $response->assertSee('Xem thêm (2 danh mục khác)');
        $response->assertSee('extra-biz-type hidden');
    }

    public function test_dang_quan_page_hides_xem_them_button_when_six_or_fewer_categories(): void
    {
        DanhMucQuan::query()->delete();

        for ($i = 1; $i <= 5; $i++) {
            DanhMucQuan::create([
                'slug' => 'danh-muc-test-' . $i,
                'ten_danh_muc' => 'Danh Muc ' . $i,
                'icon' => 'restaurant',
                'thu_tu' => $i,
                'is_hien_thi' => true,
            ]);
        }

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/chu-quan/dang-quan');

        $response->assertStatus(200);
        $response->assertDontSee('id="btnToggleBizTypes"', false);
        $response->assertDontSee('extra-biz-type hidden');
    }

    public function test_so_dien_thoai_must_be_digits_only_when_creating_quan(): void
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
        $user = User::factory()->create();

        $basePayload = [
            'ten_quan' => 'Quan Test So Dien Thoai',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'dia_chi_chi_tiet' => '123 Lê Lợi',
            'tinh_thanh_id' => '79',
            'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
            'phuong_xa_id' => '26734',
            'ten_phuong_xa' => 'Phường Tân Định',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
        ];

        $this->actingAs($user)
            ->postJson('/chu-quan/dang-quan', array_merge($basePayload, ['so_dien_thoai' => '090abc1234']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['so_dien_thoai']);

        $this->actingAs($user)
            ->postJson('/chu-quan/dang-quan', array_merge($basePayload, ['so_dien_thoai' => '090 123 4567']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['so_dien_thoai']);

        // Giới hạn độ dài 10-11 chữ số và bắt đầu bằng số 0
        $this->actingAs($user)
            ->postJson('/chu-quan/dang-quan', array_merge($basePayload, ['so_dien_thoai' => '091234567'])) // 9 số
            ->assertStatus(422)
            ->assertJsonValidationErrors(['so_dien_thoai']);

        $this->actingAs($user)
            ->postJson('/chu-quan/dang-quan', array_merge($basePayload, ['so_dien_thoai' => '091234567890'])) // 12 số
            ->assertStatus(422)
            ->assertJsonValidationErrors(['so_dien_thoai']);

        $this->actingAs($user)
            ->postJson('/chu-quan/dang-quan', array_merge($basePayload, ['so_dien_thoai' => '9123456789'])) // không bắt đầu bằng 0
            ->assertStatus(422)
            ->assertJsonValidationErrors(['so_dien_thoai']);
    }

    public function test_user_can_create_quan_with_two_tier_merged_address_without_quan_huyen(): void
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
        $roleNguoiDung = VaiTro::create(['ten' => 'nguoi_dung']);
        VaiTro::create(['ten' => 'chu_quan']);
        $user = User::factory()->create(['vai_tro_id' => $roleNguoiDung->id]);

        $response = $this->actingAs($user)->postJson('/chu-quan/dang-quan', [
            'ten_quan' => 'Quan Hai Cap Sau Sap Nhap',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'so_dien_thoai' => '0912345678',
            'dia_chi_chi_tiet' => '45 Nguyễn Huệ',
            'tinh_thanh_id' => '79',
            'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
            'phuong_xa_id' => '26740',
            'ten_phuong_xa' => 'Phường Sài Gòn',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('quan', [
            'ten_quan' => 'Quan Hai Cap Sau Sap Nhap',
            'tinh_thanh_id' => '79',
            'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
            'quan_huyen_id' => null,
            'ten_quan_huyen' => null,
            'phuong_xa_id' => '26740',
            'ten_phuong_xa' => 'Phường Sài Gòn',
        ]);
    }

    public function test_dang_quan_view_uses_merged_two_tier_location_and_numeric_phone(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/chu-quan/dang-quan');

        $response->assertOk();
        $response->assertSee('/api/dia-chi/phuong-xa-tinh/', false);
        $response->assertSee('pattern="0[0-9]{9,10}"', false);
        $response->assertSee('maxlength="11"', false);
        $response->assertDontSee('id="select-huyen"', false);
    }

    public function test_chu_quan_voucher_submit_button_has_solid_single_color(): void
    {
        $roleChuQuan = VaiTro::create(['ten' => 'chu_quan']);
        $user = User::factory()->create(['vai_tro_id' => $roleChuQuan->id]);

        $quan = \App\Models\Quan::create([
            'chu_quan_id' => $user->id,
            'ten_quan' => 'Quan Voucher Test',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'slug' => 'quan-voucher-test',
            'so_dien_thoai' => '0901234567',
            'dia_chi_chi_tiet' => '12 Lê Lợi',
            'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
            'ten_phuong_xa' => 'Phường Bến Thành',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->actingAs($user)->get(route('chu-quan.quan.show', ['slug' => $quan->slug]));

        $response->assertOk();
        $response->assertSee('id="btn-owner-save"', false);
        $response->assertDontSee('from-amber-500 to-orange-500', false);
        $response->assertDontSee('id="qr-code-container"', false);
    }

    public function test_chu_quan_qr_shows_only_when_shopeefood_url_is_set(): void
    {
        $roleChuQuan = VaiTro::create(['ten' => 'chu_quan']);
        $user = User::factory()->create(['vai_tro_id' => $roleChuQuan->id]);

        $quan = \App\Models\Quan::create([
            'chu_quan_id' => $user->id,
            'ten_quan' => 'Quan Co ShopeeFood',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'slug' => 'quan-co-shopeefood',
            'so_dien_thoai' => '0901234567',
            'dia_chi_chi_tiet' => '12 Lê Lợi',
            'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
            'ten_phuong_xa' => 'Phường Bến Thành',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
            'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/quan-co-shopeefood',
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->actingAs($user)->get(route('chu-quan.quan.show', ['slug' => $quan->slug]));

        $response->assertOk();
        $response->assertSee('id="qr-code-container"', false);
        $response->assertSee('https://shopeefood.vn/ho-chi-minh/quan-co-shopeefood', false);
    }

    public function test_user_can_create_quan_with_same_name_as_soft_deleted_quan(): void
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
        $roleNguoiDung = VaiTro::create(['ten' => 'nguoi_dung']);
        VaiTro::create(['ten' => 'chu_quan']);
        $user = User::factory()->create(['vai_tro_id' => $roleNguoiDung->id]);

        $deletedQuan = \App\Models\Quan::create([
            'chu_quan_id' => $user->id,
            'ten_quan' => 'Quán Sushi',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'slug' => 'quan-sushi',
            'so_dien_thoai' => '0901234567',
            'dia_chi_chi_tiet' => '10 Lê Lợi',
            'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
            'ten_phuong_xa' => 'Phường Bến Thành',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
            'trang_thai' => 'da_duyet',
        ]);
        $deletedQuan->delete();

        $response = $this->actingAs($user)->postJson('/chu-quan/dang-quan', [
            'ten_quan' => 'Quán Sushi',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'so_dien_thoai' => '0912345678',
            'dia_chi_chi_tiet' => '20 Nguyễn Huệ',
            'tinh_thanh_id' => '79',
            'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
            'phuong_xa_id' => '26740',
            'ten_phuong_xa' => 'Phường Sài Gòn',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('quan', ['slug' => 'quan-sushi-1', 'ten_quan' => 'Quán Sushi']);
    }
}
