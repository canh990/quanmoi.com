<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use App\Models\ThanhToan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SePay\SePayClient;
use SePay\Builders\CheckoutBuilder;

class SePayService
{
    protected ?SePayClient $client = null;

    /**
     * Khởi tạo SePay SDK Client
     */
    public function getClient(): SePayClient
    {
        if ($this->client === null) {
            $merchantId = (string) config('sepay.merchant_id');
            $secretKey = (string) config('sepay.secret_key');
            $env = $this->getEnvironment();

            $this->client = new SePayClient($merchantId, $secretKey, $env);
        }

        return $this->client;
    }

    /**
     * Lấy môi trường SePay (sandbox hoặc production)
     */
    public function getEnvironment(): string
    {
        $merchantId = (string) config('sepay.merchant_id');
        if (str_starts_with($merchantId, 'SP-LIVE')) {
            return 'production';
        }

        return strtolower((string) config('sepay.env', 'sandbox')) === 'production'
            ? 'production'
            : 'sandbox';
    }

    /**
     * Sinh URL mã QR chuyển khoản VietQR chuẩn SePay
     * Tài liệu: https://developer.sepay.vn/vi/sepay-webhooks/tao-qr-va-form-thanh-toan
     */
    public function generateVietQrUrl(string $bank, string $acc, int $amount, string $memo): string
    {
        return 'https://vietqr.app/img?' . http_build_query([
            'acc' => $acc,
            'bank' => $bank,
            'amount' => $amount,
            'des' => $memo,
        ]);
    }

    /**
     * Tạo yêu cầu nạp tiền hiển thị VietQR on-site (Mô hình SePay Webhooks)
     */
    public function createVietQrDeposit(User $user, float $amount): array
    {
        $code = 'NAP' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $thanhToan = ThanhToan::create([
            'user_id' => $user->id,
            'ma_giao_dich' => $code,
            'ten_goi_dich_vu' => 'Nạp tiền vào tài khoản ví Quán Mới',
            'so_tien' => $amount,
            'phuong_thuc' => 'vietqr',
            'trang_thai' => 'cho_thanh_toan',
            'ghi_chu' => 'Chờ khách hàng quét VietQR chuyển khoản',
        ]);

        $bankName = (string) config('sepay.bank_name', 'MBBank');
        $accNumber = (string) config('sepay.acc_number', '0901234567');
        $accHolder = (string) config('sepay.acc_holder', 'QUAN MOI');

        $qrUrl = $this->generateVietQrUrl($bankName, $accNumber, (int) round($amount), $code);

        return [
            'success' => true,
            'code' => $code,
            'invoice_number' => $code,
            'thanh_toan_id' => $thanhToan->id,
            'amount' => $amount,
            'amount_formatted' => number_format($amount, 0, ',', '.') . ' đ',
            'bank_name' => $bankName,
            'acc_number' => $accNumber,
            'acc_holder' => $accHolder,
            'qr_url' => $qrUrl,
            'status_url' => route('api.sepay.status', ['code' => $code]),
        ];
    }

