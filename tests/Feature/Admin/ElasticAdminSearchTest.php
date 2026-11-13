<?php

namespace Tests\Feature\Admin;

use App\Models\Quan;
use App\Models\User;
use App\Services\ElasticAdminSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElasticAdminSearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
        $this->seed(\Database\Seeders\VaiTroSeeder::class);
        $this->seed(\Database\Seeders\QuyenHanSeeder::class);

        $adminRole = \App\Models\VaiTro::where('ten', 'admin')->first();
        $this->admin = User::factory()->create([
            'ho_ten' => 'Admin Controller Test',
            'email' => 'admin_elastic@test.com',
            'trang_thai' => 'hoat_dong',
            'vai_tro_id' => $adminRole?->id,
        ]);
    }

    public function test_global_search_api_requires_admin_auth(): void
    {
        $response = $this->getJson(route('admin.api.global-search', ['q' => 'test']));
        $response->assertStatus(401);
    }

    public function test_global_search_api_returns_json_structure_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('admin.api.global-search', ['q' => 'Admin']));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'is_elastic',
                'query',
                'data' => [
                    'quans',
                    'users',
                    'blogs',
                    'categories',
                    'videos',
                ],
            ]);
    }

    public function test_global_search_api_handles_empty_query(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('admin.api.global-search', ['q' => '']));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'quans' => [],
                    'users' => [],
                    'blogs' => [],
                    'categories' => [],
                    'videos' => [],
                ],
            ]);
    }

    public function test_elastic_service_is_resolvable(): void
    {
        $service = app(ElasticAdminSearchService::class);
        $this->assertInstanceOf(ElasticAdminSearchService::class, $service);
    }

    public function test_quan_ly_quan_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.quan.index', ['q' => 'Phở Bò']));
        $response->assertStatus(200);
    }

    public function test_quan_ly_nguoi_dung_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.nguoi-dung.index', ['q' => 'Admin']));
        $response->assertStatus(200);
    }

    public function test_duyet_xac_thuc_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.duyet-xac-thuc.index', ['q' => 'Test']));
        $response->assertStatus(200);
    }

    public function test_quan_noi_bat_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.quan-noi-bat.index', ['q' => 'Quán']));
        $response->assertStatus(200);
    }

    public function test_blog_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.blog.posts.index', ['q' => 'Ẩm thực']));
        $response->assertStatus(200);
    }

    public function test_phan_quyen_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.phan-quyen.index', ['q' => 'Admin']));
        $response->assertStatus(200);
    }

    public function test_thong_ke_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.thong-ke.index', ['search' => 'VNPAY']));
        $response->assertStatus(200);
    }

    public function test_remove_vietnamese_tones_helper(): void
    {
        $this->assertEquals('Bun Bo Hue', ElasticAdminSearchService::removeVietnameseTones('Bún Bò Huế'));
        $this->assertEquals('Com Tam Sai Gon', ElasticAdminSearchService::removeVietnameseTones('Cơm Tấm Sài Gòn'));
    }

    public function test_quan_ly_quan_fuzzy_search_near_match_returns_ok(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.quan.index', ['q' => 'mesi']));
        $response->assertStatus(200);
    }

    public function test_danh_muc_quan_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.danh-muc-quan.index', ['q' => 'Cà phê']));
        $response->assertStatus(200);
    }

    public function test_video_shorts_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.video-shorts.index', ['q' => 'Review']));
        $response->assertStatus(200);
    }

    public function test_khuyen_mai_ai_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.khuyen-mai-ai.index', ['q' => 'Voucher']));
        $response->assertStatus(200);
    }

    public function test_ai_tools_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ai-tools.index', ['q' => 'Quán']));
        $response->assertStatus(200);
    }

    public function test_thanh_toan_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.thanh-toan.index', ['search' => 'Admin']));
        $response->assertStatus(200);
    }

    public function test_audit_log_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.audit-log.index', ['q' => 'admin']));
        $response->assertStatus(200);
    }

    public function test_blog_category_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.blog.categories.index', ['q' => 'Ẩm thực']));
        $response->assertStatus(200);
    }

    public function test_blog_tag_search_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.blog.tags.index', ['q' => 'Hot']));
        $response->assertStatus(200);
    }

    public function test_thong_bao_page_returns_ok_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.thong-bao.index'));
        $response->assertStatus(200);
    }
}
