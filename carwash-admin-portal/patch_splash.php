<?php
$file = 'shinewise_manager_app/lib/screens/splash_screen.dart';
$content = file_get_contents($file);

$oldRoute = <<<DART
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const MainNavigationScreen()),
      );
DART;

$newRoute = <<<DART
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => MainNavigationScreen(currentUser: user)),
      );
DART;

$content = str_replace($oldRoute, $newRoute, $content);
file_put_contents($file, $content);
echo "Patched splash_screen.dart.\n";
