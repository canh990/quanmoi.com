<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use App\Models\HinhAnhQuan;
use App\Models\MonTrongMenu;
use App\Services\UploadAnhService;
use Database\Seeders\VaiTroSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadFallbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(VaiTroSeeder::class);
        Storage::fake('uploads');
    }

    public function test_dang_quan_with_cover_and_gallery_succeeds_without_cloud_storage(): void
    {
        $user = User::factory()->create();

        $cover = UploadedFile::fake()->image('cover.jpg', 600, 400);
        $galleryImg = UploadedFile::fake()->image('gallery1.jpg', 800, 600);

        $data = [
            'ten_quan' => 'Quan An Thu Nghiem',
            'loai_hinh_kinh_doanh' => 'Quan an',
            'so_dien_thoai' => '0987654321',
            'email' => 'quan@example.com',
            'dia_chi_chi_tiet' => '123 Nguyen Trai',
            'tinh_thanh_id' => '79',
            'ten_tinh_thanh' => 'TP. Ho Chi Minh',
            'quan_huyen_id' => '760',
            'ten_quan_huyen' => 'Quan 1',
            'phuong_xa_id' => '26734',
            'ten_phuong_xa' => 'Phuong Ben Nghe',
            'mo_ta' => 'Quan an ngon',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
            'gia_nho_nhat' => 20000,
            'gia_lon_nhat' => 100000,
            'anh_bia' => $cover,
            'danh_sach_anh' => [$galleryImg],
        ];

        $response = $this->actingAs($user)->post('/chu-quan/dang-quan', $data, [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $quan = Quan::where('ten_quan', 'Quan An Thu Nghiem')->first();
        $this->assertNotNull($quan);
        $this->assertNotNull($quan->anh_bia);
        $this->assertStringStartsWith('/uploads/quan/anh-bia/', $quan->anh_bia);
        $this->assertStringStartsWith('local:quan/anh-bia/', $quan->anh_bia_key);

        $hinhAnh = HinhAnhQuan::where('quan_id', $quan->id)->first();
        $this->assertNotNull($hinhAnh);
        $this->assertTrue(
            str_starts_with($hinhAnh->duong_dan, '/uploads/quan/gallery/') ||
            str_starts_with($hinhAnh->duong_dan, '/media/venue/quan/gallery/')
        );
        $this->assertStringStartsWith('local:quan/gallery/', $hinhAnh->object_key);
    }

    public function test_menu_item_image_upload_succeeds_without_cloud_storage(): void
    {
        $roleChuQuan = VaiTro::where('ten', 'chu_quan')->first();
        $owner = User::factory()->create(['vai_tro_id' => $roleChuQuan->id]);
        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'trang_thai' => 'da_duyet',
        ]);

        $dishImage = UploadedFile::fake()->image('dish.jpg', 500, 500);

        $response = $this->actingAs($owner)->post(route('chu-quan.quan.menu.update', $quan->slug), [
            'menu_data' => json_encode([
                [
                    'name' => 'Khai vi',
                    'items' => [
                        [
                            'tmp_id' => 1,
                            'name' => 'Cha gio',
                            'price' => 35000,
                            'description' => 'Gion ngon',
                        ],
                    ],
                ],
            ]),
            'item_image_1' => $dishImage,
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('mon_trong_menu', [
            'ten_mon' => 'Cha gio',
            'gia' => 35000,
        ]);

        $mon = MonTrongMenu::where('ten_mon', 'Cha gio')->first();
        $this->assertNotNull($mon->hinh_anh);
        $this->assertStringStartsWith('/uploads/quan/menu/', $mon->hinh_anh);
    }

    public function test_gallery_upload_succeeds_without_cloud_storage(): void
    {
        $roleChuQuan = VaiTro::where('ten', 'chu_quan')->first();
        $owner = User::factory()->create(['vai_tro_id' => $roleChuQuan->id]);
        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'trang_thai' => 'da_duyet',
        ]);

        $image = UploadedFile::fake()->image('photo.jpg', 600, 400);

        $response = $this->actingAs($owner)->post(route('chu-quan.hinh-anh.store', $quan->id), [
            'hinh_anh' => [$image],
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('hinh_anh_quan', [
            'quan_id' => $quan->id,
        ]);

        $hinh = HinhAnhQuan::where('quan_id', $quan->id)->first();
        $this->assertTrue(
            str_starts_with($hinh->duong_dan, '/uploads/quan/gallery/') ||
            str_starts_with($hinh->duong_dan, '/media/venue/quan/gallery/')
        );
        $this->assertStringStartsWith('local:quan/gallery/', $hinh->object_key);
    }

    public function test_upload_avatar_service_succeeds_without_cloud_storage(): void
    {
        $user = User::factory()->create();
        $avatar = UploadedFile::fake()->image('avatar.jpg', 300, 300);

        $service = new UploadAnhService();
        $result = $service->uploadAvatar($avatar, (string) $user->id);

        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('key', $result);
        $this->assertStringStartsWith('/uploads/avatars/', $result['url']);
        $this->assertStringStartsWith('local:avatars/', $result['key']);
    }
}
