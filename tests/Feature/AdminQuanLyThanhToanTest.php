<?php

namespace Tests\Feature;

use App\Models\ThanhToan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminQuanLyThanhToanTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin(): User
    {
        $roleAdmin = VaiTro::firstOrCreate(['ten' => 'admin']);
        return User::factory()->create([
            'vai_tro_id' => $roleAdmin->id,
        ]);
    }

    protected function createRegularUser(float $balance = 0): User
    {
        $roleUser = VaiTro::firstOrCreate(['ten' => 'nguoi_dung']);
        return User::factory()->create([
            'vai_tro_id' => $roleUser->id,
            'so_du' => $balance,
        ]);
    }

    public function test_nguoi_dung_thuong_khong_the_truy_cap_quan_ly_thanh_toan(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->get(route('admin.thanh-toan.index'));

        $response->assertStatus(403);
    }

    public function test_admin_co_the_xem_trang_quan_ly_nap_tien_va_doanh_thu(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createRegularUser(50000);

        ThanhToan::create([
            'user_id' => $user->id,
            'ma_giao_dich' => 'NAP261030TEST01',
            'ten_goi_dich_vu' => 'Nạp tiền vào tài khoản ví Quán Mới',
            'so_tien' => 50000,
            'phuong_thuc' => 'vietqr',
            'trang_thai' => 'thanh_cong',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.thanh-toan.index'));

        $response->assertOk();
        $response->assertSee('Quản Lý Nạp Tiền & Doanh Thu Ví');
        $response->assertSee('NAP261030TEST01');
    }

    public function test_admin_duyet_thu_cong_don_nap_tien_cong_so_du_chinh_xac(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createRegularUser(10000);

        $thanhToan = ThanhToan::create([
            'user_id' => $user->id,
            'ma_giao_dich' => 'NAP261030PEND01',
            'ten_goi_dich_vu' => 'Nạp tiền vào tài khoản ví Quán Mới',
            'so_tien' => 100000,
            'phuong_thuc' => 'vietqr',
            'trang_thai' => 'cho_thanh_toan',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.thanh-toan.approve', $thanhToan->id), [
            'ma_tham_chieu' => 'FT26303999111',
            'ghi_chu_admin' => 'Đã đối soát sao kê MBBank',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('thanh_toan', [
            'id' => $thanhToan->id,
            'trang_thai' => 'thanh_cong',
            'sepay_transaction_id' => 'FT26303999111',
        ]);

        $user->refresh();
        $this->assertEquals(110000, (float) $user->so_du);

        // Duyệt lần 2 cho cùng đơn đã thành công không được cộng trùng lặp
        $secondResponse = $this->actingAs($admin)->put(route('admin.thanh-toan.approve', $thanhToan->id));
        $secondResponse->assertRedirect();
        $secondResponse->assertSessionHas('error');

        $user->refresh();
        $this->assertEquals(110000, (float) $user->so_du);
    }

    public function test_admin_huy_don_nap_tien_dang_cho(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createRegularUser(0);

        $thanhToan = ThanhToan::create([
            'user_id' => $user->id,
            'ma_giao_dich' => 'NAP261030CANC01',
            'ten_goi_dich_vu' => 'Nạp tiền vào tài khoản ví Quán Mới',
            'so_tien' => 50000,
            'phuong_thuc' => 'vietqr',
            'trang_thai' => 'cho_thanh_toan',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.thanh-toan.cancel', $thanhToan->id), [
            'ly_do_huy' => 'Quá hạn thanh toán 24h',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('thanh_toan', [
            'id' => $thanhToan->id,
            'trang_thai' => 'da_huy',
        ]);
    }

    public function test_admin_dieu_chinh_cong_va_tru_so_du_vi_nguoi_dung(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createRegularUser(20000);

        // 1. Cộng thêm 30.000đ
        $resCredit = $this->actingAs($admin)->post(route('admin.thanh-toan.adjust-balance'), [
            'email_hoac_id' => $user->email,
            'loai_dieu_chinh' => 'cong_tien',
            'so_tien' => 30000,
            'ghi_chu' => 'Nạp bù giao dịch chuyển khoản nhầm nội dung',
        ]);

        $resCredit->assertRedirect();
        $resCredit->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals(50000, (float) $user->so_du);

        // 2. Trừ 15.000đ
        $resDebit = $this->actingAs($admin)->post(route('admin.thanh-toan.adjust-balance'), [
            'email_hoac_id' => $user->email,
            'loai_dieu_chinh' => 'tru_tien',
            'so_tien' => 15000,
            'ghi_chu' => 'Thu hồi tiền nạp nhầm',
        ]);

        $resDebit->assertRedirect();
        $resDebit->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals(35000, (float) $user->so_du);

        // 3. Trừ vượt quá số dư hiện tại -> Báo lỗi và giữ nguyên số dư
        $resOverDebit = $this->actingAs($admin)->post(route('admin.thanh-toan.adjust-balance'), [
            'email_hoac_id' => $user->email,
            'loai_dieu_chinh' => 'tru_tien',
            'so_tien' => 100000,
            'ghi_chu' => 'Trừ quá số dư',
        ]);

        $resOverDebit->assertRedirect();
        $resOverDebit->assertSessionHas('error');

        $user->refresh();
        $this->assertEquals(35000, (float) $user->so_du);
    }

    public function test_admin_xuat_bao_cao_csv(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createRegularUser(50000);

        ThanhToan::create([
            'user_id' => $user->id,
            'ma_giao_dich' => 'NAP261030CSV01',
            'ten_goi_dich_vu' => 'Nạp tiền vào tài khoản ví Quán Mới',
            'so_tien' => 50000,
            'phuong_thuc' => 'vietqr',
            'trang_thai' => 'thanh_cong',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.thanh-toan.export'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
    }
}

