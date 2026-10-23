<?php
$file = 'shinewise_manager_app/lib/screens/new_wash_screen.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
    final ok = await ApiService.createBooking(
      customer: customer,
      phone: phone,
      vehiclePlate: vehiclePlate,
      vehicleModel: vehicleModel,
      vehicleType: _selectedVehicleType,
      service: _selectedService,
    );

    setState(() => _isLoading = false);

    if (ok) {
      if (!mounted) return;
      Navigator.of(context).pop(true);
    } else {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Could not create wash appointment.')),
      );
    }
DART;

$newCode = <<<DART
    try {
      final ok = await ApiService.createBooking(
        customer: customer,
        phone: phone,
        vehiclePlate: vehiclePlate,
        vehicleModel: vehicleModel,
        vehicleType: _selectedVehicleType,
        service: _selectedService,
      );

      setState(() => _isLoading = false);

      if (ok) {
        if (!mounted) return;
        Navigator.of(context).pop(true);
      }
    } catch (e) {
      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString().replaceAll('Exception: ', ''))),
        );
      }
    }
DART;

if (strpos($content, "final ok = await ApiService.createBooking") !== false) {
    $content = str_replace($oldCode, $newCode, $content);
    file_put_contents($file, $content);
    echo "Patched successfully.\n";
} else {
    echo "Could not find target block.\n";
}
