<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
        // Fetch attendance records for this date
        \$attendance = StaffAttendance::where('user_id', \$userId)
            ->whereDate('date', \$date)
            ->pluck('status', 'staff_id')
            ->toArray();

        // Attach attendance status to each staff member
        foreach (\$staffList as \$staff) {
            \$staff->attendance = \$attendance[\$staff->id] ?? 'absent';
        }
PHP;

$newCode = <<<PHP
        // Fetch attendance records for this date
        \$attendance = StaffAttendance::where('user_id', \$userId)
            ->whereDate('date', \$date)
            ->get()
            ->keyBy('staff_id');

        // Attach attendance status to each staff member
        foreach (\$staffList as \$staff) {
            if (isset(\$attendance[\$staff->id])) {
                \$staff->attendance = \$attendance[\$staff->id]->status;
                \$staff->check_in_time = \$attendance[\$staff->id]->check_in_time;
                \$staff->check_out_time = \$attendance[\$staff->id]->check_out_time;
            } else {
                \$staff->attendance = 'absent';
                \$staff->check_in_time = null;
                \$staff->check_out_time = null;
            }
        }
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Updated getStaff.\n";
