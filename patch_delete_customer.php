<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
        \$customer = \$this->applyBranchFilter(Customer::where('user_id', \$userId), request())->findOrFail(\$id);
        \$customer->delete();
PHP;

$newCode = <<<PHP
        \$customer = \$this->applyBranchFilter(Customer::where('user_id', \$userId), request())->findOrFail(\$id);
        
        // Delete all associated vehicles before deleting the customer
        \\App\Models\Vehicle::where('customer_id', \$customer->id)->delete();
        
        \$customer->delete();
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched deleteCustomer to cascade delete vehicles.\n";
