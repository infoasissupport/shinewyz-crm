<?php
$file = 'shinewise_manager_app/lib/services/api_service.dart';
$content = file_get_contents($file);

$oldCode = "static Future<bool> uploadJobCardPhoto(String id, String imagePath) async {";
$newCode = "static Future<bool> uploadJobCardPhoto(String id, String imagePath, {String? type}) async {";

$content = str_replace($oldCode, $newCode, $content);

$oldCode2 = "request.files.add(await http.MultipartFile.fromPath('photo', imagePath));";
$newCode2 = "request.files.add(await http.MultipartFile.fromPath('photo', imagePath));\n      if (type != null) request.fields['type'] = type;";

$content = str_replace($oldCode2, $newCode2, $content);
file_put_contents($file, $content);
