<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
        \$query = JobCard::where('user_id', \$userId)
            ->where(function (\$query) {
                \$query->whereNull('booking_id')
                    ->orWhereIn('booking_id', function (\$q) {
                        \$q->select('id')->from('bookings');
                    });
            });
PHP;

$newCode = <<<PHP
        \$query = JobCard::with('invoice')
            ->where('user_id', \$userId)
            ->where(function (\$query) {
                \$query->whereNull('booking_id')
                    ->orWhereIn('booking_id', function (\$q) {
                        \$q->select('id')->from('bookings');
                    });
            });
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched getJobCards to include invoice.\n";
