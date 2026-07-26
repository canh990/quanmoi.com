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

    public function test_regular_user_still_sees_dang_quan_in_navigation(): void
    {
        $roleNguoiDung = VaiTro::create(['ten' => 'nguoi_dung']);
        $user = User::factory()->create([
            'vai_tro_id' => $roleNguoiDung->id,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee('Đăng quán');
        $response->assertDontSee('Quản lý cửa hàng');
        $response->assertSee(route('chu-quan.dang-quan'), false);
    }

    public function test_user_with_quan_sees_quan_ly_cua_hang_in_navigation(): void
    {
        $roleChuQuan = VaiTro::create(['ten' => 'chu_quan']);
        $user = User::factory()->create([
            'vai_tro_id' => $roleChuQuan->id,
        ]);

        Quan::create([
            'chu_quan_id' => $user->id,
            'ten_quan' => 'Quan Cua Toi',
            'loai_hinh_kinh_doanh' => 'Quan an',
            'slug' => 'quan-cua-toi',
            'dia_chi_chi_tiet' => '12 Le Loi',
            'ten_tinh_thanh' => 'TP. Ho Chi Minh',
            'ten_quan_huyen' => 'Quan 1',
            'so_dien_thoai' => '0901234567',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
            'trang_thai' => 'chua_duyet',
            'la_nhap' => false,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee('Quản lý cửa hàng');
        $response->assertDontSee('Đăng quán');
        $response->assertSee(route('chu-quan.quan.index'), false);
    }
}
