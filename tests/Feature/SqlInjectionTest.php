<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Quan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SqlInjectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_sql_injection_payloads_do_not_affect_database(): void
    {
        $user = User::factory()->create();

        $sqlPayload = "' OR '1'='1' -- ";

        $data = [
            'ten_quan'              => 'Quán Ăn ' . $sqlPayload,
            'loai_hinh_kinh_doanh'  => 'Quán ăn bình dân',
            'so_dien_thoai'         => '0988776655',
            'dia_chi_chi_tiet'  => '456 Nguyễn Huệ',
            'ten_tinh_thanh'    => 'TP. Hồ Chí Minh',
            'ten_quan_huyen'    => 'Quận 1',
            'gio_mo_cua'        => '07:00',
            'gio_dong_cua'      => '22:00',
        ];

        $response = $this->actingAs($user)->postJson('/chu-quan/dang-quan', $data);

        $response->assertStatus(200);

        // Verify payload was safely escaped as plain text
        $quan = Quan::where('so_dien_thoai', '0988776655')->first();
        $this->assertNotNull($quan);
        $this->assertStringContainsString("' OR '1'='1'", $quan->ten_quan);
    }
}
