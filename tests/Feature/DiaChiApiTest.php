<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DiaChiApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_districts_and_wards_load_even_if_empty_legacy_cache_entries_exist(): void
    {
        Cache::put('quan_huyen_79', [], 86400);
        Cache::put('phuong_xa_760', [], 86400);

        Http::fake([
            'provinces.open-api.vn/api/v1/p/79*' => Http::response([
                'districts' => [
                    ['code' => 760, 'name' => 'Quận 1'],
                ],
            ]),
            'provinces.open-api.vn/api/v1/d/760*' => Http::response([
                'wards' => [
                    ['code' => 26734, 'name' => 'Phường Tân Định'],
                ],
            ]),
        ]);

        $this->getJson('/api/dia-chi/quan-huyen/79')
            ->assertOk()
            ->assertJsonPath('data.0.code', 760)
            ->assertJsonPath('data.0.name', 'Quận 1');

        $this->getJson('/api/dia-chi/phuong-xa/760')
            ->assertOk()
            ->assertJsonPath('data.0.code', 26734)
            ->assertJsonPath('data.0.name', 'Phường Tân Định');
    }
}
