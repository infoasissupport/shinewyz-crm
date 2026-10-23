<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
        \$userId = \$this->currentUserId();
        \$query = Booking::where('user_id', \$userId)->with('branch');
        \$query = \$this->applyBranchFilter(\$query, \$request);
        \$bookings = \$query->orderBy('minutes', 'asc')->orderByDesc('created_at')->get();
PHP;

$newCode = <<<PHP
        \$userId = \$this->currentUserId();
        \$query = Booking::where('user_id', \$userId)->with('branch');
        \$query = \$this->applyBranchFilter(\$query, \$request);
        // Order waiting washes by order time (First in, First out)
        \$bookings = \$query->orderBy('booking_date', 'asc')->orderBy('minutes', 'asc')->orderBy('created_at', 'asc')->get();
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched getBookings sort order.\n";