    /**
     * Khởi tạo giao dịch nạp tiền qua cổng thanh toán SePay PG
     */
    public function createDepositCheckout(User $user, float $amount, ?string $paymentMethod = null): array
    {
        $invoiceNumber = 'NAP' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $thanhToan = ThanhToan::create([
            'user_id' => $user->id,
            'ma_giao_dich' => $invoiceNumber,
            'ten_goi_dich_vu' => 'Nạp tiền vào tài khoản ví Quán Mới',
            'so_tien' => $amount,
            'phuong_thuc' => $paymentMethod ? strtolower($paymentMethod) : 'sepay',
            'trang_thai' => 'cho_thanh_toan',
            'ghi_chu' => 'Chờ khách hàng thanh toán qua SePay Gateway',
        ]);

        $bankName = (string) config('sepay.bank_name', 'MBBank');
        $accNumber = (string) config('sepay.acc_number', '0901234567');
        $accHolder = (string) config('sepay.acc_holder', 'QUAN MOI');
        $qrUrl = $this->generateVietQrUrl($bankName, $accNumber, (int) round($amount), $invoiceNumber);

        $client = $this->getClient();
        $env = $this->getEnvironment();

        $builder = CheckoutBuilder::make()
            ->currency('VND')
            ->orderAmount((int) round($amount))
            ->operation('PURCHASE')
            ->orderInvoiceNumber($invoiceNumber)
            ->orderDescription("Nap tien vi " . $invoiceNumber)
            ->customerId((string) $user->id)
            ->successUrl(route('tai-khoan.nap-tien.thanh-cong', ['invoice' => $invoiceNumber]))
            ->errorUrl(route('tai-khoan.nap-tien.that-bai', ['invoice' => $invoiceNumber]))
            ->cancelUrl(route('tai-khoan.nap-tien.huy', ['invoice' => $invoiceNumber]));

        if ($paymentMethod && in_array($paymentMethod, ['CARD', 'BANK_TRANSFER', 'NAPAS_BANK_TRANSFER'], true)) {
            $builder->paymentMethod($paymentMethod);
        }

        $checkoutData = $builder->build();
        $formFields = $client->checkout()->generateFormFields($checkoutData);
        $actionUrl = $client->checkout()->getCheckoutUrl($env);

        return [
            'success' => true,
            'code' => $invoiceNumber,
            'invoice_number' => $invoiceNumber,
            'thanh_toan_id' => $thanhToan->id,
            'action_url' => $actionUrl,
            'fields' => $formFields,
            'amount' => $amount,
            'amount_formatted' => number_format($amount, 0, ',', '.') . ' đ',
            'bank_name' => $bankName,
            'acc_number' => $accNumber,
            'acc_holder' => $accHolder,
            'qr_url' => $qrUrl,
            'status_url' => route('api.sepay.status', ['code' => $invoiceNumber]),
            'order_description' => $checkoutData['order_description'] ?? '',
        ];
    }

    /**
     * Xác thực header X-Secret-Key gửi từ SePay Gateway IPN
     */
    public function verifyIpnSecretKey(?string $headerSecretKey): bool
    {
        $configuredKey = (string) config('sepay.secret_key');
        if (empty($headerSecretKey) || empty($configuredKey)) {
            return false;
        }

        return hash_equals($configuredKey, $headerSecretKey);
    }

    /**
     * Xác thực Webhook SePay (HMAC-SHA256 hoặc API Key)
     * Tài liệu: https://developer.sepay.vn/vi/sepay-webhooks/xac-thuc
     */
    public function verifyWebhookAuth(Request $request): bool
    {
        $webhookSecret = (string) config('sepay.webhook_secret');
        $webhookApiKey = (string) config('sepay.webhook_api_key');

        // 1. Kiểm tra xác thực HMAC-SHA256 (khuyến nghị từ SePay)
        $signature = $request->header('X-SePay-Signature');
        $timestamp = (int) $request->header('X-SePay-Timestamp', 0);

        if (!empty($signature) && !empty($webhookSecret)) {
            if ($timestamp > 0 && abs(time() - $timestamp) > 300) {
                Log::warning('SePay Webhook: Request timestamp đã hết hạn (chênh lệch > 5 phút)', [
                    'timestamp' => $timestamp,
                    'current_time' => time(),
                ]);
                return false;
            }

            $rawBody = $request->getContent();
            $expected = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $rawBody, $webhookSecret);

            if (hash_equals($expected, $signature)) {
                return true;
            }

            Log::warning('SePay Webhook: Chữ ký HMAC-SHA256 không hợp lệ.');
            return false;
        }

        // 2. Kiểm tra xác thực API Key (Authorization: Apikey ...)
        $authHeader = $request->header('Authorization', '');
        if (!empty($webhookApiKey) && !empty($authHeader)) {
            if (str_starts_with($authHeader, 'Apikey ')) {
                $receivedKey = substr($authHeader, 7);
                if (hash_equals($webhookApiKey, $receivedKey)) {
                    return true;
                }
            }
            Log::warning('SePay Webhook: API Key không khớp.');
            return false;
        }

        // 3. Nếu hệ thống chưa cấu hình secret hay api key, cho phép (tiện cho test mode / sandbox local)
        if (empty($webhookSecret) && empty($webhookApiKey)) {
            return true;
        }

