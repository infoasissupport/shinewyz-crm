<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
        // Sync linked booking status
        \$linkedBooking = null;
        if (! empty(\$freshCard->booking_id)) {
            \$linkedBooking = Booking::where('user_id', \$userId)->find(\$freshCard->booking_id);
        }
        if (! \$linkedBooking && ! empty(\$freshCard->phone)) {
            \$linkedBooking = \$this->applyBranchFilter(Booking::where('user_id', \$userId), request())->where('phone', \$freshCard->phone)->orderByDesc('id')->first();
        }
PHP;

$newCode = <<<PHP
        // Sync linked booking status
        \$linkedBooking = null;
        if (! empty(\$freshCard->booking_id)) {
            \$linkedBooking = Booking::where('user_id', \$userId)->find(\$freshCard->booking_id);
        }
        if (! \$linkedBooking && ! empty(\$freshCard->phone) && ! empty(\$freshCard->vehicle)) {
            // Extract the plate from the vehicle string (e.g. "KL90445 (Sedan)")
            \$plate = explode(' ', \$freshCard->vehicle)[0];
            \$linkedBooking = \$this->applyBranchFilter(Booking::where('user_id', \$userId), request())
                ->where('phone', \$freshCard->phone)
                ->where('vehicle_plate', \$plate)
                ->orderByDesc('id')
                ->first();
        }
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched updateJobCard linking logic.\n";
