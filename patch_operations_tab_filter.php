<?php
$file = 'shinewise_manager_app/lib/screens/operations_tab.dart';
$content = file_get_contents($file);

// Add the state variable if not already there
if (strpos($content, 'bool _showAllJobCards') === false) {
    $content = preg_replace('/(bool _isLoading = true;)/', "$1\n  bool _showAllJobCards = false;", $content);
}

$oldCode = <<<DART
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

$newCode = <<<DART
  Widget _buildJobCardsBoard() {
    final now = DateTime.now();
    final todayStr = "\${now.year}-\${now.month.toString().padLeft(2, '0')}-\${now.day.toString().padLeft(2, '0')}";
    
    final filteredJobCards = _jobCards.where((jc) {
      if (_showAllJobCards) return true;
      if (jc.createdAt != null) {
        if (jc.createdAt!.startsWith(todayStr)) return true;
      }
      if (jc.updatedAt != null) {
        if (jc.updatedAt!.startsWith(todayStr)) return true;
      }
      return false;
    }).toList();

    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.only(left: 16, right: 16, top: 4, bottom: 4),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              const Text('Show All Days', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Color(0xFF64748B))),
              const SizedBox(width: 8),
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
              child: Text('No job cards found for today', style: TextStyle(color: Color(0xFF94A3B8))),
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

$content = str_replace($oldCode, $newCode, $content);

file_put_contents($file, $content);
echo "Patched operations tab.\n";
