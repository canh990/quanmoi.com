<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DiaChiApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_v2_provinces_and_wards_load_even_if_empty_cache_entries_exist(): void
    {
        Cache::put('tinh_thanh_list_v2', [], 86400);
        Cache::put('phuong_xa_tinh_v2_79', [], 86400);
        Cache::put('quan_huyen_v2_79', [], 86400);

        Http::fake([
            'provinces.open-api.vn/api/v2/p/' => Http::response([
                ['code' => 79, 'name' => 'Thành phố Hồ Chí Minh'],
                ['code' => 75, 'name' => 'Thành phố Đồng Nai'],
            ]),
            'provinces.open-api.vn/api/v2/p/79*' => Http::response([
                'code' => 79,
                'name' => 'Thành phố Hồ Chí Minh',
                'wards' => [
                    ['code' => 26734, 'name' => 'Phường Tân Định'],
                ],
            ]),
        ]);

        $this->getJson('/api/dia-chi/tinh-thanh')
            ->assertOk()
            ->assertJsonPath('data.0.code', 79)
            ->assertJsonPath('data.0.name', 'Thành phố Hồ Chí Minh');

        $this->getJson('/api/dia-chi/phuong-xa-tinh/79')
            ->assertOk()
            ->assertJsonPath('data.0.code', 26734)
            ->assertJsonPath('data.0.name', 'Phường Tân Định');

        $this->getJson('/api/dia-chi/quan-huyen/79')
            ->assertOk()
            ->assertJsonPath('data.0.code', 26734)
            ->assertJsonPath('data.0.name', 'Phường Tân Định');
    }

    public function test_fallback_provinces_returns_34_merged_provinces_when_api_fails(): void
    {
        Cache::forget('tinh_thanh_list_v2');

        Http::fake([
            'provinces.open-api.vn/api/v2/p/*' => Http::response(null, 500),
            'provinces.open-api.vn/api/v2/p/' => Http::response(null, 500),
        ]);

        $response = $this->getJson('/api/dia-chi/tinh-thanh')->assertOk();
        $this->assertCount(34, $response->json('data'));
    }
}
