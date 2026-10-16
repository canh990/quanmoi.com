<?php

namespace Tests\Feature\Admin;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogModerationLog;
use App\Models\User;
use App\Models\VaiTro;
use App\Notifications\BlogStatusUpdatedNotification;
use Database\Seeders\VaiTroSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $author;
    protected BlogCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->seed(VaiTroSeeder::class);

        $adminRole = VaiTro::where('ten', 'admin')->first();
        $userRole = VaiTro::where('ten', 'nguoi_dung')->first();

        $this->admin = User::factory()->create([
            'vai_tro_id' => $adminRole->id,
            'trang_thai' => 'hoat_dong',
            'ho_ten' => 'Quản Trị Viên Test',
        ]);

        $this->author = User::factory()->create([
            'vai_tro_id' => $userRole->id,
            'trang_thai' => 'hoat_dong',
            'ho_ten' => 'Tác Giả Nguyễn Văn A',
        ]);

        $this->category = BlogCategory::create([
            'name' => 'Ẩm Thực Sài Gòn',
            'slug' => 'am-thuc-sai-gon',
            'order' => 1,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_approve_pending_blog_via_ajax(): void
    {
        $blog = Blog::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'title' => 'Khám phá quán bún bò chuẩn vị Huế',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->putJson(route('admin.blog.posts.update-status', $blog), [
            'status' => 'published',
            'note' => 'Bài viết rất hay và chi tiết, đã được duyệt.',
            'expected_status' => 'pending',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'published',
                'status_label' => 'Xuất bản',
            ]);

        $blog->refresh();
        $this->assertEquals('published', $blog->status);
        $this->assertNotNull($blog->published_at);
        $this->assertEquals($this->admin->id, $blog->published_by);

        // Verify Moderation Log
        $this->assertDatabaseHas('blog_moderation_logs', [
            'blog_id' => $blog->id,
            'admin_id' => $this->admin->id,
            'action' => 'published',
            'from_status' => 'pending',
            'to_status' => 'published',
        ]);

        // Verify Author received notification immediately in database
        $notifications = $this->author->notifications;
        $this->assertCount(1, $notifications);

        $notifData = $notifications->first()->data;
        $this->assertEquals($blog->id, $notifData['blog_id']);
        $this->assertEquals('published', $notifData['status']);
        $this->assertEquals('Đã xuất bản', $notifData['status_label']);
        $this->assertStringContainsString('duyệt và xuất bản', $notifData['message']);
    }

    public function test_admin_can_reject_pending_blog_with_note(): void
    {
        $blog = Blog::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'title' => 'Review quán cà phê quận 1',
            'status' => 'pending',
        ]);

        $rejectionNote = 'Hình ảnh mờ và vi phạm bản quyền từ nguồn khác.';

        $response = $this->actingAs($this->admin)->putJson(route('admin.blog.posts.update-status', $blog), [
            'status' => 'rejected',
            'note' => $rejectionNote,
            'expected_status' => 'pending',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'rejected',
                'status_label' => 'Từ chối',
            ]);

        $blog->refresh();
        $this->assertEquals('rejected', $blog->status);

        // Verify Moderation Log
        $this->assertDatabaseHas('blog_moderation_logs', [
            'blog_id' => $blog->id,
            'admin_id' => $this->admin->id,
            'action' => 'rejected',
            'note' => $rejectionNote,
        ]);

        // Verify Author Notification
        $notifications = $this->author->notifications;
        $this->assertCount(1, $notifications);

        $notifData = $notifications->first()->data;
        $this->assertEquals('rejected', $notifData['status']);
        $this->assertEquals($rejectionNote, $notifData['note']);
        $this->assertStringContainsString('bị từ chối', $notifData['message']);
    }

    public function test_admin_can_request_revision_with_instructions(): void
    {
        $blog = Blog::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'title' => 'Tổng hợp địa chỉ ăn vặt vỉa hè',
            'status' => 'pending',
        ]);

        $revisionNote = 'Vui lòng bổ sung khung giá và địa chỉ chính xác của từng quán.';

        $response = $this->actingAs($this->admin)->putJson(route('admin.blog.posts.update-status', $blog), [
            'status' => 'need_revision',
            'note' => $revisionNote,
            'expected_status' => 'pending',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'need_revision',
                'status_label' => 'Cần sửa',
            ]);

        $blog->refresh();
        $this->assertEquals('need_revision', $blog->status);

        // Verify Author Notification
        $notifications = $this->author->notifications;
        $this->assertCount(1, $notifications);

        $notifData = $notifications->first()->data;
        $this->assertEquals('need_revision', $notifData['status']);
        $this->assertEquals($revisionNote, $notifData['note']);
        $this->assertStringContainsString('yêu cầu chỉnh sửa', $notifData['message']);
    }

    public function test_concurrency_conflict_detected_when_expected_status_mismatches(): void
    {
        $blog = Blog::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'status' => 'published', // Đã được admin khác duyệt trước đó
        ]);

        // Admin cố gắng duyệt với giả định bài vẫn là pending
        $response = $this->actingAs($this->admin)->putJson(route('admin.blog.posts.update-status', $blog), [
            'status' => 'rejected',
            'note' => 'Từ chối sau khi xem xét.',
            'expected_status' => 'pending',
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'current_status' => 'published',
            ]);
    }

    public function test_non_admin_cannot_update_blog_status(): void
    {
        $blog = Blog::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'status' => 'pending',
        ]);

        $userRole = VaiTro::where('ten', 'nguoi_dung')->first();
        $otherUser = User::factory()->create([
            'vai_tro_id' => $userRole->id,
            'trang_thai' => 'hoat_dong',
        ]);

        $response = $this->actingAs($otherUser)->putJson(route('admin.blog.posts.update-status', $blog), [
            'status' => 'published',
        ]);

        $response->assertStatus(403);
    }

    public function test_author_can_fetch_notifications_and_realtime_check(): void
    {
        $blog = Blog::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'status' => 'pending',
        ]);

        // Gửi thông báo cho tác giả
        $this->author->notify(new BlogStatusUpdatedNotification($blog, 'published', 'Chúc mừng bài viết đã được duyệt!'));

        // Kiểm tra realtime-check endpoint
        $realtimeRes = $this->actingAs($this->author)->getJson(route('nguoi-dung.thong-bao.realtime-check'));
        $realtimeRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'unread_count' => 1,
            ]);

        $notifId = $realtimeRes->json('latest.id');
        $this->assertNotNull($notifId);

        // Tác giả đọc danh sách thông báo
        $listRes = $this->actingAs($this->author)->getJson(route('nguoi-dung.thong-bao.index'));
        $listRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'unread_count' => 1,
            ]);

        // Đánh dấu đã đọc một thông báo
        $readRes = $this->actingAs($this->author)->postJson(route('nguoi-dung.thong-bao.mark-read', $notifId));
        $readRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'unread_count' => 0,
            ]);

        // Đánh dấu tất cả đã đọc
        $markAllRes = $this->actingAs($this->author)->postJson(route('nguoi-dung.thong-bao.mark-all-read'));
        $markAllRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'unread_count' => 0,
            ]);
    }

    public function test_admin_can_delete_blog_via_ajax(): void
    {
        $blog = Blog::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->deleteJson(route('admin.blog.posts.destroy', $blog));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertSoftDeleted('blogs', [
            'id' => $blog->id,
        ]);
    }

    public function test_user_blog_index_returns_moderation_logs_and_counts_via_ajax(): void
    {
        $blog = Blog::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'status' => 'need_revision',
        ]);

        BlogModerationLog::create([
            'blog_id' => $blog->id,
            'admin_id' => $this->admin->id,
            'action' => 'requested_revision',
            'from_status' => 'pending',
            'to_status' => 'need_revision',
            'note' => 'Vui lòng bổ sung địa chỉ chính xác',
        ]);

        $response = $this->actingAs($this->author)->getJson(route('nguoi-dung.blog.index'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'total_count',
                'status_counts',
                'blogs',
            ]);
    }
}
