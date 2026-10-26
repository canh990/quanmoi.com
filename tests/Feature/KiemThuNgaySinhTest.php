<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Suite kiểm thử tự động cho 20 Test Case Ngày sinh (TC_DOB_001 -> TC_DOB_020)
 * Giả định định dạng ngày sinh là dd/mm/yyyy (tương ứng 10 ký tự tính cả dấu /).
 */
class KiemThuNgaySinhTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC_DOB_001: Ngày sinh hợp lệ
     * Dữ liệu: 15/08/2000
     * Kết quả mong đợi: Chấp nhận ngày sinh, lưu thành 2000-08-15 trong CSDL
     */
    public function test_tc_dob_001_ngay_sinh_hop_le(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '15/08/2000',
        ]);

        $response->assertRedirect(route('tai-khoan.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'ngay_sinh' => '2000-08-15',
        ]);
    }

    /**
     * TC_DOB_002: Ngày sinh để trống
     * Dữ liệu: Để trống
     * Kết quả mong đợi: Cho phép vì không bắt buộc (nullable), lưu null vào CSDL
     */
    public function test_tc_dob_002_ngay_sinh_de_trong(): void
    {
        $user = User::factory()->create([
            'ngay_sinh' => '1995-10-10',
        ]);

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '',
        ]);

        $response->assertRedirect(route('tai-khoan.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'ngay_sinh' => null,
        ]);
    }

    /**
     * TC_DOB_003: Nhập thiếu ngày (1 chữ số ngày)
     * Dữ liệu: 1/08/2000
     * Kết quả mong đợi: Xử lý theo định dạng được hỗ trợ, chuẩn hóa thành 2000-08-01, không tự lưu sai ngày
     */
    public function test_tc_dob_003_nhap_thieu_ngay(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '1/08/2000',
        ]);

        $response->assertRedirect(route('tai-khoan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'ngay_sinh' => '2000-08-01',
        ]);
    }

    /**
     * TC_DOB_004: Nhập thiếu tháng (1 chữ số tháng)
     * Dữ liệu: 15/8/2000
     * Kết quả mong đợi: Xử lý theo định dạng được hỗ trợ, chuẩn hóa thành 2000-08-15
     */
    public function test_tc_dob_004_nhap_thieu_thang(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '15/8/2000',
        ]);

        $response->assertRedirect(route('tai-khoan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'ngay_sinh' => '2000-08-15',
        ]);
    }

    /**
     * TC_DOB_005: Ngày vượt phạm vi
     * Dữ liệu: 32/01/2000
     * Kết quả mong đợi: Từ chối ngày không hợp lệ, báo lỗi validation
     */
    public function test_tc_dob_005_ngay_vuot_pham_vi(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '32/01/2000',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
    }

    /**
     * TC_DOB_006: Tháng vượt phạm vi
     * Dữ liệu: 15/13/2000
     * Kết quả mong đợi: Từ chối tháng không hợp lệ (> 12)
     */
    public function test_tc_dob_006_thang_vuot_pham_vi(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '15/13/2000',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
    }

    /**
     * TC_DOB_007: Ngày bằng 00
     * Dữ liệu: 00/01/2000
     * Kết quả mong đợi: Từ chối dữ liệu ngày bằng 00
     */
    public function test_tc_dob_007_ngay_bang_00(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '00/01/2000',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
    }

    /**
     * TC_DOB_008: Tháng bằng 00
     * Dữ liệu: 15/00/2000
     * Kết quả mong đợi: Từ chối dữ liệu tháng bằng 00
     */
    public function test_tc_dob_008_thang_bang_00(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '15/00/2000',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
    }

    /**
     * TC_DOB_009: Ngày 29/02 năm nhuận
     * Dữ liệu: 29/02/2024
     * Kết quả mong đợi: Chấp nhận ngày hợp lệ (2024 là năm nhuận), lưu 2024-02-29
     */
    public function test_tc_dob_009_ngay_29_02_nam_nhuan(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '29/02/2024',
        ]);

        $response->assertRedirect(route('tai-khoan.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'ngay_sinh' => '2024-02-29',
        ]);
    }

    /**
     * TC_DOB_010: Ngày 29/02 năm không nhuận
     * Dữ liệu: 29/02/2023
     * Kết quả mong đợi: Từ chối dữ liệu (2023 không có 29/02)
     */
    public function test_tc_dob_010_ngay_29_02_nam_khong_nhuan(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '29/02/2023',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
    }

    /**
     * TC_DOB_011: Nhập ký tự chữ
     * Dữ liệu: ab/cd/efgh
     * Kết quả mong đợi: Từ chối dữ liệu không đúng định dạng
     */
    public function test_tc_dob_011_nhap_ky_tu_chu(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => 'ab/cd/efgh',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
    }

    /**
     * TC_DOB_012: Nhập ký tự đặc biệt
     * Dữ liệu: @@/##/!!!!
     * Kết quả mong đợi: Từ chối dữ liệu ký tự đặc biệt
     */
    public function test_tc_dob_012_nhap_ky_tu_dac_biet(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '@@/##/!!!!',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
    }

    /**
     * TC_DOB_013: Nhập chuỗi 9 ký tự (thiếu dấu gạch chéo)
     * Dữ liệu: 010120000
     * Kết quả mong đợi: Không chấp nhận nếu không đúng định dạng dd/mm/yyyy
     */
    public function test_tc_dob_013_nhap_chuoi_9_ky_tu(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '010120000',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
    }

    /**
     * TC_DOB_014: Nhập chuỗi 10 ký tự hợp lệ
     * Dữ liệu: 01/01/2000
     * Kết quả mong đợi: Chấp nhận dữ liệu, lưu thành 2000-01-01
     */
    public function test_tc_dob_014_nhap_chuoi_10_ky_tu_hop_le(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '01/01/2000',
        ]);

        $response->assertRedirect(route('tai-khoan.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'ngay_sinh' => '2000-01-01',
        ]);
    }

    /**
     * TC_DOB_015: Nhập chuỗi dài hơn 10 ký tự (năm 5 chữ số)
     * Dữ liệu: 01/01/20001
     * Kết quả mong đợi: Không cho nhập vượt độ dài hoặc hiển thị lỗi
     */
    public function test_tc_dob_015_nhap_chuoi_dai_hon_10_ky_tu(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '01/01/20001',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
    }

    /**
     * TC_DOB_016: Dán chuỗi ngày sinh quá dài
     * Dữ liệu: 01/01/200012345
     * Kết quả mong đợi: Dữ liệu vượt giới hạn bị chặn hoặc báo lỗi
     */
    public function test_tc_dob_016_dan_chuoi_ngay_sinh_qua_dai(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '01/01/200012345',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
    }

    /**
     * TC_DOB_017: Nhập ngày sinh trong tương lai
     * Dữ liệu: Ngày sau ngày hiện tại (ngày mai)
     * Kết quả mong đợi: Từ chối nếu quy định không cho phép ngày sinh trong tương lai
     */
    public function test_tc_dob_017_nhap_ngay_sinh_trong_tuong_lai(): void
    {
        $user = User::factory()->create();

        $ngayMai = now()->addDay()->format('d/m/Y');

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => $ngayMai,
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
    }

    /**
     * TC_DOB_018: Nhập ngày sinh bằng ngày hiện tại
     * Dữ liệu: Ngày hiện tại (today)
     * Kết quả mong đợi: Xử lý theo quy tắc hệ thống (cho phép ngày <= today)
     */
    public function test_tc_dob_018_nhap_ngay_sinh_bang_ngay_hien_tai(): void
    {
        $user = User::factory()->create();

        $homNay = now()->format('d/m/Y');
        $homNayDb = now()->format('Y-m-d');

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => $homNay,
        ]);

        $response->assertRedirect(route('tai-khoan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'ngay_sinh' => $homNayDb,
        ]);
    }

    /**
     * TC_DOB_019: Chọn ngày qua biểu tượng lịch (chuẩn HTML5 datepicker gửi wire format YYYY-MM-DD)
     * Dữ liệu: 2000-08-15
     * Kết quả mong đợi: Ngày được điền đúng và lưu chính xác
     */
    public function test_tc_dob_019_chon_ngay_qua_bieu_tuong_lich(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '2000-08-15',
        ]);

        $response->assertRedirect(route('tai-khoan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'ngay_sinh' => '2000-08-15',
        ]);
    }

    /**
     * TC_DOB_020: Lưu ngày sinh không hợp lệ
     * Dữ liệu: 31/02/2024 (Tháng 2 không bao giờ có 31 ngày)
     * Kết quả mong đợi: Hiển thị lỗi, không lưu và giữ nguyên ngày sinh cũ trong CSDL
     */
    public function test_tc_dob_020_luu_ngay_sinh_khong_hop_le(): void
    {
        $user = User::factory()->create([
            'ngay_sinh' => '1999-12-31',
        ]);

        $response = $this->actingAs($user)->put(route('tai-khoan.update'), [
            'ho_ten' => $user->ho_ten,
            'ngay_sinh' => '31/02/2024',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);

        // Xác nhận dữ liệu cũ trong CSDL vẫn giữ nguyên, không bị ghi đè sai
        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $user->id,
            'ngay_sinh' => '1999-12-31',
        ]);
    }
}
