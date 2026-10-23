<?php
$file = 'shinewise_manager_app/lib/models/models.dart';
$content = file_get_contents($file);

$content = preg_replace('/(final String\? updatedAt;)/', "$1\n  final String? createdAt;", $content);
$content = preg_replace('/(this\.updatedAt,)/', "$1\n    this.createdAt,", $content);
$content = preg_replace("/(updatedAt: json\['updated_at'\],)/", "$1\n      createdAt: json['created_at'],", $content);

file_put_contents($file, $content);
echo "Patched JobCard model.\n";
