<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$replacements = [
    "Customer::where('user_id', \$userId)->count()" => "\$this->applyBranchFilter(Customer::where('user_id', \$userId), \$request)->count()",
    
    "Customer::where('user_id', \$userId)->pluck('phone')" => "\$this->applyBranchFilter(Customer::where('user_id', \$userId), \$request)->pluck('phone')",
    
    "Invoice::where('user_id', \$userId)->selectRaw('phone')" => "\$this->applyBranchFilter(Invoice::where('user_id', \$userId), \$request)->selectRaw('phone')",
    
    "JobCard::where('user_id', \$userId)->where('stage', 'completed')\n            ->whereBetween('updated_at', [\$startOfMonth, \$now])" => "\$this->applyBranchFilter(JobCard::where('user_id', \$userId)->where('stage', 'completed'), \$request)\n            ->whereBetween('updated_at', [\$startOfMonth, \$now])",
    
    "\$staffPerformance = Staff::where('user_id', \$userId)->get()" => "\$staffPerformance = \$this->applyBranchFilter(Staff::where('user_id', \$userId), \$request)->get()",
    
    "'jobs' => JobCard::where('user_id', \$userId)->where('staff', \$staff->name)->where('stage', 'completed')->count()" => "'jobs' => \$this->applyBranchFilter(JobCard::where('user_id', \$userId), \$request)->where('staff', \$staff->name)->where('stage', 'completed')->count()",
    
    "JobCard::where('user_id', \$userId)->selectRaw('service, COUNT(*) as count')\n            ->whereBetween('updated_at', [\$startOfMonth, \$now])" => "\$this->applyBranchFilter(JobCard::where('user_id', \$userId), \$request)->selectRaw('service, COUNT(*) as count')\n            ->whereBetween('updated_at', [\$startOfMonth, \$now])",
    
    "'amount' => (float) Invoice::where('user_id', \$userId)->where('status', 'paid')" => "'amount' => (float) \$this->applyBranchFilter(Invoice::where('user_id', \$userId)->where('status', 'paid'), \$request)",
    
    "\$lowStockItemsQuery = InventoryItem::where('user_id', \$userId);" => "\$lowStockItemsQuery = \$this->applyBranchFilter(InventoryItem::where('user_id', \$userId), \$request);"
];

foreach ($replacements as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

file_put_contents($file, $content);
echo "Fully fixed getReportsData!\n";
