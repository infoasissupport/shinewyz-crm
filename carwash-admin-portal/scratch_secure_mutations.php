<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$patterns = [
    // findOrFail
    "/(Booking|Customer|Vehicle|Service|Staff|Expense|InventoryItem|JobCard|Invoice|\\\\App\\\\Models\\\\Bay|Branch)::where\('user_id', \\\$userId\)->findOrFail\((\\\$[a-zA-Z0-9_]+)\)/" => "\$this->applyBranchFilter($1::where('user_id', \$userId), request())->findOrFail($2)",
    
    // find
    "/(Booking|Customer|Vehicle|Service|Staff|Expense|InventoryItem|JobCard|Invoice|\\\\App\\\\Models\\\\Bay|Branch)::where\('user_id', \\\$userId\)->find\((\\\$[a-zA-Z0-9_]+)\)/" => "\$this->applyBranchFilter($1::where('user_id', \$userId), request())->find($2)",
    
    // first
    "/(Booking|Customer|Vehicle|Service|Staff|Expense|InventoryItem|JobCard|Invoice|\\\\App\\\\Models\\\\Bay|Branch)::where\('user_id', \\\$userId\)->where\((.*?)\)->first\(\)/" => "\$this->applyBranchFilter($1::where('user_id', \$userId), request())->where($2)->first()",
];

foreach ($patterns as $pattern => $replacement) {
    $content = preg_replace($pattern, $replacement, $content);
}

// But wait, applyBranchFilter expects a Request object as second parameter. request() helper provides it.
// Let's make sure it doesn't break.
file_put_contents($file, $content);
echo "Secured mutations!\n";
