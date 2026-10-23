<?php
$file = 'shinewise_manager_app/lib/screens/new_wash_screen.dart';
$content = file_get_contents($file);

$oldButton = <<<DART
            const SizedBox(height: 24),

            // Submit Button
DART;

$newButton = <<<DART
            const SizedBox(height: 24),
            
            if (_errorMessage != null)
              Container(
                padding: const EdgeInsets.all(12),
                margin: const EdgeInsets.only(bottom: 16),
                decoration: BoxDecoration(
                  color: const Color(0xFFFEF2F2),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: const Color(0xFFFCA5A5)),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.error_outline, color: Color(0xFFEF4444), size: 20),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        _errorMessage!,
                        style: const TextStyle(color: Color(0xFFB91C1C), fontSize: 13, fontWeight: FontWeight.w600),
                      ),
                    ),
                  ],
                ),
              ),

            // Submit Button
DART;

$content = str_replace($oldButton, $newButton, $content);
file_put_contents($file, $content);
echo "Patched error UI.\n";
