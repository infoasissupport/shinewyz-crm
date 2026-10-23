<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
        foreach (\$bookings as \$b) {
            \$jobCard = JobCard::where('user_id', \$userId)
                ->where(function (\$q) use (\$b) {
                    \$q->where('booking_id', \$b->id);
                    if (\$b->phone) {
                        \$q->orWhere('phone', \$b->phone);
                    }
                })
                ->orderByDesc('id')
                ->first();
PHP;

$newCode = <<<PHP
        foreach (\$bookings as \$b) {
            \$jobCard = JobCard::where('user_id', \$userId)
                ->where(function (\$q) use (\$b) {
                    \$q->where('booking_id', \$b->id);
                    if (\$b->phone && \$b->vehicle_plate) {
                        // Fallback only if no booking_id, but MUST match both phone and vehicle plate!
                        \$q->orWhere(function(\$sub) use (\$b) {
                            \$sub->whereNull('booking_id')
                                ->where('phone', \$b->phone)
                                ->where('vehicle', 'LIKE', \$b->vehicle_plate.'%');
                        });
                    }
                })
                ->orderByDesc('id')
                ->first();
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched getBookings mapping logic.\n";
