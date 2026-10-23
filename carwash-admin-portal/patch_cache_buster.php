<?php
$dashboard = 'resources/views/dashboard.blade.php';
$content = file_get_contents($dashboard);
$content = str_replace('js/dashboard.js', 'js/dashboard.js?v=' . time(), $content);
file_put_contents($dashboard, $content);

$queue = 'resources/views/queue.blade.php';
$content = file_get_contents($queue);
$content = preg_replace('/js\/queue\.js\?v=[0-9.]+/', 'js/queue.js?v=' . time(), $content);
file_put_contents($queue, $content);
echo "Added cache busters.\n";
