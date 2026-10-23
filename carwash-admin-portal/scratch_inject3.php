<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

function injectAfter($content, $search, $inject) {
    if (strpos($content, $search) !== false) {
        return str_replace($search, $search . "\n        " . $inject, $content);
    }
    return $content;
}

$content = injectAfter($content, "\$customer = Customer::create([", "'branch_id' => \$this->defaultBranchId(),");
$content = injectAfter($content, "return Customer::create([", "'branch_id' => \$this->defaultBranchId(),");
$content = injectAfter($content, "Vehicle::create([", "'branch_id' => \$this->defaultBranchId(),");
$content = injectAfter($content, "\$service = Service::create(array_merge(\$validated, [", "'branch_id' => \$this->defaultBranchId(),");
$content = injectAfter($content, "\$item = InventoryItem::create(\$itemData);", "\$item->update(['branch_id' => \$this->defaultBranchId()]);");


file_put_contents($file, $content);
echo "Injected remaining missing branch_id logic.\n";
