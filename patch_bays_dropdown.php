<?php
$file = 'shinewise_manager_app/lib/screens/operations_tab.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
    String selectedBay = baysList.isNotEmpty ? baysList.first['name'] : 'Bay 1';

    final result = await showDialog<bool>(
DART;

$newCode = <<<DART
    final uniqueBays = baysList.map((b) => b['name'].toString()).toSet().toList();
    String selectedBay = uniqueBays.isNotEmpty ? uniqueBays.first : 'Bay 1';
    
    String capitalize(String s) {
      if (s.isEmpty) return s;
      return s.split(' ').map((word) {
        if (word.isEmpty) return '';
        return word[0].toUpperCase() + word.substring(1).toLowerCase();
      }).join(' ');
    }

    final result = await showDialog<bool>(
DART;

$content = str_replace($oldCode, $newCode, $content);

$oldDropdown = <<<DART
                    items: baysList.isNotEmpty
                        ? baysList
                            .map((b) => DropdownMenuItem<String>(
                                value: b['name'], child: Text(b['name'])))
                            .toList()
                        : [
                            const DropdownMenuItem(
                                value: 'Bay 1', child: Text('Bay 1'))
                          ],
DART;

$newDropdown = <<<DART
                    items: uniqueBays.isNotEmpty
                        ? uniqueBays
                            .map((bName) => DropdownMenuItem<String>(
                                value: bName, child: Text(capitalize(bName))))
                            .toList()
                        : [
                            const DropdownMenuItem(
                                value: 'Bay 1', child: Text('Bay 1'))
                          ],
DART;

$content = str_replace($oldDropdown, $newDropdown, $content);
file_put_contents($file, $content);
echo "Patched bays dropdown.\n";
