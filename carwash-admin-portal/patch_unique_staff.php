<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

// Patch createStaff
$oldCreate = <<<PHP
        \$validated = \$request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'employee_id' => 'nullable|string|max:255',
            'native_place' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
PHP;

$newCreate = <<<PHP
        \$validated = \$request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'employee_id' => 'nullable|string|max:255',
            'native_place' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
PHP;

$oldCreateBottom = <<<PHP
        \$userId = \$this->currentUserId();
        \$validated['user_id'] = \$userId;
        \$validated['branch_id'] = \$validated['branch_id'] ?? \$this->defaultBranchId();

        // Handle uploaded file if present
PHP;

$newCreateBottom = <<<PHP
        \$userId = \$this->currentUserId();
        \$validated['user_id'] = \$userId;
        \$validated['branch_id'] = \$validated['branch_id'] ?? \$this->defaultBranchId();

        if (!empty(\$validated['employee_id'])) {
            \$exists = \App\Models\Staff::where('user_id', \$userId)
                ->where('employee_id', \$validated['employee_id'])
                ->exists();
            if (\$exists) {
                return response()->json(['message' => 'A staff member with this Employee ID already exists.'], 422);
            }
        }

        // Handle uploaded file if present
PHP;

$content = str_replace($oldCreateBottom, $newCreateBottom, $content);


// Patch updateStaff
$oldUpdateBottom = <<<PHP
        \$userId = \$this->currentUserId();
        \$staff = \$this->applyBranchFilter(Staff::where('user_id', \$userId), request())->findOrFail(\$id);

        if (isset(\$validated['services'])) {
PHP;

$newUpdateBottom = <<<PHP
        \$userId = \$this->currentUserId();
        \$staff = \$this->applyBranchFilter(Staff::where('user_id', \$userId), request())->findOrFail(\$id);

        if (!empty(\$validated['employee_id'])) {
            \$exists = \App\Models\Staff::where('user_id', \$userId)
                ->where('employee_id', \$validated['employee_id'])
                ->where('id', '!=', \$id)
                ->exists();
            if (\$exists) {
                return response()->json(['message' => 'Another staff member with this Employee ID already exists.'], 422);
            }
        }

        if (isset(\$validated['services'])) {
PHP;

$content = str_replace($oldUpdateBottom, $newUpdateBottom, $content);

file_put_contents($file, $content);
echo "Patched unique staff employee id.\n";
