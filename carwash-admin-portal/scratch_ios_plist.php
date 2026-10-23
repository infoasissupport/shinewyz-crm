<?php
$file = 'shinewise_manager_app/ios/Runner/Info.plist';
if (!file_exists($file)) die("No plist");

$content = file_get_contents($file);
$addition = "
	<key>NSCameraUsageDescription</key>
	<string>This app requires camera access to take before and after photos of the vehicles.</string>
	<key>NSPhotoLibraryUsageDescription</key>
	<string>This app requires photo library access to select vehicle photos.</string>
";
$content = str_replace("<dict>", "<dict>" . $addition, $content);
file_put_contents($file, $content);
echo "Added iOS permissions.\n";
