<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuanPreviewTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $role = VaiTro::firstOrCreate(
            ['ten' => 'admin'],
            ['mo_ta' => 'Quản trị viên']
        );

        $admin = User::factory()->create([
            'vai_tro_id' => $role->id,
            'trang_thai' => 'hoat_dong',
        ]);

        return $admin;
    }

    private function createNormalUser(): User
    {
        $role = VaiTro::firstOrCreate(
            ['ten' => 'thanh_vien'],
            ['mo_ta' => 'Thành viên']
        );

        return User::factory()->create([
            'vai_tro_id' => $role->id,
            'trang_thai' => 'hoat_dong',
        ]);
    }

    public function test_guest_cannot_view_pending_venue(): void
    {
        $quan = Quan::factory()->create([
            'slug' => 'quan-test-cho-duyet',
            'trang_thai' => 'chua_duyet',
        ]);

        $response = $this->get(route('quan.detail', $quan->slug));
        $response->assertNotFound();
    }

    public function test_normal_user_cannot_view_pending_venue(): void
    {
        $user = $this->createNormalUser();
        $quan = Quan::factory()->create([
            'slug' => 'quan-test-cho-duyet-2',
            'trang_thai' => 'chua_duyet',
        ]);

        $response = $this->actingAs($user)->get(route('quan.detail', $quan->slug));
        $response->assertNotFound();
    }

    public function test_admin_can_preview_pending_venue(): void
    {
        $admin = $this->createAdmin();
        $quan = Quan::factory()->create([
            'slug' => 'quan-test-cho-duyet-admin',
            'trang_thai' => 'chua_duyet',
            'ten_quan' => 'Quán Đang Chờ Duyệt Admin',
        ]);

        $response = $this->actingAs($admin)->get(route('quan.detail', $quan->slug));
        $response->assertOk();
        $response->assertSee('Chế độ xem trước');
        $response->assertSee('Quán Đang Chờ Duyệt Admin');
    }

    public function test_owner_can_preview_their_own_pending_venue(): void
    {
        $owner = $this->createNormalUser();
        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'slug' => 'quan-test-chu-quan-preview',
            'trang_thai' => 'chua_duyet',
            'ten_quan' => 'Quán Của Tôi Chờ Duyệt',
        ]);

        $response = $this->actingAs($owner)->get(route('quan.detail', $quan->slug));
        $response->assertOk();
        $response->assertSee('Chế độ xem trước');
        $response->assertSee('Quán Của Tôi Chờ Duyệt');
    }

    public function test_slug_direct_url_redirects_to_quan_detail(): void
    {
        $quan = Quan::factory()->create([
            'slug' => 'quan-direct-slug-test',
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->get('/quan-direct-slug-test');
        $response->assertRedirect(route('quan.detail', 'quan-direct-slug-test'));
    }

    public function test_admin_visiting_direct_slug_of_pending_venue_can_view_after_redirect(): void
    {
        $admin = $this->createAdmin();
        $quan = Quan::factory()->create([
            'slug' => 'sai-gon',
            'trang_thai' => 'chua_duyet',
            'ten_quan' => 'Quán Sài Gòn Đang Chờ Duyệt',
        ]);

        $response = $this->actingAs($admin)->get('/sai-gon');
        $response->assertRedirect(route('quan.detail', 'sai-gon'));

        $followResponse = $this->actingAs($admin)->get(route('quan.detail', 'sai-gon'));
        $followResponse->assertOk();
        $followResponse->assertSee('Chế độ xem trước');
        $followResponse->assertSee('Quán Sài Gòn Đang Chờ Duyệt');
    }
}

