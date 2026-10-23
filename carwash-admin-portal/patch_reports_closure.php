<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
        \$staffPerformance = \$this->applyBranchFilter(Staff::where('user_id', \$userId), \$request)->get()
            ->map(function (Staff \$staff) use (\$userId) {
                return [
                    'name' => \$staff->name,
                    'jobs' => \$this->applyBranchFilter(JobCard::where('user_id', \$userId), \$request)->where('staff', \$staff->name)->where('stage', 'completed')->count(),
                ];
            })
PHP;

$newCode = <<<PHP
        \$staffPerformance = \$this->applyBranchFilter(Staff::where('user_id', \$userId), \$request)->get()
            ->map(function (Staff \$staff) use (\$userId, \$request) {
                return [
                    'name' => \$staff->name,
                    'jobs' => \$this->applyBranchFilter(JobCard::where('user_id', \$userId), \$request)->where('staff', \$staff->name)->where('stage', 'completed')->count(),
                ];
            })
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched reports closure.\n";
