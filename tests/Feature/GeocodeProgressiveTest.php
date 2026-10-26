<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeocodeProgressiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_geocode_with_detailed_address_progressively_falls_back(): void
    {
        // Mock Nominatim responses
        Http::fake([
            // Exact address returns empty (e.g. house number not found in OSM)
            'https://nominatim.openstreetmap.org/search*q=123*Cat*Hung*' => Http::response([], 200),
            // Ward level returns coordinates
            'https://nominatim.openstreetmap.org/search*q=Phuong*Ben*Nghe*' => Http::response([
                [
                    'place_id' => 123456,
                    'lat' => '10.7818088',
                    'lon' => '106.7006195',
                    'display_name' => 'Phường Bến Nghé, Quận 1, Thành phố Hồ Chí Minh',
                ]
            ], 200),
        ]);

        $response = $this->getJson('/api/ban-do/geocode?address=' . urlencode('123 Cát Hưng, Phường Bến Nghé, Quận 1, Thành phố Hồ Chí Minh'));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_fallback', true)
            ->assertJsonPath('lat', 10.7818088)
            ->assertJsonPath('lng', 106.7006195)
            ->assertJsonPath('matched_query', 'Phường Bến Nghé, Quận 1, Thành phố Hồ Chí Minh');
    }

    public function test_geocode_with_known_city_does_not_mark_fallback(): void
    {
        Http::fake([
            'https://nominatim.openstreetmap.org/search*q=Thanh*pho*Ha*Noi*' => Http::response([
                [
                    'place_id' => 999999,
                    'lat' => '21.0283334',
                    'lon' => '105.8540410',
                    'display_name' => 'Thành phố Hà Nội, Việt Nam',
                ]
            ], 200),
        ]);

        $response = $this->getJson('/api/ban-do/geocode?address=' . urlencode('Thành phố Hà Nội'));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_fallback', false)
            ->assertJsonPath('lat', 21.0283334)
            ->assertJsonPath('lng', 105.8540410);
    }
}
