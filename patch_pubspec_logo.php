<?php
$file = 'shinewise_manager_app/pubspec.yaml';
$content = file_get_contents($file);

$content = str_replace(
    "- assets/videos/splash.mp4",
    "- assets/videos/splash.mp4\n    - assets/images/logo1.png",
    $content
);

file_put_contents($file, $content);
echo "Patched pubspec logo.\n";
