<?php
$file = 'public/js/dashboard.js';
$content = file_get_contents($file);

$oldCode = <<<JS
      waitingBookings.forEach(function (b) {
        activeCount++;
        var isVip = b.customer.includes('Priya') || b.customer.includes('Thomas');
        var vipBadge = isVip ? ' <i class="ti ti-star-filled" style="font-size:11px;color:var(--accent);margin-left:4px;"></i>' : '';
        var vehicleLine = [b.vehicle_plate, b.vehicle].filter(Boolean).join(' · ');

        waitingHtml +=
          '<div class="wl-row">' +
            '<div class="wl-top">' +
              '<div class="wl-icon">' + WL_CAR_ICON_SVG + '</div>' +
              '<div class="wl-info">' +
                '<div class="wl-customer">' + escapeHtml(b.customer) + vipBadge + '</div>' +
                (vehicleLine ? '<div class="wl-vehicle">' + escapeHtml(vehicleLine) + '</div>' : '') +
                '<div class="wl-service">' + escapeHtml(b.service) + '</div>' +
              '</div>' +
              '<span class="status-pill" style="background:var(--info-bg);color:var(--info-text);">WAITING</span>' +
            '</div>' +
          '</div>';
      });
JS;

$newCode = <<<JS
      waitingBookings.forEach(function (b) {
        activeCount++;
        var isVip = b.customer.includes('Priya') || b.customer.includes('Thomas');
        var vipBadge = isVip ? ' <i class="ti ti-star-filled" style="font-size:11px;color:var(--accent);margin-left:4px;"></i>' : '';
        var vehicleLine = [b.vehicle_plate, b.vehicle].filter(Boolean).join(' · ');
        
        var timeString = '';
        if (b.created_at) {
          var dateObj = new Date(b.created_at);
          if (!isNaN(dateObj.getTime())) {
            timeString = dateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
          }
        }

        waitingHtml +=
          '<div class="wl-row">' +
            '<div class="wl-top">' +
              '<div class="wl-icon">' + WL_CAR_ICON_SVG + '</div>' +
              '<div class="wl-info">' +
                '<div class="wl-customer">' + escapeHtml(b.customer) + vipBadge + '</div>' +
                (vehicleLine ? '<div class="wl-vehicle">' + escapeHtml(vehicleLine) + '</div>' : '') +
                '<div class="wl-service">' + escapeHtml(b.service) + (timeString ? ' • ' + timeString : '') + '</div>' +
              '</div>' +
              '<span class="status-pill" style="background:var(--info-bg);color:var(--info-text);">WAITING</span>' +
            '</div>' +
          '</div>';
      });
JS;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched dashboard.js\n";
