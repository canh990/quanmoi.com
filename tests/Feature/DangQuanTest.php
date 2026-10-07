<?php

namespace Tests\Feature;

use App\Models\VaiTro;
use App\Models\User;
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
        $roleNguoiDung = VaiTro::create(['ten' => 'nguoi_dung']);
        $roleChuQuan = VaiTro::create(['ten' => 'chu_quan']);
        $user = User::factory()->create([
            'vai_tro_id' => $roleNguoiDung->id,
        ]);

        $data = [
            'ten_quan' => 'Pho Thin Ha Noi',
            'loai_hinh_kinh_doanh' => 'Quan an binh dan',
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
        ]);

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'vai_tro_id' => $roleChuQuan->id,
        ]);

        $this->actingAs($user->fresh())
            ->get(route('chu-quan.quan.show', ['slug' => 'pho-thin-ha-noi']))
            ->assertOk();
    }
}
