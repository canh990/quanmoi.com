<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        VaiTro::firstOrCreate(['ten' => 'admin'], ['mo_ta' => 'Quản trị viên']);
        VaiTro::firstOrCreate(['ten' => 'chu_quan'], ['mo_ta' => 'Chủ quán']);
        VaiTro::firstOrCreate(['ten' => 'nguoi_dung'], ['mo_ta' => 'Người dùng']);
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');
    }

    public function test_registration_account_takeover_is_prevented(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $victim = User::create([
            'ho_ten' => 'Original Admin',
            'email' => 'admin@quanmoi.com',
            'mat_khau' => Hash::make('secret_admin_pass'),
            'vai_tro_id' => $adminRole->id,
            'da_xac_thuc' => true,
            'trang_thai' => 'hoat_dong',
        ]);

        $response = $this->postJson('/dang-ky', [
            'ho_ten' => 'Attacker',
            'email' => 'admin@quanmoi.com',
            'mat_khau' => 'new_attacker_pass',
        ]);

        $response->assertStatus(422);

        // Ensure victim's password was NOT overwritten
        $victim->refresh();
        $this->assertTrue(Hash::check('secret_admin_pass', $victim->mat_khau));
        $this->assertEquals('Original Admin', $victim->ho_ten);
        $this->assertFalse(Hash::check('new_attacker_pass', $victim->mat_khau));
    }

    public function test_restaurant_owner_cannot_self_review(): void
    {
        $ownerRole = VaiTro::where('ten', 'chu_quan')->first();
        $owner = User::create([
            'ho_ten' => 'Chu Quan 1',
            'email' => 'owner1@example.com',
            'mat_khau' => Hash::make('password123'),
            'vai_tro_id' => $ownerRole->id,
            'da_xac_thuc' => true,
            'trang_thai' => 'hoat_dong',
        ]);

        $quan = Quan::create([
            'chu_quan_id' => $owner->id,
            'ten_quan' => 'Quan Cafe Test',
            'slug' => 'quan-cafe-test',
            'dia_chi_chi_tiet' => '123 Nguyen Trai',
            'trang_thai' => 'da_duyet',
        ]);

        $response = $this->actingAs($owner)->post("/quan/{$quan->slug}/danh-gia", [
            'so_sao' => 5,
            'binh_luan' => 'Quan cua toi qua tuyet voi!',
        ]);

        $response->assertSessionHas('error', 'Chủ quán không thể tự đánh giá quán của chính mình.');
        $this->assertDatabaseMissing('quan_danh_gia', [
            'quan_id' => $quan->id,
            'nguoi_dung_id' => $owner->id,
        ]);
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $userRole = VaiTro::where('ten', 'nguoi_dung')->first();
        $user = User::create([
            'ho_ten' => 'Regular User',
            'email' => 'user@example.com',
            'mat_khau' => Hash::make('password123'),
            'vai_tro_id' => $userRole->id,
            'da_xac_thuc' => true,
            'trang_thai' => 'hoat_dong',
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_from_admin(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/dang-nhap');
    }

    public function test_regular_user_cannot_access_chu_quan_portal(): void
    {
        $userRole = VaiTro::where('ten', 'nguoi_dung')->first();
        $user = User::create([
            'ho_ten' => 'Regular User',
            'email' => 'user2@example.com',
            'mat_khau' => Hash::make('password123'),
            'vai_tro_id' => $userRole->id,
            'da_xac_thuc' => true,
            'trang_thai' => 'hoat_dong',
        ]);

        $response = $this->actingAs($user)->get('/chu-quan/quan');
        $response->assertStatus(403);
    }

    public function test_blog_content_stored_xss_is_sanitized(): void
    {
        $userRole = VaiTro::where('ten', 'nguoi_dung')->first();
        $user = User::create([
            'ho_ten' => 'Author User',
            'email' => 'author@example.com',
            'mat_khau' => Hash::make('password123'),
            'vai_tro_id' => $userRole->id,
            'da_xac_thuc' => true,
            'trang_thai' => 'hoat_dong',
        ]);

        $blog = new \App\Models\Blog();
        $blog->user_id = $user->id;
        $blog->title = 'Test XSS Blog';
        $blog->slug = 'test-xss-blog';
        $blog->content = '<p>Normal content</p><script>alert("XSS")</script><img src=x onerror=alert(1)>';
        $blog->save();

        $this->assertStringNotContainsString('<script>', $blog->fresh()->content);
        $this->assertStringNotContainsString('onerror', $blog->fresh()->content);
    }

    public function test_geocode_endpoint_validates_empty_address(): void
    {
        $response = $this->getJson('/api/ban-do/geocode');
        $response->assertStatus(422);
    }

    public function test_chu_quan_cannot_modify_other_chu_quan_venue(): void
    {
        $ownerRole = VaiTro::where('ten', 'chu_quan')->first();
        $owner1 = User::create([
            'ho_ten' => 'Owner 1',
            'email' => 'o1@example.com',
            'mat_khau' => Hash::make('password123'),
            'vai_tro_id' => $ownerRole->id,
            'da_xac_thuc' => true,
            'trang_thai' => 'hoat_dong',
        ]);

        $owner2 = User::create([
            'ho_ten' => 'Owner 2',
            'email' => 'o2@example.com',
            'mat_khau' => Hash::make('password123'),
            'vai_tro_id' => $ownerRole->id,
            'da_xac_thuc' => true,
            'trang_thai' => 'hoat_dong',
        ]);

        $quan1 = Quan::create([
            'chu_quan_id' => $owner1->id,
            'ten_quan' => 'Quan 1',
            'slug' => 'quan-1',
            'dia_chi_chi_tiet' => 'Address 1',
            'trang_thai' => 'da_duyet',
        ]);

        // Owner 2 attempts to edit Owner 1's menu
        $response = $this->actingAs($owner2)->get("/chu-quan/quan/{$quan1->slug}/thuc-don");
        // Should not find or abort with 404/403
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }
}
