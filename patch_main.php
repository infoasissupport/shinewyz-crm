<?php
$file = 'shinewise_manager_app/lib/main.dart';
$content = file_get_contents($file);

// Add import
$content = str_replace(
    "import 'screens/main_navigation_screen.dart';",
    "import 'screens/main_navigation_screen.dart';\nimport 'screens/splash_screen.dart';",
    $content
);

// Replace home
$oldHome = <<<DART
      home: FutureBuilder<User?>(
        future: ApiService.getUserSession(),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Scaffold(
              backgroundColor: Color(0xFFF8FAFC),
              body: Center(
                child: CircularProgressIndicator(color: Color(0xFFFF6B00)),
              ),
            );
          }
          if (snapshot.hasData && snapshot.data != null) {
            return MainNavigationScreen(currentUser: snapshot.data!);
          }
          return const LoginScreen();
        },
      ),
DART;

$newHome = <<<DART
      home: const SplashScreen(),
DART;

$content = str_replace($oldHome, $newHome, $content);
file_put_contents($file, $content);
echo "Patched main.dart.\n";
