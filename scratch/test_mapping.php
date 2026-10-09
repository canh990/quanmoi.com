<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$client = \Elastic\Elasticsearch\ClientBuilder::create()
    ->setHosts([config('elastic.client.hosts.0', 'http://elasticsearch:9200')])
    ->build();

try {
    $res = $client->indices()->putMapping([
        'index' => 'quan',
        'body' => [
            'properties' => [
                'co_shopeefood' => ['type' => 'boolean'],
                'duoi_50k' => ['type' => 'boolean'],
                'dang_mo_cua' => ['type' => 'boolean'],
            ]
        ]
    ]);
    echo "Put mapping success: " . json_encode($res->asArray()) . PHP_EOL;
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
