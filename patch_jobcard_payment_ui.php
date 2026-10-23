<?php
$file = 'shinewise_manager_app/lib/screens/jobcard_detail_screen.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
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

$newCode = <<<DART
              ] else if (jc.stage == 'completed') ...[
                const SizedBox(width: 12),
                if (jc.invoice != null && jc.invoice!['status'] == 'paid')
                  Expanded(
                    child: ElevatedButton(
                      onPressed: null,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFFF1F5F9),
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12)),
                        elevation: 0,
                      ),
                      child: const Text('Invoice Paid',
                          style: TextStyle(
                              color: Color(0xFF10B981),
                              fontWeight: FontWeight.w700)),
                    ),
                  )
                else
                  Expanded(
                    child: ElevatedButton(
                      onPressed: _isSaving ? null : () => _showPaymentModal(jc.invoice != null ? jc.invoice!['id'] : ''),
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
file_put_contents($file, $content);
echo "Patched job card payment button UI.\n";
