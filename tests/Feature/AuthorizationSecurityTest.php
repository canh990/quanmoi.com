<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
use App\Models\Blog;
use App\Models\HinhAnhQuan;
use App\Models\Quan;
use App\Models\User;
use App\Models\VaiTro;
use App\Models\VideoShort;
use Database\Seeders\VaiTroSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthorizationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(VaiTroSeeder::class);
    }

    public function test_guest_cannot_access_admin(): void
    {
        $this->get('/admin/quan')->assertRedirect(route('login'));
    }

    public function test_email_without_admin_role_cannot_access_admin(): void
    {
        $user = $this->userWithRole('nguoi_dung', ['email' => 'admin@quanmoi.com']);

        $this->actingAs($user)->get('/admin/quan')->assertForbidden();
    }

    public function test_active_admin_can_access_admin(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->get('/admin/quan')->assertOk();
    }

    public function test_locked_admin_session_loses_access(): void
    {
        $admin = $this->userWithRole('admin', ['trang_thai' => 'bi_khoa']);

        $this->actingAs($admin)->get('/admin/quan')->assertRedirect(route('login'));
    }

    public function test_member_cannot_access_owner_management(): void
    {
        $member = $this->userWithRole('nguoi_dung');

        $this->actingAs($member)->get(route('chu-quan.quan.index'))->assertForbidden();
    }

    public function test_submitting_a_venue_does_not_promote_member_role(): void
    {
        Storage::fake('r2');
        $member = $this->userWithRole('nguoi_dung');

        $response = $this->actingAs($member)->post(route('chu-quan.quan.store'), [
            'ten_quan' => 'Quán Chờ Duyệt',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'so_dien_thoai' => '0901234567',
            'dia_chi_chi_tiet' => '1 Đường Mới',
            'ten_tinh_thanh' => 'Hà Nội',
            'ten_quan_huyen' => 'Ba Đình',
            'ten_phuong_xa' => 'Phúc Xá',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
            'anh_bia' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $response->assertRedirect(route('home'));
        $this->assertSame('nguoi_dung', $member->fresh()->vaiTro->ten);
        $this->assertDatabaseHas('quan', ['chu_quan_id' => $member->id, 'trang_thai' => 'chua_duyet']);
    }

    public function test_admin_approval_promotes_the_venue_owner(): void
    {
        $admin = $this->userWithRole('admin');
        $member = $this->userWithRole('nguoi_dung');
        $quan = $this->quanFor($member, 'chua_duyet');

        $this->actingAs($admin)->put(route('admin.quan.update', $quan->id), $this->adminVenuePayload('da_duyet'))
            ->assertRedirect(route('admin.quan.index'));

        $this->assertSame('chu_quan', $member->fresh()->vaiTro->ten);
        $this->assertDatabaseHas('admin_audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'venue.updated',
            'target_id' => $quan->id,
        ]);
    }

    public function test_owner_cannot_update_another_owners_venue_or_image(): void
    {
        $owner = $this->userWithRole('chu_quan');
        $otherOwner = $this->userWithRole('chu_quan');
        $otherVenue = $this->quanFor($otherOwner, 'da_duyet');
        $image = HinhAnhQuan::create([
            'quan_id' => $otherVenue->id,
            'duong_dan' => 'https://example.test/image.webp',
            'tieu_de' => 'Ảnh riêng',
        ]);

        $this->actingAs($owner)->put(route('chu-quan.quan.update', $otherVenue->slug), [
            'so_dien_thoai' => '0901234567',
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '22:00',
        ])->assertNotFound();

        $this->actingAs($owner)->deleteJson(route('chu-quan.hinh-anh.destroy', $image->id))->assertNotFound();

        $this->actingAs($owner)
            ->post(route('chu-quan.quan.menu.update', $otherVenue->slug), ['menu_data' => '[]'])
            ->assertNotFound();
    }

    public function test_owner_can_manage_a_venue_before_admin_approval(): void
    {
        $owner = $this->userWithRole('chu_quan');
        $pendingVenue = $this->quanFor($owner, 'chua_duyet');

        $this->actingAs($owner)
            ->get(route('chu-quan.quan.show', $pendingVenue->slug))
            ->assertOk();
    }

    public function test_public_video_api_and_saved_venues_exclude_unapproved_venues(): void
    {
        $member = $this->userWithRole('nguoi_dung');
        $owner = $this->userWithRole('chu_quan');
        $approved = $this->quanFor($owner, 'da_duyet');
        $pending = $this->quanFor($owner, 'chua_duyet');

        VideoShort::create($this->videoPayload($approved->id, 'Video công khai'));
        VideoShort::create($this->videoPayload($pending->id, 'Video chưa duyệt'));

        $this->getJson('/api/videos')
            ->assertOk()
            ->assertJsonFragment(['tieu_de' => 'Video công khai'])
            ->assertJsonMissing(['tieu_de' => 'Video chưa duyệt']);

        $this->actingAs($member)
            ->postJson(route('quan-da-luu.toggle', $pending->id))
            ->assertNotFound();
    }

    public function test_member_cannot_mass_assign_a_role_through_profile_update(): void
    {
        $member = $this->userWithRole('nguoi_dung');
        $adminRoleId = VaiTro::where('ten', 'admin')->value('id');

        $this->actingAs($member)->put(route('tai-khoan.update'), [
            'ho_ten' => 'Người dùng an toàn',
            'so_dien_thoai' => '0901234567',
            'vai_tro_id' => $adminRoleId,
        ])->assertRedirect(route('tai-khoan.index'));

        $this->assertSame('nguoi_dung', $member->fresh()->vaiTro->ten);
    }

    public function test_only_the_owner_or_an_admin_can_view_a_draft_blog(): void
    {
        $owner = $this->userWithRole('nguoi_dung');
        $otherMember = $this->userWithRole('nguoi_dung');
        $admin = $this->userWithRole('admin');
        $blog = Blog::create([
            'user_id' => $owner->id,
            'title' => 'Bản nháp riêng tư',
            'slug' => 'ban-nhap-rieng-tu',
            'content' => '<p>Nội dung nháp</p>',
            'status' => 'draft',
        ]);

        $this->actingAs($otherMember)->get(route('blog.show', $blog->slug))->assertNotFound();
        $this->actingAs($owner)->get(route('blog.show', $blog->slug))->assertOk();
        $this->actingAs($admin)->get(route('blog.show', $blog->slug))->assertOk();
    }

    public function test_an_admin_cannot_demote_or_lock_their_own_account(): void
    {
        $admin = $this->userWithRole('admin');
        $memberRoleId = VaiTro::where('ten', 'nguoi_dung')->value('id');

        $this->actingAs($admin)
            ->put(route('admin.nguoi-dung.update', $admin->id), $this->adminUserPayload($admin, [
                'vai_tro_id' => $memberRoleId,
            ]))
            ->assertSessionHasErrors('trang_thai');

        $this->actingAs($admin)
            ->put(route('admin.nguoi-dung.update', $admin->id), $this->adminUserPayload($admin, [
                'trang_thai' => 'bi_khoa',
            ]))
            ->assertSessionHasErrors('trang_thai');

        $this->assertSame('admin', $admin->fresh()->vaiTro->ten);
        $this->assertSame('hoat_dong', $admin->fresh()->trang_thai);
    }

    public function test_the_last_active_admin_cannot_be_deleted(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->delete(route('admin.nguoi-dung.destroy', $admin->id))
            ->assertSessionHasErrors('trang_thai');

        $this->assertDatabaseHas('nguoi_dung', ['id' => $admin->id, 'ngay_xoa' => null]);
    }

    public function test_admin_role_changes_are_audited_and_do_not_log_password_values(): void
    {
        $actor = $this->userWithRole('admin');
        $target = $this->userWithRole('admin');
        $memberRoleId = VaiTro::where('ten', 'nguoi_dung')->value('id');

        $this->actingAs($actor)
            ->put(route('admin.nguoi-dung.update', $target->id), $this->adminUserPayload($target, [
                'vai_tro_id' => $memberRoleId,
            ]))
            ->assertRedirect(route('admin.nguoi-dung.index'));

        $this->assertSame('nguoi_dung', $target->fresh()->vaiTro->ten);
        $this->assertDatabaseHas('admin_audit_logs', [
            'actor_id' => $actor->id,
            'action' => 'user.updated',
            'target_id' => $target->id,
        ]);
        $metadata = AdminAuditLog::latest()->value('metadata');
        $this->assertSame([
            'role_changed' => true,
            'status_changed' => false,
            'verification_changed' => false,
            'password_changed' => false,
        ], $metadata);
    }

    public function test_current_user_api_uses_an_allow_listed_resource(): void
    {
        $member = $this->userWithRole('nguoi_dung', ['google_id' => 'private-google-subject']);

        $this->actingAs($member)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.id', $member->id)
            ->assertJsonMissingPath('data.google_id')
            ->assertJsonMissingPath('data.vai_tro_id')
            ->assertJsonMissingPath('data.trang_thai');
    }

    private function userWithRole(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'vai_tro_id' => VaiTro::where('ten', $role)->value('id'),
            'da_xac_thuc' => true,
            'trang_thai' => 'hoat_dong',
        ], $attributes));
    }

    private function quanFor(User $owner, string $status): Quan
    {
        return Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'trang_thai' => $status,
        ]);
    }

    private function adminVenuePayload(string $status): array
    {
        return [
            'ten_quan' => 'Quán đã duyệt',
            'loai_hinh_kinh_doanh' => 'Nhà hàng',
            'so_dien_thoai' => '0901234567',
            'dia_chi_chi_tiet' => '1 Đường Mới',
            'trang_thai' => $status,
        ];
    }

    private function adminUserPayload(User $user, array $overrides = []): array
    {
        return array_merge([
            'ho_ten' => $user->ho_ten,
            'email' => $user->email,
            'so_dien_thoai' => $user->so_dien_thoai,
            'vai_tro_id' => $user->vai_tro_id,
            'trang_thai' => $user->trang_thai,
            'da_xac_thuc' => $user->da_xac_thuc,
        ], $overrides);
    }

    private function videoPayload(string $quanId, string $title): array
    {
        return [
            'quan_id' => $quanId,
            'tieu_de' => $title,
            'video_id' => 'video-'.uniqid(),
            'nguoi_dang' => 'Tác giả',
            'trang_thai' => 'da_duyet',
        ];
    }
}
