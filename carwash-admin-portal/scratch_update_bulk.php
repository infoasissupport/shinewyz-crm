<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
    public function bulkUpdateAttendance(Request \$request)
    {
        \$validated = \$request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.id' => 'required|integer',
            'attendance.*.status' => 'required|string|in:present,absent',
        ]);

        \$userId = \$this->currentUserId();
        \$date = \$validated['date'];

        foreach (\$validated['attendance'] as \$item) {
            // Update staff_attendance log
            StaffAttendance::updateOrCreate(
                ['user_id' => \$userId, 'staff_id' => \$item['id'], 'date' => \$date],
                ['status' => \$item['status']]
            );

            // Also sync to staff fallback column
            Staff::where('user_id', \$userId)
                ->where('id', \$item['id'])
                ->update(['attendance' => \$item['status']]);
        }

        return response()->json(['message' => 'Attendance updated successfully']);
    }
PHP;

$newCode = <<<PHP
    public function bulkUpdateAttendance(Request \$request)
    {
        \$validated = \$request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.id' => 'required|integer',
            'attendance.*.status' => 'required|string|in:present,absent,half-day,late',
            'attendance.*.check_in_time' => 'nullable|string',
            'attendance.*.check_out_time' => 'nullable|string',
        ]);

        \$userId = \$this->currentUserId();
        \$date = \$validated['date'];

        foreach (\$validated['attendance'] as \$item) {
            // Update staff_attendance log
            StaffAttendance::updateOrCreate(
                ['user_id' => \$userId, 'staff_id' => \$item['id'], 'date' => \$date],
                [
                    'status' => \$item['status'],
                    'check_in_time' => \$item['check_in_time'] ?? null,
                    'check_out_time' => \$item['check_out_time'] ?? null,
                ]
            );

            // Also sync to staff fallback column
            Staff::where('user_id', \$userId)
                ->where('id', \$item['id'])
                ->update(['attendance' => \$item['status']]);
        }

        return response()->json(['message' => 'Attendance updated successfully']);
    }
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Updated bulkUpdateAttendance.\n";
