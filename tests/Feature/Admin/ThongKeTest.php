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
}
