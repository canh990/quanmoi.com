<?php

namespace Tests\Feature\Admin;

use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudQuanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VaiTroSeeder::class);
    }

    public function test_non_admin_cannot_access_quan_management(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/quan');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_quan_management(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/quan');
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Địa Điểm Quán');
    }

    public function test_admin_can_update_quan_status(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $owner = User::factory()->create();
        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Chờ Duyệt',
            'loai_hinh_kinh_doanh' => 'Quán ăn',
            'slug' => 'quan-cho-duyet',
            'dia_chi_chi_tiet' => '123 Đường XYZ',
            'so_dien_thoai' => '0909998887',
            'trang_thai' => 'chua_duyet',
        ]);

        $response = $this->actingAs($admin)->put('/admin/quan/' . $quan->id, [
            'ten_quan' => 'Quán Đã Duyệt',
            'loai_hinh_kinh_doanh' => 'Quán ăn',
            'so_dien_thoai' => '0909998887',
            'dia_chi_chi_tiet' => '123 Đường XYZ',
            'trang_thai' => 'da_duyet',
        ]);

        $response->assertRedirect(route('admin.quan.index'));

        $this->assertDatabaseHas('quan', [
            'id' => $quan->id,
            'ten_quan' => 'Quán Đã Duyệt',
            'trang_thai' => 'da_duyet',
        ]);
    }

    public function test_admin_can_soft_delete_quan(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $owner = User::factory()->create();
        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Xóa Tạm',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'slug' => 'quan-xoa-tam',
            'dia_chi_chi_tiet' => '456 Lê Lợi',
            'so_dien_thoai' => '0907776655',
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->actingAs($admin)->delete('/admin/quan/' . $quan->id);
        $response->assertRedirect(route('admin.quan.index'));

        $this->assertSoftDeleted('quan', ['id' => $quan->id], null, 'ngay_xoa');
    }

    public function test_admin_can_restore_soft_deleted_quan(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $owner = User::factory()->create();
        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Khôi Phục',
            'loai_hinh_kinh_doanh' => 'Bida & Giải trí',
            'slug' => 'quan-khoi-phuc',
            'dia_chi_chi_tiet' => '789 Nguyễn Trãi',
            'so_dien_thoai' => '0905554433',
            'trang_thai' => 'da_duyet',
        ]);
        $quan->delete();

        $this->assertSoftDeleted('quan', ['id' => $quan->id], null, 'ngay_xoa');

        $response = $this->actingAs($admin)->post('/admin/quan/' . $quan->id . '/restore');
        $response->assertRedirect(route('admin.quan.index'));

        $this->assertNotSoftDeleted('quan', ['id' => $quan->id], null, 'ngay_xoa');
    }
}
