<?php
$file = 'shinewise_manager_app/lib/screens/customers_tab.dart';
$content = file_get_contents($file);

// Add RefreshIndicator to _buildCustomersList
$content = preg_replace(
    "/return ListView\.builder\(/",
    "return RefreshIndicator(\n      onRefresh: _loadData,\n      color: const Color(0xFFFF6B00),\n      child: ListView.builder(",
    $content,
    1 // Only first match which is _buildCustomersList
);

// Close the RefreshIndicator
$content = preg_replace(
    "/            \]\n          \),\n        \);\n      },\n    \);/",
    "            ]\n          ),\n        );\n      },\n    ),\n    );",
    $content,
    1
);

// Do the same for _buildVehiclesList
$content = preg_replace(
    "/return ListView\.builder\(/",
    "return RefreshIndicator(\n      onRefresh: _loadData,\n      color: const Color(0xFFFF6B00),\n      child: ListView.builder(",
    $content,
    1 // Second match
);

$content = preg_replace(
    "/            \]\n          \),\n        \);\n      },\n    \);/",
    "            ]\n          ),\n        );\n      },\n    ),\n    );",
    $content,
    1
);

// Fix constructor of CustomersTab
$content = str_replace(
    "const CustomersTab({Key? key}) : super(key: key);",
    "const CustomersTab({Key? key}) : super(key: key);",
    $content
);


file_put_contents($file, $content);
echo "Added RefreshIndicators.\n";
