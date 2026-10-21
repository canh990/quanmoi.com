<?php

namespace Tests\Feature;

use App\Models\HinhAnhQuan;
use App\Models\Quan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_photo_renders_cinematic_hero_banner(): void
    {
        $owner = User::factory()->create();
        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'trang_thai' => 'da_duyet',
            'anh_bia' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5',
        ]);

        $response = $this->get(route('quan.detail', $quan->slug));

        $response->assertStatus(200);
        $response->assertSee('gallery-hero-single');
        $response->assertSee('Xem ảnh phóng to');
        $response->assertDontSee('lh3.googleusercontent.com'); // Đảm bảo tuyệt đối không có ảnh stock giả mạo
    }

    public function test_two_photos_renders_split_grid(): void
    {
        $owner = User::factory()->create();
        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'trang_thai' => 'da_duyet',
            'anh_bia' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5',
        ]);

        HinhAnhQuan::create([
            'quan_id' => $quan->id,
            'duong_dan' => 'https://images.unsplash.com/photo-gallery-1',
            'tieu_de' => 'Món ngon 1',
        ]);

        $response = $this->get(route('quan.detail', $quan->slug));

        $response->assertStatus(200);
        $response->assertSee('gallery-grid-2');
        $response->assertSee('Xem 2 ảnh');
    }

    public function test_three_photos_renders_grid_three(): void
    {
        $owner = User::factory()->create();
        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'trang_thai' => 'da_duyet',
            'anh_bia' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5',
        ]);

        HinhAnhQuan::create([
            'quan_id' => $quan->id,
            'duong_dan' => 'https://images.unsplash.com/photo-gallery-1',
        ]);
        HinhAnhQuan::create([
            'quan_id' => $quan->id,
            'duong_dan' => 'https://images.unsplash.com/photo-gallery-2',
        ]);

        $response = $this->get(route('quan.detail', $quan->slug));

        $response->assertStatus(200);
        $response->assertSee('gallery-grid-3');
        $response->assertSee('Xem tất cả (3 ảnh)');
    }

    public function test_four_or_more_photos_renders_bento_grid_with_counter(): void
    {
        $owner = User::factory()->create();
        $quan = Quan::factory()->create([
            'chu_quan_id' => $owner->id,
            'trang_thai' => 'da_duyet',
            'anh_bia' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5',
        ]);

        for ($i = 1; $i <= 4; $i++) {
            HinhAnhQuan::create([
                'quan_id' => $quan->id,
                'duong_dan' => 'https://images.unsplash.com/photo-gallery-' . $i,
            ]);
        }

        $response = $this->get(route('quan.detail', $quan->slug));

        $response->assertStatus(200);
        $response->assertSee('bento-grid');
        $response->assertSee('Xem tất cả (5)');
    }
}
