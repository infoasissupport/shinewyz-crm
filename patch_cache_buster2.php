<?php
$queue = 'resources/views/queue.blade.php';
$content = file_get_contents($queue);
$content = preg_replace('/js\/queue\.js\?v=[0-9.]+/', 'js/queue.js?v=' . time(), $content);
file_put_contents($queue, $content);
echo "Bumped cache buster.\n";
