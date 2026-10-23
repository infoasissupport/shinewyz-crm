<?php
$file = 'shinewise_manager_app/lib/services/api_service.dart';
$content = file_get_contents($file);

$newMethod = <<<DART
  static Future<bool> deleteBooking(int id) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token');
    if (token == null) return false;

    try {
      final res = await http.delete(
        Uri.parse('\$baseUrl/bookings/\$id'),
        headers: {
          'Authorization': 'Bearer \$token',
          'Accept': 'application/json',
        },
      );
      return res.statusCode == 200 || res.statusCode == 204;
    } catch (e) {
      print('deleteBooking error: \$e');
      return false;
    }
  }

DART;

$content = preg_replace('/(class ApiService \{)/', "$1\n$newMethod", $content);
file_put_contents($file, $content);
echo "Patched API service.\n";
