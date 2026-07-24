<?php

namespace Tests\Feature\Admin;

use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudNguoiDungTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VaiTroSeeder::class);
    }

    public function test_non_admin_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/nguoi-dung');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_user_management(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/nguoi-dung');
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Người Dùng');
    }

    public function test_admin_can_update_user_info(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $user = User::factory()->create([
            'ho_ten' => 'Old Name',
            'email' => 'old@example.com'
        ]);

        $response = $this->actingAs($admin)->put('/admin/nguoi-dung/' . $user->id, [
            'ho_ten' => 'Updated Name',
            'email' => 'updated@example.com',
            'so_dien_thoai' => '0911223344',
            'trang_thai' => 'hoat_dong',
        ]);

        $response->assertRedirect(route('admin.nguoi-dung.index'));

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'ho_ten' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_admin_can_soft_delete_user_and_lock_owned_venues(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $owner = User::factory()->create();

        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán của Owner',
            'loai_hinh_kinh_doanh' => 'Quán ăn',
            'slug' => 'quan-cua-owner',
            'dia_chi_chi_tiet' => '123 Đường ABC',
            'so_dien_thoai' => '0901112223',
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->actingAs($admin)->delete('/admin/nguoi-dung/' . $owner->id);
        $response->assertRedirect(route('admin.nguoi-dung.index'));

        // Assert soft delete
        $this->assertSoftDeleted('nguoi_dung', ['id' => $owner->id]);
        $this->assertSoftDeleted('quan', ['id' => $quan->id]);
    }

    public function test_admin_can_restore_soft_deleted_user(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $user = User::factory()->create();
        $user->delete();

        $this->assertSoftDeleted('nguoi_dung', ['id' => $user->id]);

        $response = $this->actingAs($admin)->post('/admin/nguoi-dung/' . $user->id . '/restore');
        $response->assertRedirect(route('admin.nguoi-dung.index'));

        $this->assertNotSoftDeleted('nguoi_dung', ['id' => $user->id]);
    }
}
