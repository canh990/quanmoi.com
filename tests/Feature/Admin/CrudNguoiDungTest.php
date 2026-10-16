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
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
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
        $this->assertSoftDeleted('nguoi_dung', ['id' => $owner->id], null, 'ngay_xoa');
        $this->assertSoftDeleted('quan', ['id' => $quan->id], null, 'ngay_xoa');
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

        $this->assertSoftDeleted('nguoi_dung', ['id' => $user->id], null, 'ngay_xoa');

        $response = $this->actingAs($admin)->post('/admin/nguoi-dung/' . $user->id . '/restore');
        $response->assertRedirect(route('admin.nguoi-dung.index'));

        $this->assertNotSoftDeleted('nguoi_dung', ['id' => $user->id], null, 'ngay_xoa');
    }

    public function test_user_update_validation_messages_are_in_vietnamese(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($admin)->put('/admin/nguoi-dung/' . $user->id, [
            'ho_ten' => '',
            'email' => 'not-an-email',
            'trang_thai' => '',
        ]);

        $response->assertSessionHasErrors([
            'ho_ten' => 'Họ và tên người dùng không được để trống.',
            'email' => 'Địa chỉ email không đúng định dạng.',
            'trang_thai' => 'Trạng thái tài khoản không được để trống.',
        ]);
    }

    public function test_admin_cannot_self_lock_or_demote_themselves(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $response = $this->actingAs($admin)->put('/admin/nguoi-dung/' . $admin->id, [
            'ho_ten' => 'Admin Self',
            'email' => 'admin@quanmoi.com',
            'trang_thai' => 'bi_khoa',
        ]);

        $response->assertSessionHasErrors([
            'trang_thai' => 'Không thể tự hạ quyền, khóa hoặc xóa tài khoản quản trị đang đăng nhập.',
        ]);
    }

    public function test_admin_can_toggle_tick_xanh(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $user = User::factory()->create(['da_xac_thuc' => false]);

        $response = $this->actingAs($admin)->put('/admin/nguoi-dung/' . $user->id . '/toggle-xac-thuc');
        $response->assertRedirect();

        $this->assertTrue($user->fresh()->da_xac_thuc);
    }
}
