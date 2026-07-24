<?php

namespace Tests\Feature\ChuQuan;

use App\Models\DanhMucMenu;
use App\Models\MonTrongMenu;
use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuanLyQuanChuQuanTest extends TestCase
{
    use RefreshDatabase;

    protected function createOwner(): User
    {
        $roleChuQuan = VaiTro::firstOrCreate(['ten' => 'chu_quan']);

        return User::factory()->create([
            'vai_tro_id' => $roleChuQuan->id,
        ]);
    }

    public function test_owner_can_view_only_their_quan_in_management_index(): void
    {
        $owner = $this->createOwner();
        $otherOwner = $this->createOwner();

        Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quan Cua Toi',
            'loai_hinh_kinh_doanh' => 'Quan an',
            'slug' => 'quan-cua-toi',
            'dia_chi_chi_tiet' => '12 Ly Thuong Kiet',
            'ten_tinh_thanh' => 'Ha Noi',
            'ten_quan_huyen' => 'Hoan Kiem',
            'so_dien_thoai' => '0901230001',
            'gio_mo_cua' => '07:00',
            'gio_dong_cua' => '21:00',
            'trang_thai' => 'chua_duyet',
        ]);

        Quan::create([
            'chu_quan_id' => $otherOwner->id,
            'ten_quan' => 'Quan Cua Nguoi Khac',
            'loai_hinh_kinh_doanh' => 'Quan an',
            'slug' => 'quan-cua-nguoi-khac',
            'dia_chi_chi_tiet' => '99 Nguyen Hue',
            'ten_tinh_thanh' => 'TP. Ho Chi Minh',
            'ten_quan_huyen' => 'Quan 1',
            'so_dien_thoai' => '0901230002',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->actingAs($owner)->get(route('chu-quan.quan.index'));

        $response->assertOk();
        $response->assertSee('Quan Cua Toi');
        $response->assertDontSee('Quan Cua Nguoi Khac');
    }

    public function test_owner_can_update_their_quan(): void
    {
        $owner = $this->createOwner();

        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Bun Dau Cu',
            'loai_hinh_kinh_doanh' => 'Quan an',
            'slug' => 'bun-dau-cu',
            'dia_chi_chi_tiet' => '55 Bach Mai',
            'ten_tinh_thanh' => 'Ha Noi',
            'ten_quan_huyen' => 'Hai Ba Trung',
            'so_dien_thoai' => '0901230003',
            'gio_mo_cua' => '09:00',
            'gio_dong_cua' => '21:00',
            'gia_nho_nhat' => 30000,
            'gia_lon_nhat' => 60000,
            'trang_thai' => 'chua_duyet',
        ]);

        $response = $this->actingAs($owner)->put(route('chu-quan.quan.update', ['slug' => $quan->slug]), [
            'ten_quan' => 'Bun Dau Moi',
            'loai_hinh_kinh_doanh' => 'Quan an',
            'so_dien_thoai' => '0901230003',
            'email' => 'owner@example.com',
            'dia_chi_chi_tiet' => '77 Minh Khai',
            'ten_tinh_thanh' => 'Ha Noi',
            'ten_quan_huyen' => 'Hai Ba Trung',
            'ten_phuong_xa' => 'Vinh Tuy',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
            'gia_nho_nhat' => 35000,
            'gia_lon_nhat' => 65000,
            'mo_ta' => 'Cap nhat thong tin moi',
        ]);

        $response->assertRedirect(route('chu-quan.quan.show', ['slug' => 'bun-dau-moi']));

        $this->assertDatabaseHas('quan', [
            'id' => $quan->id,
            'ten_quan' => 'Bun Dau Moi',
            'slug' => 'bun-dau-moi',
            'dia_chi_chi_tiet' => '77 Minh Khai',
            'gia_nho_nhat' => 35000,
        ]);
    }

    public function test_owner_cannot_edit_other_owner_quan(): void
    {
        $owner = $this->createOwner();
        $otherOwner = $this->createOwner();

        $quan = Quan::create([
            'chu_quan_id' => $otherOwner->id,
            'ten_quan' => 'Quan Khoa',
            'loai_hinh_kinh_doanh' => 'Quan an',
            'slug' => 'quan-khoa',
            'dia_chi_chi_tiet' => '11 Dien Bien Phu',
            'ten_tinh_thanh' => 'Da Nang',
            'ten_quan_huyen' => 'Hai Chau',
            'so_dien_thoai' => '0901230004',
            'gio_mo_cua' => '10:00',
            'gio_dong_cua' => '22:00',
            'trang_thai' => 'da_duyet',
        ]);

        $this->actingAs($owner)
            ->get(route('chu-quan.quan.edit', ['slug' => $quan->slug]))
            ->assertForbidden();
    }

    public function test_owner_can_add_and_remove_menu_item(): void
    {
        $owner = $this->createOwner();

        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quan Menu',
            'loai_hinh_kinh_doanh' => 'Quan an',
            'slug' => 'quan-menu',
            'dia_chi_chi_tiet' => '12 Nguyen Van Linh',
            'ten_tinh_thanh' => 'Da Nang',
            'ten_quan_huyen' => 'Thanh Khe',
            'so_dien_thoai' => '0901230005',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '23:00',
            'trang_thai' => 'da_duyet',
        ]);

        $this->actingAs($owner)
            ->post(route('chu-quan.danh-muc.store', ['slug' => $quan->slug]), [
                'ten_danh_muc' => 'Mon Chinh',
                'thu_tu' => 1,
            ])
            ->assertRedirect(route('chu-quan.quan.show', ['slug' => $quan->slug]));

        $category = DanhMucMenu::where('quan_id', $quan->id)->first();

        $this->assertNotNull($category);

        $this->actingAs($owner)
            ->post(route('chu-quan.mon.store', ['id' => $category->id]), [
                'ten_mon' => 'Pho Bo',
                'mo_ta' => 'Pho bo tai lan',
                'gia' => 65000,
                'con_hang' => 1,
            ])
            ->assertRedirect(route('chu-quan.quan.show', ['slug' => $quan->slug]));

        $item = MonTrongMenu::where('danh_muc_id', $category->id)->first();

        $this->assertNotNull($item);
        $this->assertDatabaseHas('mon_trong_menu', [
            'id' => $item->id,
            'ten_mon' => 'Pho Bo',
            'gia' => 65000,
        ]);

        $this->actingAs($owner)
            ->delete(route('chu-quan.mon.destroy', ['id' => $item->id]))
            ->assertRedirect(route('chu-quan.quan.show', ['slug' => $quan->slug]));

        $this->assertDatabaseMissing('mon_trong_menu', [
            'id' => $item->id,
        ]);
    }
}
