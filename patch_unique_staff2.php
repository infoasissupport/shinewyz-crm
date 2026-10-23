<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCreate = <<<PHP
        \$userId = \$this->currentUserId();
        \$staffData = \$validated;
PHP;

$newCreate = <<<PHP
        \$userId = \$this->currentUserId();
        
        if (!empty(\$validated['employee_id'])) {
            \$exists = \App\Models\Staff::where('user_id', \$userId)
                ->where('employee_id', \$validated['employee_id'])
                ->exists();
            if (\$exists) {
                return response()->json(['message' => 'A staff member with this Employee ID already exists.'], 422);
            }
        }

        \$staffData = \$validated;
PHP;

$content = str_replace($oldCreate, $newCreate, $content);


$oldUpdate = <<<PHP
        \$userId = \$this->currentUserId();
        \$staff = \$this->applyBranchFilter(Staff::where('user_id', \$userId), request())->findOrFail(\$id);
PHP;

$newUpdate = <<<PHP
        \$userId = \$this->currentUserId();
        
        if (!empty(\$validated['employee_id'])) {
            \$exists = \App\Models\Staff::where('user_id', \$userId)
                ->where('employee_id', \$validated['employee_id'])
                ->where('id', '!=', \$id)
                ->exists();
            if (\$exists) {
                return response()->json(['message' => 'Another staff member with this Employee ID already exists.'], 422);
            }
        }

        \$staff = \$this->applyBranchFilter(Staff::where('user_id', \$userId), request())->findOrFail(\$id);
PHP;

$content = str_replace($oldUpdate, $newUpdate, $content);
file_put_contents($file, $content);
echo "Patched createStaff and updateStaff.\n";
