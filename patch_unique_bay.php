<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
    public function createBay(Request \$request) {
        \$validated = \$request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        \$userId = \$this->currentUserId();
        \$validated['user_id'] = \$userId;
        \$validated['branch_id'] = \$validated['branch_id'] ?? \$this->defaultBranchId();
        \$bay = \App\Models\Bay::create(\$validated);
PHP;

$newCode = <<<PHP
    public function createBay(Request \$request) {
        \$validated = \$request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        \$userId = \$this->currentUserId();
        \$validated['user_id'] = \$userId;
        \$validated['branch_id'] = \$validated['branch_id'] ?? \$this->defaultBranchId();

        \$exists = \App\Models\Bay::where('user_id', \$userId)
            ->where('branch_id', \$validated['branch_id'])
            ->where('name', \$validated['name'])
            ->exists();
            
        if (\$exists) {
            return response()->json(['message' => 'A bay with this name already exists.'], 422);
        }

        \$bay = \App\Models\Bay::create(\$validated);
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched unique bay.\n";
