<?php
$file = 'shinewise_manager_app/ios/Runner/Info.plist';
$content = file_get_contents($file);

$badBlock = "
	<key>NSCameraUsageDescription</key>
	<string>This app requires camera access to take before and after photos of the vehicles.</string>
	<key>NSPhotoLibraryUsageDescription</key>
	<string>This app requires photo library access to select vehicle photos.</string>
";

$content = str_replace($badBlock, "", $content);

// Now properly insert it only ONCE right after the FIRST <dict>
$pos = strpos($content, '<dict>');
if ($pos !== false) {
    $content = substr_replace($content, "<dict>\n" . $badBlock, $pos, 6);
}

file_put_contents($file, $content);
echo "Fixed Info.plist\n";
