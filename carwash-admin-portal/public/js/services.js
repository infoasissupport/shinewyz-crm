// Page-specific behavior for services.html
(function () {
  var grid = document.getElementById('servicesGrid');
  var addBtn = document.getElementById('addServiceBtn');
  var form = document.getElementById('serviceForm');
  var modalTitle = document.getElementById('serviceModalTitle');
  var submitBtn = document.getElementById('serviceSubmitBtn');
  var deleteBtn = document.getElementById('deleteServiceBtn');
  var emptyState = document.getElementById('servicesEmptyState');
  if (!grid) return;

  var sortSelect = document.getElementById('svcSortSelect');
  var viewButtons = document.querySelectorAll('.svc-view-btn');

  var statTotalServices = document.getElementById('statTotalServices');
  var statTotalDuration = document.getElementById('statTotalDuration');
  var statTotalStaff = document.getElementById('statTotalStaff');

  var editingId = null;
  var currentView = 'grid';

  var SERVICES = [];

  var CARD_THEMES = [
    { icon: 'ti-car', color: 'blue' },
    { icon: 'ti-wash', color: 'purple' },
    { icon: 'ti-sparkles', color: 'orange' },
    { icon: 'ti-wind', color: 'red' },
    { icon: 'ti-engine', color: 'teal' },
    { icon: 'ti-shield', color: 'indigo' },
    { icon: 'ti-crown', color: 'pink' },
    { icon: 'ti-bulb', color: 'yellow' },
    { icon: 'ti-droplet', color: 'cyan' },
    { icon: 'ti-star', color: 'green' }
  ];

  var SORTERS = {
    'price-asc': function (a, b) { return a.price - b.price; },
    'price-desc': function (a, b) { return b.price - a.price; },
    'name-asc': function (a, b) { return a.name.localeCompare(b.name); },
    'duration-asc': function (a, b) { return a.durationMinutes - b.durationMinutes; }
  };

  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function formatPrice(value) {
    return '₹' + Number(value).toLocaleString('en-IN');
  }

  function formatDurationLabel(mins) {
    if (mins >= 60) {
      var hrs = mins / 60;
      return (hrs % 1 === 0 ? hrs : hrs.toFixed(1)) + ' hr';
    }
    return mins + ' min';
  }

  function formatTotalDuration(totalMins) {
    var hrs = Math.floor(totalMins / 60);
    var mins = totalMins % 60;
    if (hrs && mins) return hrs + 'h ' + mins + 'm';
    if (hrs) return hrs + 'h';
    return mins + 'm';
  }

  function findService(id) {
    return SERVICES.filter(function (s) { return s.id === id; })[0];
  }

  function cardThemeFor(svc) {
    var key = String(svc.id != null ? svc.id : svc.name || '');
    var hash = 0;
    for (var i = 0; i < key.length; i++) hash = (hash * 31 + key.charCodeAt(i)) >>> 0;
    return CARD_THEMES[hash % CARD_THEMES.length];
  }

  function getFilteredSorted() {
    var sorter = SORTERS[sortSelect ? sortSelect.value : ''] || SORTERS['price-asc'];
    return SERVICES.slice().sort(sorter);
  }

  function renderStats() {
    var totalStaff = 0, totalMins = 0;
    SERVICES.forEach(function (svc) {
      totalStaff += Number(svc.staff) || 1;
      totalMins += Number(svc.durationMinutes) || 0;
    });
    statTotalServices.textContent = SERVICES.length;
    statTotalDuration.textContent = formatTotalDuration(totalMins);
    statTotalStaff.textContent = totalStaff;
  }

  function cardHtml(svc) {
    var theme = cardThemeFor(svc);
    var staffCount = Number(svc.staff) || 1;
    var isActive = svc.active !== false;
    return (
      '<div class="service-card" data-id="' + svc.id + '">' +
        '<div class="service-card-top">' +
          '<div class="service-icon svc-badge-' + theme.color + '"><i class="ti ' + theme.icon + '"></i></div>' +
          '<div class="service-price">' + formatPrice(svc.price) + '</div>' +
        '</div>' +
        '<div class="service-name-row">' +
          '<span class="service-name">' + escapeHtml(svc.name) + '</span>' +
          '<span class="service-price-inline">' + formatPrice(svc.price) + '</span>' +
        '</div>' +
        '<div class="service-desc">' + escapeHtml(svc.desc) + '</div>' +
        '<div class="service-meta">' +
          '<span><i class="ti ti-hourglass"></i>' + escapeHtml(formatDurationLabel(svc.durationMinutes)) + '</span>' +
          '<span><i class="ti ti-users"></i>' + staffCount + ' Staff</span>' +
          '<span class="service-status ' + (isActive ? 'active' : 'inactive') + '">' + (isActive ? 'Active' : 'Inactive') + '</span>' +
        '</div>' +
        '<div class="service-actions">' +
          '<button type="button" class="btn service-edit-btn" data-action="edit" data-id="' + svc.id + '">' +
            '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>' +
            'Edit' +
          '</button>' +
          '<button type="button" class="btn service-delete-btn" data-action="delete" data-id="' + svc.id + '"><i class="ti ti-x"></i>Delete</button>' +
        '</div>' +
      '</div>'
    );
  }

  function renderGrid() {
    var filtered = getFilteredSorted();

    grid.className = 'svc-grid' + (currentView === 'list' ? ' list-view' : '');
    grid.innerHTML = filtered.map(cardHtml).join('');

    emptyState.style.display = filtered.length === 0 ? '' : 'none';

    renderStats();
  }

  grid.addEventListener('click', function (e) {
    var target = e.target.closest('[data-action]');
    if (!target) return;
    var id = Number(target.getAttribute('data-id'));
    if (target.getAttribute('data-action') === 'edit') openEdit(id);
    if (target.getAttribute('data-action') === 'delete') removeService(id);
  });

  function resetForm() {
    form.reset();
    document.getElementById('svcStaff').value = 1;
    document.getElementById('svcCategory').value = 'Exterior';
  }

  function openCreate() {
    editingId = null;
    modalTitle.textContent = 'Add service';
    submitBtn.innerHTML = '<i class="ti ti-plus"></i>Add service';
    deleteBtn.style.display = 'none';
    resetForm();
    window.SW.openModal('serviceModal');
  }

  function openEdit(id) {
    var svc = findService(id);
    if (!svc) return;
    editingId = id;
    modalTitle.textContent = 'Edit service';
    submitBtn.innerHTML = '<i class="ti ti-check"></i>Save changes';
    deleteBtn.style.display = 'inline-flex';

    document.getElementById('svcName').value = svc.name;
    document.getElementById('svcDesc').value = svc.desc;
    document.getElementById('svcCategory').value = svc.category || 'Other';
    document.getElementById('svcPrice').value = svc.price;
    document.getElementById('svcDuration').value = formatDurationLabel(svc.durationMinutes);
    document.getElementById('svcStaff').value = svc.staff;

    window.SW.openModal('serviceModal');
  }

  async function removeService(id) {
    var svc = findService(id);
    if (!svc) return;
    if (!confirm('Are you sure you want to delete this service?')) return;

    try {
      var res = await fetch('/api/services/' + id, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json' }
      });

      if (res.ok) {
        window.SW.showToast('Service removed: ' + svc.name);
        if (editingId === id) window.SW.closeModal('serviceModal');
        await loadServices();
      } else {
        alert('Could not delete service.');
      }
    } catch (err) {
      console.error(err);
      alert('A network error occurred.');
    }
  }

  addBtn.addEventListener('click', openCreate);

  if (sortSelect) sortSelect.addEventListener('change', renderGrid);

  viewButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      viewButtons.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      currentView = btn.getAttribute('data-view');
      renderGrid();
    });
  });

  function parseDurationToMinutes(str) {
    str = String(str).toLowerCase().trim();
    var num = parseFloat(str) || 0;
    if (str.indexOf('hr') !== -1 || str.indexOf('hour') !== -1) {
      return Math.round(num * 60);
    }
    return Math.round(num);
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    var name = document.getElementById('svcName').value.trim();
    var desc = document.getElementById('svcDesc').value.trim() || 'No description yet.';
    var category = document.getElementById('svcCategory').value;
    var price = Number(document.getElementById('svcPrice').value);
    var duration = document.getElementById('svcDuration').value.trim();
    var staff = Number(document.getElementById('svcStaff').value) || 1;

    var minutes = parseDurationToMinutes(duration);

    var payload = {
      name: name,
      desc: desc,
      category: category,
      price: price,
      duration: minutes,
      staff: staff
    };

    try {
      var url = editingId ? '/api/services/' + editingId : '/api/services';
      var method = editingId ? 'PUT' : 'POST';

      var res = await fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      if (res.ok) {
        window.SW.showToast(editingId ? 'Updated ' + name : 'Service added: ' + name);
        window.SW.closeModal('serviceModal');
        await loadServices();
      } else {
        var err = await res.json();
        alert('Error: ' + (err.message || 'Could not save service.'));
      }
    } catch (err) {
      console.error(err);
      alert('A network error occurred.');
    }
  });

  deleteBtn.addEventListener('click', function () {
    if (!editingId) return;
    removeService(editingId);
  });

  async function loadServices() {
    try {
      var res = await fetch('/api/services');
      if (res.ok) {
        SERVICES = await res.json();
        SERVICES.forEach(function (svc) {
          svc.durationMinutes = Number(svc.duration) || 0;
          svc.staff = Number(svc.staff) || 1;
        });
        renderGrid();
      }
    } catch (err) {
      console.error('Failed to load services:', err);
    }
  }
  loadServices();
})();
