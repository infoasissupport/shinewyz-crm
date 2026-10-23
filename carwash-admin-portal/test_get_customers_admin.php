<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/api/customers', 'GET', ['branch_code' => 'BRCH-01']);
$user = \App\Models\User::where('role', 'admin')->first() ?? \App\Models\User::first();
$request->setUserResolver(function () use ($user) {
    return $user;
});

$controller = new \App\Http\Controllers\CarwashApiController();
$response = $controller->getCustomers($request);
echo "Count for Admin on BRCH-01: " . count(json_decode($response->getContent(), true)) . "\n";
