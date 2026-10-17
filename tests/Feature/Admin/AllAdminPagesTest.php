<?php

namespace Tests\Feature\Admin;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\Quan;
use App\Models\SeoSetting;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllAdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Quan $quan;
    protected Blog $blog;

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

        $this->quan = Quan::factory()->create([
            'chu_quan_id' => $this->admin->id,
            'ten_quan' => 'Quán Mẫu Test',
            'trang_thai' => 'chua_duyet',
        ]);

        $category = BlogCategory::create([
            'name' => 'Ẩm thực',
            'slug' => 'am-thuc',
            'status' => true,
        ]);

        $this->blog = Blog::create([
            'title' => 'Bài viết mẫu kiểm thử',
            'slug' => 'bai-viet-mau-kiem-thu',
            'excerpt' => 'Tóm tắt bài viết mẫu',
            'content' => 'Nội dung chi tiết bài viết mẫu',
            'author_id' => $this->admin->id,
            'user_id' => $this->admin->id,
            'category_id' => $category->id,
            'status' => 'pending',
            'published_at' => now(),
        ]);

        SeoSetting::firstOrCreate([
            'trang' => 'trang-chu',
        ], [
            'ten_trang' => 'Trang Chủ',
            'meta_title' => 'Quán Mới - Nền tảng ẩm thực',
            'meta_description' => 'Khám phá quán ăn ngon',
        ]);
    }

    public function test_admin_dashboard_loads_cleanly(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Tổng Quan');
    }

    public function test_all_admin_navigation_pages_render_without_errors(): void
    {
        $routes = [
            route('admin.thong-ke.index'),
            route('admin.nguoi-dung.index'),
            route('admin.nguoi-dung.edit', $this->admin->id),
            route('admin.vai-tro.index'),
            route('admin.phan-quyen.index'),
            route('admin.quan.index'),
            route('admin.quan.edit', $this->quan->id),
            route('admin.duyet-xac-thuc.index'),
            route('admin.thong-bao.index'),
            route('admin.realtime-check'),
            route('admin.bao-mat.index'),
            route('admin.audit-log.index'),
            route('admin.quan-noi-bat.index'),
            route('admin.ai-tools.index'),
            route('admin.seo.index'),
            route('admin.seo.edit', 'trang-chu'),
            route('admin.blog.dashboard'),
            route('admin.blog.posts.index'),
            route('admin.blog.posts.pending'),
            route('admin.blog.posts.show', $this->blog->id),
            route('admin.blog.categories.index'),
            route('admin.blog.tags.index'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->admin)->get($url);
            $this->assertContains($response->getStatusCode(), [200, 302], "Failed asserting route renders cleanly: {$url}");
        }
    }
}
