<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$card = \App\Models\JobCard::first();
if ($card) {
    echo "Found card: " . $card->id . "\n";
    $card->stage = 'inprogress';
    $card->phone = '9999999999';
    $card->save();

    // Call update API simulation
    $request = Illuminate\Http\Request::create('/api/jobcards/' . $card->id, 'PUT', ['stage' => 'completed']);
    
    // Simulate user
    $user = \App\Models\User::first();
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    $controller = new \App\Http\Controllers\CarwashApiController();
    $controller->updateJobCard($request, $card->id);
    
    echo "WA count after: " . \App\Models\WhatsappChat::count() . "\n";
    
    $wa = \App\Models\WhatsappChat::orderBy('id', 'desc')->first();
    if ($wa) {
        echo "Last WA: " . $wa->msg . " to " . $wa->phone . "\n";
    }
} else {
    echo "No card.\n";
}
