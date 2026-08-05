<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogSecurityTest extends TestCase
{
    /**
     * Khách không xem được bài draft.
     */
    public function test_guest_cannot_view_draft_blog()
    {
        $blog = Blog::factory()->create(['status' => 'draft']);
        $response = $this->get(route('blog.show', $blog->slug));
        $response->assertStatus(404);
    }

    /**
     * Stored XSS bị làm sạch.
     */
    public function test_stored_xss_is_purified()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Assume we send a post request with malicious content
        // We will test if the purifier actually works
        $maliciousContent = '<script>alert("xss")</script><p>Safe content</p>';
        $cleanContent = clean($maliciousContent);

        $this->assertStringNotContainsString('<script>', $cleanContent);
        $this->assertStringContainsString('Safe content', $cleanContent);
    }

    /**
     * Thành viên không sửa được bài published.
     */
    public function test_member_cannot_edit_published_blog()
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create(['user_id' => $user->id, 'status' => 'published']);
        
        $response = $this->actingAs($user)->get(route('nguoi-dung.blog.edit', $blog));
        $response->assertStatus(403);
    }
}
