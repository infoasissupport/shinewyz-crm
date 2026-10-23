<?php
$file = 'shinewise_manager_app/lib/screens/jobcard_detail_screen.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
      if (nextStage != null) {
        payload['stage'] = nextStage;
      }
DART;

$newCode = <<<DART
      if (nextStage != null) {
        payload['stage'] = nextStage;
        if (nextStage == 'completed') {
          payload['signed_off_by'] = 'Customer';
          payload['signed_off_time'] = TimeOfDay.now().format(context);
        }
      }
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Added signoff logic.\n";
