<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SePay Configuration (Gateway & Webhooks)
    |--------------------------------------------------------------------------
    |
    | Hỗ trợ cả 2 mô hình tích hợp SePay:
    | 1. SePay Webhooks (VietQR on-site + Webhook tự động cộng tiền)
    |    Tài liệu: https://developer.sepay.vn/vi/sepay-webhooks
    | 2. SePay Payment Gateway (Redirect tới pay.sepay.vn)
    |    Tài liệu: https://developer.sepay.vn/vi/cong-thanh-toan/gioi-thieu
    |
    */

    // Thông tin tài khoản ngân hàng nhận tiền hiển thị mã VietQR
    'bank_name' => env('SEPAY_BANK_NAME', 'MBBank'),
    'acc_number' => env('SEPAY_ACC_NUMBER', '0901234567'),
    'acc_holder' => env('SEPAY_ACC_HOLDER', 'QUAN MOI'),

    // Khóa xác thực Webhook SePay (Hỗ trợ xác thực HMAC-SHA256 hoặc API Key)
    // https://developer.sepay.vn/vi/sepay-webhooks/xac-thuc
    'webhook_secret' => env('SEPAY_WEBHOOK_SECRET', env('SEPAY_SECRET_KEY', '')),
    'webhook_api_key' => env('SEPAY_WEBHOOK_API_KEY', ''),

    // Cấu hình Cổng thanh toán SePay PG
    'merchant_id' => env('SEPAY_MERCHANT_ID', 'SP-TEST-DEMO'),
    'secret_key' => env('SEPAY_SECRET_KEY', 'spsk_live_testdemo1234567890'),
    'env' => env('SEPAY_ENV', 'sandbox'),
    'currency' => 'VND',
];
