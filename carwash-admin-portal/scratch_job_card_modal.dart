  Future<void> _showStartWashModal() async {
    final picker = ImagePicker();
    XFile? image;

    await showDialog(
      context: context,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return AlertDialog(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              title: const Text('Start Job', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 18)),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text("Add a photo of the vehicle's starting condition before beginning work (optional).", style: TextStyle(fontSize: 14, color: Color(0xFF64748B))),
                  const SizedBox(height: 16),
                  if (image == null)
                    InkWell(
                      onTap: () async {
                        final picked = await picker.pickImage(source: ImageSource.camera);
                        if (picked != null) {
                          setModalState(() => image = picked);
                        }
                      },
                      borderRadius: BorderRadius.circular(12),
                      child: Container(
                        height: 80,
                        width: 80,
                        decoration: BoxDecoration(
                          border: Border.all(color: const Color(0xFFE2E8F0), style: BorderStyle.solid),
                          borderRadius: BorderRadius.circular(12),
                          color: const Color(0xFFF8FAFC),
                        ),
                        child: const Center(child: Icon(Icons.camera_alt_outlined, color: Color(0xFF94A3B8))),
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
                            image: DecorationImage(image: FileImage(File(image!.path)), fit: BoxFit.cover),
                          ),
                        ),
                        Positioned(
                          right: -10,
                          top: -10,
                          child: InkWell(
                            onTap: () => setModalState(() => image = null),
                            child: Container(
                              decoration: const BoxDecoration(shape: BoxShape.circle, color: Colors.white),
                              child: const Icon(Icons.cancel, color: Colors.red),
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
                  child: const Text('Cancel', style: TextStyle(color: Color(0xFF64748B))),
                ),
                ElevatedButton(
                  onPressed: () async {
                    Navigator.pop(context);
                    setState(() => _isSaving = true);
                    
                    try {
                      if (image != null) {
                        await ApiService.uploadJobCardPhoto(widget.jobCard.id, image!.path);
                      }
                      await _saveChanges('inprogress');
                    } catch (e) {
                      if (mounted) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(content: Text(e.toString().replaceAll('Exception: ', '')), backgroundColor: Colors.red),
                        );
                      }
                    } finally {
                      if (mounted) setState(() => _isSaving = false);
                    }
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFFF6B00),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                  ),
                  child: const Text('Start Job', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
                ),
              ],
            );
          },
        );
      },
    );
  }
