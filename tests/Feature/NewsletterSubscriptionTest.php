<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogSubscriber;
use App\Models\User;
use App\Models\VaiTro;
use App\Notifications\NewBlogPublishedNotification;
use App\Notifications\NewsletterWelcomeNotification;
use Database\Seeders\VaiTroSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
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
        ]);

        $this->user = User::factory()->create([
            'vai_tro_id' => $userRole->id,
            'trang_thai' => 'hoat_dong',
        ]);

        $this->category = BlogCategory::firstOrCreate(
            ['slug' => 'quan-ngon-sai-gon'],
            ['name' => 'Quán ngon Sài Gòn', 'status' => true]
        );
    }

    public function test_user_can_subscribe_to_newsletter_and_receives_welcome_email()
    {
        Notification::fake();

        $testEmail = 'amthuc_yeuthich_' . uniqid() . '@gmail.com';

        $response = $this->postJson(route('blog.newsletter.subscribe'), [
            'email' => $testEmail,
            'source' => 'test_suite',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Check database
        $subscriber = BlogSubscriber::where('email', $testEmail)->first();
        $this->assertNotNull($subscriber);
        $this->assertEquals('active', $subscriber->status);
        $this->assertNotEmpty($subscriber->token);

        // Check welcome notification was sent
        Notification::assertSentOnDemand(
            NewsletterWelcomeNotification::class,
            function ($notification, $channels, $notifiable) use ($testEmail) {
                return $notifiable->routes['mail'] === $testEmail;
            }
        );
    }

    public function test_when_new_blog_is_published_all_subscribers_receive_email_notification()
    {
        Notification::fake();

        // 1. Create 2 active subscribers and 1 unsubscribed
        $sub1 = BlogSubscriber::create([
            'email' => 'subscriber1_' . uniqid() . '@gmail.com',
            'status' => 'active',
            'token' => 'token1_' . uniqid(),
        ]);

        $sub2 = BlogSubscriber::create([
            'email' => 'subscriber2_' . uniqid() . '@gmail.com',
            'status' => 'active',
            'token' => 'token2_' . uniqid(),
        ]);

        $subUnsub = BlogSubscriber::create([
            'email' => 'unsubscribed_' . uniqid() . '@gmail.com',
            'status' => 'unsubscribed',
            'token' => 'token3_' . uniqid(),
        ]);

        // 2. Create pending blog
        $blog = Blog::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Top 5 Quán Bún Bò Huế Chuẩn Vị Nhất Sài Gòn',
            'slug' => 'top-5-quan-bun-bo-hue-' . uniqid(),
            'content' => 'Nội dung review chi tiết các quán bún bò huế thơm ngon nức tiếng',
            'status' => 'pending',
        ]);

        // 3. Admin approves and publishes the blog
        $response = $this->actingAs($this->admin)->putJson(route('admin.blog.posts.update-status', $blog->id), [
            'status' => 'published',
            'expected_status' => 'pending',
            'note' => 'Bài viết chất lượng, duyệt ngay!',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('published', $blog->fresh()->status);

        // 4. Verify notification sent to active subscribers, but NOT to unsubscribed
        Notification::assertSentOnDemand(
            NewBlogPublishedNotification::class,
            function ($notification, $channels, $notifiable) use ($sub1) {
                return $notifiable->routes['mail'] === $sub1->email;
            }
        );

        Notification::assertSentOnDemand(
            NewBlogPublishedNotification::class,
            function ($notification, $channels, $notifiable) use ($sub2) {
                return $notifiable->routes['mail'] === $sub2->email;
            }
        );

        Notification::assertNotSentTo(
            [$subUnsub],
            NewBlogPublishedNotification::class
        );
    }

    public function test_subscriber_can_unsubscribe_via_token_link()
    {
        $subscriber = BlogSubscriber::create([
            'email' => 'cancelsub_' . uniqid() . '@gmail.com',
            'status' => 'active',
            'token' => 'valid_token_' . uniqid(),
        ]);

        $response = $this->get(route('blog.newsletter.unsubscribe', $subscriber->token));
        $response->assertStatus(200);
        $response->assertSee('Đã hủy đăng ký nhận tin');

        $this->assertEquals('unsubscribed', $subscriber->fresh()->status);
    }
}
