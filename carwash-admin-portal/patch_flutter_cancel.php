<?php
$file = 'shinewise_manager_app/lib/screens/jobcard_detail_screen.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
        actions: [
          Container(
            margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
            decoration: BoxDecoration(
                color: stageColor.withOpacity(0.1),
                borderRadius: BorderRadius.circular(20)),
            child: Text(jc.stage.toUpperCase(),
                style: TextStyle(
                    color: stageColor,
                    fontSize: 12,
                    fontWeight: FontWeight.w700)),
          ),
        ],
DART;

$newCode = <<<DART
        actions: [
          Container(
            margin: const EdgeInsets.symmetric(vertical: 12),
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
            decoration: BoxDecoration(
                color: stageColor.withOpacity(0.1),
                borderRadius: BorderRadius.circular(20)),
            child: Text(jc.stage.toUpperCase(),
                style: TextStyle(
                    color: stageColor,
                    fontSize: 12,
                    fontWeight: FontWeight.w700)),
          ),
          if (jc.stage != 'completed' && jc.stage != 'cancelled')
            PopupMenuButton<String>(
              onSelected: (val) async {
                if (val == 'cancel') {
                  final confirm = await showDialog<bool>(
                    context: context,
                    builder: (ctx) => AlertDialog(
                      title: const Text('Cancel Job'),
                      content: const Text('Are you sure you want to cancel this job?'),
                      actions: [
                        TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('No')),
                        TextButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Yes', style: TextStyle(color: Colors.red))),
                      ],
                    )
                  );
                  if (confirm == true) {
                    await _saveChanges('cancelled');
                    if (mounted) Navigator.pop(context);
                  }
                }
              },
              itemBuilder: (ctx) => [
                const PopupMenuItem(
                  value: 'cancel',
                  child: Text('Cancel Job', style: TextStyle(color: Colors.red)),
                )
              ],
            )
        ],
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Added Cancel option.\n";
