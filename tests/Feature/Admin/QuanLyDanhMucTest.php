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
        $this->assertSame('Chưa phân loại', $quan->loai_hinh_kinh_doanh);
    }

    public function test_deleting_tiem_banh_category_moves_active_trashed_and_alias_quans_to_chua_phan_loai_and_filters_correctly(): void
    {
        $category = DanhMucQuan::firstOrCreate(
            ['slug' => 'tiem-banh'],
            [
                'ten_danh_muc' => 'Tiệm bánh',
                'icon'         => 'bakery_dining',
                'mau_sac'      => 'pink',
            ]
        );

        $activeQuan = Quan::factory()->create([
            'chu_quan_id'          => $this->admin->id,
            'ten_quan'             => 'Tiệm Bánh BreadTalk Test',
            'danh_muc_id'          => $category->id,
            'loai_hinh_kinh_doanh' => 'Tiệm bánh',
            'trang_thai'           => 'da_duyet',
        ]);

        $trashedQuan = Quan::factory()->create([
            'chu_quan_id'          => $this->admin->id,
            'ten_quan'             => 'Tiệm Bánh Đã Xóa Mềm',
            'danh_muc_id'          => $category->id,
            'loai_hinh_kinh_doanh' => 'Tiệm bánh',
        ]);
        $trashedQuan->delete();

        $aliasQuan = Quan::factory()->create([
            'chu_quan_id'          => $this->admin->id,
            'ten_quan'             => 'Tiệm Bánh Ngọt Unmapped',
            'danh_muc_id'          => null,
            'loai_hinh_kinh_doanh' => 'Bánh ngọt',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.danh-muc-quan.destroy', $category->id));
        $response->assertRedirect(route('admin.danh-muc-quan.index'));
        $this->assertDatabaseMissing('danh_muc_quan', ['id' => $category->id]);

        foreach ([$activeQuan, $trashedQuan, $aliasQuan] as $quan) {
            $fresh = Quan::withTrashed()->findOrFail($quan->id);
            $this->assertNull($fresh->danh_muc_id);
            $this->assertSame('Chưa phân loại', $fresh->loai_hinh_kinh_doanh);
        }

        // Lọc theo "Chưa phân loại" trên trang admin/quan
        $adminQuanResponse = $this->actingAs($this->admin)->get(route('admin.quan.index', ['loai_hinh' => 'Chưa phân loại']));
        $adminQuanResponse->assertStatus(200);
        $adminQuanResponse->assertSee('Tiệm Bánh BreadTalk Test');
        $adminQuanResponse->assertSee('Chưa phân loại');

        // Quick view JSON trên admin/quan/{id}
        $showResponse = $this->actingAs($this->admin)->getJson(route('admin.quan.show', $activeQuan->id));
        $showResponse->assertStatus(200);
        $showResponse->assertJsonPath('data.loai_hinh_kinh_doanh', 'Chưa phân loại');

        // Trang chi tiết công khai hiển thị đúng "Chưa phân loại"
        $detailResponse = $this->get(route('quan.detail', $activeQuan->slug));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Chưa phân loại');
        $detailResponse->assertDontSee('Chưa Phân Loại');
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

    public function test_admin_cannot_create_duplicate_danh_muc_name_ignoring_case_and_whitespace(): void
    {
        $initialCount = DanhMucQuan::count();

        // Dù nhập khác hoa/thường, thừa khoảng trắng và đổi sang slug khác ("nha-hang1"), hệ thống vẫn chặn
        $webResponse = $this->actingAs($this->admin)->post(route('admin.danh-muc-quan.store'), [
            'ten_danh_muc' => '  nhà   HÀNG  ',
            'slug'         => 'nha-hang1',
            'mau_sac'      => 'amber',
        ]);
        $webResponse->assertSessionHasErrors('ten_danh_muc');
        $this->assertSame($initialCount, DanhMucQuan::count());

        // Không còn khoảng trắng giữa các từ vẫn bị chặn qua JSON (422)
        $jsonResponse = $this->actingAs($this->admin)->postJson(route('admin.danh-muc-quan.store'), [
            'ten_danh_muc' => 'NhàHàng',
            'slug'         => 'nhahang-moi',
        ]);
        $jsonResponse->assertStatus(422);
        $jsonResponse->assertJsonValidationErrors('ten_danh_muc');

        // Chỉ toàn khoảng trắng cũng không hợp lệ
        $blankResponse = $this->actingAs($this->admin)->postJson(route('admin.danh-muc-quan.store'), [
            'ten_danh_muc' => '     ',
            'slug'         => 'blank-cat',
        ]);
        $blankResponse->assertStatus(422);
        $blankResponse->assertJsonValidationErrors('ten_danh_muc');

        $this->assertSame($initialCount, DanhMucQuan::count());
    }

    public function test_admin_cannot_update_danh_muc_to_duplicate_name_ignoring_case_and_whitespace(): void
    {
        $nhaHang = DanhMucQuan::where('slug', 'nha-hang')->firstOrFail();
        $caPhe = DanhMucQuan::where('slug', 'ca-phe-tra')->firstOrFail();

        // Không thể đổi tên "Cà phê & Trà" thành biến thể hoa/thường/khoảng trắng của "Nhà hàng"
        $conflictResponse = $this->actingAs($this->admin)->putJson(route('admin.danh-muc-quan.update', $caPhe->id), [
            'ten_danh_muc' => '  NHÀ   hàng ',
            'slug'         => $caPhe->slug,
            'mo_ta'        => 'Mô tả test',
            'mau_sac'      => 'amber',
            'thu_tu'       => 2,
        ]);
        $conflictResponse->assertStatus(422);
        $conflictResponse->assertJsonValidationErrors('ten_danh_muc');
        $this->assertSame('Cà phê & Trà', $caPhe->fresh()->ten_danh_muc);

        // Vẫn cho phép lưu chính danh mục "Nhà hàng" với tên của nó (đồng thời dọn khoảng trắng thừa)
        $selfUpdateResponse = $this->actingAs($this->admin)->putJson(route('admin.danh-muc-quan.update', $nhaHang->id), [
            'ten_danh_muc' => '  Nhà   hàng  ',
            'slug'         => $nhaHang->slug,
            'mo_ta'        => 'Cập nhật mô tả hợp lệ',
            'mau_sac'      => 'rose',
            'thu_tu'       => 1,
            'is_hien_thi'  => '1',
            'is_noi_bat'   => '1',
        ]);
        $selfUpdateResponse->assertStatus(200);
        $this->assertSame('Nhà hàng', $nhaHang->fresh()->ten_danh_muc);
    }

    public function test_model_level_save_blocks_duplicate_danh_muc_name_and_cleans_whitespace(): void
    {
        $created = DanhMucQuan::create([
            'ten_danh_muc' => '  Cơm   Tấm   Sài Gòn  ',
            'slug'         => 'com-tam-sai-gon',
        ]);
        $this->assertSame('Cơm Tấm Sài Gòn', $created->ten_danh_muc);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        DanhMucQuan::create([
            'ten_danh_muc' => 'cơm tấm   sài gòn',
            'slug'         => 'com-tam-sai-gon-2',
        ]);
    }

    public function test_deleting_nonexistent_or_already_deleted_danh_muc_in_second_tab_shows_invalid_delete_error(): void
    {
        $category = DanhMucQuan::create([
            'ten_danh_muc' => 'Danh Mục Tab Test',
            'slug'         => 'danh-muc-tab-test',
        ]);

        // Tab 1: Xóa thành công
        $resTab1 = $this->actingAs($this->admin)->delete(route('admin.danh-muc-quan.destroy', $category->id));
        $resTab1->assertRedirect(route('admin.danh-muc-quan.index'));
        $resTab1->assertSessionHas('success');

        // Tab 2: Xóa lại cùng ID -> Không văng 404, trả về redirect kèm thông báo lỗi xóa không hợp lệ
        $resTab2 = $this->actingAs($this->admin)->delete(route('admin.danh-muc-quan.destroy', $category->id));
        $resTab2->assertRedirect(route('admin.danh-muc-quan.index'));
        $resTab2->assertSessionHas('error');

        // ID không hợp lệ (abc hoặc 99999999999) cũng báo lỗi hợp lệ, không bị 500 TypeError
        $resAbc = $this->actingAs($this->admin)->delete('/admin/danh-muc-quan/abc');
        $resAbc->assertRedirect(route('admin.danh-muc-quan.index'));
        $resAbc->assertSessionHas('error');

        $resBig = $this->actingAs($this->admin)->delete('/admin/danh-muc-quan/99999999999');
        $resBig->assertRedirect(route('admin.danh-muc-quan.index'));
        $resBig->assertSessionHas('error');
    }

    public function test_duplicate_tab_update_and_special_inputs_fullwidth_2byte_space_and_select_validation(): void
    {
        $category = DanhMucQuan::create([
            'ten_danh_muc' => 'Danh Mục Update Tab',
            'slug'         => 'danh-muc-update-tab',
            'mau_sac'      => 'amber',
            'icon'         => 'restaurant',
        ]);
        $staleTimestamp = $category->updated_at->toISOString();

        // Tab 1: Cập nhật thành công
        $resTab1 = $this->actingAs($this->admin)->put(route('admin.danh-muc-quan.update', $category->id), [
            'ten_danh_muc'    => 'Danh Mục Update Tab',
            'slug'            => 'danh-muc-update-tab',
            'mau_sac'         => 'rose',
            'icon'            => 'restaurant',
            'last_updated_at' => $staleTimestamp,
        ]);
        $resTab1->assertRedirect(route('admin.danh-muc-quan.index'));
        $resTab1->assertSessionHas('success');

        // Tab 2: Gửi cùng last_updated_at cũ -> Bị chặn và báo tải lại trang trước khi cập nhật
        $resTab2 = $this->actingAs($this->admin)->put(route('admin.danh-muc-quan.update', $category->id), [
            'ten_danh_muc'    => 'Danh Mục Update Tab 2',
            'slug'            => 'danh-muc-update-tab',
            'mau_sac'         => 'emerald',
            'icon'            => 'restaurant',
            'last_updated_at' => $staleTimestamp,
        ]);
        $resTab2->assertRedirect(route('admin.danh-muc-quan.index'));
        $resTab2->assertSessionHas('error');
        $this->assertSame('rose', $category->fresh()->mau_sac);

        // Kiểm tra khoảng trắng 2-byte (　), số full-width (０１２３) và select-option (mau_sac / icon giả mạo)
        $resInvalid = $this->actingAs($this->admin)->postJson(route('admin.danh-muc-quan.store'), [
            'ten_danh_muc' => '　　',
            'thu_tu'       => '０１２３',
            'mau_sac'      => 'invalid-color',
            'icon'         => 'invalid-icon',
        ]);
        $resInvalid->assertStatus(422);
        $resInvalid->assertJsonValidationErrors(['ten_danh_muc', 'thu_tu', 'mau_sac', 'icon']);
    }
}

