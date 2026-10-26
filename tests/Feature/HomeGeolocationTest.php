<?php

namespace Tests\Feature;

use App\Models\Quan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeGeolocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_displays_normally_without_location(): void
    {
        $quan = Quan::factory()->create([
            'ten_quan' => 'Quán Phở Nổi Bật',
            'trang_thai' => 'da_duyet',
            'is_noi_bat' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Quán ăn nổi bật được đánh giá cao');
        $response->assertSee('Quán Phở Nổi Bật');
        $response->assertViewHas('hasGps', false);
        $response->assertViewHas('quanGanBan', fn ($items) => $items->isEmpty());
    }

    public function test_home_displays_nearby_restaurants_when_location_provided_via_query_params(): void
    {
        // Tọa độ người dùng: Hồ Hoàn Kiếm (21.0285, 105.8542)
        $userLat = 21.0285;
        $userLng = 105.8542;

        // Quán A: Rất gần (~200m)
        $quanA = Quan::factory()->create([
            'ten_quan' => 'Quán Ăn Gần Nhất',
            'trang_thai' => 'da_duyet',
            'vi_do' => 21.0300,
            'kinh_do' => 105.8542,
        ]);

        // Quán B: Xa hơn (~3km)
        $quanB = Quan::factory()->create([
            'ten_quan' => 'Quán Ăn Xa Hơn',
            'trang_thai' => 'da_duyet',
            'vi_do' => 21.0550,
            'kinh_do' => 105.8542,
        ]);

        // Quán C: Quá xa (> 50km)
        $quanC = Quan::factory()->create([
            'ten_quan' => 'Quán Rất Xa',
            'trang_thai' => 'da_duyet',
            'vi_do' => 20.5000,
            'kinh_do' => 105.8542,
        ]);

        $response = $this->get(route('home', ['lat' => $userLat, 'lng' => $userLng]));

        $response->assertStatus(200);
        $response->assertViewHas('hasGps', true);
        $response->assertViewHas('quanGanBan', function ($items) use ($quanA, $quanB, $quanC) {
            $ids = $items->pluck('id')->all();
            // Phải chứa quán A và B, không chứa quán C (>10km)
            $containsNear = in_array($quanA->id, $ids, true) && in_array($quanB->id, $ids, true);
            $excludesFar = !in_array($quanC->id, $ids, true);

            // Sắp xếp tăng dần theo khoảng cách
            $sorted = $items->first()->id === $quanA->id;

            return $containsNear && $excludesFar && $sorted;
        });

        $response->assertSee('Quán Ăn Gần Nhất');
        $response->assertSee('Gần bạn nhất (bán kính 10km)');
    }

    public function test_home_displays_nearby_restaurants_when_location_provided_via_cookies(): void
    {
        $userLat = 21.0285;
        $userLng = 105.8542;

        $quanA = Quan::factory()->create([
            'ten_quan' => 'Quán Gần Qua Cookie',
            'trang_thai' => 'da_duyet',
            'vi_do' => 21.0290,
            'kinh_do' => 105.8542,
        ]);

        $response = $this->withCookies([
            'user_lat' => (string) $userLat,
            'user_lng' => (string) $userLng,
        ])->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewHas('hasGps', true);
        $response->assertViewHas('quanGanBan', function ($items) use ($quanA) {
            return $items->contains('id', $quanA->id);
        });
        $response->assertSee('Quán Gần Qua Cookie');
    }

    public function test_home_falls_back_normally_when_invalid_location_provided(): void
    {
        $response = $this->get(route('home', ['lat' => 'not-a-number', 'lng' => 999]));

        $response->assertStatus(200);
        $response->assertViewHas('hasGps', false);
        $response->assertViewHas('quanGanBan', fn ($items) => $items->isEmpty());
        $response->assertSee('Quán ăn nổi bật được đánh giá cao');
    }
}
