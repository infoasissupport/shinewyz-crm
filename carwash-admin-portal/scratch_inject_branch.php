<?php

$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$replacements = [
    // createVehicle
    "/public function createVehicle\(Request \\\$request\)\s*\{\s*\\\$validated = \\\$request->validate\(\[/" => "public function createVehicle(Request \$request)\n    {\n        \$validated = \$request->validate([\n",
    "/\\\$validated\['user_id'\] = \\\$userId;\s*\\\$vehicle = Vehicle::create\(\\\$validated\);/" => "\$validated['user_id'] = \$userId;\n        \$validated['branch_id'] = \$validated['branch_id'] ?? \$this->defaultBranchId();\n        \$vehicle = Vehicle::create(\$validated);",
    
    // createService
    "/\\\$validated\['user_id'\] = \\\$this->currentUserId\(\);\s*\\\$service = Service::create\(\\\$validated\);/" => "\$validated['user_id'] = \$this->currentUserId();\n        \$validated['branch_id'] = \$validated['branch_id'] ?? \$this->defaultBranchId();\n        \$service = Service::create(\$validated);",
    
    // createStaff
    "/\\\$validated\['user_id'\] = \\\$userId;\s*\\\$staff = Staff::create\(\\\$validated\);/" => "\$validated['user_id'] = \$userId;\n        \$validated['branch_id'] = \$validated['branch_id'] ?? \$this->defaultBranchId();\n        \$staff = Staff::create(\$validated);",
    
    // createExpense
    "/\\\$validated\['user_id'\] = \\\$this->currentUserId\(\);\s*\\\$expense = Expense::create\(\\\$validated\);/" => "\$validated['user_id'] = \$this->currentUserId();\n        \$validated['branch_id'] = \$validated['branch_id'] ?? \$this->defaultBranchId();\n        \$expense = Expense::create(\$validated);",
    
    // createInventory
    "/\\\$validated\['user_id'\] = \\\$this->currentUserId\(\);\s*\\\$item = InventoryItem::create\(\\\$validated\);/" => "\$validated['user_id'] = \$this->currentUserId();\n        \$validated['branch_id'] = \$validated['branch_id'] ?? \$this->defaultBranchId();\n        \$item = InventoryItem::create(\$validated);",
    
    // createBay
    "/\\\$validated\['user_id'\] = \\\$userId;\s*\\\$bay = \\\\App\\\\Models\\\\Bay::create\(\\\$validated\);/" => "\$validated['user_id'] = \$userId;\n        \$validated['branch_id'] = \$validated['branch_id'] ?? \$this->defaultBranchId();\n        \$bay = \\App\\Models\\Bay::create(\$validated);",

    // createCustomer
    "/\\\$validated\['user_id'\] = \\\$this->currentUserId\(\);\s*\\\$customer = Customer::create\(\\\$validated\);/" => "\$validated['user_id'] = \$this->currentUserId();\n        \$validated['branch_id'] = \$validated['branch_id'] ?? \$this->defaultBranchId();\n        \$customer = Customer::create(\$validated);",
];

foreach ($replacements as $pattern => $replacement) {
    $content = preg_replace($pattern, $replacement, $content);
}

// And update defaultBranchId itself!
$content = preg_replace(
    "/private function defaultBranchId\(\): \?int\s*\{\s*\\\$user = Auth::user\(\);\s*if \(\\\$user && \\\$user->assigned_branch_id\) \{\s*return \\\$user->assigned_branch_id;\s*\}\s*return Branch::where\('active', true\)->orderBy\('id'\)->value\('id'\);\s*\}/",
    "private function defaultBranchId(): ?int\n    {\n        \$user = Auth::user();\n        if (\$user && \$user->role === 'manager' && \$user->assigned_branch_id) {\n            return \$user->assigned_branch_id;\n        }\n        if (request()->filled('branch_code')) {\n            \$branch = Branch::where('user_id', \$this->currentUserId())->where('code', request()->input('branch_code'))->first();\n            if (\$branch) return \$branch->id;\n        }\n        return Branch::where('active', true)->orderBy('id')->value('id');\n    }",
    $content
);


file_put_contents($file, $content);
echo "Injected branch_id logic.\n";
