<?php
$file = 'shinewise_manager_app/lib/screens/login_screen.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
                Center(
                  child: Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: const Color(0xFFFF6B00),
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFFFF6B00).withOpacity(0.3),
                          blurRadius: 16,
                          offset: const Offset(0, 8),
                        ),
                      ],
                    ),
                    child: const Icon(Icons.local_car_wash_rounded, size: 44, color: Colors.white),
                  ),
                ),
DART;

$newCode = <<<DART
                Center(
                  child: Image.asset(
                    'assets/images/logo1.png',
                    height: 80,
                  ),
                ),
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched login logo.\n";
