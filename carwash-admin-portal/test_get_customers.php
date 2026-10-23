<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/api/customers', 'GET');
$user = \App\Models\User::where('role', 'manager')->first() ?? \App\Models\User::first();
$request->setUserResolver(function () use ($user) {
    return $user;
});

$controller = new \App\Http\Controllers\CarwashApiController();
$response = $controller->getCustomers($request);
echo "Count: " . count(json_decode($response->getContent(), true)) . "\n";
