<?php
$file = 'shinewise_manager_app/lib/screens/dashboard_tab.dart';
$content = file_get_contents($file);

$oldCode = <<<DART
                // Branch Dropdown Selector Pill
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                  ),
                  child: DropdownButtonHideUnderline(
                    child: DropdownButton<String>(
                      value: widget.currentBranchCode.isNotEmpty
                          ? widget.currentBranchCode
                          : (_branches.isNotEmpty ? _branches.first.code : null),
                      icon: const Icon(Icons.keyboard_arrow_down_rounded, size: 18, color: Color(0xFF64748B)),
                      isDense: true,
                      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: Color(0xFF334155)),
                      onChanged: (val) {
                        if (val != null) widget.onBranchChanged(val);
                      },
                      items: _branches.isEmpty
                          ? (widget.currentBranchCode.isNotEmpty
                              ? [DropdownMenuItem(value: widget.currentBranchCode, child: Text(widget.currentBranchCode))]
                              : [])
                          : _branches.map((b) {
                              return DropdownMenuItem(
                                value: b.code,
                                child: Text(b.code),
                              );
                            }).toList(),
                    ),
                  ),
                ),
DART;

$newCode = <<<DART
                // Removed Branch Dropdown Selector Pill
DART;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Removed branch dropdown.\n";
