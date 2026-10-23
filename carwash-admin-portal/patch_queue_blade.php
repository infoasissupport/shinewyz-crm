<?php
$file = 'resources/views/queue.blade.php';
$content = file_get_contents($file);
$content = str_replace(
    "<script src=\"{{ asset('js/queue.js') }}?v=1.1\"></script>",
    "<script src=\"{{ asset('js/queue.js') }}?v=".time()."\"></script>",
    $content
);
file_put_contents($file, $content);
echo "Patched queue.blade.php cache bust.\n";
