<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['bays', 'customers', 'vehicles', 'services', 'inventory_items', 'staff', 'bookings', 'job_cards', 'invoices'];
foreach ($tables as $t) {
    if (\Illuminate\Support\Facades\Schema::hasColumn($t, 'branch_id')) {
        \Illuminate\Support\Facades\DB::table($t)->whereNull('branch_id')->update(['branch_id' => 1]);
    }
}
echo "Migrated NULLs to branch 1.\n";
