<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cấu hình Nhà cung cấp AI
    |--------------------------------------------------------------------------
    | Hỗ trợ Kira AI (OpenAI API compatible), Google Gemini hoặc Chế độ Dự phòng
    */
    'provider' => env('AI_PROVIDER', 'kira'),

    'kira' => [
        'api_key' => env('KIRAAI_API_KEY', ''),
        'base_url' => env('KIRAAI_BASE_URL', 'https://kiraai.vn/api/v1'),
        'model' => env('KIRAAI_MODEL', 'kira-3.5-flash'),
        'timeout' => env('KIRAAI_TIMEOUT', 30),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    ],
];
