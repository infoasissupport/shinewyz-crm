<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$codeToAdd = <<<PHP

    public function getBranches()
    {
        \$userId = \$this->currentUserId();
        return response()->json(Branch::where('user_id', \$userId)->orderBy('name')->get());
    }

    public function updateBranch(Request \$request, \$id)
    {
        if (Auth::user()?->role === 'manager') {
            return response()->json(['message' => 'Managers cannot edit branches.'], 403);
        }

        \$validated = \$request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'active' => 'nullable|boolean',
        ]);

        \$userId = \$this->currentUserId();
        \$branch = Branch::where('user_id', \$userId)->findOrFail(\$id);
        \$branch->update(\$validated);
        return response()->json(\$branch);
    }

    public function deleteBranch(\$id)
    {
        if (Auth::user()?->role === 'manager') {
            return response()->json(['message' => 'Managers cannot delete branches.'], 403);
        }

        \$userId = \$this->currentUserId();
        \$branch = Branch::where('user_id', \$userId)->findOrFail(\$id);
        \$branch->delete();
        return response()->json(['message' => 'Branch deleted']);
    }

PHP;

// Find the last closing brace of the class
$pos = strrpos($content, '}');
if ($pos !== false) {
    $content = substr_replace($content, $codeToAdd . "\n}", $pos, 1);
    file_put_contents($file, $content);
    echo "Added successfully at the end of class.\n";
} else {
    echo "Failed to find end of class.\n";
}
