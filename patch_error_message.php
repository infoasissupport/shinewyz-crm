<?php
$file = 'shinewise_manager_app/lib/screens/new_wash_screen.dart';
$content = file_get_contents($file);

// Add _errorMessage
$content = preg_replace(
    '/(bool _isLoading = false;)/',
    "$1\n  String? _errorMessage;",
    $content
);

// Modify _handleSubmit
$oldSubmit = <<<DART
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

$newSubmit = <<<DART
    setState(() { _errorMessage = null; });
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
      setState(() {
        _isLoading = false;
        _errorMessage = e.toString().replaceAll('Exception: ', '');
      });
    }
DART;

$content = str_replace($oldSubmit, $newSubmit, $content);

// Modify build method to show error message above the button
$oldButton = <<<DART
            _isLoading
                ? const Center(child: CircularProgressIndicator(color: Color(0xFFFF6B00)))
                : SizedBox(
DART;

$newButton = <<<DART
            if (_errorMessage != null)
              Container(
                padding: const EdgeInsets.all(12),
                margin: const EdgeInsets.only(bottom: 16),
                decoration: BoxDecoration(
                  color: const Color(0xFFFEF2F2),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: const Color(0xFFFCA5A5)),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.error_outline, color: Color(0xFFEF4444), size: 20),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        _errorMessage!,
                        style: const TextStyle(color: Color(0xFFB91C1C), fontSize: 13, fontWeight: FontWeight.w600),
                      ),
                    ),
                  ],
                ),
              ),
            _isLoading
                ? const Center(child: CircularProgressIndicator(color: Color(0xFFFF6B00)))
                : SizedBox(
DART;

$content = str_replace($oldButton, $newButton, $content);

file_put_contents($file, $content);
echo "Patched new_wash_screen.dart to use inline error message.\n";
