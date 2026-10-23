<?php
$dashboard = 'resources/views/dashboard.blade.php';
$content = file_get_contents($dashboard);
$content = str_replace(
    "<script src=\"{{ asset('js/dashboard.js?v=1788172692') }}?v={{ filemtime(public_path('js/dashboard.js?v=1788172692')) }}\"></script>", 
    "<script src=\"{{ asset('js/dashboard.js') }}?v={{ filemtime(public_path('js/dashboard.js')) }}\"></script>", 
    $content
);
file_put_contents($dashboard, $content);
echo "Fixed dashboard.blade.php\n";
