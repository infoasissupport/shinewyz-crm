<?php
$file = 'shinewise_manager_app/lib/services/api_service.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
      final res = await http.post(
        Uri.parse('\$baseUrl/bookings'),
        headers: await _headers,
        body: jsonEncode({
          'customer': customer,
          'phone': phone,
          'vehicle_plate': vehiclePlate,
          'vehicle_type': vehicleType,
          'vehicle': vehicleModel.isNotEmpty ? vehicleModel : vehiclePlate,
          'service': service,
          'source': 'Walk-in',
          'status': 'waiting',
          'time': 'Today',
          'branch_id': branchId,
        }),
      );
      return res.statusCode == 201 || res.statusCode == 200;
    } catch (e) {
      print('Create booking error: \$e');
    }
    return false;
  }
DART;

$newCode = <<<DART
      final res = await http.post(
        Uri.parse('\$baseUrl/bookings'),
        headers: await _headers,
        body: jsonEncode({
          'customer': customer,
          'phone': phone,
          'vehicle_plate': vehiclePlate,
          'vehicle_type': vehicleType,
          'vehicle': vehicleModel.isNotEmpty ? vehicleModel : vehiclePlate,
          'service': service,
          'source': 'Walk-in',
          'status': 'waiting',
          'time': 'Today',
          'branch_id': branchId,
        }),
      );
      if (res.statusCode == 201 || res.statusCode == 200) return true;
      final body = jsonDecode(res.body);
      if (body['message'] != null) throw Exception(body['message']);
      return false;
    } catch (e) {
      print('Create booking error: \$e');
      rethrow;
    }
  }
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched ApiService.createBooking to throw errors.\n";
