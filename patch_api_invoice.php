<?php
$file = 'shinewise_manager_app/lib/services/api_service.dart';
$content = file_get_contents($file);

$newMethod = <<<DART
  static Future<bool> updateInvoice(String id, Map<String, dynamic> payload) async {
    try {
      final res = await http.put(
        Uri.parse('\$baseUrl/invoices/\$id'),
        headers: await _headers,
        body: jsonEncode(payload),
      );
      return res.statusCode == 200;
    } catch (e) {
      print('Update invoice error: \$e');
    }
    return false;
  }
DART;

// Insert before the last closing brace
$content = preg_replace('/}\s*$/', "\n" . $newMethod . "\n}\n", $content);
file_put_contents($file, $content);
echo "Added updateInvoice to api_service.dart\n";
