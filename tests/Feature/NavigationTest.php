<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_sees_dang_quan_link_in_navigation(): void
    {
        $roleNguoiDung = VaiTro::create(['ten' => 'nguoi_dung']);
        $user = User::factory()->create(['vai_tro_id' => $roleNguoiDung->id]);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee('Đăng quán');
        $response->assertSee(route('chu-quan.dang-quan'), false);
    }

    public function test_owner_without_quan_sees_quan_ly_quan_but_link_points_to_create_form(): void
    {
        $roleChuQuan = VaiTro::create(['ten' => 'chu_quan']);
        $owner = User::factory()->create(['vai_tro_id' => $roleChuQuan->id]);

        $response = $this->actingAs($owner)->get('/');

        $response->assertOk();
        $response->assertSee('Quản lý quán');
        $response->assertSee(route('chu-quan.dang-quan'), false);
    }

    public function test_owner_with_quan_sees_management_link_to_owner_index(): void
    {
        $roleChuQuan = VaiTro::create(['ten' => 'chu_quan']);
        $owner = User::factory()->create(['vai_tro_id' => $roleChuQuan->id]);

        Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Lau Thai 999',
            'loai_hinh_kinh_doanh' => 'Quan an',
            'slug' => 'lau-thai-999',
            'dia_chi_chi_tiet' => '88 Tran Hung Dao',
            'ten_tinh_thanh' => 'Da Nang',
            'ten_quan_huyen' => 'Hai Chau',
            'so_dien_thoai' => '0901112222',
            'gio_mo_cua' => '09:00',
            'gio_dong_cua' => '22:00',
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->actingAs($owner)->get('/');

        $response->assertOk();
        $response->assertSee('Quản lý quán');
        $response->assertSee(route('chu-quan.quan.index'), false);
    }
}
