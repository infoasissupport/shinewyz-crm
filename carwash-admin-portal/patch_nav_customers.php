<?php
$file = 'shinewise_manager_app/lib/screens/main_navigation_screen.dart';
$content = file_get_contents($file);

$content = str_replace(
    "const CustomersTab(),",
    "CustomersTab(key: ValueKey('customers_\$_refreshCount')),",
    $content
);

file_put_contents($file, $content);
echo "Patched main_navigation_screen.dart\n";
