<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$content = str_replace(
    "public function deleteBay(\$id) {\n        \$bay = \App\Models\Bay::findOrFail(\$id);",
    "public function deleteBay(\$id) {\n        \$bay = \$this->applyBranchFilter(\App\Models\Bay::where('user_id', \$this->currentUserId()), request())->findOrFail(\$id);",
    $content
);

file_put_contents($file, $content);
echo "Fixed deleteBay.\n";
