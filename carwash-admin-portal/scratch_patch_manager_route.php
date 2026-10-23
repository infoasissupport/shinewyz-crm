<?php
$file = 'routes/web.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
    Route::get('/expenses', function () {
        return view('expenses');
    });
});
PHP;

$newCode = <<<PHP
    Route::get('/expenses', function () {
        return view('expenses');
    });
    
    // Manager Portal
    Route::get('/manager', function () {
        return view('manager');
    });
});
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Added /manager route.\n";
