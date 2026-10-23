<?php
$file = 'resources/views/staff.blade.php';
$content = file_get_contents($file);

// Remove the wrongly appended string at the end of the file
$appended = <<<HTML
<div class="modal-overlay" id="calendarModal">
  <div class="modal" style="max-width: 600px;">
    <div class="modal-header">
      <div class="modal-title" id="calendarModalTitle">Monthly Attendance</div>
      <button class="icon-btn" onclick="window.SW.closeModal('calendarModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <button class="icon-btn" id="prevMonthBtn"><i class="ti ti-chevron-left"></i></button>
        <div id="calendarMonthDisplay" style="font-weight:600; font-size:1.1rem;"></div>
        <button class="icon-btn" id="nextMonthBtn"><i class="ti ti-chevron-right"></i></button>
      </div>
      <div id="calendarGrid" style="display:grid; grid-template-columns:repeat(7, 1fr); gap:4px; text-align:center;">
        <!-- Days of week -->
        <div style="font-size:0.75rem; color:#667085; font-weight:600; padding-bottom:8px;">Sun</div>
        <div style="font-size:0.75rem; color:#667085; font-weight:600; padding-bottom:8px;">Mon</div>
        <div style="font-size:0.75rem; color:#667085; font-weight:600; padding-bottom:8px;">Tue</div>
        <div style="font-size:0.75rem; color:#667085; font-weight:600; padding-bottom:8px;">Wed</div>
        <div style="font-size:0.75rem; color:#667085; font-weight:600; padding-bottom:8px;">Thu</div>
        <div style="font-size:0.75rem; color:#667085; font-weight:600; padding-bottom:8px;">Fri</div>
        <div style="font-size:0.75rem; color:#667085; font-weight:600; padding-bottom:8px;">Sat</div>
      </div>
      <div id="calendarDays" style="display:grid; grid-template-columns:repeat(7, 1fr); gap:4px; text-align:center;">
        <!-- Calendar days injected here -->
      </div>
      <div id="calendarLoading" style="display:none; text-align:center; padding:2rem; color:#667085;">Loading attendance data...</div>
    </div>
  </div>
</div>
HTML;

$content = str_replace($appended, "", $content);
$content = trim($content);

// Now insert it before the first @endsection after attendanceForm
$pos = strpos($content, "</form>\n  </div>\n</div>\n@endsection");
if ($pos !== false) {
    $content = substr_replace($content, "</form>\n  </div>\n</div>\n\n" . $appended . "\n@endsection", $pos, strlen("</form>\n  </div>\n</div>\n@endsection"));
    file_put_contents($file, $content);
    echo "Fixed blade placement.\n";
} else {
    echo "Could not find placement anchor.\n";
}
