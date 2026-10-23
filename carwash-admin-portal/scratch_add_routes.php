<?php
$file = 'routes/web.php';
$content = file_get_contents($file);

$codeToAdd = <<<PHP
    Route::get('/branches', [CarwashApiController::class, 'getBranches']);
    Route::put('/branches/{id}', [CarwashApiController::class, 'updateBranch']);
    Route::delete('/branches/{id}', [CarwashApiController::class, 'deleteBranch']);
PHP;

$content = str_replace(
    "Route::post('/branches', [CarwashApiController::class, 'createBranch']);",
    "Route::post('/branches', [CarwashApiController::class, 'createBranch']);\n" . $codeToAdd,
    $content
);

file_put_contents($file, $content);
echo "Added routes to web.php.\n";
