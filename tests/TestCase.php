<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        putenv('APP_ENV=testing');
        $_ENV['APP_ENV'] = 'testing';
        $_SERVER['APP_ENV'] = 'testing';

        putenv('DB_CONNECTION=sqlite');
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_SERVER['DB_CONNECTION'] = 'sqlite';

        putenv('DB_DATABASE=:memory:');
        $_ENV['DB_DATABASE'] = ':memory:';
        $_SERVER['DB_DATABASE'] = ':memory:';

        // Tránh test ghi vào cache thật (rate limit còn lưu giữa các lần chạy) và upload ảnh lên R2 thật.
        putenv('CACHE_STORE=array');
        $_ENV['CACHE_STORE'] = 'array';
        $_SERVER['CACHE_STORE'] = 'array';

        putenv('UPLOAD_DISK=uploads');
        $_ENV['UPLOAD_DISK'] = 'uploads';
        $_SERVER['UPLOAD_DISK'] = 'uploads';

        parent::setUp();
        // Giả lập đĩa uploads để test không ghi file thật vào public/uploads.
        \Illuminate\Support\Facades\Storage::fake('uploads');
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
    }
}
