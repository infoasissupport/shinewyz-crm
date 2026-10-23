<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JobCard;

foreach(JobCard::where('user_id', 2)->get() as $j) {
    echo "ID: {$j->id}, Stage: {$j->stage}, Bay: '{$j->bay}'\n";
}
