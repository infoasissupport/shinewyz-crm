<?php
$file = 'shinewise_manager_app/lib/screens/operations_tab.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
            ));
      },
    );
  }

  Widget _buildBookingsList()
DART;

$newCode = <<<DART
            ));
      },
    ),
    ),
      ],
    );
  }

  Widget _buildBookingsList()
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched end of operations tab.\n";
