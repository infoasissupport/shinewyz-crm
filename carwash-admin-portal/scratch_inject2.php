<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

function injectAfter($content, $search, $inject) {
    if (strpos($content, $search) !== false) {
        return str_replace($search, $search . "\n        " . $inject, $content);
    }
    return $content;
}

$content = injectAfter($content, "\$customer = Customer::create(\$validated);", "\$customer->update(['branch_id' => \$this->defaultBranchId()]);");
$content = injectAfter($content, "\$vehicle = Vehicle::create(\$validated);", "\$vehicle->update(['branch_id' => \$this->defaultBranchId()]);");
$content = injectAfter($content, "\$service = Service::create(\$validated);", "\$service->update(['branch_id' => \$this->defaultBranchId()]);");
$content = injectAfter($content, "\$staff = Staff::create(\$staffData);", "\$staff->update(['branch_id' => \$this->defaultBranchId()]);");
$content = injectAfter($content, "\$expense = Expense::create(\$validated);", "\$expense->update(['branch_id' => \$this->defaultBranchId()]);");
$content = injectAfter($content, "\$item = InventoryItem::create(\$validated);", "\$item->update(['branch_id' => \$this->defaultBranchId()]);");

file_put_contents($file, $content);
echo "Injected missing branch_id logic.\n";
