<?php
$file = 'shinewise_manager_app/lib/screens/operations_tab.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
            trailing: ElevatedButton(
              onPressed: _processingBookings.contains(b.id)
                  ? null
                  : () => _showAssignModal(b),
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFFFF6B00),
                disabledBackgroundColor:
                    const Color(0xFFFF6B00).withValues(alpha: 0.5),
                elevation: 0,
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8)),
              ),
              child: _processingBookings.contains(b.id)
                  ? const SizedBox(
                      width: 14,
                      height: 14,
                      child: CircularProgressIndicator(
                          color: Colors.white, strokeWidth: 2))
                  : const Text('Start Wash',
                      style: TextStyle(
                          color: Colors.white,
                          fontSize: 12,
                          fontWeight: FontWeight.w700)),
            ),
DART;

$newCode = <<<DART
            trailing: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                ElevatedButton(
                  onPressed: _processingBookings.contains(b.id)
                      ? null
                      : () => _showAssignModal(b),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFFF6B00),
                    disabledBackgroundColor:
                        const Color(0xFFFF6B00).withValues(alpha: 0.5),
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8)),
                  ),
                  child: _processingBookings.contains(b.id)
                      ? const SizedBox(
                          width: 14,
                          height: 14,
                          child: CircularProgressIndicator(
                              color: Colors.white, strokeWidth: 2))
                      : const Text('Start Wash',
                          style: TextStyle(
                              color: Colors.white,
                              fontSize: 12,
                              fontWeight: FontWeight.w700)),
                ),
                const SizedBox(width: 8),
                IconButton(
                  icon: const Icon(Icons.delete_outline, color: Colors.redAccent, size: 20),
                  padding: EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                  onPressed: () async {
                    final confirm = await showDialog<bool>(
                      context: context,
                      builder: (ctx) => AlertDialog(
                        title: const Text('Delete Wash'),
                        content: Text('Are you sure you want to remove \${b.vehiclePlate ?? 'this vehicle'} from the queue?'),
                        actions: [
                          TextButton(
                            onPressed: () => Navigator.pop(ctx, false),
                            child: const Text('Cancel'),
                          ),
                          TextButton(
                            onPressed: () => Navigator.pop(ctx, true),
                            child: const Text('Delete', style: TextStyle(color: Colors.red)),
                          ),
                        ],
                      ),
                    );
                    if (confirm == true) {
                      setState(() => _processingBookings.add(b.id));
                      final ok = await ApiService.deleteBooking(b.id);
                      if (ok) {
                        _loadOperationsData();
                      } else {
                        setState(() => _processingBookings.remove(b.id));
                        if (mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Failed to delete booking')));
                        }
                      }
                    }
                  },
                ),
              ],
            ),
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched delete button.\n";
