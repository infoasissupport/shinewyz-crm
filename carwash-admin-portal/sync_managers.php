<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Staff;
use App\Models\Branch;

$managers = User::where('role', 'manager')->get();
foreach($managers as $m) {
    $existingStaff = Staff::where('user_id', $m->owner_id)->where('name', $m->name)->first();
    if (!$existingStaff) {
        $branch = Branch::find($m->assigned_branch_id);
        $branchName = $branch ? $branch->name : 'Main Branch';
        
        Staff::create([
            'name' => $m->name,
            'phone' => '0000000000',
            'branch' => $branchName,
            'shift' => '9 AM - 6 PM',
            'attendance' => 'present',
            'base_salary' => 0,
            'role' => 'manager',
            'user_id' => $m->owner_id,
            'is_active' => true,
            'joined_at' => now()
        ]);
        echo "Synced {$m->name} into staff table.\n";
    } else {
        echo "{$m->name} already in staff table.\n";
    }
}
