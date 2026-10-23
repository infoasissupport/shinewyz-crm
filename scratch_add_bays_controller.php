<?php
$content = file_get_contents('app/Http/Controllers/CarwashApiController.php');

$methods = <<<EOT

    public function getBays(Request \$request) {
        \$userId = \$this->getOwnerId(\$request->user());
        return response()->json(\App\Models\Bay::where('user_id', \$userId)->orderBy('created_at', 'asc')->get());
    }

    public function createBay(Request \$request) {
        \$validated = \$request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        \$userId = \$this->getOwnerId(\$request->user());
        \$validated['user_id'] = \$userId;
        
        \$bay = \App\Models\Bay::create(\$validated);
        return response()->json(\$bay, 201);
    }

    public function deleteBay(\$id) {
        \$bay = \App\Models\Bay::findOrFail(\$id);
        \$bay->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

EOT;

// Insert before the last closing brace
$content = preg_replace('/}([^}]*)$/', $methods . "}\n$1", $content);
file_put_contents('app/Http/Controllers/CarwashApiController.php', $content);
echo "Added methods.";
