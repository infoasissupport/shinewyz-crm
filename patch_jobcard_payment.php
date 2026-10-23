<?php
$file = 'shinewise_manager_app/lib/screens/jobcard_detail_screen.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
              if (jc.stage != 'completed') ...[
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton(
                    onPressed: _isSaving
                        ? null
                        : () {
                            if (jc.stage == 'assigned') {
                              _showStartWashModal();
                            } else {
                              _showCompleteWashModal();
                            }
                          },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFFF6B00),
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12)),
                      elevation: 0,
                    ),
                    child: _isSaving
                        ? const SizedBox(
                            height: 20,
                            width: 20,
                            child: CircularProgressIndicator(
                                color: Colors.white, strokeWidth: 2))
                        : Text(
                            jc.stage == 'assigned'
                                ? 'Start Wash'
                                : 'Mark Completed',
                            style: const TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.w700)),
                  ),
                ),
              ],
DART;

$newCode = <<<DART
              if (jc.stage != 'completed') ...[
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton(
                    onPressed: _isSaving
                        ? null
                        : () {
                            if (jc.stage == 'assigned') {
                              _showStartWashModal();
                            } else {
                              _showCompleteWashModal();
                            }
                          },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFFF6B00),
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12)),
                      elevation: 0,
                    ),
                    child: _isSaving
                        ? const SizedBox(
                            height: 20,
                            width: 20,
                            child: CircularProgressIndicator(
                                color: Colors.white, strokeWidth: 2))
                        : Text(
                            jc.stage == 'assigned'
                                ? 'Start Wash'
                                : 'Mark Completed',
                            style: const TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.w700)),
                  ),
                ),
              ] else if (jc.invoice != null && jc.invoice!['status'] != 'paid') ...[
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton(
                    onPressed: _isSaving ? null : () => _showPaymentModal(jc.invoice!['id']),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF10B981),
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12)),
                      elevation: 0,
                    ),
                    child: _isSaving
                        ? const SizedBox(
                            height: 20,
                            width: 20,
                            child: CircularProgressIndicator(
                                color: Colors.white, strokeWidth: 2))
                        : const Text('Make Payment',
                            style: TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.w700)),
                  ),
                ),
              ],
DART;

$content = str_replace($oldCode, $newCode, $content);

$paymentModal = <<<DART
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
                      if (mounted) Navigator.pop(context); // close screen
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
DART;

$content = preg_replace('/}\s*$/', "\n" . $paymentModal . "\n}\n", $content);
file_put_contents($file, $content);
echo "Patched job card payment button.\n";
