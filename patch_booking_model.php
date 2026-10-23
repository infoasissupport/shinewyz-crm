<?php
$file = 'shinewise_manager_app/lib/models/models.dart';
$content = file_get_contents($file);

// Add the field
$content = preg_replace(
    '/(final int\? branchId;)/',
    "$1\n  final String? createdAt;",
    $content
);

// Add it to the constructor
$content = preg_replace(
    '/(this\.branchId,)/',
    "$1\n    this.createdAt,",
    $content
);

// Add it to fromJson
$content = preg_replace(
    "/(branchId: json\['branch_id'\],)/",
    "$1\n      createdAt: json['created_at'],",
    $content
);

file_put_contents($file, $content);
echo "Patched Booking model correctly.\n";
