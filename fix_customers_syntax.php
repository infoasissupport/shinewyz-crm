<?php
$file = 'shinewise_manager_app/lib/screens/customers_tab.dart';
$content = file_get_contents($file);

// Add missing closing paren for RefreshIndicator at the end of both builder blocks
$content = preg_replace(
    "/      },\n    \);\n  }/m",
    "      },\n    ),\n    );\n  }",
    $content
);

file_put_contents($file, $content);
