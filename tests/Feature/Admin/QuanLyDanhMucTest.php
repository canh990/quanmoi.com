<?php

namespace Tests\Feature\Admin;

use App\Models\DanhMucQuan;
use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuanLyDanhMucTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VaiTroSeeder::class);

        $adminRole = VaiTro::where('ten', 'admin')->first();
        $userRole = VaiTro::where('ten', 'user')->first();

        $this->admin = User::factory()->create([
            'email' => 'admin_dm@quanmoi.com',
            'vai_tro_id' => $adminRole?->id,
            'da_xac_thuc' => true,
        ]);

        $this->regularUser = User::factory()->create([
            'email' => 'user_dm@quanmoi.com',
            'vai_tro_id' => $userRole?->id,
        ]);
    }

    public function test_guest_or_regular_user_cannot_access_danh_muc_management(): void
    {
        $response = $this->get(route('admin.danh-muc-quan.index'));
        $response->assertRedirect(route('login'));

        $response = $this->actingAs($this->regularUser)->get(route('admin.danh-muc-quan.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_danh_muc_page(): void
    {
        DanhMucQuan::create([
            'ten_danh_muc' => 'Nhà hàng đặc sản',
            'slug'         => 'nha-hang-dac-san',
            'icon'         => 'restaurant',
            'mau_sac'      => 'rose',
            'thu_tu'       => 1,
            'is_hien_thi'  => true,
            'is_noi_bat'   => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.danh-muc-quan.index'));
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Danh Mục Quán Ăn');
        $response->assertSee('Nhà hàng đặc sản');
        $response->assertSee('#f43f5e'); // Rose color hex
    }

    public function test_admin_can_search_danh_muc(): void
    {
        DanhMucQuan::create([
            'ten_danh_muc' => 'Cà phê acoustic',
            'slug'         => 'ca-phe-acoustic',
            'icon'         => 'local_cafe',
            'mau_sac'      => 'amber',
        ]);
        DanhMucQuan::create([
            'ten_danh_muc' => 'Lẩu bò ba toa',
            'slug'         => 'lau-bo-ba-toa',
            'icon'         => 'ramen_dining',
            'mau_sac'      => 'emerald',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.danh-muc-quan.index', ['q' => 'acoustic']));
        $response->assertStatus(200);
        $response->assertSee('Cà phê acoustic');
        $response->assertDontSee('Lẩu bò ba toa');
    }

    public function test_admin_can_create_new_danh_muc(): void
    {
        $payload = [
            'ten_danh_muc' => 'Trà Sữa Đài Loan',
            'slug'         => 'tra-sua-dai-loan',
            'mo_ta'        => 'Thơm ngon mát lạnh',
            'icon'         => 'emoji_food_beverage',
            'mau_sac'      => 'pink',
            'thu_tu'       => 5,
            'is_hien_thi'  => '1',
            'is_noi_bat'   => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.danh-muc-quan.store'), $payload);
        $response->assertRedirect(route('admin.danh-muc-quan.index'));

        $this->assertDatabaseHas('danh_muc_quan', [
            'ten_danh_muc' => 'Trà Sữa Đài Loan',
            'slug'         => 'tra-sua-dai-loan',
            'icon'         => 'emoji_food_beverage',
            'mau_sac'      => 'pink',
            'is_hien_thi'  => true,
            'is_noi_bat'   => true,
        ]);
    }

    public function test_admin_can_update_danh_muc_and_color(): void
    {
        $category = DanhMucQuan::create([
            'ten_danh_muc' => 'Cà phê truyền thống',
            'slug'         => 'ca-phe-truyen-thong',
            'icon'         => 'local_cafe',
            'mau_sac'      => 'amber',
            'thu_tu'       => 1,
            'is_hien_thi'  => true,
            'is_noi_bat'   => false,
        ]);

        $quan = Quan::factory()->create([
            'chu_quan_id'          => $this->admin->id,
            'ten_quan'             => 'Quán Cafe Test',
            'danh_muc_id'          => $category->id,
            'loai_hinh_kinh_doanh' => 'Cà phê truyền thống',
        ]);

        $updatePayload = [
            'ten_danh_muc' => 'Cà Phê & Trà Thượng Hạng',
            'slug'         => 'ca-phe-tra-thuong-hang',
            'mo_ta'        => 'Hương vị cà phê nguyên bản',
            'icon'         => 'local_cafe',
            'mau_sac'      => 'emerald',
            'thu_tu'       => 2,
            'is_hien_thi'  => '1',
            'is_noi_bat'   => '1',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.danh-muc-quan.update', $category->id), $updatePayload);
        $response->assertRedirect(route('admin.danh-muc-quan.index'));

        $category->refresh();
        $this->assertSame('Cà Phê & Trà Thượng Hạng', $category->ten_danh_muc);
        $this->assertSame('emerald', $category->mau_sac);
        $this->assertSame('#10b981', $category->color_info['hex']);
        $this->assertTrue($category->is_noi_bat);

        // Verify sync loai_hinh_kinh_doanh on associated venues
        $quan->refresh();
        $this->assertSame('Cà Phê & Trà Thượng Hạng', $quan->loai_hinh_kinh_doanh);
    }

    public function test_admin_can_toggle_hien_thi_via_ajax(): void
    {
        $category = DanhMucQuan::create([
            'ten_danh_muc' => 'Món Ăn Vặt',
            'slug'         => 'mon-an-vat',
            'icon'         => 'fastfood',
            'mau_sac'      => 'orange',
            'is_hien_thi'  => true,
        ]);

        $response = $this->actingAs($this->admin)->putJson(route('admin.danh-muc-quan.toggle-hien-thi', $category->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success'     => true,
            'is_hien_thi' => false,
        ]);

        $this->assertFalse($category->fresh()->is_hien_thi);
    }

    public function test_admin_can_toggle_noi_bat_via_ajax(): void
    {
        $category = DanhMucQuan::create([
            'ten_danh_muc' => 'Lẩu Nướng Đêm',
            'slug'         => 'lau-nuong-dem',
            'icon'         => 'ramen_dining',
            'mau_sac'      => 'rose',
            'is_noi_bat'   => false,
        ]);

        $response = $this->actingAs($this->admin)->putJson(route('admin.danh-muc-quan.toggle-noi-bat', $category->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success'    => true,
            'is_noi_bat' => true,
        ]);

        $this->assertTrue($category->fresh()->is_noi_bat);
    }

    public function test_admin_can_destroy_danh_muc_safely(): void
    {
        $category = DanhMucQuan::create([
            'ten_danh_muc' => 'Bida Giải Trí',
            'slug'         => 'bida-giai-tri',
            'icon'         => 'sports_esports',
            'mau_sac'      => 'indigo',
        ]);

        $quan = Quan::factory()->create([
            'chu_quan_id' => $this->admin->id,
            'ten_quan'    => 'CLB Bida Test',
            'danh_muc_id' => $category->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.danh-muc-quan.destroy', $category->id));
        $response->assertRedirect(route('admin.danh-muc-quan.index'));

        $this->assertDatabaseMissing('danh_muc_quan', ['id' => $category->id]);
        $quan->refresh();
        $this->assertNull($quan->danh_muc_id);
    }

    public function test_homepage_renders_category_colors_dynamically(): void
    {
        $category = DanhMucQuan::create([
            'ten_danh_muc' => 'Quán Ăn Healthy',
            'slug'         => 'quan-an-healthy',
            'icon'         => 'spa',
            'mau_sac'      => 'teal',
            'thu_tu'       => 1,
            'is_hien_thi'  => true,
            'is_noi_bat'   => true,
        ]);

        \App\Models\SeoSetting::firstOrCreate([
            'trang' => 'trang-chu',
        ], [
            'ten_trang' => 'Trang Chủ',
            'meta_title' => 'Quán Mới',
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Quán Ăn Healthy');
        // Check that the category color hex (#14b8a6 for teal) is rendered in the HTML style
        $response->assertSee('--cat-hex: #14b8a6', false);
    }

    public function test_quan_automatically_syncs_danh_muc_id_on_save(): void
    {
        $category = DanhMucQuan::firstOrCreate(
            ['slug' => 'billiards-giai-tri'],
            [
                'ten_danh_muc' => 'Billiards & Giải trí',
                'icon'         => 'sports_esports',
                'mau_sac'      => 'indigo',
            ]
        );

        $quan = Quan::factory()->create([
            'chu_quan_id'          => $this->admin->id,
            'ten_quan'             => 'CLB Bida Hoàng Gia',
            'loai_hinh_kinh_doanh' => 'Billiards & Giải trí',
            'danh_muc_id'          => null,
        ]);

        $this->assertSame($category->id, $quan->danh_muc_id);
    }

    public function test_category_page_auto_syncs_unmapped_quans_and_displays_correct_counts(): void
    {
        $cat1 = DanhMucQuan::where('slug', 'lau-nuong')->first();
        $cat2 = DanhMucQuan::where('slug', 'do-an-vat')->first();

        // Tạo quán bằng DB raw không kích hoạt model event để giả lập dữ liệu cũ chưa map
        $quanId1 = (string) \Illuminate\Support\Str::uuid();
        $quanId2 = (string) \Illuminate\Support\Str::uuid();

        \Illuminate\Support\Facades\DB::table('quan')->insert([
            array_merge(Quan::factory()->raw(), [
                'id'                   => $quanId1,
                'chu_quan_id'          => $this->admin->id,
                'ten_quan'             => 'Quán Nướng Test Raw',
                'slug'                 => 'quan-nuong-test-raw',
                'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
                'danh_muc_id'          => null,
                'trang_thai'           => 'da_duyet',
            ]),
            array_merge(Quan::factory()->raw(), [
                'id'                   => $quanId2,
                'chu_quan_id'          => $this->admin->id,
                'ten_quan'             => 'Quán Ăn Vặt Test Raw',
                'slug'                 => 'quan-an-vat-test-raw',
                'loai_hinh_kinh_doanh' => 'Ăn vặt',
                'danh_muc_id'          => null,
                'trang_thai'           => 'da_duyet',
            ]),
        ]);

        // Truy cập trang admin danh mục
        $response = $this->actingAs($this->admin)->get(route('admin.danh-muc-quan.index'));
        $response->assertStatus(200);

        // Kiểm tra sau khi tải trang thì các quán trên đã tự động được sync danh_muc_id
        $this->assertEquals($cat1->id, \Illuminate\Support\Facades\DB::table('quan')->where('id', $quanId1)->value('danh_muc_id'));
        $this->assertEquals($cat2->id, \Illuminate\Support\Facades\DB::table('quan')->where('id', $quanId2)->value('danh_muc_id'));
    }
}

