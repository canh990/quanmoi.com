<?php

namespace Tests\Feature\Admin;

use App\Models\QuyenHan;
use App\Models\User;
use App\Models\VaiTro;
use Database\Seeders\QuyenHanSeeder;
use Database\Seeders\VaiTroSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VaiTroPhanQuyenTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
        $this->seed(VaiTroSeeder::class);
        $this->seed(QuyenHanSeeder::class);

        $adminRole = VaiTro::where('ten', 'admin')->first();
        $userRole = VaiTro::where('ten', 'nguoi_dung')->first();

        $this->admin = User::factory()->create([
            'email' => 'admin@quanmoi.com',
            'vai_tro_id' => $adminRole->id,
            'trang_thai' => 'hoat_dong',
        ]);

        $this->user = User::factory()->create([
            'email' => 'user@quanmoi.com',
            'vai_tro_id' => $userRole->id,
            'trang_thai' => 'hoat_dong',
            'da_xac_thuc' => false,
        ]);
    }

    public function test_admin_can_view_vai_tro_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.vai-tro.index'));
        $response->assertStatus(200);
        $response->assertSee('admin');
        $response->assertSee('chu_quan');
        $response->assertSee('nguoi_dung');
    }

    public function test_admin_can_create_new_role(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.vai-tro.store'), [
            'ten' => 'bien_tap_vien',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vai_tro', ['ten' => 'bien_tap_vien']);
    }

    public function test_admin_can_update_role_permissions(): void
    {
        $role = VaiTro::where('ten', 'chu_quan')->first();
        $perm = QuyenHan::first();

        $response = $this->actingAs($this->admin)->put(route('admin.vai-tro.update-permissions', $role->id), [
            'quyen_han' => [$perm->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vai_tro_quyen_han', [
            'vai_tro_id' => $role->id,
            'quyen_han_id' => $perm->id,
        ]);
    }

    public function test_admin_can_override_user_permission(): void
    {
        $perm = QuyenHan::first();

        // 1. Grant permission explicitly (Cho phep)
        $response = $this->actingAs($this->admin)->post(route('admin.phan-quyen.override'), [
            'nguoi_dung_id' => $this->user->id,
            'quyen_han_id' => $perm->id,
            'cho_phep' => '1',
        ]);

        $response->assertRedirect();
        $this->assertTrue($this->user->fresh()->hasPermissionTo($perm->ten));

        // 2. Deny permission explicitly (Tu choi)
        $response = $this->actingAs($this->admin)->post(route('admin.phan-quyen.override'), [
            'nguoi_dung_id' => $this->user->id,
            'quyen_han_id' => $perm->id,
            'cho_phep' => '0',
        ]);

        $response->assertRedirect();
        $this->assertFalse($this->user->fresh()->hasPermissionTo($perm->ten));

        // 3. Remove override (Dung mac dinh)
        $response = $this->actingAs($this->admin)->post(route('admin.phan-quyen.override'), [
            'nguoi_dung_id' => $this->user->id,
            'quyen_han_id' => $perm->id,
            'cho_phep' => '-1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('nguoi_dung_quyen_han', [
            'nguoi_dung_id' => $this->user->id,
            'quyen_han_id' => $perm->id,
        ]);
    }

    public function test_user_can_request_verification_and_admin_can_approve(): void
    {
        // 1. User requests verification
        $reqResponse = $this->actingAs($this->user)->post(route('tai-khoan.yeu-cau-xac-thuc'), [
            'ly_do' => 'Tôi muốn xác thực tài khoản để đánh giá món ăn uy tín hơn.',
        ]);
        $reqResponse->assertRedirect();
        $this->assertEquals('cho_duyet', $this->user->fresh()->getSetting('yeu_cau_xac_thuc.trang_thai'));

        // 2. Admin approves verification
        $approveResponse = $this->actingAs($this->admin)->put(route('admin.duyet-xac-thuc.user', $this->user->id));
        $approveResponse->assertRedirect();

        $freshUser = $this->user->fresh();
        $this->assertTrue((bool) $freshUser->da_xac_thuc);
        $this->assertNotNull($freshUser->ngay_xac_thuc);
        $this->assertEquals('da_duyet', $freshUser->getSetting('yeu_cau_xac_thuc.trang_thai'));

        // 3. User receives real-time notification
        $notifResponse = $this->actingAs($freshUser)->get(route('nguoi-dung.thong-bao.realtime-check'));
        $notifResponse->assertStatus(200);
        $notifData = $notifResponse->json();
        $this->assertTrue($notifData['success']);
        $this->assertGreaterThan(0, $notifData['unread_count']);
        $this->assertEquals('verified', $notifData['latest']['action']);
    }

    public function test_admin_can_reject_user_verification(): void
    {
        $this->actingAs($this->user)->post(route('tai-khoan.yeu-cau-xac-thuc'), [
            'ly_do' => 'Yêu cầu test',
        ]);

        $rejectResponse = $this->actingAs($this->admin)->put(route('admin.duyet-xac-thuc.user.reject', $this->user->id), [
            'note' => 'Hình ảnh giấy tờ hoặc thông tin chưa rõ ràng.',
        ]);
        $rejectResponse->assertRedirect();

        $freshUser = $this->user->fresh();
        $this->assertFalse((bool) $freshUser->da_xac_thuc);
        $this->assertNull($freshUser->ngay_xac_thuc);
        $this->assertEquals('tu_choi', $freshUser->getSetting('yeu_cau_xac_thuc.trang_thai'));

        $notifResponse = $this->actingAs($freshUser)->get(route('nguoi-dung.thong-bao.realtime-check'));
        $this->assertEquals('verification_rejected', $notifResponse->json('latest.action'));
    }

    public function test_admin_cannot_delete_protected_system_roles(): void
    {
        $adminRole = VaiTro::where('ten', 'admin')->first();
        $response = $this->actingAs($this->admin)->delete(route('admin.vai-tro.destroy', $adminRole->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('vai_tro', ['id' => $adminRole->id]);
    }

    public function test_admin_cannot_delete_role_with_assigned_users(): void
    {
        $customRole = VaiTro::create(['ten' => 'kiem_duyet_vien']);
        $this->user->update(['vai_tro_id' => $customRole->id]);

        $response = $this->actingAs($this->admin)->delete(route('admin.vai-tro.destroy', $customRole->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('vai_tro', ['id' => $customRole->id]);
    }

    public function test_admin_can_delete_unused_custom_role(): void
    {
        $customRole = VaiTro::create(['ten' => 'vai_tro_tam']);
        $response = $this->actingAs($this->admin)->delete(route('admin.vai-tro.destroy', $customRole->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('vai_tro', ['id' => $customRole->id]);
    }

    public function test_gate_denies_explicitly_revoked_permission_override(): void
    {
        $perm = QuyenHan::first();

        // 1. Explicitly deny permission override for user
        $this->actingAs($this->admin)->post(route('admin.phan-quyen.override'), [
            'nguoi_dung_id' => $this->user->id,
            'quyen_han_id' => $perm->id,
            'cho_phep' => '0',
        ]);

        $freshUser = $this->user->fresh();
        $this->assertTrue($freshUser->hasPermissionExplicitlyDenied($perm->ten));
        $this->assertFalse(\Illuminate\Support\Facades\Gate::forUser($freshUser)->allows($perm->ten));

        // 2. Grant override
        $this->actingAs($this->admin)->post(route('admin.phan-quyen.override'), [
            'nguoi_dung_id' => $this->user->id,
            'quyen_han_id' => $perm->id,
            'cho_phep' => '1',
        ]);

        $freshUser2 = $this->user->fresh();
        $this->assertFalse($freshUser2->hasPermissionExplicitlyDenied($perm->ten));
        $this->assertTrue(\Illuminate\Support\Facades\Gate::forUser($freshUser2)->allows($perm->ten));
    }

    public function test_admin_cannot_revoke_permission_from_only_active_admin(): void
    {
        $perm = QuyenHan::first();

        // Admin tries to self-revoke
        $response = $this->actingAs($this->admin)->post(route('admin.phan-quyen.override'), [
            'nguoi_dung_id' => $this->admin->id,
            'quyen_han_id' => $perm->id,
            'cho_phep' => '0',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('nguoi_dung_quyen_han', [
            'nguoi_dung_id' => $this->admin->id,
            'quyen_han_id' => $perm->id,
            'cho_phep' => false,
        ]);
    }

    public function test_admin_can_perform_role_and_permission_actions_via_ajax(): void
    {
        // 1. Create role via AJAX
        $resRole = $this->actingAs($this->admin)->postJson(route('admin.vai-tro.store'), [
            'ten' => 'kiem_duyet_vien',
        ]);
        $resRole->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
        $this->assertDatabaseHas('vai_tro', ['ten' => 'kiem_duyet_vien']);
        $role = VaiTro::where('ten', 'kiem_duyet_vien')->first();

        // 2. Update role permissions via AJAX
        $perm = QuyenHan::first();
        $resPerm = $this->actingAs($this->admin)->putJson(route('admin.vai-tro.update-permissions', $role->id), [
            'quyen_han' => [$perm->id],
        ]);
        $resPerm->assertStatus(200)
            ->assertJson([
                'success' => true,
                'role_id' => $role->id,
            ]);

        // 3. Override user permission via AJAX (Grant)
        $resOverride = $this->actingAs($this->admin)->postJson(route('admin.phan-quyen.override'), [
            'nguoi_dung_id' => $this->user->id,
            'quyen_han_id' => $perm->id,
            'cho_phep' => '1',
        ]);
        $resOverride->assertStatus(200)
            ->assertJson([
                'success' => true,
                'action' => '1',
                'permission_name' => $perm->ten,
            ]);

        // 4. Override user permission via AJAX (Reset to default)
        $resReset = $this->actingAs($this->admin)->postJson(route('admin.phan-quyen.override'), [
            'nguoi_dung_id' => $this->user->id,
            'quyen_han_id' => $perm->id,
            'cho_phep' => '-1',
        ]);
        $resReset->assertStatus(200)
            ->assertJson([
                'success' => true,
                'action' => '-1',
            ]);

        // 5. Delete custom role via AJAX
        $resDel = $this->actingAs($this->admin)->deleteJson(route('admin.vai-tro.destroy', $role->id));
        $resDel->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
        $this->assertDatabaseMissing('vai_tro', ['id' => $role->id]);
    }

    public function test_admin_can_toggle_user_verification_via_ajax(): void
    {
        $this->assertFalse($this->user->da_xac_thuc);

        // Turn on Tick Xanh
        $res1 = $this->actingAs($this->admin)->putJson(route('admin.nguoi-dung.toggle-xac-thuc', $this->user->id));
        $res1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'verified' => true,
            ]);
        $this->assertTrue($this->user->fresh()->da_xac_thuc);

        // Turn off Tick Xanh
        $res2 = $this->actingAs($this->admin)->putJson(route('admin.nguoi-dung.toggle-xac-thuc', $this->user->id));
        $res2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'verified' => false,
            ]);
        $this->assertFalse($this->user->fresh()->da_xac_thuc);
    }
}
