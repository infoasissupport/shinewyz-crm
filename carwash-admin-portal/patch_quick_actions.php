<?php
$file = 'shinewise_manager_app/lib/screens/operations_tab.dart';
$content = file_get_contents($file);

// Add import
if (strpos($content, "package:image_picker/image_picker.dart") === false) {
    $content = preg_replace('/(import \'package:flutter\/material\.dart\';)/', "$1\nimport 'package:image_picker/image_picker.dart';", $content);
}

// Add ImagePicker instance
if (strpos($content, "final ImagePicker _picker = ImagePicker();") === false) {
    $content = preg_replace('/(class _OperationsTabState extends State<OperationsTab> \{)/', "$1\n  final ImagePicker _picker = ImagePicker();", $content);
}

// Add helper methods
$helperMethods = <<<DART
  Future<void> _startWashWithPhoto(JobCard jc) async {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Start Wash'),
        content: const Text('Would you like to take a BEFORE photo?'),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.pop(ctx);
              _updateJobCardStage(jc.id, 'inprogress');
            },
            child: const Text('Skip'),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(ctx);
              final image = await _picker.pickImage(source: ImageSource.camera, imageQuality: 70);
              if (image == null) return;
              setState(() => _processingJobCards.add(jc.id));
              final uploaded = await ApiService.uploadJobCardPhoto(jc.id, image.path, type: 'before');
              if (uploaded) {
                _updateJobCardStage(jc.id, 'inprogress');
              } else {
                setState(() => _processingJobCards.remove(jc.id));
                if (mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Failed to upload photo')));
              }
            },
            child: const Text('Take Photo'),
          ),
        ],
      ),
    );
  }

  Future<void> _completeWashWithPhoto(JobCard jc) async {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Complete Wash'),
        content: const Text('Would you like to take an AFTER photo?'),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.pop(ctx);
              _updateJobCardStage(jc.id, 'completed');
            },
            child: const Text('Skip'),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(ctx);
              final image = await _picker.pickImage(source: ImageSource.camera, imageQuality: 70);
              if (image == null) return;
              setState(() => _processingJobCards.add(jc.id));
              final uploaded = await ApiService.uploadJobCardPhoto(jc.id, image.path, type: 'after');
              if (uploaded) {
                _updateJobCardStage(jc.id, 'completed');
              } else {
                setState(() => _processingJobCards.remove(jc.id));
                if (mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Failed to upload photo')));
              }
            },
            child: const Text('Take Photo'),
          ),
        ],
      ),
    );
  }

  Future<void> _updateJobCardStage(int id, String stage) async {
    setState(() => _processingJobCards.add(id));
    try {
      await ApiService.updateJobCardStage(id, stage);
      await _loadOperationsData();
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString().replaceAll('Exception: ', ''))),
        );
      }
    } finally {
      if (mounted) setState(() => _processingJobCards.remove(id));
    }
  }

DART;

// inject helper methods before _showAssignModal
$content = preg_replace('/(  Future<void> _showAssignModal)/', $helperMethods . "$1", $content);

// replace inline button click
$oldButtonLogic = <<<DART
                                onPressed: _processingJobCards.contains(jc.id)
                                    ? null
                                    : () async {
                                        setState(
                                            () => _processingJobCards.add(jc.id));
                                        try {
                                          final next = jc.stage == 'assigned'
                                              ? 'inprogress'
                                              : 'completed';
                                          await ApiService.updateJobCardStage(
                                              jc.id, next);
                                          await _loadOperationsData();
                                        } catch (e) {
                                          if (mounted) {
                                            ScaffoldMessenger.of(context)
                                                .showSnackBar(
                                              SnackBar(
                                                  content: Text(e
                                                      .toString()
                                                      .replaceAll(
                                                          'Exception: ', ''))),
                                            );
                                          }
                                        } finally {
                                          if (mounted)
                                            setState(() =>
                                                _processingJobCards.remove(jc.id));
                                        }
                                      },
DART;

$newButtonLogic = <<<DART
                                onPressed: _processingJobCards.contains(jc.id)
                                    ? null
                                    : () {
                                        if (jc.stage == 'assigned') {
                                          _startWashWithPhoto(jc);
                                        } else {
                                          _completeWashWithPhoto(jc);
                                        }
                                      },
DART;

$content = str_replace($oldButtonLogic, $newButtonLogic, $content);
file_put_contents($file, $content);
echo "Patched quick actions.\n";
