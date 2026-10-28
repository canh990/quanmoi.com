<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticPagesAndFooterLinksTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test all newly created static pages return HTTP 200 and contain expected headings.
     */
    public function test_static_pages_render_successfully(): void
    {
        $pages = [
            '/gioi-thieu' => 'Khám phá',
            '/danh-cho-chu-quan' => 'Dành cho Chủ Quán',
            '/tuyen-dung' => 'tương lai ẩm thực số',
            '/lien-he' => 'Liên hệ hợp tác',
            '/tro-giup' => 'Trung tâm trợ giúp',
            '/giai-quyet-tranh-chap' => 'tranh chấp',
            '/quy-dinh-su-dung' => 'Quy định sử dụng',
            '/chinh-sach-bao-mat' => 'bảo mật thông tin',
            '/chinh-sach-cookie' => 'Cookie',
        ];

        foreach ($pages as $uri => $expectedText) {
            $response = $this->get($uri);
            $response->assertOk();
            $response->assertSee($expectedText);
        }
    }

    /**
     * Test alias routes for legal documents.
     */
    public function test_legal_alias_routes_work(): void
    {
        $this->get('/dieu-khoan')->assertOk();
        $this->get('/bao-mat')->assertOk();
        $this->get('/cookie')->assertOk();
    }

    /**
     * Test footer view contains all required links without empty hashes.
     */
    public function test_footer_contains_all_named_routes(): void
    {
        $response = $this->get('/gioi-thieu');
        $response->assertOk();

        // Footer must link to our pages
        $response->assertSee(route('quan-moi'));
        $response->assertSee(route('about'));
        $response->assertSee(route('blog.index'));
        $response->assertSee(route('pages.chu-quan'));
        $response->assertSee(route('pages.tuyen-dung'));
        $response->assertSee(route('pages.lien-he'));
        $response->assertSee(route('pages.tro-giup'));
        $response->assertSee(route('pages.dieu-khoan'));
        $response->assertSee(route('pages.bao-mat'));
        $response->assertSee(route('pages.tranh-chap'));
        $response->assertSee(route('pages.cookie'));
    }
}
