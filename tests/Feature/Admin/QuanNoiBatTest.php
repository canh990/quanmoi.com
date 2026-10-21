<?php

namespace Tests\Feature\Admin;

use App\Models\AdminAuditLog;
use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuanNoiBatTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;
    protected User $chuQuan;
    protected Quan $quanNoiBat;
    protected Quan $quanThuong;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VaiTroSeeder::class);

        $adminRole = VaiTro::where('ten', 'admin')->first();

        $this->admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
            'da_xac_thuc' => true,
        ]);

        $this->regularUser = User::factory()->create([
            'email' => 'user@quanmoi.com',
            'da_xac_thuc' => true,
        ]);

        $this->chuQuan = User::factory()->create([
            'ho_ten' => 'Chủ Quán Mẫu',
            'email' => 'chuquan@quanmoi.com',
            'da_xac_thuc' => true,
        ]);

        // 1 Quán đang nổi bật
        $this->quanNoiBat = Quan::create([
            'chu_quan_id' => $this->chuQuan->id,
            'ten_quan' => 'Cà Phê Phố Cổ Nổi Bật',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'slug' => 'ca-phe-pho-co-noi-bat',
            'dia_chi_chi_tiet' => '12 Hàng Buồm',
            'ten_quan_huyen' => 'Hoàn Kiếm',
            'ten_tinh_thanh' => 'Hà Nội',
            'so_dien_thoai' => '0912345678',
            'trang_thai' => 'da_duyet',
            'is_noi_bat' => true,
            'is_xac_thuc' => true,
            'luot_xem' => 1250,
        ]);

        // 1 Quán thường đã duyệt (chưa nổi bật)
        $this->quanThuong = Quan::create([
            'chu_quan_id' => $this->chuQuan->id,
            'ten_quan' => 'Bún Bò Huế Đề Cử',
            'loai_hinh_kinh_doanh' => 'Quán ăn',
            'slug' => 'bun-bo-hue-de-cu',
            'dia_chi_chi_tiet' => '88 Kim Mã',
            'ten_quan_huyen' => 'Ba Đình',
            'ten_tinh_thanh' => 'Hà Nội',
            'so_dien_thoai' => '0987654321',
            'trang_thai' => 'da_duyet',
            'is_noi_bat' => false,
            'is_xac_thuc' => false,
            'luot_xem' => 300,
        ]);
    }

    public function test_guest_and_regular_user_cannot_access_quan_noi_bat_admin(): void
    {
        // 1. Khách vãng lai
        $guestResponse = $this->get(route('admin.quan-noi-bat.index'));
        $guestResponse->assertRedirect(route('login'));

        // 2. Người dùng thường
        $userResponse = $this->actingAs($this->regularUser)->get(route('admin.quan-noi-bat.index'));
        $userResponse->assertStatus(403);
    }

    public function test_admin_can_view_quan_noi_bat_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.quan-noi-bat.index'));
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Quán Nổi Bật');
        $response->assertSee('Cà Phê Phố Cổ Nổi Bật');
        $response->assertSee('Bún Bò Huế Đề Cử');
        $response->assertSee('Kho Quán Đã Duyệt');
    }

    public function test_admin_can_search_available_venues(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('admin.quan-noi-bat.search-available', ['q' => 'Bún Bò']));
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'ten_quan' => 'Bún Bò Huế Đề Cử',
            ]);
    }

    public function test_admin_can_add_venue_to_featured(): void
    {
        $this->assertFalse($this->quanThuong->fresh()->is_noi_bat);

        $response = $this->actingAs($this->admin)->postJson(route('admin.quan-noi-bat.store'), [
            'quan_id' => $this->quanThuong->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_noi_bat' => true,
            ]);

        $this->assertTrue($this->quanThuong->fresh()->is_noi_bat);

        // Kiểm tra Audit Log
        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'venue.featured_added',
            'target_id' => (string) $this->quanThuong->id,
        ]);
    }

    public function test_admin_can_update_featured_venue_information(): void
    {
        $response = $this->actingAs($this->admin)->putJson(route('admin.quan-noi-bat.update', $this->quanNoiBat->id), [
            'ten_quan' => 'Cà Phê Phố Cổ Cập Nhật',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'so_dien_thoai' => '0999888777',
            'gio_mo_cua' => '07:30',
            'gio_dong_cua' => '22:30',
            'is_xac_thuc' => true,
            'is_noi_bat' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $fresh = $this->quanNoiBat->fresh();
        $this->assertEquals('Cà Phê Phố Cổ Cập Nhật', $fresh->ten_quan);
        $this->assertEquals('0999888777', $fresh->so_dien_thoai);
        $this->assertEquals('07:30', substr($fresh->gio_mo_cua, 0, 5));

        // Kiểm tra Audit Log
        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'venue.featured_updated',
            'target_id' => (string) $this->quanNoiBat->id,
        ]);
    }

    public function test_admin_can_toggle_featured_status(): void
    {
        $response = $this->actingAs($this->admin)->putJson(route('admin.quan-noi-bat.toggle', $this->quanNoiBat->id));
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_noi_bat' => false,
            ]);

        $this->assertFalse($this->quanNoiBat->fresh()->is_noi_bat);

        // Toggle lại thành true
        $response2 = $this->actingAs($this->admin)->putJson(route('admin.quan-noi-bat.toggle', $this->quanNoiBat->id));
        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_noi_bat' => true,
            ]);

        $this->assertTrue($this->quanNoiBat->fresh()->is_noi_bat);
    }

    public function test_admin_can_remove_venue_from_featured(): void
    {
        $response = $this->actingAs($this->admin)->deleteJson(route('admin.quan-noi-bat.destroy', $this->quanNoiBat->id));
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_noi_bat' => false,
            ]);

        $this->assertFalse($this->quanNoiBat->fresh()->is_noi_bat);

        // Kiểm tra Audit Log
        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'venue.featured_removed',
            'target_id' => (string) $this->quanNoiBat->id,
        ]);
    }

    public function test_featured_venues_synchronize_with_user_facing_pages(): void
    {
        // 1. Trang chủ: hiển thị quán nổi bật
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Cà Phê Phố Cổ Nổi Bật');

        // 2. Trang người dùng /quan-noi-bat
        $featuredResponse = $this->get(route('quan-noi-bat'));
        $featuredResponse->assertStatus(200);
        $featuredResponse->assertSee('Cà Phê Phố Cổ Nổi Bật');
    }
}
