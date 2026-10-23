<?php
$file = 'shinewise_manager_app/lib/screens/jobcard_detail_screen.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
              else if (jc.stage == 'completed' && isPaid)
                Expanded(
                  flex: 2,
                  child: ElevatedButton(
                    onPressed: null, // Just disabled to match Paid status, or we can use it to open PDF later
                    style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF10B981), padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)), elevation: 0),
                    child: const Text('Invoice Paid', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
                  ),
                ),
DART;

$newCode = <<<DART
              else if (jc.stage == 'completed' && isPaid)
                Expanded(
                  flex: 2,
                  child: ElevatedButton(
                    onPressed: () async {
                      final url = Uri.parse('\${ApiService.baseUrl.replaceAll('/api', '')}/invoice/\${jc.invoice!['id']}/view');
                      if (await canLaunchUrl(url)) {
                        await launchUrl(url, mode: LaunchMode.externalApplication);
                      }
                    },
                    style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF1E293B), padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)), elevation: 0),
                    child: const Text('View Invoice PDF', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
                  ),
                ),
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched flutter pdf button.\n";
