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

    public function test_admin_outside_admin_panel_sees_admin_notifications_in_navigation(): void
    {
        $roleAdmin = VaiTro::create(['ten' => 'admin']);
        $admin = User::factory()->create([
            'vai_tro_id' => $roleAdmin->id,
        ]);

        Quan::create([
            'chu_quan_id' => $admin->id,
            'ten_quan' => 'Quán Chờ Duyệt Test',
            'loai_hinh_kinh_doanh' => 'Quán cà phê',
            'slug' => 'quan-cho-duyet-test',
            'dia_chi_chi_tiet' => '100 Nguyễn Huệ',
            'ten_tinh_thanh' => 'TP. Hồ Chí Minh',
            'ten_quan_huyen' => 'Quận 1',
            'so_dien_thoai' => '0909999999',
            'gio_mo_cua' => '07:00',
            'gio_dong_cua' => '22:00',
            'trang_thai' => 'chua_duyet',
            'la_nhap' => false,
        ]);

        $response = $this->actingAs($admin)->get('/');

        $response->assertOk();
        $response->assertSee('Trang quản trị');
        $response->assertSee('Thông báo Quản trị');
        $response->assertSee('Quán Chờ Duyệt Test');
        $response->assertSee(route('admin.thong-bao.index'), false);
    }

    public function test_admin_realtime_check_returns_valid_data(): void
    {
        $roleAdmin = VaiTro::create(['ten' => 'admin']);
        $admin = User::factory()->create([
            'vai_tro_id' => $roleAdmin->id,
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.realtime-check'));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'unread_count',
            'all_notifications',
        ]);
        $response->assertJson(['success' => true]);
    }
}
