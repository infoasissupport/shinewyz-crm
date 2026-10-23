<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('role', 'manager')->first() ?? \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

$request = Illuminate\Http\Request::create('/api/bookings', 'GET');
$controller = new \App\Http\Controllers\CarwashApiController();
$response = $controller->getBookings($request);
$data = json_decode($response->getContent(), true);

if (count($data) > 0) {
    echo json_encode($data[0], JSON_PRETTY_PRINT);
} else {
    echo "No bookings found.";
}
