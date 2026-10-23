// Page-specific behavior for dashboard.html
(function () {
  var btn = document.getElementById('newBookingBtn');
  var form = document.getElementById('newBookingForm');
  if (!btn || !form) return;

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

  var addServiceToggle = document.getElementById('nbAddServiceToggle');
  var addServiceForm = document.getElementById('nbAddServiceForm');
  var newServiceNameInput = document.getElementById('nbNewServiceName');
  var newServicePriceInput = document.getElementById('nbNewServicePrice');
  var newServiceSaveBtn = document.getElementById('nbNewServiceSave');
  var newServiceCancelBtn = document.getElementById('nbNewServiceCancel');

  function closeAddServiceForm() {
    if (addServiceForm) addServiceForm.style.display = 'none';
    if (newServiceNameInput) newServiceNameInput.value = '';
    if (newServicePriceInput) newServicePriceInput.value = '';
  }

  if (addServiceToggle && addServiceForm) {
    addServiceToggle.addEventListener('click', function () {
      var showing = addServiceForm.style.display === 'flex';
      addServiceForm.style.display = showing ? 'none' : 'flex';
      if (!showing && newServiceNameInput) newServiceNameInput.focus();
    });
  }

  if (newServiceCancelBtn) {
    newServiceCancelBtn.addEventListener('click', closeAddServiceForm);
  }

  if (newServiceSaveBtn) {
    newServiceSaveBtn.addEventListener('click', async function () {
      var name = newServiceNameInput ? newServiceNameInput.value.trim() : '';
      var price = newServicePriceInput ? Number(newServicePriceInput.value) : Number.NaN;
      if (!name) {
        alert('Please enter a service name.');
        return;
      }
      if (!Number.isFinite(price) || price < 0) {
        alert('Please enter a valid price.');
        return;
      }
      newServiceSaveBtn.disabled = true;
      try {
        var res = await fetch('/api/services', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ name: name, price: price, duration: 30 })
        });
        if (res.ok) {
          await populateServices(true);
          document.querySelectorAll('input[name="nbServices"]').forEach(function (cb) {
            if (cb.value === name) cb.checked = true;
          });
          renderServiceChips();
          closeAddServiceForm();
          window.SW?.showToast?.('Service added: ' + name);
        } else {
          var err = await res.json();
          alert('Error: ' + (err.message || 'Could not add service.'));
        }
      } catch (e) {
        console.error(e);
        alert('A network error occurred.');
      } finally {
        newServiceSaveBtn.disabled = false;
      }
    });
  }

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
      var res = await fetch('/api/bookings', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          customer: customer,
          phone: phone,
          vehicle: vehicle,
          vehicle_plate: vehiclePlate,
          vehicle_type: vehicleType,
          service: service,
          staff: staff || null,
          bay: bay || null,
          time: time,
          source: source,
          status: status,
          booking_date: bookingDate || null,
          pickup_required: pickupRequired,
          whatsapp_opt_in: whatsappOptIn,
          notes: notes
        })
      });

      if (res.ok) {
        window.SW.closeModal('newBookingModal');
        window.SW.showToast('Booking created for ' + customer);
        
        // Reload dashboard stats to update numbers and chart
        if (window.loadDashboardStats) {
          window.loadDashboardStats();
        }
        form.reset();
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

  var fullscreenButton = document.getElementById('revenueFullscreenBtn');
  if (fullscreenButton) {
    fullscreenButton.addEventListener('click', function () {
      if (document.fullscreenElement) document.exitFullscreen();
      else if (container.requestFullscreen) container.requestFullscreen();
    });
  }
  document.addEventListener('fullscreenchange', function () {
    if (!chart) return;
    if (document.fullscreenElement) {
      setTimeout(function () { chart.reflow(); }, 50);
      return;
    }

    // The browser restores the card size after the fullscreen event. Reapply the
    // normal chart dimensions after that layout pass so the fullscreen width does
    // not remain on the dashboard.
    container.style.height = '220px';
    setTimeout(function () {
      chart.setSize(container.clientWidth, 220, false);
      chart.reflow();
    }, 180);
  });

  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  var WL_CAR_ICON_SVG = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h-2v-6l2-5h9l4 5h1a2 2 0 0 1 2 2v4h-2"></path><circle cx="7" cy="17" r="2"></circle><circle cx="17" cy="17" r="2"></circle><path d="M9 17h6"></path></svg>';

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
        queueContainer.innerHTML = '<div class="dropdown-empty">No cars in queue right now.</div>';
      } else {
        queueContainer.innerHTML = html;
      }

    } catch (err) {
      console.error('Failed to load dashboard queue:', err);
    }
  }

  async function loadDashboardStats() {
    try {
      var res = await fetch('/api/dashboard/stats');
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
})();
