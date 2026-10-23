<?php
$file = 'shinewise_manager_app/lib/services/api_service.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
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

$newCode = <<<DART
  static Future<bool> deleteBooking(int id) async {
    try {
      final res = await http.delete(
        Uri.parse('\$baseUrl/bookings/\$id'),
        headers: await _headers,
      );
      return res.statusCode == 200 || res.statusCode == 204;
    } catch (e) {
      print('deleteBooking error: \$e');
      return false;
    }
  }
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched deleteBooking auth.\n";
