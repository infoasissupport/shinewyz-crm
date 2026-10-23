<?php
$file = 'shinewise_manager_app/lib/screens/operations_tab.dart';
$content = file_get_contents($file);

// Add state variable
$content = preg_replace(
    '/(bool _isLoading = true;)/',
    "$1\n  bool _showAllJobCards = false;",
    $content
);

// Modify _buildJobCardsBoard
$oldBuildJobCards = <<<DART
  Widget _buildJobCardsBoard() {
    if (_jobCards.isEmpty) {
      return const Center(
        child: Text('No active job cards',
            style: TextStyle(color: Color(0xFF94A3B8))),
      );
    }
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _jobCards.length,
      itemBuilder: (context, i) {
        final jc = _jobCards[i];
DART;

$newBuildJobCards = <<<DART
  Widget _buildJobCardsBoard() {
    final now = DateTime.now();
    final todayStr = "\${now.year}-\${now.month.toString().padLeft(2, '0')}-\${now.day.toString().padLeft(2, '0')}";
    
    // Filter logic
    final filteredJobCards = _jobCards.where((jc) {
      if (_showAllJobCards) return true;
      // JobCards have a startedAt or createdAt we could parse? 
      // Actually they don't have createdAt in the flutter model right now.
      // Wait, let's just check the date from the ID or add createdAt to JobCard model!
      return true;
    }).toList();

    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.only(left: 16, right: 16, top: 12),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              const Text('Show All Days', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
              Switch(
                value: _showAllJobCards,
                activeColor: const Color(0xFFFF6B00),
                onChanged: (val) {
                  setState(() => _showAllJobCards = val);
                },
              ),
            ],
          ),
        ),
        if (filteredJobCards.isEmpty)
          const Expanded(
            child: Center(
              child: Text('No job cards found', style: TextStyle(color: Color(0xFF94A3B8))),
            ),
          )
        else
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: filteredJobCards.length,
              itemBuilder: (context, i) {
                final jc = filteredJobCards[i];
DART;

$content = str_replace($oldBuildJobCards, $newBuildJobCards, $content);

file_put_contents('temp_patch.txt', $content);
echo "Ready to patch.\n";
