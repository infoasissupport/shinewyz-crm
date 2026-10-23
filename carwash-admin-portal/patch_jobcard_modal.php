<?php
$file = 'shinewise_manager_app/lib/screens/jobcard_detail_screen.dart';
$content = file_get_contents($file);

// Replace the generic uploadJobCardPhoto with type: 'before'
$content = str_replace(
    "await ApiService.uploadJobCardPhoto(\n                            widget.jobCard.id, image!.path);",
    "await ApiService.uploadJobCardPhoto(\n                            widget.jobCard.id, image!.path, type: 'before');",
    $content
);

// Add _showCompleteWashModal
$completeModalStr = <<<DART
  Future<void> _showCompleteWashModal() async {
    final picker = ImagePicker();
    XFile? image;

    await showDialog(
      context: context,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return AlertDialog(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16)),
              title: const Text('Complete Wash',
                  style: TextStyle(fontWeight: FontWeight.w800, fontSize: 18)),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                      "Add a photo of the completed vehicle before marking it as done (optional).",
                      style: TextStyle(fontSize: 14, color: Color(0xFF64748B))),
                  const SizedBox(height: 16),
                  if (image == null)
                    InkWell(
                      onTap: () async {
                        final picked =
                            await picker.pickImage(source: ImageSource.camera);
                        if (picked != null) {
                          setModalState(() => image = picked);
                        }
                      },
                      borderRadius: BorderRadius.circular(12),
                      child: Container(
                        height: 80,
                        width: 80,
                        decoration: BoxDecoration(
                          border: Border.all(
                              color: const Color(0xFFE2E8F0),
                              style: BorderStyle.solid),
                          borderRadius: BorderRadius.circular(12),
                          color: const Color(0xFFF8FAFC),
                        ),
                        child: const Center(
                            child: Icon(Icons.camera_alt_outlined,
                                color: Color(0xFF94A3B8))),
                      ),
                    )
                  else
                    Stack(
                      clipBehavior: Clip.none,
                      children: [
                        Container(
                          height: 80,
                          width: 80,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            image: DecorationImage(
                                image: FileImage(File(image!.path)),
                                fit: BoxFit.cover),
                          ),
                        ),
                        Positioned(
                          right: -10,
                          top: -10,
                          child: InkWell(
                            onTap: () => setModalState(() => image = null),
                            child: Container(
                              decoration: const BoxDecoration(
                                  shape: BoxShape.circle, color: Colors.white),
                              child:
                                  const Icon(Icons.cancel, color: Colors.red),
                            ),
                          ),
                        ),
                      ],
                    ),
                ],
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text('Cancel',
                      style: TextStyle(color: Color(0xFF64748B))),
                ),
                ElevatedButton(
                  onPressed: () async {
                    Navigator.pop(context);
                    setState(() => _isSaving = true);

                    try {
                      if (image != null) {
                        await ApiService.uploadJobCardPhoto(
                            widget.jobCard.id, image!.path, type: 'after');
                      }
                      await _saveChanges('completed');
                    } catch (e) {
                      if (mounted) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                              content: Text(
                                  'Error completing wash. Please try again.')),
                        );
                      }
                      setState(() => _isSaving = false);
                    }
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF10B981),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8)),
                  ),
                  child: const Text('Complete',
                      style: TextStyle(color: Colors.white)),
                ),
              ],
            );
          },
        );
      },
    );
  }
DART;

// Inject it right before the last closing brace.
$content = preg_replace('/}\s*$/', $completeModalStr . "\n}\n", $content);

// Change the button that calls _saveChanges('completed') to call _showCompleteWashModal()
$content = str_replace(
    "_saveChanges('completed');",
    "_showCompleteWashModal();",
    $content
);

file_put_contents($file, $content);
