<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Suite kiểm thử tự động cho:
 * 2. Test case hiển thị giao diện hồ sơ cá nhân (TC_PROFILE_001 -> TC_PROFILE_010)
 */
class HienThiHoSoCaNhanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC_PROFILE_001: Hiển thị trang hồ sơ cá nhân
     * Các bước: Đăng nhập và mở Hồ sơ cá nhân
     * Kỳ vọng: Trang hồ sơ hiển thị đầy đủ, không lỗi giao diện, khách vãng lai bị chuyển hướng đăng nhập
     */
    public function test_tc_profile_001_hien_thi_trang_ho_so_ca_nhan(): void
    {
        // 1. Khách chưa đăng nhập truy cập sẽ bị chuyển hướng sang trang đăng nhập
        $guestResponse = $this->get(route('tai-khoan.index'));
        $guestResponse->assertRedirect(route('login'));

        // 2. Người dùng đã đăng nhập truy cập trang hồ sơ cá nhân
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('tai-khoan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('nguoi-dung.tai-khoan.index');
        $response->assertSee('Thông tin cá nhân');
        $response->assertSee('Bảo mật & Mật khẩu', false);
        $response->assertSee('form-update-profile', false);
        $response->assertSee('form-update-password', false);
    }

    /**
     * TC_PROFILE_002: Hiển thị họ và tên
     * Các bước: Quan sát trường Họ và tên
     * Kỳ vọng: Hiển thị đúng dữ liệu họ tên đã lưu trong CSDL ở cả form và sidebar
     */
    public function test_tc_profile_002_hien_thi_ho_va_ten(): void
    {
        $hoTen = 'Nguyễn Minh Quân';
        $user = User::factory()->create([
            'ho_ten' => $hoTen,
        ]);

        $response = $this->actingAs($user)->get(route('tai-khoan.index'));

        $response->assertStatus(200);
        // Kiểm tra giá trị trong ô input họ tên
        $response->assertSee('name="ho_ten"', false);
        $response->assertSee('value="' . $hoTen . '"', false);
        // Kiểm tra họ tên hiển thị trên phần avatar sidebar
        $response->assertSee($hoTen);
    }

    /**
     * TC_PROFILE_003: Hiển thị email
     * Các bước: Quan sát trường Địa chỉ Email
     * Kỳ vọng: Email hiển thị đúng với tài khoản và được khóa (disabled/readonly) để bảo mật
     */
    public function test_tc_profile_003_hien_thi_email(): void
    {
        $email = 'quan.nguyen@quanmoi.vn';
        $user = User::factory()->create([
            'email' => $email,
        ]);

        $response = $this->actingAs($user)->get(route('tai-khoan.index'));

        $response->assertStatus(200);
        $response->assertSee('type="email"', false);
        $response->assertSee('value="' . $email . '"', false);
        // Email là trường cố định, không được phép sửa trực tiếp
        $response->assertSee('disabled=""', false);
    }

    /**
     * TC_PROFILE_004: Hiển thị giới tính
     * Các bước: Quan sát trường Giới tính
     * Kỳ vọng: Hiển thị đúng giới tính đã lưu (nam, nữ hoặc khác) với thuộc tính selected
     */
    public function test_tc_profile_004_hien_thi_gioi_tinh(): void
    {
        // 1. Kiểm tra giới tính Nam
        $userNam = User::factory()->create(['gioi_tinh' => 'nam']);
        $resNam = $this->actingAs($userNam)->get(route('tai-khoan.index'));
        $resNam->assertStatus(200);
        $resNam->assertSee('<option value="nam" selected', false);

        // 2. Kiểm tra giới tính Nữ
        $userNu = User::factory()->create(['gioi_tinh' => 'nữ']);
        $resNu = $this->actingAs($userNu)->get(route('tai-khoan.index'));
        $resNu->assertStatus(200);
        $resNu->assertSee('<option value="nữ" selected', false);

        // 3. Kiểm tra giới tính Khác
        $userKhac = User::factory()->create(['gioi_tinh' => 'khác']);
        $resKhac = $this->actingAs($userKhac)->get(route('tai-khoan.index'));
        $resKhac->assertStatus(200);
        $resKhac->assertSee('<option value="khác" selected', false);
    }

    /**
     * TC_PROFILE_005: Hiển thị ngày sinh
     * Các bước: Quan sát trường Ngày sinh
     * Kỳ vọng: Hiển thị ngày sinh đã lưu (định dạng Y-m-d) hoặc để trống nếu chưa có
     */
    public function test_tc_profile_005_hien_thi_ngay_sinh(): void
    {
        // 1. Trường hợp người dùng ĐÃ có ngày sinh
        $userCoNgaySinh = User::factory()->create([
            'ngay_sinh' => '1998-05-20',
        ]);
        $resCo = $this->actingAs($userCoNgaySinh)->get(route('tai-khoan.index'));
        $resCo->assertStatus(200);
        $resCo->assertSee('name="ngay_sinh"', false);
        $resCo->assertSee('value="1998-05-20"', false);

        // 2. Trường hợp người dùng CHƯA có ngày sinh (để trống)
        $userChuaNgaySinh = User::factory()->create([
            'ngay_sinh' => null,
        ]);
        $resChua = $this->actingAs($userChuaNgaySinh)->get(route('tai-khoan.index'));
        $resChua->assertStatus(200);
        $resChua->assertSee('name="ngay_sinh"', false);
        $resChua->assertSee('value=""', false);
    }

    /**
     * TC_PROFILE_006: Hiển thị số điện thoại
     * Các bước: Quan sát trường Số điện thoại
     * Kỳ vọng: Hiển thị đúng số điện thoại hoặc để trống nếu chưa có
     */
    public function test_tc_profile_006_hien_thi_so_dien_thoai(): void
    {
        // 1. Người dùng ĐÃ có số điện thoại
        $userCoSdt = User::factory()->create([
            'so_dien_thoai' => '0901234567',
        ]);
        $resCo = $this->actingAs($userCoSdt)->get(route('tai-khoan.index'));
        $resCo->assertStatus(200);
        $resCo->assertSee('name="so_dien_thoai"', false);
        $resCo->assertSee('value="0901234567"', false);

        // 2. Người dùng CHƯA có số điện thoại (để trống)
        $userChuaSdt = User::factory()->create([
            'so_dien_thoai' => null,
        ]);
        $resChua = $this->actingAs($userChuaSdt)->get(route('tai-khoan.index'));
        $resChua->assertStatus(200);
        $resChua->assertSee('name="so_dien_thoai"', false);
        $resChua->assertSee('value=""', false);
    }

    /**
     * TC_PROFILE_007: Hiển thị địa chỉ hiện tại
     * Các bước: Quan sát trường Địa chỉ hiện tại
     * Kỳ vọng: Hiển thị đúng địa chỉ đã lưu hoặc để trống nếu chưa có
     */
    public function test_tc_profile_007_hien_thi_dia_chi_hien_tai(): void
    {
        // 1. Người dùng ĐÃ có địa chỉ
        $diaChi = '72 Lê Thánh Tôn, Phường Bến Nghé, Quận 1';
        $userCoDiaChi = User::factory()->create([
            'dia_chi' => $diaChi,
        ]);
        $resCo = $this->actingAs($userCoDiaChi)->get(route('tai-khoan.index'));
        $resCo->assertStatus(200);
        $resCo->assertSee('name="dia_chi"', false);
        $resCo->assertSee('value="' . $diaChi . '"', false);

        // 2. Người dùng CHƯA có địa chỉ (để trống)
        $userChuaDiaChi = User::factory()->create([
            'dia_chi' => null,
        ]);
        $resChua = $this->actingAs($userChuaDiaChi)->get(route('tai-khoan.index'));
        $resChua->assertStatus(200);
        $resChua->assertSee('name="dia_chi"', false);
        $resChua->assertSee('value=""', false);
    }

    /**
     * TC_PROFILE_008: Hiển thị ảnh đại diện
     * Các bước: Quan sát khu vực avatar
     * Kỳ vọng: Ảnh đại diện hiển thị đúng URL đã lưu, hoặc ảnh mặc định UI-Avatars nếu chưa có, kèm nút chỉnh sửa
     */
    public function test_tc_profile_008_hien_thi_anh_dai_dien(): void
    {
        // 1. Người dùng có ảnh đại diện tùy chỉnh
        $avatarUrl = 'https://example.com/uploads/my-avatar.webp';
        $userCustom = User::factory()->create([
            'anh_dai_dien' => $avatarUrl,
        ]);
        $resCustom = $this->actingAs($userCustom)->get(route('tai-khoan.index'));
        $resCustom->assertStatus(200);
        $resCustom->assertSee($avatarUrl, false);
        $resCustom->assertSee('id="avatar-form"', false);
        $resCustom->assertSee('label for="anh_dai_dien"', false);

        // 2. Người dùng chưa có ảnh đại diện -> sử dụng fallback UI-Avatars với tên người dùng
        $userDefault = User::factory()->create([
            'ho_ten' => 'Lê Hoàng Nam',
            'anh_dai_dien' => null,
        ]);
        $resDefault = $this->actingAs($userDefault)->get(route('tai-khoan.index'));
        $resDefault->assertStatus(200);
        $resDefault->assertSee('ui-avatars.com', false);
        $resDefault->assertSee(urlencode('Lê Hoàng Nam'), false);
    }

    /**
     * TC_PROFILE_009: Hiển thị nút Lưu thay đổi
     * Các bước: Quan sát góc trên bên phải khu vực thông tin
     * Kỳ vọng: Nút hiển thị rõ ràng, có nhãn "LƯU THAY ĐỔI", loại submit và có thể thao tác
     */
    public function test_tc_profile_009_hien_thi_nut_luu_thay_doi(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tai-khoan.index'));

        $response->assertStatus(200);
        $response->assertSee('LƯU THAY ĐỔI');
        $response->assertSee('<button type="submit"', false);
        // Đảm bảo nút nằm trong form cập nhật hồ sơ và không bị vô hiệu hóa
        $response->assertDontSee('disabled>LƯU THAY ĐỔI');
    }

    /**
     * TC_PROFILE_010: Điều hướng menu bên trái
     * Các bước: Lần lượt chọn Hồ sơ cá nhân, Quản lý bài viết, Thông báo, Cài đặt
     * Kỳ vọng: Hiển thị đầy đủ 4 liên kết menu và điều hướng chính xác đến từng chức năng
     */
    public function test_tc_profile_010_dieu_huong_menu_ben_trai(): void
    {
        $user = User::factory()->create();

        // 1. Kiểm tra các liên kết menu bên trái hiển thị trên trang hồ sơ
        $response = $this->actingAs($user)->get(route('tai-khoan.index'));
        $response->assertStatus(200);
        $response->assertSee('Hồ sơ cá nhân');
        $response->assertSee('Quản lý bài viết');
        $response->assertSee('Thông báo');
        $response->assertSee('Cài đặt');

        $response->assertSee(route('tai-khoan.index'), false);
        $response->assertSee(route('nguoi-dung.blog.index'), false);
        $response->assertSee(route('nguoi-dung.thong-bao.index'), false);
        $response->assertSee(route('tai-khoan.cai-dat'), false);

        // 2. Kiểm tra điều hướng thực tế tới từng chức năng:
        // A. Hồ sơ cá nhân
        $this->actingAs($user)->get(route('tai-khoan.index'))->assertOk();

        // B. Quản lý bài viết
        $this->actingAs($user)->get(route('nguoi-dung.blog.index'))->assertOk();

        // C. Thông báo
        $this->actingAs($user)->get(route('nguoi-dung.thong-bao.index'))->assertOk();

        // D. Cài đặt tài khoản
        $this->actingAs($user)->get(route('tai-khoan.cai-dat'))->assertOk();
    }
}
