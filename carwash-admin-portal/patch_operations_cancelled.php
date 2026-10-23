<?php
$file = 'shinewise_manager_app/lib/screens/operations_tab.dart';
$content = file_get_contents($file);

$content = str_replace(
    "if (jc.stage == 'inprogress') stageColor = const Color(0xFFFF6B00);",
    "if (jc.stage == 'inprogress') stageColor = const Color(0xFFFF6B00);\n        if (jc.stage == 'cancelled') stageColor = Colors.red;",
    $content
);

file_put_contents($file, $content);
echo "Added cancelled color.\n";
