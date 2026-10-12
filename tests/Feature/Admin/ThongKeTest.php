<?php

namespace Tests\Feature\Admin;

use App\Models\Quan;
use App\Models\ThanhToan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThongKeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VaiTroSeeder::class);
    }

    public function test_non_admin_cannot_access_statistics(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/thong-ke');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_statistics_dashboard(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/thong-ke');
        $response->assertStatus(200);
        $response->assertSee('Báo Cáo & Thống Kê Chi Tiết');
    }

    public function test_admin_can_filter_statistics_by_date_preset(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/thong-ke?preset=7days');
        $response->assertStatus(200);
        $response->assertSee('Báo Cáo & Thống Kê Chi Tiết');
    }

    public function test_admin_can_export_statistics_csv(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/thong-ke/export?preset=30days');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_filter_handles_inverted_dates_and_caps_over_365_days(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        // Inverted dates: tu_ngay > den_ngay
        $response = $this->actingAs($admin)->get('/admin/thong-ke?tu_ngay=2026-10-20&den_ngay=2026-10-01');
        $response->assertStatus(200);

        // Over 365 days: from 2020-01-01 to 2026-10-12
        $responseRange = $this->actingAs($admin)->get('/admin/thong-ke?tu_ngay=2020-01-01&den_ngay=2026-10-12');
        $responseRange->assertStatus(200);
    }

    public function test_admin_search_handles_special_characters_safely(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/thong-ke?search=%25_test');
        $response->assertStatus(200);
    }

    public function test_admin_pages_contain_back_to_home_button(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Quay về trang chủ');
    }
}
