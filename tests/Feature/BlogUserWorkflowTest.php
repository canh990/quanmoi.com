<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogUserWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected BlogCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
        $this->seed(\Database\Seeders\VaiTroSeeder::class);

        $userRole = \App\Models\VaiTro::where('ten', 'nguoi_dung')->first();

        $this->user = User::factory()->create([
            'vai_tro_id' => $userRole?->id,
            'trang_thai' => 'hoat_dong',
        ]);
        $this->category = BlogCategory::firstOrCreate(
            ['slug' => 'am-thuc-viet'],
            ['name' => 'Ẩm thực Việt', 'status' => true]
        );
    }

    public function test_can_view_blog_index_and_search()
    {
        $publishedBlog = Blog::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Khám phá Phở Hà Nội ngon nức tiếng',
            'slug' => 'kham-pha-pho-ha-noi-' . uniqid(),
            'content' => 'Nội dung chi tiết về phở bò gia truyền',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get(route('blog.index', ['search' => 'Phở Hà Nội']));
        $response->assertStatus(200);
        $response->assertSee('Khám phá Phở Hà Nội');
    }

    public function test_can_view_blog_detail_with_related_posts()
    {
        $mainBlog = Blog::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Top 10 Quán Ốc Sài Gòn Đáng Thử',
            'slug' => 'top-10-quan-oc-sai-gon-' . uniqid(),
            'content' => 'Review chi tiết các quán ốc đêm tươi ngon',
            'status' => 'published',
            'published_at' => now(),
            'allow_comments' => true,
        ]);

        $relatedBlog = Blog::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Ốc Đào Nguyễn Trãi siêu cay',
            'slug' => 'oc-dao-nguyen-trai-' . uniqid(),
            'content' => 'Quán ốc gia truyền khu trung tâm',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get(route('blog.show', $mainBlog->slug));
        $response->assertStatus(200);
        $response->assertSee('Bài viết liên quan');
        $response->assertSee('Ốc Đào Nguyễn Trãi siêu cay');
    }

    public function test_ajax_record_view_increments_count()
    {
        $blog = Blog::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Bún Đậu Mắm Tôm Phố Cổ',
            'slug' => 'bun-dau-mam-tom-' . uniqid(),
            'content' => 'Địa chỉ ăn bún đậu chuẩn vị Bắc',
            'status' => 'published',
            'published_at' => now(),
            'view_count' => 5,
        ]);

        $response = $this->postJson(route('blog.view.record', $blog->slug));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'view_count']);
    }

    public function test_can_post_comment_and_like_and_delete()
    {
        $blog = Blog::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Cà Phê Trứng Giảng Hà Nội',
            'slug' => 'ca-phe-trung-giang-' . uniqid(),
            'content' => 'Hương vị cà phê trứng béo ngậy cổ kính',
            'status' => 'published',
            'published_at' => now(),
            'allow_comments' => true,
            'comment_count' => 0,
        ]);

        $reader = User::factory()->create();

        // 1. Post comment
        $commentResponse = $this->actingAs($reader)->postJson(route('blog.comment.store', $blog->slug), [
            'content' => 'Bài viết rất hữu ích, mình đã ghé thử!',
        ]);
        $commentResponse->assertStatus(200);
        $commentId = $commentResponse->json('comment.id');
        $this->assertNotNull($commentId);

        // 2. Like comment
        $likeResponse = $this->actingAs($reader)->postJson(route('blog.comment.like', ['slug' => $blog->slug, 'commentId' => $commentId]));
        $likeResponse->assertStatus(200);
        $likeResponse->assertJson(['success' => true, 'liked' => true]);

        // 3. Delete comment by author of comment
        $deleteResponse = $this->actingAs($reader)->deleteJson(route('blog.comment.destroy', ['slug' => $blog->slug, 'commentId' => $commentId]));
        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJson(['success' => true]);
        $this->assertDatabaseMissing('blog_comments', ['id' => $commentId]);
    }

    public function test_can_view_author_profile()
    {
        $blog = Blog::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Bánh Mì Huỳnh Hoa Sài Gòn',
            'slug' => 'banh-mi-huynh-hoa-' . uniqid(),
            'content' => 'Bánh mì ô môi nổi tiếng đắt đỏ bậc nhất',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get(route('blog.author', $this->user->id));
        $response->assertStatus(200);
        $response->assertSee($this->user->ho_ten);
        $response->assertSee('Bánh Mì Huỳnh Hoa Sài Gòn');
    }

    public function test_newsletter_subscription()
    {
        $email = 'test_' . uniqid() . '@example.com';
        $response = $this->postJson(route('blog.newsletter.subscribe'), [
            'email' => $email,
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('blog_subscribers', ['email' => $email]);
    }

    public function test_member_can_create_and_update_and_delete_blog()
    {
        Storage::fake('public');

        // 1. Create draft
        $createResponse = $this->actingAs($this->user)->post(route('nguoi-dung.blog.store'), [
            'title' => 'Kinh nghiệm đi ăn buffet nướng 99k',
            'category_id' => $this->category->id,
            'excerpt' => 'Một vài lưu ý nhỏ cho các bạn sinh viên',
            'content' => '<p>Chi tiết các quán nướng giá rẻ sinh viên</p>',
            'action' => 'draft',
        ]);
        $createResponse->assertRedirect(route('nguoi-dung.blog.index'));

        $blog = Blog::where('title', 'Kinh nghiệm đi ăn buffet nướng 99k')->first();
        $this->assertNotNull($blog);
        $this->assertEquals('draft', $blog->status);

        // 2. Update and submit for review
        $updateResponse = $this->actingAs($this->user)->put(route('nguoi-dung.blog.update', $blog->id), [
            'title' => 'Kinh nghiệm đi ăn buffet nướng 99k chất lượng',
            'category_id' => $this->category->id,
            'excerpt' => 'Một vài lưu ý nhỏ đã cập nhật',
            'content' => '<p>Chi tiết các quán nướng giá rẻ sinh viên đã kiểm chứng</p>',
            'action' => 'pending',
        ]);
        $updateResponse->assertRedirect(route('nguoi-dung.blog.index'));

        $blog->refresh();
        $this->assertEquals('pending', $blog->status);

        // 3. Delete blog
        $deleteResponse = $this->actingAs($this->user)->delete(route('nguoi-dung.blog.destroy', $blog->id));
        $deleteResponse->assertRedirect();
        $this->assertSoftDeleted('blogs', ['id' => $blog->id]);
    }

    public function test_ai_suggest_titles_endpoint()
    {
        $response = $this->actingAs($this->user)->postJson(route('ai.blog.suggest-titles'), [
            'topic' => 'Quán cà phê cá koi view đẹp',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'titles']);
    }

    public function test_ai_suggest_tags_category_endpoint()
    {
        $response = $this->actingAs($this->user)->postJson(route('ai.blog.suggest-tags-category'), [
            'title' => 'Bún chả Hương Liên tổng thống Obama từng ghé',
            'content' => 'Review chi tiết bún chả que tre Hà Nội',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }
}
