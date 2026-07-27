<?php

$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
foreach ($dir as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        $original = $content;
        
        // 1. asset('storage/' . $quan->anh_bia) => $quan->anh_bia
        $content = preg_replace("/asset\('storage\/' \. \\\$([a-zA-Z0-9_]+)->anh_bia\)/", "\\$\\1->anh_bia", $content);
        
        // 2. Storage::url($quan->anh_bia) => $quan->anh_bia
        $content = preg_replace("/Storage::url\(\\\$([a-zA-Z0-9_]+)->anh_bia\)/", "\\$\\1->anh_bia", $content);
        
        // 3. Storage::url($hinhAnhs[0]->duong_dan) => $hinhAnhs[0]->duong_dan
        // (For HinhAnhQuan duong_dan)
        $content = preg_replace("/Storage::url\(\\\$([a-zA-Z0-9_]+)\[(\d+)\]->duong_dan\)/", "\\$\\1[\\2]->duong_dan", $content);
        $content = preg_replace("/Storage::url\(\\\$([a-zA-Z0-9_]+)->duong_dan\)/", "\\$\\1->duong_dan", $content);
        
        if ($content !== $original) {
            file_put_contents($path, $content);
            echo "Updated: $path\n";
        }
    }
}
