<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$content = str_replace("public function getReportsData()", "public function getReportsData(\Illuminate\Http\Request \$request)", $content);

$content = str_replace(
    "Invoice::where('user_id', \$userId)->where('status', 'paid')\n            ->where('created_at', '>=', \$startOfMonth)",
    "\$this->applyBranchFilter(Invoice::where('user_id', \$userId)->where('status', 'paid'), \$request)\n            ->where('created_at', '>=', \$startOfMonth)",
    $content
);

$content = str_replace(
    "Invoice::where('user_id', \$userId)->where('status', 'paid')\n            ->whereBetween('created_at', [\$startOfLastMonth, \$endOfLastMonth])",
    "\$this->applyBranchFilter(Invoice::where('user_id', \$userId)->where('status', 'paid'), \$request)\n            ->whereBetween('created_at', [\$startOfLastMonth, \$endOfLastMonth])",
    $content
);

$content = str_replace(
    "Invoice::where('user_id', \$userId)->where('status', 'paid')\n                ->where('created_at', '>=', \$startOfMonth)",
    "\$this->applyBranchFilter(Invoice::where('user_id', \$userId)->where('status', 'paid'), \$request)\n                ->where('created_at', '>=', \$startOfMonth)",
    $content
);

$content = str_replace(
    "\$staffQuery = Staff::where('user_id', \$userId);",
    "\$staffQuery = \$this->applyBranchFilter(Staff::where('user_id', \$userId), \$request);",
    $content
);

$content = str_replace(
    "\$expenses = Expense::where('user_id', \$userId)\n            ->where('expense_date', '>=', \$startOfMonth->toDateString())",
    "\$expenses = \$this->applyBranchFilter(Expense::where('user_id', \$userId), \$request)\n            ->where('expense_date', '>=', \$startOfMonth->toDateString())",
    $content
);

$content = str_replace(
    "\$expensesLastMonth = Expense::where('user_id', \$userId)\n            ->whereBetween('expense_date', [\$startOfLastMonth->toDateString(), \$endOfLastMonth->toDateString()])",
    "\$expensesLastMonth = \$this->applyBranchFilter(Expense::where('user_id', \$userId), \$request)\n            ->whereBetween('expense_date', [\$startOfLastMonth->toDateString(), \$endOfLastMonth->toDateString()])",
    $content
);

$content = str_replace(
    "\$invoices = Invoice::where('user_id', \$userId)\n            ->whereBetween('created_at', [\$startOfMonth, \$now])",
    "\$invoices = \$this->applyBranchFilter(Invoice::where('user_id', \$userId), \$request)\n            ->whereBetween('created_at', [\$startOfMonth, \$now])",
    $content
);

file_put_contents($file, $content);
echo "Fixed getReportsData branch filters.\n";
