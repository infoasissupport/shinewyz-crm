<?php
$file = 'shinewise_manager_app/android/app/src/main/AndroidManifest.xml';
$content = file_get_contents($file);

// Add Internet permission
$content = str_replace(
    '<uses-permission android:name="android.permission.CAMERA" />',
    '<uses-permission android:name="android.permission.INTERNET" />'."\n".'    <uses-permission android:name="android.permission.CAMERA" />',
    $content
);

// Add usesCleartextTraffic
$content = str_replace(
    '<application',
    '<application'."\n".'        android:usesCleartextTraffic="true"',
    $content
);

file_put_contents($file, $content);
echo "Patched AndroidManifest.xml.\n";
