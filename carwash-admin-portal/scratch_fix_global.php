<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$content = str_replace("public function getGlobalData()", "public function getGlobalData(\Illuminate\Http\Request \$request)", $content);

$oldCode = <<<PHP
        return response()->json([
            'branches' => Branch::where('user_id', \$userId)->where('active', true)->orderBy('name')->get(),
            'services' => Service::where('user_id', \$userId)->where('active', true)->orderBy('price', 'asc')->get(),
            'staff' => Staff::where('user_id', \$userId)->orderBy('name')->get(),
            'notifications' => Notification::where('user_id', \$userId)->orderBy('id', 'desc')->get(),
            'whatsapp' => WhatsappChat::where('user_id', \$userId)->get(),
        ]);
PHP;

$newCode = <<<PHP
        return response()->json([
            'branches' => Branch::where('user_id', \$userId)->where('active', true)->orderBy('name')->get(),
            'services' => \$this->applyBranchFilter(Service::where('user_id', \$userId)->where('active', true), \$request)->orderBy('price', 'asc')->get(),
            'staff' => \$this->applyBranchFilter(Staff::where('user_id', \$userId), \$request)->orderBy('name')->get(),
            'notifications' => \$this->applyBranchFilter(Notification::where('user_id', \$userId), \$request)->orderBy('id', 'desc')->get(),
            'whatsapp' => \$this->applyBranchFilter(WhatsappChat::where('user_id', \$userId), \$request)->get(),
        ]);
PHP;

$content = str_replace($oldCode, $newCode, $content);

file_put_contents($file, $content);
echo "Fixed getGlobalData branch filters.\n";
