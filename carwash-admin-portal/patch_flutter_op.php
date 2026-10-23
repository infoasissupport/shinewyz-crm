<?php
$file = 'shinewise_manager_app/lib/screens/operations_tab.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
            subtitle: Text('\${b.service} • \${b.vehicle}',
                style: const TextStyle(fontSize: 12, color: Color(0xFF64748B))),
DART;

$newCode = <<<DART
            subtitle: Builder(
              builder: (ctx) {
                String timeStr = '';
                if (b.createdAt != null) {
                  try {
                    final d = DateTime.parse(b.createdAt!).toLocal();
                    final hour = d.hour % 12 == 0 ? 12 : d.hour % 12;
                    final min = d.minute.toString().padLeft(2, '0');
                    final ampm = d.hour >= 12 ? 'PM' : 'AM';
                    timeStr = ' • \$hour:\$min \$ampm';
                  } catch (_) {}
                }
                return Text('\${b.service}\$timeStr • \${b.vehicle}',
                  style: const TextStyle(fontSize: 12, color: Color(0xFF64748B)));
              }
            ),
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched operations_tab.dart\n";
