<?php
$path = __DIR__ . '/resources/views/welcome.blade.php';
$content = file_get_contents($path);

// Replace HTML comments with Blade comments
$content = preg_replace('/<!--(.*?)-->/s', '{{--$1--}}', $content);

file_put_contents($path, $content);
echo "Replaced HTML comments with Blade comments in welcome.blade.php\n";
