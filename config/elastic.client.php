<?php declare(strict_types=1);

$defaultElasticHost = (gethostbyname('elasticsearch') !== 'elasticsearch')
    ? 'http://elasticsearch:9200'
    : 'http://localhost:9200';

return [
    'default' => env('ELASTIC_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'hosts' => [
                env('ELASTIC_HOST', $defaultElasticHost),
            ],
        ],
    ],
];
