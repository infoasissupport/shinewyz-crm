<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Staff;

$managers = User::where('role', 'manager')->get();
$deletedCount = 0;

foreach($managers as $m) {
    // Check if a staff record exists for this manager
    $staffExists = Staff::where('user_id', $m->owner_id)
        ->where(function($q) use ($m) {
            $q->where('email', $m->email)->orWhere('name', $m->name);
        })
        ->where('role', 'manager')
        ->exists();
        
    if (!$staffExists) {
        echo "Deleting orphaned manager user: {$m->name} ({$m->email})\n";
        $m->delete();
        $deletedCount++;
    }
}

echo "Deleted {$deletedCount} orphaned managers.\n";
