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
      widget.onWashStarted();
      if (mounted) Navigator.pop(context);
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Failed to add vehicle to queue')),
        );
      }
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
        widget.onWashStarted();
        if (mounted) Navigator.pop(context);
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

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched new_wash_screen.dart to catch errors.\n";
