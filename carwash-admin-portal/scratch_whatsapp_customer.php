<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
                    'customer_id' => null,
PHP;

$newCode = <<<PHP
                    'customer_id' => \App\Models\Customer::where('phone', \$freshCard->phone)->value('id'),
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Added customer_id lookup.\n";
