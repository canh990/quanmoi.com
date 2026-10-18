<?php

namespace Tests\Feature\Admin;

use App\Models\Quan;
use App\Models\QuyenHan;
use App\Models\User;
use App\Models\VaiTro;
use Database\Seeders\QuyenHanSeeder;
use Database\Seeders\VaiTroSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudQuanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
        $this->seed(VaiTroSeeder::class);
        $this->seed(QuyenHanSeeder::class);
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
        $response->assertSee('Thêm Quán Mới');
    }

    public function test_admin_can_view_create_page(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.quan.create'));
        $response->assertStatus(200);
        $response->assertSee('Thêm Địa Điểm Quán Mới');
        $response->assertSee('Tạo Địa Điểm Quán Mới');
    }

    public function test_admin_can_store_new_quan(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $owner = User::factory()->create(['ho_ten' => 'Chủ Quán Test']);

        $postData = [
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Bún Chả Hương Liên',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'so_dien_thoai' => '0988776655',
            'email' => 'buncha@example.com',
            'dia_chi_chi_tiet' => '24 Lê Văn Hưu',
            'ten_tinh_thanh' => 'Thành phố Hà Nội',
            'ten_quan_huyen' => 'Quận Hai Bà Trưng',
            'ten_phuong_xa' => 'Phường Phan Chu Trinh',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '21:00',
            'gia_nho_nhat' => 40000,
            'gia_lon_nhat' => 120000,
            'trang_thai' => 'da_duyet',
            'is_noi_bat' => 1,
            'is_xac_thuc' => 1,
            'mo_ta' => 'Bún chả gia truyền nổi tiếng Hà Nội.',
        ];

        $response = $this->actingAs($admin)->post(route('admin.quan.store'), $postData);
        $response->assertRedirect(route('admin.quan.index'));

        $this->assertDatabaseHas('quan', [
            'ten_quan' => 'Quán Bún Chả Hương Liên',
            'chu_quan_id' => $owner->id,
            'trang_thai' => 'da_duyet',
            'is_noi_bat' => 1,
            'is_xac_thuc' => 1,
        ]);

        $ownerRole = VaiTro::where('ten', 'chu_quan')->first();
        $this->assertEquals($ownerRole->id, $owner->fresh()->vai_tro_id);
    }

    public function test_store_quan_validation_fails_for_invalid_data(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        // Thiếu tên quán, địa chỉ, số điện thoại
        $response = $this->actingAs($admin)->post(route('admin.quan.store'), [
            'ten_quan' => '',
            'so_dien_thoai' => '',
            'dia_chi_chi_tiet' => '',
            'trang_thai' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors(['ten_quan', 'so_dien_thoai', 'dia_chi_chi_tiet', 'trang_thai']);
    }

    public function test_admin_can_view_quan_details_via_ajax(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $quan = Quan::create([
            'chu_quan_id' => $admin->id,
            'ten_quan' => 'Quán Cà Phê Trứng',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'slug' => 'quan-ca-phe-trung',
            'dia_chi_chi_tiet' => '39 Nguyễn Hữu Huân',
            'so_dien_thoai' => '0912345678',
            'trang_thai' => 'da_duyet',
            'gia_nho_nhat' => 35000,
            'gia_lon_nhat' => 65000,
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.quan.show', $quan->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $quan->id,
                'ten_quan' => 'Quán Cà Phê Trứng',
                'slug' => 'quan-ca-phe-trung',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            ],
        ]);
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

    public function test_admin_can_force_delete_quan(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $owner = User::factory()->create();
        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Xóa Vĩnh Viễn',
            'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
            'slug' => 'quan-xoa-vinh-vien',
            'dia_chi_chi_tiet' => '101 Nguyễn Huệ',
            'so_dien_thoai' => '0903332211',
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->actingAs($admin)->delete('/admin/quan/' . $quan->id . '/force');
        $response->assertRedirect(route('admin.quan.index'));

        $this->assertDatabaseMissing('quan', ['id' => $quan->id]);
    }

    public function test_admin_can_toggle_quan_status_via_ajax(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $owner = User::factory()->create();
        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Test AJAX',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'slug' => 'quan-test-ajax',
            'dia_chi_chi_tiet' => '123 Kim Mã',
            'so_dien_thoai' => '0901112233',
            'trang_thai' => 'da_duyet',
            'is_xac_thuc' => false,
            'is_noi_bat' => false,
        ]);

        // 1. Toggle Tick Xanh via AJAX
        $resXacThuc = $this->actingAs($admin)->putJson('/admin/quan/' . $quan->id . '/toggle-xac-thuc');
        $resXacThuc->assertStatus(200)->assertJson(['success' => true, 'is_xac_thuc' => true]);
        $this->assertTrue($quan->fresh()->is_xac_thuc);

        // 2. Toggle Noi Bat via AJAX
        $resNoiBat = $this->actingAs($admin)->putJson('/admin/quan/' . $quan->id . '/toggle-noi-bat');
        $resNoiBat->assertStatus(200)->assertJson(['success' => true, 'is_noi_bat' => true]);
        $this->assertTrue($quan->fresh()->is_noi_bat);
    }

    public function test_admin_can_search_and_filter_quan(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $owner = User::factory()->create(['ho_ten' => 'Nguyễn Văn Chủ Quán']);

        $quanA = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Bún Chả Cầu Giấy',
            'loai_hinh_kinh_doanh' => 'Quán ăn',
            'slug' => 'bun-cha-cau-giay',
            'dia_chi_chi_tiet' => '55 Cầu Giấy',
            'so_dien_thoai' => '0912111222',
            'trang_thai' => 'da_duyet',
            'is_noi_bat' => true,
            'is_xac_thuc' => true,
        ]);

        $quanB = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Trà Sữa Đống Đa',
            'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
            'slug' => 'tra-sua-dong-da',
            'dia_chi_chi_tiet' => '88 Xã Đàn',
            'so_dien_thoai' => '0913333444',
            'trang_thai' => 'chua_duyet',
            'is_noi_bat' => false,
            'is_xac_thuc' => false,
        ]);

        // 1. Search keyword
        $resSearch = $this->actingAs($admin)->get('/admin/quan?q=Cầu+Giấy');
        $resSearch->assertStatus(200);
        $resSearch->assertSee('Bún Chả Cầu Giấy');
        $resSearch->assertDontSee('Trà Sữa Đống Đa');

        // 2. Filter status
        $resStatus = $this->actingAs($admin)->get('/admin/quan?status=chua_duyet');
        $resStatus->assertStatus(200);
        $resStatus->assertSee('Trà Sữa Đống Đa');
        $resStatus->assertDontSee('Bún Chả Cầu Giấy');

        // 3. Filter is_noi_bat
        $resNoiBat = $this->actingAs($admin)->get('/admin/quan?is_noi_bat=1');
        $resNoiBat->assertStatus(200);
        $resNoiBat->assertSee('Bún Chả Cầu Giấy');
        $resNoiBat->assertDontSee('Trà Sữa Đống Đa');

        // 4. Filter is_xac_thuc
        $resXacThuc = $this->actingAs($admin)->get('/admin/quan?is_xac_thuc=1');
        $resXacThuc->assertStatus(200);
        $resXacThuc->assertSee('Bún Chả Cầu Giấy');
        $resXacThuc->assertDontSee('Trà Sữa Đống Đa');
    }

    public function test_user_with_quan_ly_quan_permission_can_manage_quan(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'staff_admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $perm = QuyenHan::where('ten', 'quan_ly_quan')->first();
        if ($perm) {
            $admin->quyenHanOverrides()->attach($perm->id, ['cho_phep' => true]);
        }

        $response = $this->actingAs($admin)->get('/admin/quan');
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Địa Điểm Quán');
    }
}
