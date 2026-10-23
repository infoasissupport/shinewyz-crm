<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

$request = Illuminate\Http\Request::create('/api/customers', 'GET', ['branch_code' => 'BRCH-01']);
$controller = new \App\Http\Controllers\CarwashApiController();
$response = $controller->getCustomers($request);
echo "Count: " . count(json_decode($response->getContent(), true)) . "\n";
