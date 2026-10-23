<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

// Patch createBooking
$oldBooking = <<<PHP
        \$userId = \$this->currentUserId();

        return DB::transaction(function () use (\$validated, \$userId) {
PHP;

$newBooking = <<<PHP
        \$userId = \$this->currentUserId();
        
        if (!empty(\$validated['vehicle_plate'])) {
            \$existing = \App\Models\Booking::where('user_id', \$userId)
                ->whereIn('status', ['waiting', 'confirmed', 'inprogress'])
                ->where('vehicle_plate', \$validated['vehicle_plate'])
                ->exists();
            if (\$existing) {
                return response()->json(['message' => 'Vehicle already in queue'], 422);
            }
            
            \$existingJc = \App\Models\JobCard::where('user_id', \$userId)
                ->whereIn('stage', ['assigned', 'inprogress'])
                ->where('vehicle', 'LIKE', \$validated['vehicle_plate'].'%')
                ->exists();
            if (\$existingJc) {
                return response()->json(['message' => 'Vehicle already in queue'], 422);
            }
        }

        return DB::transaction(function () use (\$validated, \$userId) {
PHP;
$content = str_replace($oldBooking, $newBooking, $content);

// Patch createJobCard
$oldJobCard = <<<PHP
        \$userId = \$this->currentUserId();
        \$booking = null;
PHP;

$newJobCard = <<<PHP
        \$userId = \$this->currentUserId();
        
        if (!empty(\$validated['vehicle'])) {
            \$existingJc = \App\Models\JobCard::where('user_id', \$userId)
                ->whereIn('stage', ['assigned', 'inprogress'])
                ->where('vehicle', \$validated['vehicle'])
                ->exists();
            if (\$existingJc) {
                return response()->json(['message' => 'Vehicle already in queue'], 422);
            }
        }
        
        \$booking = null;
PHP;
$content = str_replace($oldJobCard, $newJobCard, $content);

file_put_contents($file, $content);
echo "Patched duplicate queue validation.\n";