        return false;
    }

    /**
     * Xử lý webhook SePay Webhooks (nhận thông báo biến động số dư VietQR)
     * Tài liệu: https://developer.sepay.vn/vi/sepay-webhooks/tich-hop-webhook
     */
    public function processWebhook(array $payload): array
    {
        $transferType = $payload['transferType'] ?? 'in';
        if ($transferType !== 'in') {
            Log::info('SePay Webhook: Bỏ qua giao dịch tiền ra (transferType = ' . $transferType . ')');
            return [
                'success' => true,
                'message' => 'Bỏ qua giao dịch tiền ra',
            ];
        }

        $transactionId = $payload['id'] ?? null;
        $content = $payload['content'] ?? '';
        $code = $payload['code'] ?? null;

        if (empty($code) && !empty($content)) {
            if (preg_match('/(NAP[0-9A-Z]+)/i', $content, $matches)) {
                $code = strtoupper($matches[1]);
            }
        }

        if (empty($code)) {
            Log::warning('SePay Webhook: Không tìm thấy mã nạp tiền NAP... trong payload', $payload);
            return [
                'success' => true,
                'message' => 'Không tìm thấy mã nạp tiền trong nội dung chuyển khoản',
            ];
        }

        $thanhToan = ThanhToan::where('ma_giao_dich', $code)->first();

        if (! $thanhToan) {
            Log::warning("SePay Webhook: Không tìm thấy giao dịch thanh toán mã {$code}", $payload);
            return [
                'success' => true,
                'message' => "Không tìm thấy giao dịch thanh toán {$code}",
            ];
        }

        if ($thanhToan->trang_thai === 'thanh_cong' || ($transactionId && $thanhToan->sepay_transaction_id == $transactionId)) {
            Log::info("SePay Webhook: Giao dịch {$code} đã được xử lý trước đó (Idempotent).");
            return [
                'success' => true,
                'message' => 'Giao dịch đã được xử lý trước đó',
                'idempotent' => true,
            ];
        }

        return DB::transaction(function () use ($thanhToan, $payload, $code, $transactionId) {
            $thanhToan = ThanhToan::lockForUpdate()->find($thanhToan->id);

            if ($thanhToan->trang_thai === 'thanh_cong') {
                return [
                    'success' => true,
                    'message' => 'Giao dịch đã được xử lý trước đó',
                    'idempotent' => true,
                ];
            }

            $transferAmount = (float) ($payload['transferAmount'] ?? $thanhToan->so_tien);
            $gateway = $payload['gateway'] ?? 'VietQR';

            $thanhToan->trang_thai = 'thanh_cong';
            $thanhToan->sepay_transaction_id = $transactionId ? (string) $transactionId : null;
            $thanhToan->du_lieu_ipn = $payload;
            $thanhToan->phuong_thuc = 'vietqr';
            $thanhToan->ghi_chu = "Nạp tiền thành công qua SePay Webhook ({$gateway})";
            $thanhToan->save();

            if ($thanhToan->user_id) {
                $user = User::lockForUpdate()->find($thanhToan->user_id);
                if ($user) {
                    $oldBalance = (float) $user->so_du;
                    $newBalance = $oldBalance + $transferAmount;
                    $user->so_du = $newBalance;
                    $user->save();

                    try {
                        AdminAuditLog::create([
                            'actor_id' => $user->id,
                            'action' => 'user.deposit_success',
                            'target_type' => User::class,
                            'target_id' => (string) $user->id,
                            'metadata' => [
                                'ma_giao_dich' => $code,
                                'so_tien' => $transferAmount,
                                'so_du_truoc' => $oldBalance,
                                'so_du_sau' => $newBalance,
                                'phuong_thuc' => 'vietqr',
                                'sepay_transaction_id' => $transactionId,
                                'gateway' => $gateway,
                            ],
                            'ip_address' => request()->ip(),
                        ]);
                    } catch (\Throwable $e) {
                        Log::warning('Không thể tạo AdminAuditLog cho webhook: ' . $e->getMessage());
                    }

                    Log::info("SePay Webhook: Đã cộng {$transferAmount} VNĐ vào ví user {$user->id} ({$user->email}). Số dư mới: {$newBalance} VNĐ.");
                }
            }

            return [
                'success' => true,
                'message' => 'Nạp tiền thành công và số dư đã được cộng vào tài khoản.',
            ];
        });
    }

    /**
     * Xử lý webhook IPN nhận từ SePay Gateway
     */
    public function processIpn(array $payload): array
    {
        $notificationType = $payload['notification_type'] ?? null;
        $orderData = $payload['order'] ?? [];
        $transactionData = $payload['transaction'] ?? [];

        $invoiceNumber = $orderData['order_invoice_number'] ?? null;

        if (empty($invoiceNumber)) {
            Log::warning('SePay IPN: Thiếu order_invoice_number trong payload', $payload);
            return [
                'success' => false,
                'message' => 'Thiếu thông tin order_invoice_number',
            ];
        }

        $thanhToan = ThanhToan::where('ma_giao_dich', $invoiceNumber)->first();

        if (! $thanhToan) {
            Log::warning("SePay IPN: Không tìm thấy giao dịch với mã {$invoiceNumber}", $payload);
            return [
                'success' => false,
                'message' => "Không tìm thấy giao dịch với mã hóa đơn {$invoiceNumber}",
            ];
        }

        if ($thanhToan->trang_thai === 'thanh_cong') {
            Log::info("SePay IPN: Giao dịch {$invoiceNumber} đã hoàn tất thành công trước đó (Idempotent).");
            return [
                'success' => true,
                'message' => 'Giao dịch đã được xử lý trước đó',
                'idempotent' => true,
            ];
        }

        if ($notificationType === 'ORDER_PAID') {
            return DB::transaction(function () use ($thanhToan, $payload, $orderData, $transactionData, $invoiceNumber) {
                $thanhToan = ThanhToan::lockForUpdate()->find($thanhToan->id);

                if ($thanhToan->trang_thai === 'thanh_cong') {
                    return [
                        'success' => true,
                        'message' => 'Giao dịch đã được xử lý trước đó',
                        'idempotent' => true,
                    ];
                }

                $paidAmount = isset($orderData['order_amount']) ? (float) $orderData['order_amount'] : (float) $thanhToan->so_tien;

                $thanhToan->trang_thai = 'thanh_cong';
                $thanhToan->sepay_order_id = $orderData['id'] ?? $orderData['order_id'] ?? null;
                $thanhToan->sepay_transaction_id = $transactionData['id'] ?? $transactionData['transaction_id'] ?? null;
                $thanhToan->du_lieu_ipn = $payload;
                $thanhToan->ghi_chu = 'Nạp tiền thành công qua Cổng SePay (IPN xác nhận)';
                if (!empty($transactionData['payment_method'])) {
                    $thanhToan->phuong_thuc = strtolower($transactionData['payment_method']);
                }
                $thanhToan->save();

                if ($thanhToan->user_id) {
                    $user = User::lockForUpdate()->find($thanhToan->user_id);
                    if ($user) {
                        $oldBalance = (float) $user->so_du;
                        $newBalance = $oldBalance + $paidAmount;
                        $user->so_du = $newBalance;
                        $user->save();

                        try {
                            AdminAuditLog::create([
                                'actor_id' => $user->id,
                                'action' => 'user.deposit_success',
                                'target_type' => User::class,
                                'target_id' => (string) $user->id,
                                'metadata' => [
                                    'ma_giao_dich' => $invoiceNumber,
                                    'so_tien' => $paidAmount,
                                    'so_du_truoc' => $oldBalance,
                                    'so_du_sau' => $newBalance,
                                    'phuong_thuc' => $thanhToan->phuong_thuc,
                                    'sepay_transaction_id' => $thanhToan->sepay_transaction_id,
                                ],
                                'ip_address' => request()->ip(),
                            ]);
                        } catch (\Throwable $e) {
                            Log::warning('Không thể tạo AdminAuditLog cho nạp tiền: ' . $e->getMessage());
                        }

                        Log::info("SePay IPN: Đã cộng {$paidAmount} VNĐ vào tài khoản user {$user->id} ({$user->email}). Số dư mới: {$newBalance} VNĐ.");
                    }
                }

                return [
                    'success' => true,
                    'message' => 'Nạp tiền thành công và số dư tài khoản đã được cộng.',
                ];
            });
        }

        if ($notificationType === 'TRANSACTION_VOID') {
            $thanhToan->trang_thai = 'da_huy';
            $thanhToan->du_lieu_ipn = $payload;
            $thanhToan->ghi_chu = 'Giao dịch bị hủy trên cổng thanh toán SePay';
            $thanhToan->save();

            return [
                'success' => true,
                'message' => 'Giao dịch đã được cập nhật trạng thái hủy.',
            ];
        }

        return [
            'success' => true,
            'message' => 'IPN nhận thành công nhưng loại thông báo chưa được xử lý cụ thể.',
        ];
    }

    /**
     * Chủ động kiểm tra và đồng bộ trạng thái đơn hàng từ SePay API
     * Giải quyết trường hợp IPN chưa đến hoặc chưa cấu hình webhook ngrok trên dashboard
     */
    public function syncOrderStatusWithSePay(string $invoiceNumber): array
    {
        $thanhToan = ThanhToan::where('ma_giao_dich', $invoiceNumber)->first();
        if (! $thanhToan) {
            return ['success' => false, 'message' => "Không tìm thấy giao dịch {$invoiceNumber}"];
        }

        if ($thanhToan->trang_thai === 'thanh_cong') {
            return ['success' => true, 'message' => 'Giao dịch đã hoàn tất trước đó', 'status' => 'thanh_cong'];
        }

        try {
            $client = $this->getClient();
            $ordersResponse = $client->orders()->list(['per_page' => 20]);
            $orders = $ordersResponse['data'] ?? [];

            $matchedOrder = null;
            foreach ($orders as $o) {
                if (($o['order_invoice_number'] ?? '') === $invoiceNumber) {
                    $matchedOrder = $o;
                    break;
                }
            }

            if (! $matchedOrder) {
                return ['success' => false, 'message' => 'Chưa tìm thấy đơn hàng trên SePay', 'status' => $thanhToan->trang_thai];
            }

            $orderStatus = strtoupper((string) ($matchedOrder['order_status'] ?? ''));

            if ($orderStatus === 'CAPTURED' || $orderStatus === 'PAID') {
                return DB::transaction(function () use ($thanhToan, $matchedOrder, $invoiceNumber) {
                    $thanhToan = ThanhToan::lockForUpdate()->find($thanhToan->id);
                    if ($thanhToan->trang_thai === 'thanh_cong') {
                        return ['success' => true, 'message' => 'Giao dịch đã xử lý trước đó', 'status' => 'thanh_cong'];
                    }

                    $amount = isset($matchedOrder['order_amount']) ? (float) $matchedOrder['order_amount'] : (float) $thanhToan->so_tien;

                    $thanhToan->trang_thai = 'thanh_cong';
                    $thanhToan->sepay_order_id = $matchedOrder['id'] ?? $matchedOrder['order_id'] ?? null;
                    $thanhToan->ghi_chu = 'Nạp tiền thành công (Tự động đồng bộ từ SePay Gateway)';
                    $thanhToan->du_lieu_ipn = $matchedOrder;
                    $thanhToan->save();

                    if ($thanhToan->user_id) {
                        $user = User::lockForUpdate()->find($thanhToan->user_id);
                        if ($user) {
                            $oldBalance = (float) $user->so_du;
                            $newBalance = $oldBalance + $amount;
                            $user->so_du = $newBalance;
                            $user->save();

                            try {
                                AdminAuditLog::create([
                                    'actor_id' => $user->id,
                                    'action' => 'user.deposit_success',
                                    'target_type' => User::class,
                                    'target_id' => (string) $user->id,
                                    'metadata' => [
                                        'ma_giao_dich' => $invoiceNumber,
                                        'so_tien' => $amount,
                                        'so_du_truoc' => $oldBalance,
                                        'so_du_sau' => $newBalance,
                                        'sepay_order_id' => $thanhToan->sepay_order_id,
                                        'sync_source' => 'api_direct_sync',
                                    ],
                                    'ip_address' => request()->ip(),
                                ]);
                            } catch (\Throwable $e) {
                                Log::warning('Audit log error: ' . $e->getMessage());
                            }

                            Log::info("SePay Sync: Đã cộng {$amount} VNĐ vào ví user {$user->id} ({$user->email}). Số dư mới: {$newBalance} VNĐ.");
                        }
                    }

                    return [
                        'success' => true,
                        'message' => 'Nạp tiền thành công và số dư tài khoản đã được cộng.',
                        'status' => 'thanh_cong',
                        'amount' => $amount,
                    ];
                });
            }

            return [
                'success' => true,
                'message' => 'Đơn hàng đang chờ xử lý trên SePay',
                'status' => $thanhToan->trang_thai,
            ];
        } catch (\Throwable $e) {
            Log::warning("Lỗi đồng bộ SePay cho đơn {$invoiceNumber}: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'status' => $thanhToan->trang_thai];
        }
    }

}
