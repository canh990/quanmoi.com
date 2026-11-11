<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cấu hình Nhà cung cấp AI & Bể khóa đa tầng (Multi-Key & Multi-Provider Pool)
    |--------------------------------------------------------------------------
    | Đảm bảo Kira AI hoạt động 24/7 vô tận không bị mất kết nối:
    | - Hỗ trợ nhiều API Key cùng lúc (phân tách bởi dấu phẩy trong .env)
    | - Tự động xoay tua (Key Rotation) ngay lập tức khi 1 key bị 429 / Quota Exceeded
    | - Tự động chuyển tiếp (Failover) qua các nhà cung cấp khác: Gemini -> Groq -> OpenRouter -> OpenAI -> Kira
    | - Dự phòng thông minh nội bộ (Smart Fallback Engine) trả lời đời sống & ẩm thực mượt mà khi ngoại tuyến
    */
    'provider' => env('AI_PROVIDER', 'gemini'),

    // Danh sách thứ tự ưu tiên các nhà cung cấp
    'failover_chain' => ['gemini', 'groq', 'openrouter', 'openai', 'kira'],

    // 1. Google Gemini API Pool (Khuyên dùng, miễn phí & chất lượng cao)
    'gemini' => [
        'api_keys' => env('GEMINI_API_KEYS')
            ? array_filter(array_map('trim', explode(',', (string) env('GEMINI_API_KEYS'))))
            : (env('GEMINI_API_KEY') ? [trim((string) env('GEMINI_API_KEY'))] : []),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'fallback_models' => ['gemini-1.5-flash-8b', 'gemini-2.0-flash-exp', 'gemini-1.5-pro'],
        'timeout' => (int) env('GEMINI_TIMEOUT', 10),
    ],

    // 2. Groq Cloud Pool (Cực nhanh, miễn phí Llama 3.3 70B & Llama 3.1 8B)
    'groq' => [
        'api_keys' => env('GROQ_API_KEYS')
            ? array_filter(array_map('trim', explode(',', (string) env('GROQ_API_KEYS'))))
            : (env('GROQ_API_KEY') ? [trim((string) env('GROQ_API_KEY'))] : []),
        'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'timeout' => (int) env('GROQ_TIMEOUT', 10),
    ],

    // 3. OpenRouter Pool (Hàng chục mô hình miễn phí như Gemini 2.0, DeepSeek, Qwen)
    'openrouter' => [
        'api_keys' => env('OPENROUTER_API_KEYS')
            ? array_filter(array_map('trim', explode(',', (string) env('OPENROUTER_API_KEYS'))))
            : (env('OPENROUTER_API_KEY') ? [trim((string) env('OPENROUTER_API_KEY'))] : []),
        'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
        'model' => env('OPENROUTER_MODEL', 'google/gemini-2.0-flash-exp:free'),
        'timeout' => (int) env('OPENROUTER_TIMEOUT', 10),
    ],

    // 4. OpenAI / ChatGPT Compatible Pool
    'openai' => [
        'api_keys' => env('OPENAI_API_KEYS')
            ? array_filter(array_map('trim', explode(',', (string) env('OPENAI_API_KEYS'))))
            : (env('OPENAI_API_KEY') ? [trim((string) env('OPENAI_API_KEY'))] : []),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'timeout' => (int) env('OPENAI_TIMEOUT', 12),
    ],

    // 5. Kira AI API
    'kira' => [
        'api_keys' => env('KIRAAI_API_KEY') ? [trim((string) env('KIRAAI_API_KEY'))] : [],
        'base_url' => env('KIRAAI_BASE_URL', 'https://kiraai.vn/api/v1'),
        'model' => env('KIRAAI_MODEL', 'kira-3.5-flash'),
        'timeout' => (int) env('KIRAAI_TIMEOUT', 15),
    ],
];
