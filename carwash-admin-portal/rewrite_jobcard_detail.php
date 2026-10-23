<?php
$file = 'shinewise_manager_app/lib/screens/jobcard_detail_screen.dart';
$content = file_get_contents($file);

$newContent = <<<DART
import 'package:flutter/material.dart';
import '../models/models.dart';
import '../services/api_service.dart';
import 'dart:convert';
import 'package:url_launcher/url_launcher.dart';

class JobCardDetailScreen extends StatefulWidget {
  final JobCard jobCard;
  final VoidCallback onUpdated;

  const JobCardDetailScreen({
    Key? key,
    required this.jobCard,
    required this.onUpdated,
  }) : super(key: key);

  @override
  State<JobCardDetailScreen> createState() => _JobCardDetailScreenState();
}

class _JobCardDetailScreenState extends State<JobCardDetailScreen> {
  late List<dynamic> _checklist;
  late TextEditingController _noteController;
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _checklist = List.from(widget.jobCard.checklist);
    _noteController = TextEditingController(text: widget.jobCard.note ?? '');
  }

  @override
  void dispose() {
    _noteController.dispose();
    super.dispose();
  }

  Future<void> _saveChanges(String? nextStage) async {
    setState(() => _isSaving = true);
    try {
      final payload = {
        'checklist': _checklist,
        'note': _noteController.text.trim(),
      };
      if (nextStage != null) {
        payload['stage'] = nextStage;
        if (nextStage == 'completed') {
          payload['signed_off_by'] = 'Customer';
          payload['signed_off_time'] = TimeOfDay.now().format(context);
        }
      }
      final ok = await ApiService.updateJobCard(widget.jobCard.id, payload);
      if (ok) {
        widget.onUpdated();
        if (mounted) Navigator.pop(context);
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Failed to update Job Card.')),
          );
        }
      }
    } catch (e) {
      print('Error saving changes: \$e');
    } finally {
      if (mounted) setState(() => _isSaving = false);
    }
  }

  void _showStartWashModal() {
    showDialog(
        context: context,
        builder: (ctx) => AlertDialog(
              title: const Text('Start Wash'),
              content: const Text('Would you like to take a BEFORE photo?'),
              actions: [
                TextButton(
                  onPressed: () {
                    Navigator.pop(ctx);
                    _saveChanges('inprogress');
                  },
                  child: const Text('Skip'),
                ),
                ElevatedButton(
                  onPressed: () async {
                    Navigator.pop(ctx);
                    final uploaded = await ApiService.uploadJobCardPhoto(widget.jobCard.id, type: 'before');
                    if (uploaded) {
                      _saveChanges('inprogress');
                    }
                  },
                  child: const Text('Take Photo'),
                ),
              ],
            ));
  }

  void _showCompleteWashModal() {
    showDialog(
        context: context,
        builder: (ctx) => AlertDialog(
              title: const Text('Complete Wash'),
              content: const Text('Would you like to take an AFTER photo?'),
              actions: [
                TextButton(
                  onPressed: () {
                    Navigator.pop(ctx);
                    _saveChanges('completed');
                  },
                  child: const Text('Skip'),
                ),
                ElevatedButton(
                  onPressed: () async {
                    Navigator.pop(ctx);
                    final uploaded = await ApiService.uploadJobCardPhoto(widget.jobCard.id, type: 'after');
                    if (uploaded) {
                      _saveChanges('completed');
                    }
                  },
                  child: const Text('Take Photo'),
                ),
              ],
            ));
  }

  Future<void> _showPaymentModal(String invoiceId) async {
    String selectedMethod = 'UPI';
    await showDialog(
      context: context,
      builder: (ctx) {
        return StatefulBuilder(
          builder: (ctx, setModalState) {
            return AlertDialog(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              title: const Text('Record Payment', style: TextStyle(fontWeight: FontWeight.w800)),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Select the payment method used by the customer:', style: TextStyle(color: Color(0xFF64748B))),
                  const SizedBox(height: 16),
                  DropdownButtonFormField<String>(
                    value: selectedMethod,
                    decoration: InputDecoration(
                      filled: true,
                      fillColor: const Color(0xFFF8FAFC),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
                    ),
                    items: ['UPI', 'Cash', 'Card'].map((e) => DropdownMenuItem(value: e, child: Text(e))).toList(),
                    onChanged: (val) {
                      if (val != null) setModalState(() => selectedMethod = val);
                    },
                  ),
                ],
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(ctx),
                  child: const Text('Cancel', style: TextStyle(color: Color(0xFF64748B))),
                ),
                ElevatedButton(
                  onPressed: () async {
                    Navigator.pop(ctx);
                    setState(() => _isSaving = true);
                    final ok = await ApiService.updateInvoice(invoiceId, {
                      'status': 'paid',
                      'method': selectedMethod,
                    });
                    if (ok) {
                      widget.onUpdated();
                      if (mounted) Navigator.pop(context);
                    } else {
                      setState(() => _isSaving = false);
                      if (mounted) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Failed to record payment.')),
                        );
                      }
                    }
                  },
                  style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF10B981)),
                  child: const Text('Confirm Payment', style: TextStyle(color: Colors.white)),
                ),
              ],
            );
          }
        );
      }
    );
  }

  Future<void> _addPhoto(String type) async {
    final uploaded = await ApiService.uploadJobCardPhoto(widget.jobCard.id, type: type);
    if (uploaded) {
      widget.onUpdated();
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Added \$type photo!')));
      Navigator.pop(context); 
    }
  }

  @override
  Widget build(BuildContext context) {
    final jc = widget.jobCard;
    Color stageColor = const Color(0xFF3B82F6);
    if (jc.stage == 'completed') stageColor = const Color(0xFF10B981);
    if (jc.stage == 'inprogress') stageColor = const Color(0xFFFF6B00);
    if (jc.stage == 'cancelled') stageColor = Colors.red;

    int doneCount = _checklist.where((item) => item['done'] == true).length;
    int totalCount = _checklist.length;
    double progress = totalCount > 0 ? (doneCount / totalCount) : 0;

    bool hasInvoice = jc.invoice != null;
    bool isPaid = hasInvoice && jc.invoice!['status'] == 'paid';

    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9), // Light grey background like modal
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Color(0xFF0F172A)),
        title: Row(
          children: [
            Text('#\${jc.id}',
                style: const TextStyle(
                    color: Color(0xFF0F172A),
                    fontWeight: FontWeight.w800,
                    fontSize: 18)),
            const SizedBox(width: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                  color: stageColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(20)),
              child: Text(jc.stage.toUpperCase(),
                  style: TextStyle(
                      color: stageColor,
                      fontSize: 10,
                      fontWeight: FontWeight.w800)),
            ),
          ],
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Vehicle Info Card
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: const Color(0xFFE2E8F0)),
              ),
              child: Row(
                children: [
                  Container(
                    width: 48,
                    height: 48,
                    decoration: BoxDecoration(
                      color: const Color(0xFFEFF6FF),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.directions_car, color: Color(0xFF3B82F6)),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(jc.vehicle, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Color(0xFF0F172A))),
                        const SizedBox(height: 2),
                        Text(jc.service, style: const TextStyle(fontSize: 13, color: Color(0xFF64748B))),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),

            // Meta Info Grid
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: const Color(0xFFE2E8F0)),
              ),
              child: Wrap(
                spacing: 24,
                runSpacing: 16,
                children: [
                  _buildMetaItem(Icons.people_outline, 'Staff', jc.staff),
                  _buildMetaItem(Icons.pin_drop_outlined, 'Bay', jc.bay),
                  if (jc.startedAt != null) _buildMetaItem(Icons.access_time, 'Started', jc.startedAt!),
                  if (hasInvoice) _buildMetaItem(Icons.credit_card, 'Payment', isPaid ? 'Paid' : 'Pending', valueColor: isPaid ? const Color(0xFF10B981) : Colors.red),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Checklist
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: const Color(0xFFE2E8F0)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Checklist', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Color(0xFF0F172A))),
                      Text('\$doneCount/\$totalCount', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: Color(0xFF3B82F6))),
                    ],
                  ),
                  const SizedBox(height: 12),
                  LinearProgressIndicator(
                    value: progress,
                    backgroundColor: const Color(0xFFE2E8F0),
                    color: const Color(0xFF3B82F6),
                    minHeight: 8,
                    borderRadius: BorderRadius.circular(4),
                  ),
                  const SizedBox(height: 16),
                  ...List.generate(_checklist.length, (index) {
                    final item = _checklist[index];
                    final isDone = item['done'] == true;
                    return InkWell(
                      onTap: () {
                        setState(() {
                          _checklist[index]['done'] = !isDone;
                        });
                      },
                      child: Padding(
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        child: Row(
                          children: [
                            Container(
                              width: 24,
                              height: 24,
                              decoration: BoxDecoration(
                                color: isDone ? const Color(0xFF3B82F6) : Colors.white,
                                border: Border.all(color: isDone ? const Color(0xFF3B82F6) : const Color(0xFFCBD5E1)),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: isDone ? const Icon(Icons.check, size: 16, color: Colors.white) : null,
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Text(
                                item['label'] ?? '',
                                style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w500,
                                  color: isDone ? const Color(0xFF94A3B8) : const Color(0xFF334155),
                                  decoration: isDone ? TextDecoration.lineThrough : null,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  }),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Before Photos
            _buildPhotoSection('Before Photos', 'before', jc.photosBefore),
            const SizedBox(height: 16),

            // After Photos
            _buildPhotoSection('After Photos', 'after', jc.photosAfter),
            const SizedBox(height: 16),

            // Damage Note
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: const Color(0xFFE2E8F0)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Damage note', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Color(0xFF0F172A))),
                  const SizedBox(height: 12),
                  TextField(
                    controller: _noteController,
                    maxLines: 3,
                    decoration: InputDecoration(
                      hintText: 'No damage reported',
                      hintStyle: const TextStyle(color: Color(0xFF94A3B8)),
                      filled: true,
                      fillColor: const Color(0xFFF8FAFC),
                      border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: BorderSide.none),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 80),
          ],
        ),
      ),
      bottomNavigationBar: SafeArea(
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: const BoxDecoration(
            color: Colors.white,
            border: Border(top: BorderSide(color: Color(0xFFE2E8F0))),
          ),
          child: Row(
            children: [
              if (jc.stage != 'cancelled')
                Expanded(
                  flex: 1,
                  child: OutlinedButton(
                    onPressed: _isSaving ? null : () => _saveChanges('cancelled'),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      side: const BorderSide(color: Color(0xFFE2E8F0)),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                    child: const Text('Cancel job', style: TextStyle(color: Color(0xFF64748B), fontWeight: FontWeight.w700)),
                  ),
                ),
              if (jc.stage != 'cancelled') const SizedBox(width: 12),
              
              if (jc.stage == 'assigned')
                Expanded(
                  flex: 2,
                  child: ElevatedButton(
                    onPressed: _isSaving ? null : _showStartWashModal,
                    style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFFFF6B00), padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)), elevation: 0),
                    child: const Text('Start Wash', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
                  ),
                )
              else if (jc.stage == 'inprogress')
                Expanded(
                  flex: 2,
                  child: ElevatedButton(
                    onPressed: _isSaving ? null : _showCompleteWashModal,
                    style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFFFF6B00), padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)), elevation: 0),
                    child: const Text('Mark Completed', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
                  ),
                )
              else if (jc.stage == 'completed' && hasInvoice && !isPaid)
                Expanded(
                  flex: 2,
                  child: ElevatedButton(
                    onPressed: _isSaving ? null : () => _showPaymentModal(jc.invoice!['id']),
                    style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF10B981), padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)), elevation: 0),
                    child: const Text('Make Payment', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
                  ),
                )
              else if (jc.stage == 'completed' && isPaid)
                Expanded(
                  flex: 2,
                  child: ElevatedButton(
                    onPressed: null, // Just disabled to match Paid status, or we can use it to open PDF later
                    style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF10B981), padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)), elevation: 0),
                    child: const Text('Invoice Paid', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildMetaItem(IconData icon, String label, String value, {Color? valueColor}) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 16, color: const Color(0xFF64748B)),
        const SizedBox(width: 6),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w600, color: Color(0xFF94A3B8))),
            Text(value, style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: valueColor ?? const Color(0xFF1E293B))),
          ],
        ),
      ],
    );
  }

  Widget _buildPhotoSection(String title, String type, int count) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Color(0xFF0F172A))),
          const SizedBox(height: 12),
          Wrap(
            spacing: 12,
            runSpacing: 12,
            children: [
              ...List.generate(count, (i) {
                return Container(
                  width: 80,
                  height: 80,
                  decoration: BoxDecoration(
                    color: const Color(0xFFF1F5F9),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                  ),
                  child: const Center(child: Icon(Icons.image, color: Color(0xFFCBD5E1))),
                );
              }),
              InkWell(
                onTap: () => _addPhoto(type),
                child: Container(
                  width: 80,
                  height: 80,
                  decoration: BoxDecoration(
                    color: const Color(0xFFEFF6FF),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: const Color(0xFFBFDBFE), width: 1.5, style: BorderStyle.solid),
                  ),
                  child: const Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.add_a_photo, color: Color(0xFF3B82F6), size: 20),
                      SizedBox(height: 4),
                      Text('Add', style: TextStyle(color: Color(0xFF3B82F6), fontSize: 12, fontWeight: FontWeight.w600)),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
DART;

file_put_contents($file, $newContent);
echo "Rewrote JobCardDetailScreen to match admin panel layout.\n";
