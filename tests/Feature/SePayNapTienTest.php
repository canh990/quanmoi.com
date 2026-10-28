<?php

namespace Tests\Feature;

use App\Models\ThanhToan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SePayNapTienTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('sepay.merchant_id', 'SP-TEST-DEMO');
        Config::set('sepay.secret_key', 'spsk_test_secret_key_1234567890');
        Config::set('sepay.env', 'sandbox');
        Config::set('sepay.bank_name', 'MBBank');
        Config::set('sepay.acc_number', '0901234567');
        Config::set('sepay.acc_holder', 'QUAN MOI');
    }

    public function test_khach_chua_dang_nhap_khong_the_nap_tien(): void
    {
        $response = $this->post(route('tai-khoan.nap-tien.checkout'), [
            'so_tien' => 50000,
        ]);

        $response->assertRedirect('/dang-nhap');
    }

    public function test_nap_tien_that_bai_khi_so_tien_nho_hon_10_nghin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tai-khoan.nap-tien.checkout'), [
            'so_tien' => 5000,
        ]);

        $response->assertSessionHasErrors(['so_tien']);
    }

    public function test_nap_tien_tao_thanh_toan_va_tra_ve_form_checkout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tai-khoan.nap-tien.checkout'), [
            'so_tien' => 100000,
            'phuong_thuc' => 'ALL',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('nguoi-dung.tai-khoan.nap-tien-redirect');
        $response->assertViewHas('checkoutInfo');

        $this->assertDatabaseHas('thanh_toan', [
            'user_id' => $user->id,
            'so_tien' => 100000,
            'trang_thai' => 'cho_thanh_toan',
        ]);
    }

    public function test_nap_tien_tra_ve_json_khi_request_ajax(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('tai-khoan.nap-tien.checkout'), [
            'so_tien' => 200000,
            'phuong_thuc' => 'BANK_TRANSFER',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'action_url',
            'fields' => [
                'merchant',
                'currency',
                'order_amount',
                'operation',
                'order_invoice_number',
                'signature',
            ],
            'invoice_number',
            'qr_url',
            'status_url',
        ]);
    }

    public function test_nap_tien_ajax_tra_ve_vietqr_info(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('tai-khoan.nap-tien.checkout'), [
            'so_tien' => 50000,
            'phuong_thuc' => 'VIETQR',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'code',
            'amount',
            'amount_formatted',
            'bank_name',
            'acc_number',
            'acc_holder',
            'qr_url',
            'status_url',
        ]);
        $this->assertEquals(50000, $response->json('amount'));
        $this->assertStringContainsString('vietqr.app/img', $response->json('qr_url'));
    }

    public function test_tra_cuu_trang_thai_don_nap_tien(): void
    {
        $user = User::factory()->create(['so_du' => 50000]);

        $thanhToan = ThanhToan::create([
            'user_id' => $user->id,
            'ma_giao_dich' => 'NAP_TEST_STATUS',
            'ten_goi_dich_vu' => 'Nạp tiền vào tài khoản ví Quán Mới',
            'so_tien' => 100000,
            'phuong_thuc' => 'vietqr',
            'trang_thai' => 'cho_thanh_toan',
        ]);

        $resPending = $this->getJson(route('api.sepay.status', ['code' => 'NAP_TEST_STATUS']));
        $resPending->assertStatus(200);
        $resPending->assertJson([
            'success' => true,
            'code' => 'NAP_TEST_STATUS',
            'status' => 'cho_thanh_toan',
            'amount' => 100000,
        ]);

        // Cập nhật thành công
        $thanhToan->trang_thai = 'thanh_cong';
        $thanhToan->save();

        $resPaid = $this->getJson(route('api.sepay.status', ['code' => 'NAP_TEST_STATUS']));
        $resPaid->assertStatus(200);
        $resPaid->assertJson([
            'success' => true,
            'status' => 'thanh_cong',
        ]);
    }

    public function test_sepay_webhook_xac_thuc_api_key_thanh_cong(): void
    {
        Config::set('sepay.webhook_api_key', 'my_sepay_api_key_test');

        $user = User::factory()->create(['so_du' => 0]);
        $code = 'NAP' . strtoupper(substr(uniqid(), -6));

        $thanhToan = ThanhToan::create([
            'user_id' => $user->id,
            'ma_giao_dich' => $code,
            'ten_goi_dich_vu' => 'Nạp tiền vào ví',
            'so_tien' => 200000,
            'phuong_thuc' => 'vietqr',
            'trang_thai' => 'cho_thanh_toan',
        ]);

        $payload = [
            'id' => 99901,
            'gateway' => 'MBBank',
            'transactionDate' => '2026-10-28 09:30:00',
            'accountNumber' => '0901234567',
            'code' => $code,
            'content' => $code . ' chuyen tien',
            'transferType' => 'in',
            'transferAmount' => 200000,
            'referenceCode' => 'REF99901',
        ];

        // 1. Gửi sai API Key -> 401
        $resFail = $this->postJson(route('api.sepay.webhook'), $payload, [
            'Authorization' => 'Apikey wrong_api_key',
        ]);
        $resFail->assertStatus(401);

        // 2. Gửi đúng API Key -> 200 {"success": true}
        $resOk = $this->postJson(route('api.sepay.webhook'), $payload, [
            'Authorization' => 'Apikey my_sepay_api_key_test',
        ]);
        $resOk->assertStatus(200);
        $resOk->assertJson(['success' => true]);

        $thanhToan->refresh();
        $this->assertEquals('thanh_cong', $thanhToan->trang_thai);
        $this->assertEquals('99901', $thanhToan->sepay_transaction_id);

        $user->refresh();
        $this->assertEquals(200000.0, (float) $user->so_du);
    }

    public function test_sepay_webhook_xac_thuc_hmac_sha256_va_idempotency(): void
    {
        $secret = 'test_webhook_hmac_secret_123';
        Config::set('sepay.webhook_secret', $secret);

        $user = User::factory()->create(['so_du' => 50000]);
        $code = 'NAP' . strtoupper(substr(uniqid(), -6));

        $thanhToan = ThanhToan::create([
            'user_id' => $user->id,
            'ma_giao_dich' => $code,
            'ten_goi_dich_vu' => 'Nạp tiền vào ví',
            'so_tien' => 100000,
            'phuong_thuc' => 'vietqr',
            'trang_thai' => 'cho_thanh_toan',
        ]);

        $payload = [
            'id' => 99902,
            'gateway' => 'Vietcombank',
            'transactionDate' => '2026-10-28 09:35:00',
            'accountNumber' => '1017588888',
            'code' => $code,
            'content' => $code . ' nop tien tai khoan',
            'transferType' => 'in',
            'transferAmount' => 100000,
            'referenceCode' => 'REF99902',
        ];

        $rawBody = json_encode($payload);
        $timestamp = time();
        $signature = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);

        $response = $this->call(
            'POST',
            route('api.sepay.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_SEPAY_SIGNATURE' => $signature,
                'HTTP_X_SEPAY_TIMESTAMP' => (string) $timestamp,
            ],
            $rawBody
        );

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $user->refresh();
        $this->assertEquals(150000.0, (float) $user->so_du);

        // Idempotency: Gửi lại lần 2 không cộng thêm tiền
        $response2 = $this->call(
            'POST',
            route('api.sepay.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_SEPAY_SIGNATURE' => $signature,
                'HTTP_X_SEPAY_TIMESTAMP' => (string) $timestamp,
            ],
            $rawBody
        );

        $response2->assertStatus(200);
        $response2->assertJson(['success' => true]);

        $user->refresh();
        $this->assertEquals(150000.0, (float) $user->so_du, 'Số dư không bị cộng đúp khi nhận webhook lần 2');
    }

    public function test_sepay_webhook_tu_dong_tim_ma_nap_tien_trong_content_khi_code_null(): void
    {
        Config::set('sepay.webhook_secret', '');
        Config::set('sepay.webhook_api_key', '');

        $user = User::factory()->create(['so_du' => 0]);
        $code = 'NAPABC123';

        $thanhToan = ThanhToan::create([
            'user_id' => $user->id,
            'ma_giao_dich' => $code,
            'ten_goi_dich_vu' => 'Nạp tiền vào ví',
            'so_tien' => 500000,
            'phuong_thuc' => 'vietqr',
            'trang_thai' => 'cho_thanh_toan',
        ]);

        $payload = [
            'id' => 99903,
            'gateway' => 'Techcombank',
            'transactionDate' => '2026-10-28 09:40:00',
            'accountNumber' => '1903333333',
            'code' => null,
            'content' => 'NGUYEN VAN A CHUYEN TIEN NAPABC123 QUA APP',
            'transferType' => 'in',
            'transferAmount' => 500000,
            'referenceCode' => 'REF99903',
        ];

        $res = $this->postJson(route('api.sepay.webhook'), $payload);
        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        $thanhToan->refresh();
        $this->assertEquals('thanh_cong', $thanhToan->trang_thai);

        $user->refresh();
        $this->assertEquals(500000.0, (float) $user->so_du);
    }

    public function test_ipn_tu_choi_khi_thieu_hoac_sai_x_secret_key(): void
    {
        $response = $this->postJson(route('api.sepay.ipn'), [
            'notification_type' => 'ORDER_PAID',
        ], [
            'X-Secret-Key' => 'sai_secret_key',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_ipn_order_paid_cap_nhat_thanh_toan_va_cong_so_du_user(): void
    {
        $user = User::factory()->create([
            'so_du' => 0,
        ]);

        $invoiceNumber = 'NAP_TEST_' . uniqid();

        $thanhToan = ThanhToan::create([
            'user_id' => $user->id,
            'ma_giao_dich' => $invoiceNumber,
            'ten_goi_dich_vu' => 'Nạp tiền vào tài khoản ví Quán Mới',
            'so_tien' => 150000,
            'phuong_thuc' => 'sepay',
            'trang_thai' => 'cho_thanh_toan',
        ]);

        $ipnPayload = [
            'timestamp' => time(),
            'notification_type' => 'ORDER_PAID',
            'order' => [
                'id' => 'sepay-order-uuid-1234',
                'order_id' => 'SP_ORD_001',
                'order_status' => 'CAPTURED',
                'order_currency' => 'VND',
                'order_amount' => '150000.00',
                'order_invoice_number' => $invoiceNumber,
            ],
            'transaction' => [
                'id' => 'sepay-trans-uuid-5678',
                'payment_method' => 'BANK_TRANSFER',
                'transaction_id' => 'TRANS_123456',
                'transaction_status' => 'APPROVED',
                'transaction_amount' => '150000',
            ],
        ];

        $response = $this->postJson(route('api.sepay.ipn'), $ipnPayload, [
            'X-Secret-Key' => 'spsk_test_secret_key_1234567890',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $thanhToan->refresh();
        $this->assertEquals('thanh_cong', $thanhToan->trang_thai);
        $this->assertEquals('sepay-order-uuid-1234', $thanhToan->sepay_order_id);
        $this->assertEquals('sepay-trans-uuid-5678', $thanhToan->sepay_transaction_id);
        $this->assertEquals('bank_transfer', $thanhToan->phuong_thuc);

        $user->refresh();
        $this->assertEquals(150000.00, (float) $user->so_du);

        // Idempotency: Gửi lại lần 2
        $responseSecond = $this->postJson(route('api.sepay.ipn'), $ipnPayload, [
            'X-Secret-Key' => 'spsk_test_secret_key_1234567890',
        ]);

        $responseSecond->assertStatus(200);

        $user->refresh();
        $this->assertEquals(150000.00, (float) $user->so_du, 'Số dư không được cộng đúp khi nhận IPN lần 2');
    }

    public function test_callbacks_redirect_dung_ve_trang_vi_tien(): void
    {
        $user = User::factory()->create();

        $resSuccess = $this->actingAs($user)->get(route('tai-khoan.nap-tien.thanh-cong', ['invoice' => 'NAP123']));
        $resSuccess->assertRedirect(route('tai-khoan.vi-tien'));
        $resSuccess->assertSessionHas('success');

        $resError = $this->actingAs($user)->get(route('tai-khoan.nap-tien.that-bai', ['invoice' => 'NAP123']));
        $resError->assertRedirect(route('tai-khoan.vi-tien'));
        $resError->assertSessionHas('error');

        $resCancel = $this->actingAs($user)->get(route('tai-khoan.nap-tien.huy', ['invoice' => 'NAP123']));
        $resCancel->assertRedirect(route('tai-khoan.vi-tien'));
        $resCancel->assertSessionHas('info');
    }

    public function test_nguoi_dung_co_the_truy_cap_trang_vi_tien_chuyen_biet(): void
    {
        // Khách chưa đăng nhập bị redirect về login
        $guestRes = $this->get(route('tai-khoan.vi-tien'));
        $guestRes->assertRedirect(route('login'));

        $user = User::factory()->create(['so_du' => 250000]);

        $res = $this->actingAs($user)->get(route('tai-khoan.vi-tien'));
        $res->assertStatus(200);
        $res->assertSee('Ví tiền &amp; Nạp tiền SePay', false);
        $res->assertSee('250.000');
        $res->assertSee('Lịch sử nạp tiền');

        // Test alias route
        $aliasRes = $this->actingAs($user)->get('/vi-tien');
        $aliasRes->assertRedirect(route('tai-khoan.vi-tien'));
    }
}
