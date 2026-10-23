<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$content = str_replace(
    "\App\Models\$this->applyBranchFilter(Branch::",
    "\$this->applyBranchFilter(\App\Models\Branch::",
    $content
);

file_put_contents($file, $content);
echo "Fixed syntax.\n";
