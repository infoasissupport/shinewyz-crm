<?php
$file = 'public/js/queue.js';
$content = file_get_contents($file);

$oldCode = <<<JS
                  var bg = isActive ? 'var(--accent)' : 'var(--bg-3)';
                  var fg = isActive ? '#fff' : 'var(--text-2)';
JS;

$newCode = <<<JS
                  var bg = isActive ? '#f97316' : '#f1f5f9';
                  var fg = isActive ? '#fff' : '#64748b';
JS;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched tab colors.\n";
