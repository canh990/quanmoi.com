<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Quan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class XssBaoMatTest extends TestCase
{
    use RefreshDatabase;

    public function test_input_strings_are_sanitized_against_xss_attacks(): void
    {
        $user = User::factory()->create();

        $xssInput = '<script>alert("Hacked!")</script>Quán Phở Ngon';

        $data = [
            'ten_quan'              => $xssInput,
            'loai_hinh_kinh_doanh'  => 'Quán ăn bình dân',
            'so_dien_thoai'         => '0900000000',
            'dia_chi_chi_tiet'  => '<b>123 Lê Lợi</b>',
            'ten_tinh_thanh'    => 'TP. Hồ Chí Minh',
            'ten_quan_huyen'    => 'Quận 1',
            'gio_mo_cua'        => '08:00',
            'gio_dong_cua'      => '21:00',
        ];

        $response = $this->actingAs($user)->postJson('/chu-quan/dang-quan', $data);

        $response->assertStatus(200);

        // Verify HTML tags were stripped from DB
        $quan = Quan::where('so_dien_thoai', '0900000000')->first();
        $this->assertNotNull($quan);
        $this->assertEquals('alert("Hacked!")Quán Phở Ngon', $quan->ten_quan);
        $this->assertEquals('123 Lê Lợi', $quan->dia_chi_chi_tiet);
    }
}
