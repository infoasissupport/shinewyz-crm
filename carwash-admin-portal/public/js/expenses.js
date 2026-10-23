(function () {
  var tableBody = document.getElementById('expTableBody');
  var emptyState = document.getElementById('expEmptyState');
  var searchInput = document.getElementById('expSearchInput');
  var categoryFilter = document.getElementById('expCategoryFilter');
  var sortableHeaders = document.querySelectorAll('.exp-sortable');
  var prevBtn = document.getElementById('expPrevBtn');
  var nextBtn = document.getElementById('expNextBtn');
  var pageInfo = document.getElementById('expPageInfo');
  var pageNumbers = document.getElementById('expPageNumbers');
  var newBtn = document.getElementById('newExpenseBtn');
  var modal = document.getElementById('expenseModal');
  var modalTitle = document.getElementById('expenseModalTitle');
  var submitBtn = document.getElementById('expenseSubmitBtn');
  var form = document.getElementById('expenseForm');

  var dateRangeDropdown = document.getElementById('expDateRangeDropdown');
  var dateRangeBtn = document.getElementById('expDateRangeBtn');
  var dateRangeLabel = document.getElementById('expDateRangeLabel');
  var dateRangeMenu = document.getElementById('expDateRangeMenu');
  var dateFromInput = document.getElementById('expDateFrom');
  var dateToInput = document.getElementById('expDateTo');
  var dateApplyBtn = document.getElementById('expDateApplyBtn');

  var categorySelect = document.getElementById('expCategory');
  var branchSelect = document.getElementById('expBranch');
  var staffRow = document.getElementById('expStaffRow');
  var staffSelect = document.getElementById('expStaff');
  var inventoryRow = document.getElementById('expInventoryRow');
  var inventorySelect = document.getElementById('expInventory');
  var nameInput = document.getElementById('expName');
  var amountInput = document.getElementById('expAmount');
  var dateInput = document.getElementById('expDate');
  var notesInput = document.getElementById('expNotes');

  if (!tableBody) return;

  var PAGE_SIZE = 5;
  var activeFilter = 'all';
  var currentPage = 1;
  var editingId = null;
  var sortKey = 'expense_date';
  var sortDir = 'desc';

  var EXPENSES = [];
  var BRANCHES = [];
  var STAFF = [];
  var INVENTORY_ITEMS = [];

  var CATEGORY_META = {
    emi: { label: 'EMI', icon: 'ti-credit-card', bg: '#EEF2FF', color: '#4338CA' },
    rent: { label: 'Rent', icon: 'ti-building-store', bg: '#EFF6FF', color: '#1D4ED8' },
    staff_salary: { label: 'Staff Salary', icon: 'ti-users', bg: '#ECFDF5', color: '#15803D' },
    inventory: { label: 'Inventory', icon: 'ti-package', bg: '#FFF7ED', color: '#C2410C' },
    utilities: { label: 'Utilities', icon: 'ti-bulb', bg: '#F5F3FF', color: '#6D28D9' },
    other: { label: 'Other', icon: 'ti-dots', bg: '#F1F5F9', color: '#475569' }
  };

  var TRASH_SVG = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6M14 11v6"></path><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>';
  var PENCIL_SVG = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>';

  function categoryMeta(cat) {
    return CATEGORY_META[cat] || CATEGORY_META.other;
  }

  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function findExpense(id) {
    return EXPENSES.filter(function (e) { return e.id === id; })[0];
  }

  function formatMoney(amount) {
    return '₹' + Number(amount || 0).toLocaleString('en-IN');
  }

  function parseExpenseDate(e) {
    var d = new Date(e.expense_date);
    return isNaN(d.getTime()) ? null : d;
  }

  function matchesFilter(expense) {
    return activeFilter === 'all' || expense.category === activeFilter;
  }

  function matchesSearch(expense, query) {
    if (!query) return true;
    var categoryLabel = categoryMeta(expense.category).label;
    var haystack = (expense.name + ' ' + categoryLabel + ' ' + (expense.notes || '')).toLowerCase();
    return haystack.indexOf(query) !== -1;
  }

  function matchesDateRange(expense) {
    var from = dateFromInput && dateFromInput.value ? new Date(dateFromInput.value + 'T00:00:00') : null;
    var to = dateToInput && dateToInput.value ? new Date(dateToInput.value + 'T23:59:59') : null;
    if (!from && !to) return true;
    var d = parseExpenseDate(expense);
    if (!d) return false;
    if (from && d < from) return false;
    if (to && d > to) return false;
    return true;
  }

  function getBranchName(branchId) {
    var match = BRANCHES.find(function (b) { return b.id == branchId; });
    return match ? match.name : 'All branches';
  }

  function sortValue(e) {
    switch (sortKey) {
      case 'expense_date': return e.expense_date || '';
      case 'name': return (e.name || '').toLowerCase();
      case 'category': return categoryMeta(e.category).label.toLowerCase();
      case 'branch': return getBranchName(e.branch_id).toLowerCase();
      case 'amount': return Number(e.amount) || 0;
      case 'notes': return (e.notes || '').toLowerCase();
      default: return '';
    }
  }

  function getFiltered() {
    var query = searchInput ? searchInput.value.trim().toLowerCase() : '';
    var list = EXPENSES.filter(function (e) {
      return matchesFilter(e) && matchesSearch(e, query) && matchesDateRange(e);
    });

    list.sort(function (a, b) {
      var va = sortValue(a);
      var vb = sortValue(b);
      var cmp = va < vb ? -1 : (va > vb ? 1 : 0);
      return sortDir === 'asc' ? cmp : -cmp;
    });

    return list;
  }

  function formatDateCell(dateStr) {
    var d = new Date(dateStr);
    if (isNaN(d.getTime())) return { day: '—', month: escapeHtml(dateStr || ''), sub: '' };
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    return {
      day: String(d.getDate()).padStart(2, '0'),
      month: months[d.getMonth()] + ' ' + d.getFullYear(),
      sub: days[d.getDay()]
    };
  }

  function updateSortHeaders() {
    sortableHeaders.forEach(function (th) {
      th.classList.toggle('sort-active', th.getAttribute('data-sort') === sortKey);
    });
  }

  function renderTable() {
    var filtered = getFiltered();
    var totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    var start = (currentPage - 1) * PAGE_SIZE;
    var pageItems = filtered.slice(start, start + PAGE_SIZE);

    tableBody.innerHTML = '';
    pageItems.forEach(function (e) {
      var tr = document.createElement('tr');
      var meta = categoryMeta(e.category);
      var dateInfo = formatDateCell(e.expense_date);

      tr.innerHTML =
        '<td><div class="exp-date-cell">' +
          '<div class="exp-date-day">' + dateInfo.day + '</div>' +
          '<div><div class="exp-date-month">' + dateInfo.month + '</div><div class="exp-date-sub">' + dateInfo.sub + '</div></div>' +
        '</div></td>' +
        '<td><div class="exp-name-cell">' +
          '<div class="exp-icon-badge" style="background:' + meta.bg + ';color:' + meta.color + ';"><i class="ti ' + meta.icon + '"></i></div>' +
          '<div><div class="exp-name">' + escapeHtml(e.name) + '</div><div class="exp-sub" title="' + escapeHtml(e.notes || '') + '">' + escapeHtml(e.notes || '') + '</div></div>' +
        '</div></td>' +
        '<td><span class="exp-cat-pill" style="background:' + meta.bg + ';color:' + meta.color + ';">' + escapeHtml(meta.label) + '</span></td>' +
        '<td>' + escapeHtml(getBranchName(e.branch_id)) + '</td>' +
        '<td class="exp-amount">' + formatMoney(e.amount) + '</td>' +
        '<td class="exp-notes" title="' + escapeHtml(e.notes || '') + '">' + escapeHtml(e.notes || '—') + '</td>' +
        '<td><div class="exp-actions">' +
          '<button type="button" class="exp-action-btn edit" title="Edit expense">' + PENCIL_SVG + '</button>' +
          '<button type="button" class="exp-action-btn delete" title="Delete expense">' + TRASH_SVG + '</button>' +
        '</div></td>';

      tr.querySelector('.exp-action-btn.edit').addEventListener('click', function () {
        openEdit(e.id);
      });

      tr.querySelector('.exp-action-btn.delete').addEventListener('click', async function (evt) {
        evt.stopPropagation();
        if (!confirm('Are you sure you want to delete expense "' + e.name + '"?')) return;

        try {
          var res = await fetch('/api/expenses/' + e.id, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
          });

          if (res.ok) {
            if (window.SW) window.SW.showToast('Expense deleted successfully');
            await loadExpenses();
          } else {
            alert('Could not delete expense.');
          }
        } catch (err) {
          console.error(err);
          alert('A connection error occurred.');
        }
      });

      tableBody.appendChild(tr);
    });

    if (emptyState) emptyState.style.display = filtered.length ? 'none' : 'block';

    if (pageInfo) {
      if (filtered.length === 0) {
        pageInfo.textContent = 'No expenses';
      } else {
        var rangeStart = start + 1;
        var rangeEnd = Math.min(start + PAGE_SIZE, filtered.length);
        pageInfo.textContent = 'Showing ' + rangeStart + ' to ' + rangeEnd + ' of ' + filtered.length + ' expenses';
      }
    }

    if (pageNumbers) {
      pageNumbers.innerHTML = '';
      for (var p = 1; p <= totalPages; p++) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'exp-page-btn' + (p === currentPage ? ' active' : '');
        btn.textContent = p;
        btn.addEventListener('click', function (page) {
          return function () { currentPage = page; renderTable(); };
        }(p));
        pageNumbers.appendChild(btn);
      }
    }

    if (prevBtn) prevBtn.disabled = currentPage <= 1;
    if (nextBtn) nextBtn.disabled = currentPage >= totalPages;

    updateSortHeaders();
  }

  function renderKpis() {
    var total = 0;
    var categoriesSeen = {};
    var now = new Date();
    var thisKey = now.getFullYear() + '-' + now.getMonth();
    var lastMonthDate = new Date(now.getFullYear(), now.getMonth() - 1, 1);
    var lastKey = lastMonthDate.getFullYear() + '-' + lastMonthDate.getMonth();

    var thisMonthSum = 0, thisMonthCount = 0, lastMonthSum = 0, lastMonthCount = 0;

    EXPENSES.forEach(function (e) {
      var amount = Number(e.amount) || 0;
      total += amount;
      categoriesSeen[e.category] = true;

      var d = parseExpenseDate(e);
      if (!d) return;
      var key = d.getFullYear() + '-' + d.getMonth();
      if (key === thisKey) {
        thisMonthSum += amount;
        thisMonthCount++;
      } else if (key === lastKey) {
        lastMonthSum += amount;
        lastMonthCount++;
      }
    });

    var totalEl = document.getElementById('expKpiTotal');
    if (totalEl) totalEl.textContent = formatMoney(total);

    var catEl = document.getElementById('expKpiCategories');
    if (catEl) catEl.textContent = Object.keys(categoriesSeen).length;

    var thisMonthEl = document.getElementById('expKpiThisMonth');
    if (thisMonthEl) thisMonthEl.textContent = formatMoney(thisMonthSum);
    var thisMonthSubEl = document.getElementById('expKpiThisMonthSub');
    if (thisMonthSubEl) thisMonthSubEl.textContent = thisMonthCount + (thisMonthCount === 1 ? ' Expense' : ' Expenses');

    var lastMonthEl = document.getElementById('expKpiLastMonth');
    if (lastMonthEl) lastMonthEl.textContent = formatMoney(lastMonthSum);
    var lastMonthSubEl = document.getElementById('expKpiLastMonthSub');
    if (lastMonthSubEl) lastMonthSubEl.textContent = lastMonthCount + (lastMonthCount === 1 ? ' Expense' : ' Expenses');
  }

  // category select change handler to reveal options
  if (categorySelect) {
    categorySelect.addEventListener('change', function () {
      var cat = categorySelect.value;

      staffRow.style.display = cat === 'staff_salary' ? 'block' : 'none';
      inventoryRow.style.display = cat === 'inventory' ? 'block' : 'none';

      staffSelect.value = '';
      inventorySelect.value = '';
    });
  }

  // staff dropdown select auto-filler
  if (staffSelect) {
    staffSelect.addEventListener('change', function () {
      var staffId = staffSelect.value;
      if (!staffId) return;

      var match = STAFF.find(function (s) { return s.id == staffId; });
      if (match) {
        nameInput.value = 'Salary: ' + match.name;
        amountInput.value = match.base_salary || '';
        notesInput.value = 'Monthly salary payment for ' + match.name + ' (' + match.branch + ' branch)';
      }
    });
  }

  // inventory dropdown select auto-filler
  if (inventorySelect) {
    inventorySelect.addEventListener('change', function () {
      var itemId = inventorySelect.value;
      if (!itemId) return;

      var match = INVENTORY_ITEMS.find(function (i) { return i.id == itemId; });
      if (match) {
        nameInput.value = 'Purchase: ' + match.name;
        amountInput.value = match.unit_cost || '';
        notesInput.value = 'Stock purchase for ' + match.name + '. Unit Cost: ₹' + (match.unit_cost || 0);
      }
    });
  }

  if (categoryFilter) {
    categoryFilter.addEventListener('change', function () {
      activeFilter = categoryFilter.value;
      currentPage = 1;
      renderTable();
    });
  }

  sortableHeaders.forEach(function (th) {
    th.addEventListener('click', function () {
      var key = th.getAttribute('data-sort');
      if (sortKey === key) {
        sortDir = sortDir === 'asc' ? 'desc' : 'asc';
      } else {
        sortKey = key;
        sortDir = 'asc';
      }
      renderTable();
    });
  });

  if (searchInput) {
    searchInput.addEventListener('input', function () {
      currentPage = 1;
      renderTable();
    });
  }

  if (prevBtn) prevBtn.addEventListener('click', function () { if (currentPage > 1) { currentPage--; renderTable(); } });
  if (nextBtn) nextBtn.addEventListener('click', function () { currentPage++; renderTable(); });

  function formatRangeLabel() {
    if (!dateFromInput.value && !dateToInput.value) return 'Select date range';
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    function fmt(v) {
      if (!v) return '…';
      var d = new Date(v + 'T00:00:00');
      return String(d.getDate()).padStart(2, '0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }
    return fmt(dateFromInput.value) + ' - ' + fmt(dateToInput.value);
  }

  if (dateRangeBtn && dateRangeMenu) {
    dateRangeBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var willOpen = !dateRangeMenu.classList.contains('open');
      document.querySelectorAll('.dropdown-menu.open').forEach(function (m) { m.classList.remove('open'); });
      if (willOpen) {
        dateRangeMenu.classList.add('open');
      }
    });
    dateRangeMenu.addEventListener('click', function (e) { e.stopPropagation(); });
    document.addEventListener('click', function () {
      dateRangeMenu.classList.remove('open');
    });
  }

  if (dateApplyBtn) {
    dateApplyBtn.addEventListener('click', function () {
      dateRangeMenu.classList.remove('open');
      if (dateRangeLabel) dateRangeLabel.textContent = formatRangeLabel();
      currentPage = 1;
      renderTable();
    });
  }

  function resetForm() {
    form.reset();
    staffRow.style.display = 'none';
    inventoryRow.style.display = 'none';
  }

  function populateBranchOptions(selectedId) {
    if (!branchSelect) return;
    branchSelect.innerHTML = BRANCHES.map(function (b) {
      return '<option value="' + b.id + '">' + escapeHtml(b.name) + '</option>';
    }).join('');
    if (selectedId != null) branchSelect.value = selectedId;
  }

  function openCreate() {
    editingId = null;
    modalTitle.textContent = 'New Expense';
    submitBtn.innerHTML = '<i class="ti ti-check"></i>Save expense';
    resetForm();

    if (dateInput) dateInput.value = new Date().toISOString().substring(0, 10);
    populateBranchOptions(null);

    if (window.SW) window.SW.openModal('expenseModal');
  }

  function openEdit(id) {
    var e = findExpense(id);
    if (!e) return;
    editingId = id;
    modalTitle.textContent = 'Edit Expense';
    submitBtn.innerHTML = '<i class="ti ti-check"></i>Save changes';

    populateBranchOptions(e.branch_id);

    categorySelect.value = e.category;
    var cat = categorySelect.value;
    staffRow.style.display = cat === 'staff_salary' ? 'block' : 'none';
    inventoryRow.style.display = cat === 'inventory' ? 'block' : 'none';
    staffSelect.value = cat === 'staff_salary' && e.ref_id ? e.ref_id : '';
    inventorySelect.value = cat === 'inventory' && e.ref_id ? e.ref_id : '';

    nameInput.value = e.name;
    amountInput.value = e.amount;
    dateInput.value = e.expense_date;
    notesInput.value = e.notes || '';

    if (window.SW) window.SW.openModal('expenseModal');
  }

  if (newBtn) newBtn.addEventListener('click', openCreate);

  if (form) {
    form.addEventListener('submit', async function (evt) {
      evt.preventDefault();

      var category = categorySelect.value;
      var branchId = branchSelect.value;
      var name = nameInput.value.trim();
      var amount = parseInt(amountInput.value) || 0;
      var expenseDate = dateInput.value;
      var notes = notesInput.value.trim();

      var refId = null;
      if (category === 'staff_salary') {
        refId = staffSelect.value || null;
      } else if (category === 'inventory') {
        refId = inventorySelect.value || null;
      }

      var payload = {
        category: category,
        branch_id: branchId || null,
        name: name,
        amount: amount,
        expense_date: expenseDate,
        notes: notes,
        ref_id: refId
      };

      try {
        var res;
        if (editingId) {
          res = await fetch('/api/expenses/' + editingId, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
          });
        } else {
          res = await fetch('/api/expenses', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
          });
        }

        if (res.ok) {
          if (window.SW) {
            window.SW.showToast(editingId ? 'Expense updated successfully' : 'Expense created successfully');
            window.SW.closeModal('expenseModal');
          }
          await loadExpenses();
        } else {
          var err = await res.json();
          alert('Error: ' + (err.message || 'Could not save expense.'));
        }
      } catch (err) {
        console.error(err);
        alert('A network connection error occurred.');
      }
    });
  }

  async function loadMetadata() {
    try {
      var globalRes = await fetch('/api/global');
      if (globalRes.ok) {
        var data = await globalRes.json();
        BRANCHES = data.branches || [];
      }

      var staffRes = await fetch('/api/staff');
      if (staffRes.ok) {
        STAFF = await staffRes.json();
        if (staffSelect) {
          staffSelect.innerHTML = '<option value="">-- Select Staff Member --</option>' +
            STAFF.map(function (s) {
              return '<option value="' + s.id + '">' + escapeHtml(s.name) + ' (Base Salary: ₹' + (s.base_salary || 0) + ')</option>';
            }).join('');
        }
      }

      var invRes = await fetch('/api/inventory');
      if (invRes.ok) {
        INVENTORY_ITEMS = await invRes.json();
        if (inventorySelect) {
          inventorySelect.innerHTML = '<option value="">-- Select Inventory Item --</option>' +
            INVENTORY_ITEMS.map(function (i) {
              return '<option value="' + i.id + '">' + escapeHtml(i.name) + ' (Unit Cost: ₹' + (i.unit_cost || 0) + ')</option>';
            }).join('');
        }
      }
    } catch (err) {
      console.error('Failed to load metadata:', err);
    }
  }

  async function loadExpenses() {
    try {
      var res = await fetch('/api/expenses');
      if (res.ok) {
        EXPENSES = await res.json();
        renderKpis();
        renderTable();
      }
    } catch (err) {
      console.error('Failed to load expenses:', err);
    }
  }

  loadMetadata().then(loadExpenses);
})();
