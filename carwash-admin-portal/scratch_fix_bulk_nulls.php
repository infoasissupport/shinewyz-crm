<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
            // Update staff_attendance log
            StaffAttendance::updateOrCreate(
                ['user_id' => \$userId, 'staff_id' => \$item['id'], 'date' => \$date],
                [
                    'status' => \$item['status'],
                    'check_in_time' => \$item['check_in_time'] ?? null,
                    'check_out_time' => \$item['check_out_time'] ?? null,
                ]
            );
PHP;

$newCode = <<<PHP
            // Update staff_attendance log
            \$inTime = (!empty(\$item['check_in_time'])) ? \$item['check_in_time'] : null;
            \$outTime = (!empty(\$item['check_out_time'])) ? \$item['check_out_time'] : null;

            StaffAttendance::updateOrCreate(
                ['user_id' => \$userId, 'staff_id' => \$item['id'], 'date' => \$date],
                [
                    'status' => \$item['status'],
                    'check_in_time' => \$inTime,
                    'check_out_time' => \$outTime,
                ]
            );
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Fixed nulls for times.\n";
