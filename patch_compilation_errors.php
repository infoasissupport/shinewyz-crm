<?php
$file = 'shinewise_manager_app/lib/screens/operations_tab.dart';
$content = file_get_contents($file);

// 1. Fix _updateJobCardStage signature
$content = str_replace(
    "Future<void> _updateJobCardStage(int id, String stage) async {",
    "Future<void> _updateJobCardStage(String id, String stage) async {",
    $content
);

// 2. Inject _picker correctly
// Find 'bool _isLoading = true;' and inject below it
$content = preg_replace(
    '/(bool _isLoading = true;)/',
    "$1\n  final ImagePicker _picker = ImagePicker();",
    $content
);

file_put_contents($file, $content);
echo "Patched compilation errors.\n";
