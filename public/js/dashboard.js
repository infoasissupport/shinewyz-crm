// Page-specific behavior for dashboard.html
(function () {
  var btn = document.getElementById('newBookingBtn');
  var form = document.getElementById('newBookingForm');
  if (!btn || !form) return;

  window.nbSelectedPhotos = [];
  var nbPhotosPreview = document.getElementById('nbPhotosPreview');
  
  function renderNbPhotosPreview() {
    if (!nbPhotosPreview) return;
    nbPhotosPreview.innerHTML = '';
    window.nbSelectedPhotos.forEach(function(file) {
      var reader = new FileReader();
      reader.onload = function(evt) {
        var div = document.createElement('div');
        div.className = 'photo-ph';
        div.style.backgroundImage = 'url(' + evt.target.result + ')';
        div.style.backgroundSize = 'cover';
        div.style.backgroundPosition = 'center';
        nbPhotosPreview.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
  }

  function handleNbPhotoPick(capture) {
    var input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    if (capture) {
      input.capture = 'environment';
    } else {
      input.multiple = true;
    }
    input.style.display = 'none';
    document.body.appendChild(input);
    input.onchange = async function () {
      var files = input.files;
      if (files && files.length > 0) {
        for (var i = 0; i < files.length; i++) {
          await new Promise(function(resolve) {
            var reader = new FileReader();
            reader.readAsDataURL(files[i]);
            reader.onload = function(event) {
              var img = new Image();
              img.src = event.target.result;
              img.onload = function() {
                var canvas = document.createElement('canvas');
                var max_size = 1200;
                var width = img.width;
                var height = img.height;
                if (width > height) {
                  if (width > max_size) { height *= max_size / width; width = max_size; }
                } else {
                  if (height > max_size) { width *= max_size / height; height = max_size; }
                }
                canvas.width = width;
                canvas.height = height;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                canvas.toBlob(function(blob) {
                  window.nbSelectedPhotos.push(new File([blob], 'photo.jpg', { type: 'image/jpeg' }));
                  resolve();
                }, 'image/jpeg', 0.85);
              };
            };
          });
        }
        renderNbPhotosPreview();
      }
      document.body.removeChild(input);
    };
    input.click();
  }

  var btnGal = document.getElementById('nbAddBeforePhotoGallery');
  var btnCam = document.getElementById('nbAddBeforePhotoCamera');
  if (btnGal) btnGal.addEventListener('click', function() { handleNbPhotoPick(false); });
  if (btnCam) btnCam.addEventListener('click', function() { handleNbPhotoPick(true); });

  async function populateServices(preserveSelection) {
    var container = document.getElementById('nbServicesContainer');
    if (!container) return;
    var previouslyChecked = [];
    if (preserveSelection) {
      document.querySelectorAll('input[name="nbServices"]:checked').forEach(function (cb) {
        previouslyChecked.push(cb.value);
      });
    }
    try {
      var res = await fetch('/api/services');
      if (res.ok) {
        var services = await res.json();
        container.innerHTML = '';
        services.forEach(function (s) {
          var label = document.createElement('label');
          label.className = 'svc-option';
          label.setAttribute('data-name', s.name.toLowerCase());

          var checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.name = 'nbServices';
          checkbox.value = s.name;
          checkbox.checked = previouslyChecked.includes(s.name);

          label.appendChild(checkbox);
          label.appendChild(document.createTextNode(s.name + ' (₹' + s.price + ')'));
          container.appendChild(label);
        });
      }
    } catch (e) {
      console.error(e);
    }
    renderServiceChips();
    applyServiceFilter(document.getElementById('nbServiceSearch') ? document.getElementById('nbServiceSearch').value : '');
  }

  function renderServiceChips() {
    var chipList = document.getElementById('nbSvcChips');
    if (!chipList) return;
    var checked = Array.prototype.slice.call(document.querySelectorAll('input[name="nbServices"]:checked'));
    if (checked.length === 0) {
      chipList.innerHTML = '<span class="svc-placeholder" id="nbSvcPlaceholder">Search and select services…</span>';
      return;
    }
    chipList.innerHTML = checked.map(function (cb) {
      return '<span class="svc-chip" data-service="' + cb.value.replace(/"/g, '&quot;') + '">' + cb.value + '<span class="svc-chip-remove" title="Remove">&times;</span></span>';
    }).join('');
  }

  function applyServiceFilter(query) {
    var normalized = (query || '').trim().toLowerCase();
    var emptyState = document.getElementById('nbSvcEmpty');
    var options = document.querySelectorAll('#nbServicesContainer .svc-option');
    var visibleCount = 0;
    options.forEach(function (opt) {
      var matches = !normalized || opt.getAttribute('data-name').indexOf(normalized) !== -1;
      opt.classList.toggle('svc-option-hidden', !matches);
      if (matches) visibleCount++;
    });
    if (emptyState) emptyState.style.display = (normalized && visibleCount === 0) ? 'block' : 'none';
  }

  var svcSelect = document.getElementById('nbSvcSelect');
  var svcControl = document.getElementById('nbSvcControl');
  var svcDropdown = document.getElementById('nbSvcDropdown');
  var svcSearchInput = document.getElementById('nbServiceSearch');
  var svcServicesContainer = document.getElementById('nbServicesContainer');
  var svcChipsEl = document.getElementById('nbSvcChips');

  if (svcControl && svcDropdown) {
    svcControl.addEventListener('click', function () {
      var willOpen = !svcDropdown.classList.contains('open');
      if (willOpen) {
        svcDropdown.classList.add('open');
        if (svcSearchInput) svcSearchInput.focus();
      } else {
        svcDropdown.classList.remove('open');
        if (svcSearchInput) svcSearchInput.value = '';
        applyServiceFilter('');
      }
    });
  }

  if (svcSearchInput) {
    svcSearchInput.addEventListener('input', function () {
      applyServiceFilter(svcSearchInput.value);
    });
  }

  if (svcServicesContainer) {
    svcServicesContainer.addEventListener('change', function (e) {
      if (e.target && e.target.name === 'nbServices') renderServiceChips();
    });
  }

  if (svcChipsEl) {
    svcChipsEl.addEventListener('click', function (e) {
      var chip = e.target.closest('.svc-chip-remove');
      if (!chip) return;
      var serviceName = chip.parentElement.getAttribute('data-service');
      var cb = document.querySelector('input[name="nbServices"][value="' + CSS.escape(serviceName) + '"]');
      if (cb) cb.checked = false;
      renderServiceChips();
    });
  }

  document.addEventListener('click', function (e) {
    if (svcSelect && svcDropdown && svcDropdown.classList.contains('open') && !svcSelect.contains(e.target)) {
      svcDropdown.classList.remove('open');
      if (svcSearchInput) svcSearchInput.value = '';
      applyServiceFilter('');
    }
  });

  async function populateStaffDropdown() {
    var dropdown = document.getElementById('nbStaff');
    if (!dropdown) return;
    try {
      var res = await fetch('/api/staff');
      if (!res.ok) return;
      var staff = await res.json();
      dropdown.innerHTML = '<option value="">Select staff</option>';
      staff.forEach(function (st) {
        var opt = document.createElement('option');
        opt.value = st.name;
        opt.textContent = st.name;
        dropdown.appendChild(opt);
      });
    } catch (e) {
      console.error(e);
    }
  }

  btn.addEventListener('click', async function () {
    form.reset();
    var loyaltyEl = document.getElementById('nbVehicleLoyalty');
    if (loyaltyEl) {
      loyaltyEl.textContent = '';
      loyaltyEl.style.display = 'none';
    }
    if (document.getElementById('nbPhotosPreview')) {
      document.getElementById('nbPhotosPreview').innerHTML = '';
    }
    window.nbSelectedPhotos = [];
    var dateInput = document.getElementById('nbDate');
    if (dateInput) dateInput.value = new Date().toISOString().slice(0, 10);

    var timeInput = document.getElementById('nbTime');
    if (timeInput) {
      var now = new Date();
      var hrs = now.getHours();
      var mins = now.getMinutes();
      timeInput.value = (hrs < 10 ? '0' + hrs : hrs) + ':' + (mins < 10 ? '0' + mins : mins);
    }

    var pickupInput = document.getElementById('nbPickup');
    if (pickupInput) pickupInput.checked = false;
    var waInput = document.getElementById('nbWhatsappOptIn');
    if (waInput) waInput.checked = true;
    var vehicleTypeInput = document.getElementById('nbVehicleType');
    if (vehicleTypeInput) vehicleTypeInput.value = 'sedan';
    await populateStaffDropdown();
    await populateServices();
    window.SW.openModal('newBookingModal');
  });

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    var customer = document.getElementById('nbCustomer').value.trim();
    var phone = document.getElementById('nbPhone') ? document.getElementById('nbPhone').value.trim() : '';
    var vehicle = document.getElementById('nbVehicle') ? document.getElementById('nbVehicle').value.trim() : '';
    var vehiclePlate = document.getElementById('nbVehiclePlate') ? document.getElementById('nbVehiclePlate').value.trim() : '';
    var servicesList = [];
    document.querySelectorAll('input[name="nbServices"]:checked').forEach(function (cb) {
      servicesList.push(cb.value);
    });
    if (servicesList.length === 0) {
      alert('Please select at least one service.');
      return;
    }
    var service = servicesList.join(', ');
    var vehicleType = document.getElementById('nbVehicleType') ? document.getElementById('nbVehicleType').value : 'sedan';
    var staff = document.getElementById('nbStaff') ? document.getElementById('nbStaff').value : '';
    var bay = document.getElementById('nbBay') ? document.getElementById('nbBay').value : '';
    var time = document.getElementById('nbTime').value;
    var source = document.getElementById('nbSource').value || 'walkin';
    var bookingDate = document.getElementById('nbDate') ? document.getElementById('nbDate').value : '';
    var pickupRequired = document.getElementById('nbPickup') ? document.getElementById('nbPickup').checked : false;
    var whatsappOptIn = document.getElementById('nbWhatsappOptIn') ? document.getElementById('nbWhatsappOptIn').checked : true;
    var notes = document.getElementById('nbNotes') ? document.getElementById('nbNotes').value.trim() : '';
    var status = 'confirmed'; // Default status

    try {
      var formData = new FormData();
      formData.append('customer', customer);
      if (phone) formData.append('phone', phone);
      if (vehicle) formData.append('vehicle', vehicle);
      if (vehiclePlate) formData.append('vehicle_plate', vehiclePlate);
      if (vehicleType) formData.append('vehicle_type', vehicleType);
      formData.append('service', service);
      if (staff) formData.append('staff', staff);
      if (bay) formData.append('bay', bay);
      formData.append('time', time);
      formData.append('source', source);
      formData.append('status', status);
      if (bookingDate) formData.append('booking_date', bookingDate);
      formData.append('pickup_required', pickupRequired ? '1' : '0');
      formData.append('whatsapp_opt_in', whatsappOptIn ? '1' : '0');
      if (notes) formData.append('notes', notes);

      if (window.nbSelectedPhotos && window.nbSelectedPhotos.length > 0) {
        for (var i = 0; i < window.nbSelectedPhotos.length; i++) {
          formData.append('photos_before[]', window.nbSelectedPhotos[i]);
        }
      }

      var res = await fetch('/api/bookings', {
        method: 'POST',
        headers: {
          'Accept': 'application/json'
        },
        body: formData
      });

      if (res.ok) {
        window.SW.closeModal('newBookingModal');
        window.SW.showToast('Booking created for ' + customer);
        
        // Reload dashboard stats to update numbers and chart
        if (window.loadDashboardStats) {
          window.loadDashboardStats();
        }
        form.reset();
    var loyaltyEl = document.getElementById('nbVehicleLoyalty');
    if (loyaltyEl) {
      loyaltyEl.textContent = '';
      loyaltyEl.style.display = 'none';
    }
    if (document.getElementById('nbPhotosPreview')) {
      document.getElementById('nbPhotosPreview').innerHTML = '';
    }
    window.nbSelectedPhotos = [];
      } else {
        var err = await res.json();
        alert('Error: ' + (err.message || 'Could not create booking.'));
      }
    } catch (err) {
      console.error(err);
      alert('A connection error occurred.');
    }
  });
})();

(function () {
  var container = document.getElementById('revenueChart');
  if (!container || !window.Highcharts) return;

  function themeColor(varName) {
    return getComputedStyle(document.documentElement).getPropertyValue(varName).trim();
  }

  function trimmed(value, decimals) {
    var factor = Math.pow(10, decimals);
    return String(Math.round(value * factor) / factor);
  }

  function formatRupees(value) {
    if (value >= 100000) return '₹' + trimmed(value / 100000, 2) + 'L';
    if (value >= 1000) return '₹' + trimmed(value / 1000, 1) + 'k';
    return '₹' + value;
  }

  var accent = themeColor('--accent');
  var accentDim = themeColor('--accent-dim');
  var borderColor = themeColor('--border');
  var textColor = themeColor('--text-3');
  var text1 = themeColor('--text-1');
  var surface2 = themeColor('--surface-2');

  var chart;
  var selectedChartType = 'column';
  var latestCategories = [];
  var latestData = [];

  function renderChart(categories, dataSeries) {
    latestCategories = categories;
    latestData = dataSeries;
    var chartColors = ['#8b5cf6', '#3b82f6', '#14b8a6', '#22c55e', '#f59e0b', '#f97316', '#ec4899'];
    var formattedData = dataSeries.map(function (value, i) {
      return { y: value, color: chartColors[i % chartColors.length] };
    });

    if (chart) {
      chart.destroy();
    }

    chart = Highcharts.chart('revenueChart', {
      chart: {
        type: selectedChartType,
        backgroundColor: 'transparent',
        style: { fontFamily: 'Inter, -apple-system, sans-serif' },
        spacing: [16, 8, 8, 8]
      },
      title: { text: null },
      credits: { enabled: false },
      exporting: { enabled: false },
      legend: { enabled: false },
      xAxis: {
        categories: categories,
        lineColor: '#ddd',
        tickColor: '#ddd',
        labels: { style: { color: textColor, fontSize: '10.5px' } }
      },
      yAxis: {
        title: { text: null },
        gridLineColor: '#ddd',
        labels: {
          style: { color: textColor, fontSize: '10.5px' },
          formatter: function () { return formatRupees(this.value); }
        }
      },
      tooltip: {
        backgroundColor: surface2,
        borderColor: borderColor,
        borderRadius: 8,
        style: { color: text1, fontSize: '12.5px' },
        formatter: function () {
          return '<b>' + this.key + '</b><br>' + formatRupees(this.y);
        }
      },
      plotOptions: {
        column: {
          borderRadius: 4,
          borderWidth: 0,
          pointPadding: 0.15,
          groupPadding: 0.08,
          states: { hover: { brightness: 0.15 } }
        }
      },
      series: [{ data: formattedData }]
    });
  }

  var chartTypeSelect = document.getElementById('revenueChartType');
  if (chartTypeSelect) {
    chartTypeSelect.addEventListener('change', function () {
      selectedChartType = this.value;
      var chartTypeLabel = document.getElementById('revenueChartTypeLabel');
      if (chartTypeLabel) chartTypeLabel.textContent = this.options[this.selectedIndex].text;
      if (latestCategories.length) renderChart(latestCategories, latestData);
    });
  }

  var rangeSelect = document.getElementById('revenueRange');
  if (rangeSelect) {
    rangeSelect.addEventListener('change', function () {
      var text = this.options[this.selectedIndex].text;
      var rangeLabel = document.getElementById('revenueRangeLabel');
      var rangeTitle = document.getElementById('revenueRangeTitle');
      if (rangeLabel) rangeLabel.textContent = text;
      if (rangeTitle) rangeTitle.textContent = text;
      if (window.loadDashboardStats) window.loadDashboardStats();
    });
  }


  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  var WL_CAR_ICON_SVG = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h-2v-6l2-5h9l4 5h1a2 2 0 0 1 2 2v4h-2"></path><circle cx="7" cy="17" r="2"></circle><circle cx="17" cy="17" r="2"></circle><path d="M9 17h6"></path></svg>';

  // Empty-state markup lives in dashboard.blade.php; keep a copy to restore when the queue empties
  var queueRowsEl = document.getElementById('dashboardQueueRows');
  var QUEUE_EMPTY_HTML = queueRowsEl ? queueRowsEl.innerHTML : '';

  async function loadLiveQueue() {
    var queueContainer = document.getElementById('dashboardQueueRows');
    if (!queueContainer) return;

    try {
      var jcRes = await fetch('/api/jobcards');
      var bkRes = await fetch('/api/bookings');
      if (!jcRes.ok || !bkRes.ok) {
        var dashQueueBody = document.getElementById('dashQueueBody');
        var dashActiveBody = document.getElementById('dashActiveBody');
        if (dashQueueBody) dashQueueBody.innerHTML = '<tr><td colspan="4" style="color:red;text-align:center;">Failed to load data (Database down?)</td></tr>';
        if (dashActiveBody) dashActiveBody.innerHTML = '<tr><td colspan="4" style="color:red;text-align:center;">Failed to load data (Database down?)</td></tr>';
        return;
      }

      var jobcards = await jcRes.json();
      var bookings = await bkRes.json();

      var waitingHtml = '';
      var startingHtml = '';
      var washingHtml = '';
      var activeCount = 0;

      // 1. Active job cards — split into "Starting" and "Washing" groups
      jobcards.forEach(function (card) {
        if (card.stage !== 'completed' && card.stage !== 'cancelled') {
          activeCount++;
          var bayName = card.bay || 'Unassigned Bay';
          var isWashing = card.stage === 'inprogress';
          var statusStyle = isWashing
            ? 'background:var(--success-bg);color:var(--success-text);'
            : 'background:var(--warn-bg);color:var(--warn-text);';
          var stageLabel = isWashing ? 'Washing' : 'Starting';

          var rowHtml =
            '<div class="wl-row">' +
              '<div class="wl-top">' +
                '<div class="wl-icon" style="' + statusStyle + '">' + WL_CAR_ICON_SVG + '</div>' +
                '<div class="wl-info">' +
                  '<div class="wl-customer">' + escapeHtml(bayName) + '</div>' +
                  '<div class="wl-vehicle">' + escapeHtml(card.vehicle) + '</div>' +
                  '<div class="wl-service">' + escapeHtml(card.staff || 'No staff') + '</div>' +
                '</div>' +
                '<span class="status-pill" style="' + statusStyle + '">' + stageLabel.toUpperCase() + '</span>' +
              '</div>' +
            '</div>';

          if (isWashing) washingHtml += rowHtml;
          else startingHtml += rowHtml;
        }
      });

      // 2. Waitlist
      var waitingBookings = bookings.filter(function (b) {
        return b.status === 'waiting' || b.status === 'confirmed';
      });

      waitingBookings.forEach(function (b) {
        activeCount++;
        var isVip = b.customer.includes('Priya') || b.customer.includes('Thomas');
        var vipBadge = isVip ? ' <i class="ti ti-star-filled" style="font-size:11px;color:var(--accent);margin-left:4px;"></i>' : '';
        var vehicleLine = [b.vehicle_plate, b.vehicle].filter(Boolean).join(' · ');
        
        var timeString = '';
        if (b.created_at) {
          var dateObj = new Date(b.created_at);
          if (!isNaN(dateObj.getTime())) {
            timeString = dateObj.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });
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

      // Order: Waiting, then Starting, then Washing
      var html = waitingHtml + startingHtml + washingHtml;

      if (activeCount === 0) {
        queueContainer.innerHTML = QUEUE_EMPTY_HTML;
      } else {
        queueContainer.innerHTML = html;
      }

    } catch (err) {
      console.error('Failed to load dashboard queue:', err);
    }
  }

  async function loadDashboardStats() {
    try {
      var rangeSelect = document.getElementById('revenueRange');
      var days = rangeSelect ? rangeSelect.value : '7';
      var res = await fetch('/api/dashboard/stats?days=' + encodeURIComponent(days));
      if (!res.ok) return;
      var stats = await res.json();

      // Update KPI cards with real computed data
      var kpiBookings = document.getElementById('kpiTodayBookings');
      if (kpiBookings) kpiBookings.textContent = stats.todayBookings;

      var kpiWaiting = document.getElementById('kpiCarsWaiting');
      if (kpiWaiting) kpiWaiting.textContent = stats.carsWaiting;

      var kpiCompleted = document.getElementById('kpiCompletedToday');
      if (kpiCompleted) kpiCompleted.textContent = stats.completedToday;

      var kpiRevenue = document.getElementById('kpiRevenueToday');
      if (kpiRevenue) kpiRevenue.textContent = formatRupees(stats.revenueToday);

      var kpiWeekly = document.getElementById('kpiRevenueWeekly');
      if (kpiWeekly) kpiWeekly.textContent = formatRupees(stats.revenueWeekly);

      var kpiMonthly = document.getElementById('kpiRevenueMonthly');
      if (kpiMonthly) kpiMonthly.textContent = formatRupees(stats.revenueMonthly);

      var kpiPending = document.getElementById('kpiPendingPayments');
      if (kpiPending) kpiPending.textContent = formatRupees(stats.pendingPayments);

      var pendingDelta = document.getElementById('kpiPendingDeltaText');
      if (pendingDelta) pendingDelta.textContent = stats.pendingCount + ' invoices';

      // Render Highcharts chart with real revenue data
      renderChart(stats.revenueChart.categories, stats.revenueChart.data);

    } catch (err) {
      console.error('Failed to load dashboard stats:', err);
    }
  }

  window.loadDashboardStats = loadDashboardStats;
  loadDashboardStats();
  loadLiveQueue();
  setInterval(loadLiveQueue, 5000);
  var nbVehiclePlate = document.getElementById('nbVehiclePlate');
  var nbVehicleLoyalty = document.getElementById('nbVehicleLoyalty');
  if (nbVehiclePlate && nbVehicleLoyalty) {
    var nbLoyaltyTimeout;
    nbVehiclePlate.addEventListener('input', function() {
      clearTimeout(nbLoyaltyTimeout);
      nbVehicleLoyalty.style.color = '#10B981';
      nbVehicleLoyalty.style.display = 'none';
      var val = nbVehiclePlate.value.trim();
      if (val.length < 3) return;
      
      nbLoyaltyTimeout = setTimeout(async function() {
        try {
          var res = await fetch('/api/loyalty?vehicle=' + encodeURIComponent(val));
          if (res.ok) {
            var data = await res.json();
            if (data.count > 0) {
              nbVehicleLoyalty.textContent = '🌟 Loyalty: Washed ' + data.count + ' times previously';
              nbVehicleLoyalty.style.display = 'block';
            } else {
              nbVehicleLoyalty.textContent = 'New Vehicle';
              nbVehicleLoyalty.style.color = '#64748B';
              nbVehicleLoyalty.style.display = 'block';
            }
          }
        } catch (e) {}
      }, 600);
    });
  }

  // Autocomplete logic for New Booking Customer/Phone
  var CUSTOMERS_DATA = [];
  var hasFetchedCustomers = false;
  
  async function fetchCustomersForAutocomplete() {
    if (hasFetchedCustomers) return;
    try {
      var res = await fetch('/api/customers');
      if (res.ok) {
        CUSTOMERS_DATA = await res.json();
        hasFetchedCustomers = true;
      }
    } catch(e) {}
  }

  var nbCustomerInput = document.getElementById('nbCustomer');
  var nbPhoneInput = document.getElementById('nbPhone');
  var nbCustomerDropdown = document.getElementById('nbCustomerDropdown');
  var nbPhoneDropdown = document.getElementById('nbPhoneDropdown');

  function getMatches(query) {
    if (!query) return [];
    var q = query.toLowerCase();
    var matches = [];
    CUSTOMERS_DATA.forEach(function(c) {
      if ((c.name && c.name.toLowerCase().includes(q)) || (c.phone && c.phone.includes(q))) {
        if (c.vehicles && c.vehicles.length > 0) {
          c.vehicles.forEach(function(v) {
            matches.push({ customer: c, vehicle: v });
          });
        } else {
          matches.push({ customer: c, vehicle: null });
        }
      }
    });
    return matches.slice(0, 10);
  }

  function renderDropdown(dropdown, matches, inputEl) {
    if (matches.length === 0) {
      dropdown.style.display = 'none';
      return;
    }
    dropdown.innerHTML = '';
    matches.forEach(function(m) {
      var item = document.createElement('div');
      item.className = 'autocomplete-item';
      
      var nameHtml = '<div class="autocomplete-item-name">' + escapeHtml(m.customer.name) + ' (' + escapeHtml(m.customer.phone) + ')</div>';
      var descHtml = '';
      if (m.vehicle) {
        descHtml = '<div class="autocomplete-item-desc">Vehicle: ' + escapeHtml(m.vehicle.brand || 'Car') + ' - ' + escapeHtml(m.vehicle.plate || '') + '</div>';
      }
      item.innerHTML = nameHtml + descHtml;
      
      item.addEventListener('click', function() {
        if (nbCustomerInput) nbCustomerInput.value = m.customer.name || '';
        if (nbPhoneInput) nbPhoneInput.value = m.customer.phone || '';
        if (m.vehicle) {
          var nbVehicleInput = document.getElementById('nbVehicle');
          var nbVehiclePlateInput = document.getElementById('nbVehiclePlate');
          var nbVehicleTypeInput = document.getElementById('nbVehicleType');
          
          if (nbVehicleInput) nbVehicleInput.value = m.vehicle.brand || '';
          if (nbVehiclePlateInput) nbVehiclePlateInput.value = m.vehicle.plate || '';
          if (nbVehicleTypeInput && m.vehicle.type) {
             nbVehicleTypeInput.value = m.vehicle.type.toLowerCase();
          }
        }
        dropdown.style.display = 'none';
        
        var nbVehiclePlateInput = document.getElementById('nbVehiclePlate');
        if (nbVehiclePlateInput) {
            nbVehiclePlateInput.dispatchEvent(new Event('input'));
        }
      });
      
      dropdown.appendChild(item);
    });
    dropdown.style.display = 'block';
  }

  function handleInput(e, dropdown) {
    fetchCustomersForAutocomplete();
    var query = e.target.value.trim();
    if (query.length < 2) {
      dropdown.style.display = 'none';
      return;
    }
    var matches = getMatches(query);
    renderDropdown(dropdown, matches, e.target);
  }

  if (nbCustomerInput) {
    nbCustomerInput.addEventListener('input', function(e) { handleInput(e, nbCustomerDropdown); });
    nbCustomerInput.addEventListener('focus', function(e) { handleInput(e, nbCustomerDropdown); });
  }
  if (nbPhoneInput) {
    nbPhoneInput.addEventListener('input', function(e) { handleInput(e, nbPhoneDropdown); });
    nbPhoneInput.addEventListener('focus', function(e) { handleInput(e, nbPhoneDropdown); });
  }

  document.addEventListener('click', function(e) {
    if (nbCustomerInput && !nbCustomerInput.contains(e.target) && !nbCustomerDropdown.contains(e.target)) {
      nbCustomerDropdown.style.display = 'none';
    }
    if (nbPhoneInput && !nbPhoneInput.contains(e.target) && !nbPhoneDropdown.contains(e.target)) {
      nbPhoneDropdown.style.display = 'none';
    }
  });

})();
// KPI cards open the page behind each number (click, Enter or Space)
(function () {
  document.querySelectorAll('#dashboard .dash-stat-card[data-href]').forEach(function (card) {
    function go() { window.location.href = card.dataset.href; }
    card.addEventListener('click', go);
    card.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); go(); }
    });
  });
})();
