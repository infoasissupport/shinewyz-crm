<?php
$file = 'shinewise_manager_app/lib/screens/operations_tab.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
        if (ok) {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Wash assigned! Moved to \$selectedBay.')),
            );
          }
          await _loadOperationsData();
        }
DART;

$newCode = <<<DART
        if (ok) {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Wash assigned! Moved to \$selectedBay.')),
            );
            _tabController.animateTo(1);
          }
          await _loadOperationsData();
        }
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched switch tab.\n";
