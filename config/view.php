<?php

$compiledPath = env('VIEW_COMPILED_PATH', '/tmp/views');
if (!is_dir($compiledPath)) {
    @mkdir($compiledPath, 0777, true);
}

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    */

    'compiled' => $compiledPath,

];

