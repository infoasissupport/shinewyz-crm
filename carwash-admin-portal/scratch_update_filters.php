<?php

$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

// getVehicles
$content = preg_replace(
    "/public function getVehicles\(\)\s*\{\s*\\\$userId = \\\$this->currentUserId\(\);\s*return response\(\)->json\(Vehicle::where\('user_id', \\\$userId\)->with\('customer'\)->orderBy\('updated_at', 'desc'\)->get\(\)\);\s*\}/",
    "public function getVehicles(Request \$request)\n    {\n        \$userId = \$this->currentUserId();\n        \$query = Vehicle::where('user_id', \$userId)->with('customer');\n        \$query = \$this->applyBranchFilter(\$query, \$request);\n        return response()->json(\$query->orderBy('updated_at', 'desc')->get());\n    }",
    $content
);

// getServices
$content = preg_replace(
    "/public function getServices\(\)\s*\{\s*\\\$userId = \\\$this->currentUserId\(\);\s*\\\$query = Service::where\('user_id', \\\$userId\);\s*if \(Schema::hasColumn\('services', 'active'\)\) \{\s*\\\$query->where\('active', true\);\s*\}\s*return response\(\)->json\(\\\$query->orderBy\('name', 'asc'\)->get\(\)\);\s*\}/",
    "public function getServices(Request \$request)\n    {\n        \$userId = \$this->currentUserId();\n        \$query = Service::where('user_id', \$userId);\n        if (Schema::hasColumn('services', 'active')) {\n            \$query->where('active', true);\n        }\n        \$query = \$this->applyBranchFilter(\$query, \$request);\n        return response()->json(\$query->orderBy('name', 'asc')->get());\n    }",
    $content
);

// getStaff
$content = preg_replace(
    "/\\\$staffList = Staff::where\('user_id', \\\$userId\)\s*->orderBy\('name', 'asc'\)\s*->get\(\);/",
    "\$query = Staff::where('user_id', \$userId);\n        \$query = \$this->applyBranchFilter(\$query, \$request);\n        \$staffList = \$query->orderBy('name', 'asc')->get();",
    $content
);

// getExpenses
$content = preg_replace(
    "/public function getExpenses\(\)\s*\{\s*\\\$userId = \\\$this->currentUserId\(\);\s*return response\(\)->json\(Expense::where\('user_id', \\\$userId\)->orderBy\('expense_date', 'desc'\)->get\(\)\);\s*\}/",
    "public function getExpenses(Request \$request)\n    {\n        \$userId = \$this->currentUserId();\n        \$query = Expense::where('user_id', \$userId);\n        \$query = \$this->applyBranchFilter(\$query, \$request);\n        return response()->json(\$query->orderBy('expense_date', 'desc')->get());\n    }",
    $content
);

// getInventory
$content = preg_replace(
    "/public function getInventory\(\)\s*\{\s*\\\$userId = \\\$this->currentUserId\(\);\s*return response\(\)->json\(InventoryItem::where\('user_id', \\\$userId\)->orderBy\('name', 'asc'\)->get\(\)\);\s*\}/",
    "public function getInventory(Request \$request)\n    {\n        \$userId = \$this->currentUserId();\n        \$query = InventoryItem::where('user_id', \$userId);\n        \$query = \$this->applyBranchFilter(\$query, \$request);\n        return response()->json(\$query->orderBy('name', 'asc')->get());\n    }",
    $content
);

// getInvoices
$content = preg_replace(
    "/public function getInvoices\(\)\s*\{\s*\\\$userId = \\\$this->currentUserId\(\);\s*return response\(\)->json\(Invoice::where\('user_id', \\\$userId\)->orderBy\('id', 'desc'\)->get\(\)\);\s*\}/",
    "public function getInvoices(Request \$request)\n    {\n        \$userId = \$this->currentUserId();\n        \$query = Invoice::where('user_id', \$userId);\n        \$query = \$this->applyBranchFilter(\$query, \$request);\n        return response()->json(\$query->orderBy('id', 'desc')->get());\n    }",
    $content
);

// getBays
$content = preg_replace(
    "/public function getBays\(Request \\\$request\) \{\s*\\\$userId = \\\$this->currentUserId\(\);\s*return response\(\)->json\(\\\\App\\\\Models\\\\Bay::where\('user_id', \\\$userId\)->orderBy\('created_at', 'asc'\)->get\(\)\);\s*\}/",
    "public function getBays(Request \$request) {\n        \$userId = \$this->currentUserId();\n        \$query = \\App\\Models\\Bay::where('user_id', \$userId);\n        \$query = \$this->applyBranchFilter(\$query, \$request);\n        return response()->json(\$query->orderBy('created_at', 'asc')->get());\n    }",
    $content
);

file_put_contents($file, $content);
echo "Replaced missing applyBranchFilter logic.\n";
