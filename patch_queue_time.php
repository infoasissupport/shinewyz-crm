<?php
$file = 'public/js/queue.js';
$content = file_get_contents($file);

$oldCode = <<<JS
      var vehicleLine = [b.vehicle_plate, b.vehicle].filter(Boolean).join(' · ');

      var el = document.createElement('tr');
      el.className = 'wl-row';
      el.setAttribute('data-id', b.id);
      el.setAttribute('data-customer', b.customer);
      el.setAttribute('data-phone', b.phone || '');
      el.setAttribute('data-vehicle', b.vehicle || '');
      el.setAttribute('data-vehicle-plate', b.vehicle_plate || '');
      el.setAttribute('data-service', b.service);
      el.setAttribute('data-branch-id', b.branch_id || '');

      el.innerHTML =
        '<td><div class="wl-cell"><div class="wl-icon">' + WL_CAR_ICON_SVG + '</div><div class="wl-vehicle">' + (vehicleLine ? escapeHtml(vehicleLine) : '—') + '</div></div></td>' +
        '<td><div class="wl-customer">' + escapeHtml(b.customer) + vipBadge + '</div></td>' +
        '<td><div class="wl-service">' + escapeHtml(b.service) + '</div></td>' +
JS;

$newCode = <<<JS
      var vehicleLine = [b.vehicle_plate, b.vehicle].filter(Boolean).join(' · ');
      
      var timeString = '';
      if (b.created_at) {
        var dateObj = new Date(b.created_at);
        if (!isNaN(dateObj.getTime())) {
          timeString = dateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
      }

      var el = document.createElement('tr');
      el.className = 'wl-row';
      el.setAttribute('data-id', b.id);
      el.setAttribute('data-customer', b.customer);
      el.setAttribute('data-phone', b.phone || '');
      el.setAttribute('data-vehicle', b.vehicle || '');
      el.setAttribute('data-vehicle-plate', b.vehicle_plate || '');
      el.setAttribute('data-service', b.service);
      el.setAttribute('data-branch-id', b.branch_id || '');

      el.innerHTML =
        '<td><div class="wl-cell"><div class="wl-icon">' + WL_CAR_ICON_SVG + '</div><div class="wl-vehicle">' + (vehicleLine ? escapeHtml(vehicleLine) : '—') + '</div></div></td>' +
        '<td><div class="wl-customer">' + escapeHtml(b.customer) + vipBadge + '</div></td>' +
        '<td><div class="wl-service">' + escapeHtml(b.service) + (timeString ? '<br><span style="font-size:11px;color:var(--text-3);"><i class="ti ti-clock" style="margin-right:4px;"></i>' + timeString + '</span>' : '') + '</div></td>' +
JS;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched queue.js\n";
