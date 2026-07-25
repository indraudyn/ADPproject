<?php
$files = [
    'resources/views/video/create.blade.php',
    'resources/views/audio/create.blade.php',
    'resources/views/admin/video/create.blade.php',
    'resources/views/admin/audio/create.blade.php',
    'resources/views/narasumber/video/create.blade.php',
    'resources/views/narasumber/audio/create.blade.php',
    'resources/views/cerita/create.blade.php',
    // also edits if they use it, let's just grep all blade files
];

$allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
foreach ($allFiles as $file) {
    if ($file->isFile() && strpos($file->getFilename(), '.blade.php') !== false) {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, '/api/parwa/sections-by-book') !== false) {
            $content = str_replace('/api/parwa/sections-by-book', '/ajax/parwa/sections-by-book', $content);
            file_put_contents($file->getPathname(), $content);
            echo "Updated " . $file->getPathname() . "\n";
        }
    }
}
