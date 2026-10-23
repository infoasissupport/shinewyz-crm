<?php
$file = 'shinewise_manager_app/lib/screens/customers_tab.dart';
$content = file_get_contents($file);

$newContent = <<<DART
import 'package:flutter/material.dart';
import '../services/api_service.dart';

class CustomersTab extends StatefulWidget {
  const CustomersTab({Key? key}) : super(key: key);

  @override
  State<CustomersTab> createState() => _CustomersTabState();
}

class _CustomersTabState extends State<CustomersTab> {
  bool _isLoading = true;
  List<dynamic> _vehicles = [];

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final vehicles = await ApiService.fetchVehicles();
    if (mounted) {
      setState(() {
        _vehicles = vehicles;
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        title: const Text('Customers & Vehicles', style: TextStyle(color: Color(0xFF0F172A), fontWeight: FontWeight.w800)),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFFFF6B00)))
          : _buildVehiclesList(),
    );
  }

  Widget _buildVehiclesList() {
    if (_vehicles.isEmpty) {
      return RefreshIndicator(
        onRefresh: _loadData,
        color: const Color(0xFFFF6B00),
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: SizedBox(
            height: MediaQuery.of(context).size.height * 0.7,
            child: const Center(
              child: Text('No customers/vehicles registered yet', style: TextStyle(color: Color(0xFF94A3B8))),
            ),
          ),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadData,
      color: const Color(0xFFFF6B00),
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _vehicles.length,
        itemBuilder: (context, i) {
          final v = _vehicles[i];
          final plate = v['plate'] ?? 'Unknown';
          final brand = v['brand'] ?? '';
          final type = v['type'] ?? '';
          
          final c = v['customer'];
          final owner = c != null ? c['name'] ?? 'Unknown' : 'Unknown';
          final phone = c != null ? c['phone'] ?? '' : '';

          return Card(
            margin: const EdgeInsets.only(bottom: 12),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(14),
              side: const BorderSide(color: Color(0xFFE2E8F0)),
            ),
            elevation: 0,
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(plate, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16)),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: const Color(0xFFF1F5F9),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(type.toString().toUpperCase(), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: Color(0xFF475569))),
                      )
                    ],
                  ),
                  const SizedBox(height: 6),
                  Text(brand, style: const TextStyle(color: Color(0xFF64748B))),
                  const Divider(height: 24, color: Color(0xFFF1F5F9)),
                  Row(
                    children: [
                      const Icon(Icons.person_outline, size: 16, color: Color(0xFF94A3B8)),
                      const SizedBox(width: 6),
                      Text(owner, style: const TextStyle(fontWeight: FontWeight.w600, color: Color(0xFF334155))),
                      if (phone.isNotEmpty) ...[
                        const SizedBox(width: 8),
                        Text('•', style: const TextStyle(color: Color(0xFF94A3B8))),
                        const SizedBox(width: 8),
                        Text(phone, style: const TextStyle(color: Color(0xFF64748B))),
                      ],
                    ],
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
DART;

file_put_contents($file, $newContent);
echo "Refactored customers_tab.dart to single screen.\n";
