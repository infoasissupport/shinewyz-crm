<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
        // Order waiting washes by order time (First in, First out)
        \$bookings = \$query->orderBy('booking_date', 'asc')->orderBy('minutes', 'asc')->orderBy('created_at', 'asc')->get();
PHP;

$newCode = <<<PHP
        // Order waiting washes by newly added first
        \$bookings = \$query->orderByDesc('created_at')->get();
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched getBookings sort order to DESC.\n";
