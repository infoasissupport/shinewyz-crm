<?php
$file = 'shinewise_manager_app/pubspec.yaml';
$content = file_get_contents($file);

// Add video_player dependency
$content = str_replace(
    "url_launcher: ^6.3.0",
    "url_launcher: ^6.3.0\n  video_player: ^2.8.2",
    $content
);

// Add assets
$content = str_replace(
    "uses-material-design: true",
    "uses-material-design: true\n\n  assets:\n    - assets/videos/splash.mp4",
    $content
);

file_put_contents($file, $content);
echo "Patched pubspec.yaml.\n";
