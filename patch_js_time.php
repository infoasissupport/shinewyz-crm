<?php
$files = ['public/js/dashboard.js', 'public/js/queue.js'];

foreach ($files as $file) {
    $content = file_get_contents($file);
    $oldCode = "toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })";
    $newCode = "toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true })";
    $content = str_replace($oldCode, $newCode, $content);
    file_put_contents($file, $content);
    echo "Patched $file\n";
}
