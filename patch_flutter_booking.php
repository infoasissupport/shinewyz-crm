<?php
$file = 'shinewise_manager_app/lib/models/models.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
  final String status;
  final String time;
  final int? branchId;

  Booking({
    required this.id,
    required this.customer,
    required this.phone,
    required this.vehicle,
    this.vehiclePlate,
    required this.service,
    required this.status,
    required this.time,
    this.branchId,
  });

  factory Booking.fromJson(Map<String, dynamic> json) {
    return Booking(
      id: json['id'] ?? 0,
      customer: json['customer'] ?? 'Walk-in Customer',
      phone: json['phone'] ?? '',
      vehicle: json['vehicle'] ?? '',
      vehiclePlate: json['vehicle_plate'],
      service: json['service'] ?? '',
      status: json['status'] ?? 'waiting',
      time: json['time_label'] ?? json['time'] ?? 'Today',
      branchId: json['branch_id'],
    );
  }
DART;

$newCode = <<<DART
  final String status;
  final String time;
  final int? branchId;
  final String? createdAt;

  Booking({
    required this.id,
    required this.customer,
    required this.phone,
    required this.vehicle,
    this.vehiclePlate,
    required this.service,
    required this.status,
    required this.time,
    this.branchId,
    this.createdAt,
  });

  factory Booking.fromJson(Map<String, dynamic> json) {
    return Booking(
      id: json['id'] ?? 0,
      customer: json['customer'] ?? 'Walk-in Customer',
      phone: json['phone'] ?? '',
      vehicle: json['vehicle'] ?? '',
      vehiclePlate: json['vehicle_plate'],
      service: json['service'] ?? '',
      status: json['status'] ?? 'waiting',
      time: json['time_label'] ?? json['time'] ?? 'Today',
      branchId: json['branch_id'],
      createdAt: json['created_at'],
    );
  }
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched Booking model.\n";
