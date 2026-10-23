<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Staff;

echo "Staff:\n";
foreach(Staff::all() as $s) {
    echo "ID: {$s->id}, Name: {$s->name}, UserID: {$s->user_id}, Role: {$s->role}, Branch: {$s->branch}\n";
}
