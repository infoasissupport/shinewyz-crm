<?php
$file = 'shinewise_manager_app/lib/screens/jobcard_detail_screen.dart';
$content = file_get_contents($file);

// Add import
$content = str_replace(
    "import 'package:url_launcher/url_launcher.dart';",
    "import 'package:url_launcher/url_launcher.dart';\nimport 'package:image_picker/image_picker.dart';",
    $content
);

// Add _picker variable
$content = str_replace(
    "bool _isSaving = false;",
    "bool _isSaving = false;\n  final ImagePicker _picker = ImagePicker();",
    $content
);

// Fix _showStartWashModal
$content = preg_replace(
    "/final uploaded = await ApiService\.uploadJobCardPhoto\(widget\.jobCard\.id, type: 'before'\);/",
    "final image = await _picker.pickImage(source: ImageSource.camera, imageQuality: 70);\n                    if (image == null) return;\n                    final uploaded = await ApiService.uploadJobCardPhoto(widget.jobCard.id, image.path, type: 'before');",
    $content
);

// Fix _showCompleteWashModal
$content = preg_replace(
    "/final uploaded = await ApiService\.uploadJobCardPhoto\(widget\.jobCard\.id, type: 'after'\);/",
    "final image = await _picker.pickImage(source: ImageSource.camera, imageQuality: 70);\n                    if (image == null) return;\n                    final uploaded = await ApiService.uploadJobCardPhoto(widget.jobCard.id, image.path, type: 'after');",
    $content
);

// Fix _addPhoto
$content = preg_replace(
    "/final uploaded = await ApiService\.uploadJobCardPhoto\(widget\.jobCard\.id, type: type\);/",
    "final image = await _picker.pickImage(source: ImageSource.camera, imageQuality: 70);\n    if (image == null) return;\n    final uploaded = await ApiService.uploadJobCardPhoto(widget.jobCard.id, image.path, type: type);",
    $content
);

file_put_contents($file, $content);
echo "Fixed image picker logic.\n";
