<?php

namespace Tests\Feature\Admin;

use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use Database\Seeders\VaiTroSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuyetXacThucTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(VaiTroSeeder::class);
    }

    private function createAdmin(): User
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        return User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole->id,
        ]);
    }

    public function test_admin_can_view_duyet_xac_thuc_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.duyet-xac-thuc.index'));

        $response->assertStatus(200);
        $response->assertSee('Quán Chờ Duyệt');
        $response->assertSee('Tài Khoản Chưa Xác Thực');
    }

    public function test_admin_can_approve_quan(): void
    {
        $admin = $this->createAdmin();
        $owner = User::factory()->create();

        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Bún Bò Huế Chờ Duyệt',
            'loai_hinh_kinh_doanh' => 'Quán ăn',
            'slug' => 'bun-bo-hue-cho-duyet',
            'dia_chi_chi_tiet' => '456 Lê Lợi',
            'so_dien_thoai' => '0912345678',
            'trang_thai' => 'chua_duyet',
            'is_xac_thuc' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.duyet-xac-thuc.quan', $quan->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $quan->refresh();
        $this->assertEquals('da_duyet', $quan->trang_thai);
        $this->assertTrue((bool) $quan->is_xac_thuc);

        $owner->refresh();
        $this->assertEquals('chu_quan', $owner->vaiTro?->ten);
    }

    public function test_admin_can_reject_quan(): void
    {
        $admin = $this->createAdmin();
        $owner = User::factory()->create();

        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Không Hợp Lệ',
            'loai_hinh_kinh_doanh' => 'Quán ăn',
            'slug' => 'quan-khong-hop-le',
            'dia_chi_chi_tiet' => '789 Trần Hưng Đạo',
            'so_dien_thoai' => '0933333333',
            'trang_thai' => 'chua_duyet',
            'is_xac_thuc' => false,
        ]);

        // Thao tác Không duyệt qua route reject
        $response = $this->actingAs($admin)->put(route('admin.duyet-xac-thuc.quan.reject', $quan->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $quan->refresh();
        $this->assertEquals('bi_khoa', $quan->trang_thai);
        $this->assertFalse((bool) $quan->is_xac_thuc);
    }

    public function test_admin_can_reject_quan_via_action_param(): void
    {
        $admin = $this->createAdmin();
        $owner = User::factory()->create();

        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quán Spam',
            'loai_hinh_kinh_doanh' => 'Quán ăn',
            'slug' => 'quan-spam',
            'dia_chi_chi_tiet' => '999 Hai Bà Trưng',
            'so_dien_thoai' => '0944444444',
            'trang_thai' => 'chua_duyet',
            'is_xac_thuc' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.duyet-xac-thuc.quan', $quan->id), [
            'action' => 'reject',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $quan->refresh();
        $this->assertEquals('bi_khoa', $quan->trang_thai);
        $this->assertFalse((bool) $quan->is_xac_thuc);
    }

    public function test_admin_can_verify_and_reject_user(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create(['da_xac_thuc' => false]);

        // Xác thực người dùng
        $response = $this->actingAs($admin)->put(route('admin.duyet-xac-thuc.user', $user->id));
        $response->assertRedirect();
        $user->refresh();
        $this->assertTrue((bool) $user->da_xac_thuc);
        $this->assertNotNull($user->ngay_xac_thuc);

        // Từ chối / Không duyệt người dùng
        $responseReject = $this->actingAs($admin)->put(route('admin.duyet-xac-thuc.user.reject', $user->id));
        $responseReject->assertRedirect();
        $user->refresh();
        $this->assertFalse((bool) $user->da_xac_thuc);
        $this->assertNull($user->ngay_xac_thuc);
    }
}
