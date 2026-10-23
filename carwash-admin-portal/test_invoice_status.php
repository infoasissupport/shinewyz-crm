<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('role', 'manager')->first() ?? \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

$request = Illuminate\Http\Request::create('/api/jobcards', 'GET', ['branch_code' => 'BRCH-01']);
$controller = new \App\Http\Controllers\CarwashApiController();
$response = $controller->getJobCards($request);
$data = json_decode($response->getContent(), true);

foreach($data as $jc) {
    if ($jc['stage'] === 'completed' && isset($jc['invoice'])) {
        echo "JobCard {$jc['id']} invoice status: {$jc['invoice']['status']}\n";
    }
}
