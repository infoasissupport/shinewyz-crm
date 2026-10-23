<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('role', 'manager')->first() ?? \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

$card = \App\Models\JobCard::where('stage', 'completed')->first();
echo "JobCard ID: {$card->id}\n";
echo "Card's invoice_id: {$card->invoice_id}\n";
echo "Relation invoice: " . json_encode($card->invoice) . "\n";
