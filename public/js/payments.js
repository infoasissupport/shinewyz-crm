// Page-specific behavior for payments.html
(function () {
  var tableBody = document.getElementById('invoiceTableBody');
  var emptyState = document.getElementById('invoiceEmptyState');
  var searchInput = document.getElementById('invoiceSearchInput');
  var filterSelect = document.getElementById('invoiceFilterSelect');
  var customRangeChip = document.getElementById('customRangeChip');
  var invDateRangeMenu = document.getElementById('invDateRangeMenu');
  var dateFromInput = document.getElementById('invDateFrom');
  var dateToInput = document.getElementById('invDateTo');
  var dateApplyBtn = document.getElementById('invDateApplyBtn');
  var prevBtn = document.getElementById('invoicePrevBtn');
  var nextBtn = document.getElementById('invoiceNextBtn');
  var pageInfo = document.getElementById('invoicePageInfo');
  var createBtn = document.getElementById('createInvoiceBtn');
  var exportBtn = document.getElementById('exportInvoicesBtn');
  var form = document.getElementById('invoiceForm');
  if (!tableBody) return;

  var PAGE_SIZE = 10;
  var activeFilter = 'all';
  var currentPage = 1;
  var selectedId = '';
  var nextNumber = 843;

  var METHOD_LABEL = { cash: 'Cash', upi: 'UPI', card: 'Card', upi_cash: 'UPI + Cash', split: 'UPI + Cash' };
  var METHOD_STYLE = {
    cash: 'background:#f1f2f4;color:#475569;',
    upi: 'background:#EFF6FF;color:#1D4ED8;',
    card: 'background:#f1f2f4;color:#475569;',
    upi_cash: 'background:#F3E8FF;color:#7E22CE;',
    split: 'background:#F3E8FF;color:#7E22CE;'
  };

  var INVOICES = [];
  var BRANCHES = [];

  // Fallback business details for the printable tax invoice — used whenever a
  // branch record doesn't have the field itself. There's no company-profile
  // table in the app yet, so these are placeholders until one exists.
  var COMPANY_PROFILE = {
    name: 'Shinewise',
    tagline: 'Car Wash & Detailing Studio',
    addressLines: ['Kochi, Kerala - 695013'],
    phone: '+91 9656192912',
    email: 'care@shinewise.in',
    website: 'www.shinewise.in',
    gstin: '32ABCDE1234F1Z5',
    bank: {
      name: 'State Bank of India',
      account: 'XXXXXXXX0001',
      ifsc: 'SBIN0001234',
      branch: 'Kochi',
      upi: 'shinewise@upi'
    }
  };

  var INVOICE_TERMS = [
    { title: 'Agreement to terms', text: 'By scheduling a service with us, you agree to abide by the following terms and conditions.' },
    { title: 'Services and pricing', text: 'Service descriptions and prices are subject to change without notice.' },
    { title: 'Appointments & cancellations', text: 'Appointments can be made via phone or in person. We require at least 24 hours’ notice for cancellations.' },
    { title: 'Vehicle condition', text: 'We are not responsible for pre-existing damage or conditions such as scratches, dents or mechanical issues.' },
    { title: 'Liability & warranty', text: 'We take the utmost care with your vehicle, but are not liable for damage resulting from detailing unless caused by our negligence.' },
    { title: 'Pickup & drop service', text: 'We are not responsible for any damage during pickup or drop-off, where offered.' },
    { title: 'Payment', text: 'Payment is due on completion of the service. We accept cash, UPI and card.' },
    { title: 'Privacy policy', text: 'We collect personal information solely to provide our services and never share it with third parties.' },
    { title: 'Changes to terms', text: 'We reserve the right to update these terms at any time. Changes will be posted at our branches.' },
    { title: 'Contact information', text: 'For any questions or concerns about this invoice, please contact us.' }
  ];

  function splitVehicleLabel(vehicle) {
    var parts = String(vehicle || '').trim().split(/\s+/).filter(Boolean);
    if (!parts.length) return { make: '—', model: '—' };
    return { make: parts[0], model: parts.slice(1).join(' ') || '—' };
  }

  // Invoice vehicle is saved as "PLATE (Model)"; older rows may hold only a plate or only a model.
  function parseInvoiceVehicle(vehicle) {
    var v = String(vehicle || '').trim();
    if (!v) return { number: '—', model: '—' };
    // Plates may contain spaces/dashes ("KL09 E 4567"), so take everything before the first "("
    var m = /^([^()]+)\((.*)\)\s*$/.exec(v);
    if (m) {
      var plate = m[1].trim();
      var model = m[2].trim();
      // Fallback labels look like "Sedan (KL66996)" - drop the repeated plate
      var dup = model.toLowerCase().lastIndexOf('(' + plate.toLowerCase() + ')');
      if (dup !== -1) model = model.slice(0, dup).trim();
      return { number: plate.toUpperCase(), model: model || '—' };
    }
    if (/\d/.test(v) && !/\s/.test(v)) return { number: v.toUpperCase(), model: '—' };
    return { number: '—', model: v };
  }

  function formatInvoiceDateTime(inv) {
    var raw = inv.created_at || inv.date;
    var d = raw ? new Date(raw) : null;
    if (!d || isNaN(d.getTime())) return escapeHtml(inv.date || '—');
    return d.toLocaleString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', hour: 'numeric', minute: '2-digit' });
  }

  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function findInvoice(id) {
    return INVOICES.filter(function (i) { return i.id === id; })[0];
  }

  function formatMoney(value) {
    return '₹' + value.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  var NUM_WORDS_ONES = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
    'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
  var NUM_WORDS_TENS = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

  function numberToWordsIndian(num) {
    num = Math.round(num);
    if (num === 0) return 'Zero';

    function twoDigits(n) {
      if (n < 20) return NUM_WORDS_ONES[n];
      return NUM_WORDS_TENS[Math.floor(n / 10)] + (n % 10 ? ' ' + NUM_WORDS_ONES[n % 10] : '');
    }
    function threeDigits(n) {
      var str = '';
      if (n >= 100) {
        str += NUM_WORDS_ONES[Math.floor(n / 100)] + ' Hundred';
        n %= 100;
        if (n) str += ' ';
      }
      if (n > 0) str += twoDigits(n);
      return str;
    }

    var crore = Math.floor(num / 10000000); num %= 10000000;
    var lakh = Math.floor(num / 100000); num %= 100000;
    var thousand = Math.floor(num / 1000); num %= 1000;
    var rest = num;

    var parts = [];
    if (crore) parts.push(threeDigits(crore) + ' Crore');
    if (lakh) parts.push(threeDigits(lakh) + ' Lakh');
    if (thousand) parts.push(threeDigits(thousand) + ' Thousand');
    if (rest) parts.push(threeDigits(rest));
    return parts.join(' ');
  }

  async function ensureBranchesLoaded() {
    if (BRANCHES.length) return;
    try {
      var res = await fetch('/api/global?t=' + new Date().getTime());
      if (res.ok) {
        var data = await res.json();
        BRANCHES = data.branches || [];
      }
    } catch (e) {
      console.error(e);
    }
  }

  function methodCell(inv) {
    if (inv.status === 'pending') return '—';
    var label = METHOD_LABEL[inv.method] || String(inv.method).toUpperCase();
    return '<span class="tag" style="' + (METHOD_STYLE[inv.method] || 'background:#f1f2f4;color:#475569;') + '">' + escapeHtml(label) + '</span>';
  }

  function statusCell(inv) {
    return inv.status === 'paid'
      ? '<span class="tag" style="background:#E7F7EE;color:#166534;">Paid</span>'
      : '<span class="tag" style="background:#fdecec;color:#991b1b;">Pending</span>';
  }

  function invoiceDate(inv) {
    var raw = inv.created_at || inv.date;
    var d = new Date(raw);
    return isNaN(d.getTime()) ? null : d;
  }

  function matchesFilter(inv) {
    var d = invoiceDate(inv);
    var passesDate = true;
    
    var tfSelect = document.getElementById('invoiceDateFilterSelect');
    var tf = tfSelect ? tfSelect.value : 'all';

    if (activeFilter === 'custom') {
      var from = dateFromInput && dateFromInput.value ? new Date(dateFromInput.value + 'T00:00:00') : null;
      var to = dateToInput && dateToInput.value ? new Date(dateToInput.value + 'T23:59:59') : null;
      if (d) {
        if (from && d < from) passesDate = false;
        if (to && d > to) passesDate = false;
      } else if (from || to) {
        passesDate = false;
      }
    } else if (tf !== 'all' && d) {
      var now = new Date();
      if (tf === 'daily') {
        if (d.toDateString() !== now.toDateString()) passesDate = false;
      } else if (tf === 'weekly') {
        var weekAgo = new Date();
        weekAgo.setDate(now.getDate() - 7);
        if (d < weekAgo) passesDate = false;
      } else if (tf === 'monthly') {
        var monthAgo = new Date();
        monthAgo.setMonth(now.getMonth() - 1);
        if (d < monthAgo) passesDate = false;
      }
    }
    
    if (!passesDate) return false;
    
    // Custom range mode should still respect the method filter dropdown
    var methodVal = document.getElementById('invoiceFilterSelect') ? document.getElementById('invoiceFilterSelect').value : 'all';
    
    if (methodVal === 'all') return true;
    if (methodVal === 'pending') return inv.status === 'pending';
    return inv.method === methodVal;
  }

  function matchesSearch(inv, query) {
    if (!query) return true;
    var veh = parseInvoiceVehicle(inv.vehicle);
    var haystack = (inv.id + ' ' + inv.customer + ' ' + veh.number + ' ' + veh.model).toLowerCase();
    if (haystack.indexOf(query) !== -1) return true;
    // Ignore spaces/dashes on both sides so "kl09e4567", "KL 765", "kl-765" all match
    var compactQuery = query.replace(/[\s-]+/g, '');
    var compactPlate = veh.number.toLowerCase().replace(/[\s-]+/g, '');
    return !!compactQuery && compactPlate.indexOf(compactQuery) !== -1;
  }

  function getFiltered() {
    var query = searchInput ? searchInput.value.trim().toLowerCase() : '';
    return INVOICES.filter(function (i) { return matchesFilter(i) && matchesSearch(i, query); });
  }

  function renderKpis(filtered) {
    var cashSum = 0, cashCount = 0;
    var upiSum = 0, upiCount = 0;
    var splitSum = 0, splitCount = 0;
    var grandTotalSum = 0, grandTotalCount = 0;
    
    filtered.forEach(function(inv) {
      if (inv.status === 'paid') {
        var m = (inv.method || '').toLowerCase();
        var amt = Number(inv.amount) || 0;
        
        grandTotalSum += amt;
        grandTotalCount++;

        if (m === 'cash') {
          cashSum += amt;
          cashCount++;
        } else if (m === 'upi') {
          upiSum += amt;
          upiCount++;
        } else if (m === 'upi_cash' || m === 'split') {
          splitSum += amt;
          splitCount++;
        }
      }
    });

    var formatAmount = function(val) { return '₹' + val.toLocaleString('en-IN'); };

    var cashEl = document.getElementById('payKpiCash');
    if (cashEl) cashEl.textContent = formatAmount(cashSum);
    var cashSub = document.getElementById('payKpiCashSub');
    if (cashSub) cashSub.textContent = cashCount + ' Invoices';

    var upiEl = document.getElementById('payKpiUpi');
    if (upiEl) upiEl.textContent = formatAmount(upiSum);
    var upiSub = document.getElementById('payKpiUpiSub');
    if (upiSub) upiSub.textContent = upiCount + ' Invoices';

    var totalEl = document.getElementById('payKpiTotal');
    if (totalEl) totalEl.textContent = formatAmount(splitSum);
    var totalSub = document.getElementById('payKpiTotalSub');
    if (totalSub) totalSub.textContent = splitCount + ' Invoices';

    var grandEl = document.getElementById('payKpiGrandTotal');
    if (grandEl) grandEl.textContent = formatAmount(grandTotalSum);
    var grandSub = document.getElementById('payKpiGrandTotalSub');
    if (grandSub) grandSub.textContent = grandTotalCount + ' Invoices';
  }

  function renderTable() {
    var filtered = getFiltered();
    renderKpis(filtered);
    var totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    var start = (currentPage - 1) * PAGE_SIZE;
    var pageItems = filtered.slice(start, start + PAGE_SIZE);

    tableBody.innerHTML = '';
    pageItems.forEach(function (inv, idx) {
      var tr = document.createElement('tr');
      var veh = parseInvoiceVehicle(inv.vehicle);
      tr.className = 'inv-row' + (inv.id === selectedId ? ' selected' : '');
      tr.dataset.id = inv.id;
      tr.innerHTML =
        '<td class="col-num">' + (start + idx + 1) + '</td>' +
        '<td class="cell-primary inv-id">' + escapeHtml(inv.id) + '</td>' +
        '<td class="inv-customer">' + escapeHtml(inv.customer) + '</td>' +
        '<td class="inv-veh-no">' + escapeHtml(veh.number) + '</td>' +
        '<td class="inv-veh-model">' + escapeHtml(veh.model) + '</td>' +
        '<td class="inv-amount">₹' + inv.amount.toLocaleString('en-IN') + '</td>' +
        '<td class="inv-method">' + methodCell(inv) + '</td>' +
        '<td class="inv-status">' + statusCell(inv) + '</td>' +
        '<td class="inv-wash"><div class="inv-wash-date">' + escapeHtml(inv.last_wash || '—') + '</div>' +
          (inv.loyalty_count > 0 ? '<div class="inv-wash-count"><svg width="13" height="13" viewBox="0 0 24 24" aria-hidden="true"><path fill="#F5B301" d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9z"/></svg>Washed ' + inv.loyalty_count + ' ' + (inv.loyalty_count === 1 ? 'Time' : 'Times') + '</div>' : '') + '</td>';
      tr.addEventListener('click', function () { selectInvoice(inv.id); });
      tableBody.appendChild(tr);
    });
    renderPageNumbers(totalPages);

    if (emptyState) emptyState.style.display = filtered.length ? 'none' : 'block';
    if (pageInfo) {
      if (filtered.length === 0) {
        pageInfo.textContent = 'No invoices';
      } else {
        var rangeStart = start + 1;
        var rangeEnd = Math.min(start + PAGE_SIZE, filtered.length);
        pageInfo.textContent = 'Showing ' + rangeStart + '–' + rangeEnd + ' of ' + filtered.length + ' invoices';
      }
    }
    if (prevBtn) prevBtn.disabled = currentPage <= 1;
    if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
  }

  function renderPageNumbers(totalPages) {
    var wrap = document.getElementById('invoicePageNums');
    if (!wrap) return;
    wrap.innerHTML = '';
    // Show at most 5 page buttons around the current page
    var first = Math.max(1, Math.min(currentPage - 2, totalPages - 4));
    var last = Math.min(totalPages, first + 4);
    for (var p = first; p <= last; p++) {
      var b = document.createElement('button');
      b.type = 'button';
      b.className = 'page-num' + (p === currentPage ? ' active' : '');
      b.textContent = p;
      if (p === currentPage) b.setAttribute('aria-current', 'page');
      b.addEventListener('click', (function (page) {
        return function () { currentPage = page; renderTable(); };
      })(p));
      wrap.appendChild(b);
    }
  }

  function selectInvoice(id) {
    var inv = findInvoice(id);
    if (!inv) return;
    selectedId = id;

    var pv = parseInvoiceVehicle(inv.vehicle);
    document.getElementById('previewCustomerName').textContent = inv.customer || '—';
    document.getElementById('previewVehiclePlate').textContent = pv.number !== '—' ? pv.number : (pv.model !== '—' ? pv.model : '—');
    document.getElementById('previewVehicleModel').textContent = pv.number !== '—' && pv.model !== '—' ? pv.model : 'Vehicle';

    tableBody.querySelectorAll('tr').forEach(function (row, i) {
      var filtered = getFiltered();
      var start = (currentPage - 1) * PAGE_SIZE;
      var rowInv = filtered[start + i];
      row.classList.toggle('selected', rowInv && rowInv.id === id);
    });

    var subtotal = Number(inv.subtotal || inv.amount || 0);
    var taxRate = (inv.tax_rate !== undefined && inv.tax_rate !== null) ? Number(inv.tax_rate) : (inv.tax_amount > 0 ? 18 : 0);
    var gst = Number(inv.tax_amount !== undefined && inv.tax_amount !== null ? inv.tax_amount : (subtotal * (taxRate / 100)));
    var discount = Number(inv.discount_amount || 0);
    var itemPrice = Number(inv.item_price || 0);
    var total = Number(inv.amount || (subtotal + gst + itemPrice - discount));

    document.getElementById('previewMeta').textContent = inv.id + ' · ' + inv.date + (inv.branch_id ? ' · Branch #' + inv.branch_id : '');
    document.getElementById('previewService').textContent = inv.service;
    document.getElementById('previewAmount').textContent = formatMoney(subtotal);
    document.getElementById('previewGst').textContent = formatMoney(gst);
    
    var itemRow = document.getElementById('previewItemRow');
    if (itemRow) {
      if (inv.item_name && itemPrice > 0) {
        itemRow.style.display = 'flex';
        document.getElementById('previewItemName').textContent = inv.item_name;
        document.getElementById('previewItemPrice').textContent = formatMoney(itemPrice);
      } else {
        itemRow.style.display = 'none';
      }
    }
    
    var discountRow = document.getElementById('discountRow');
    if (discountRow) {
      if (discount > 0) {
        discountRow.style.display = 'flex';
        document.getElementById('previewDiscount').textContent = '-' + formatMoney(discount);
      } else {
        discountRow.style.display = 'none';
      }
    }

    document.getElementById('previewTotal').textContent = formatMoney(total);

    var payAction = document.getElementById('payActionContainer');
    if (payAction) {
      payAction.style.display = inv.status === 'pending' ? 'block' : 'none';
    }
  }

  function clearPreview() {
    document.getElementById('previewMeta').textContent = 'No invoice selected';
    document.getElementById('previewCustomerName').textContent = '—';
    document.getElementById('previewVehiclePlate').textContent = '—';
    document.getElementById('previewVehicleModel').textContent = 'Vehicle';
    document.getElementById('previewService').textContent = 'Create or complete a wash to see billing here';
    document.getElementById('previewAmount').textContent = '₹0.00';
    document.getElementById('previewGst').textContent = '₹0.00';
    document.getElementById('previewTotal').textContent = '₹0.00';
    var payAction = document.getElementById('payActionContainer');
    if (payAction) payAction.style.display = 'none';
  }

  if (filterSelect) {
    // Dashboard "Pending payments" card links here with ?filter=pending
    var requestedFilter = new URLSearchParams(window.location.search).get('filter');
    if (requestedFilter && filterSelect.querySelector('option[value="' + requestedFilter + '"]')) {
      filterSelect.value = requestedFilter;
      activeFilter = requestedFilter;
      // Show every matching invoice, not just today's (the page defaults to Daily)
      var tfDefault = document.getElementById('invoiceDateFilterSelect');
      if (tfDefault) tfDefault.value = 'all';
    }
    filterSelect.addEventListener('change', function () {
      if (invDateRangeMenu) invDateRangeMenu.classList.remove('open');
      if (customRangeChip) customRangeChip.classList.remove('active');
      activeFilter = filterSelect.value;
      currentPage = 1;
      renderTable();
    });
    
    var dateFilterSelect = document.getElementById('invoiceDateFilterSelect');
    if (dateFilterSelect) {
      dateFilterSelect.addEventListener('change', function () {
        if (invDateRangeMenu) invDateRangeMenu.classList.remove('open');
        if (customRangeChip) customRangeChip.classList.remove('active');
        if (activeFilter === 'custom') activeFilter = 'all'; // Reset custom if they change timeframe
        currentPage = 1;
        renderTable();
      });
    }
  }

  if (customRangeChip && invDateRangeMenu) {
    customRangeChip.addEventListener('click', function (e) {
      e.stopPropagation();
      var willOpen = !invDateRangeMenu.classList.contains('open');
      document.querySelectorAll('.dropdown-menu.open').forEach(function (m) { m.classList.remove('open'); });
      if (willOpen) {
        var rect = customRangeChip.getBoundingClientRect();
        var menuWidth = invDateRangeMenu.offsetWidth || 230;
        var left = Math.min(rect.left, window.innerWidth - menuWidth - 12);
        left = Math.max(left, 12);
        invDateRangeMenu.style.top = (rect.bottom + 8) + 'px';
        invDateRangeMenu.style.left = left + 'px';
        invDateRangeMenu.style.right = 'auto';
        invDateRangeMenu.classList.add('open');
        if (filterSelect) filterSelect.value = 'all';
        customRangeChip.classList.add('active');
        activeFilter = 'custom';
        currentPage = 1;
        renderTable();
      }
    });
    invDateRangeMenu.addEventListener('click', function (e) { e.stopPropagation(); });
    document.addEventListener('click', function () {
      invDateRangeMenu.classList.remove('open');
    });
  }

  if (dateApplyBtn) {
    dateApplyBtn.addEventListener('click', function () {
      invDateRangeMenu.classList.remove('open');
    });
  }

  if (dateFromInput) {
    dateFromInput.addEventListener('change', function () {
      currentPage = 1;
      renderTable();
    });
  }
  if (dateToInput) {
    dateToInput.addEventListener('change', function () {
      currentPage = 1;
      renderTable();
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', function () {
      currentPage = 1;
      renderTable();
    });
  }

  if (prevBtn) prevBtn.addEventListener('click', function () { currentPage--; renderTable(); });
  if (nextBtn) nextBtn.addEventListener('click', function () { currentPage++; renderTable(); });

  var emailBtn = document.getElementById('previewEmailBtn');
  if (emailBtn) {
    emailBtn.addEventListener('click', function () {
      var inv = findInvoice(selectedId);
      if (!inv) return;

      var email = prompt('Enter customer email address:', '');
      if (!email) return;

      var profileName = 'Shinewise Sparkle Auto Care';
      try {
        var swUser = localStorage.getItem('sw_user');
        if (swUser) {
          var userObj = JSON.parse(swUser);
          if (userObj && userObj.name) profileName = userObj.name;
        }
      } catch (e) {
        console.error(e);
      }

      var subtotal = Number(inv.subtotal || inv.amount || 0);
      var taxRate = Number(inv.tax_rate || 18);
      var gst = Number(inv.tax_amount || (subtotal * (taxRate / 100)));
      var discount = Number(inv.discount_amount || 0);
      var total = Number(inv.amount || (subtotal + gst - discount));

      var body = 'Hello ' + inv.customer + ',\n\n' +
                 'Thank you for your visit to ' + profileName + '.\n' +
                 'Here are your invoice details:\n\n' +
                 'Invoice ID: ' + inv.id + '\n' +
                 'Date: ' + inv.date + '\n' +
                 'Service: ' + inv.service + '\n' +
                 'Subtotal: ' + formatMoney(subtotal) + '\n' +
                 'GST (' + taxRate + '%): ' + formatMoney(gst) + '\n' +
                 (discount > 0 ? 'Discount: -' + formatMoney(discount) + '\n' : '') +
                 'Total Amount: ' + formatMoney(total) + '\n' +
                 'Status: ' + inv.status.toUpperCase() + '\n\n' +
                 'Best regards,\n' +
                 profileName;

      var mailtoLink = 'mailto:' + encodeURIComponent(email) +
                       '?subject=' + encodeURIComponent('Invoice ' + inv.id + ' - ' + profileName) +
                       '&body=' + encodeURIComponent(body);

      window.open(mailtoLink, '_self');
    });
  }

  var whatsappBtn = document.getElementById('previewWhatsappBtn');
  if (whatsappBtn) {
    whatsappBtn.addEventListener('click', async function () {
      var inv = findInvoice(selectedId);
      if (!inv) return;
      var digits = inv.phone.replace(/\D/g, '');
      if (!digits) {
        alert('Customer phone number is not available.');
        return;
      }
      if (digits.length === 10) {
        digits = '91' + digits; // Auto-prepend India country code
      }

      var profileName = 'Car wash!';
      var instagram = '';
      var google_business = '';
      try {
        var setRes = await fetch('/api/settings?t=' + new Date().getTime());
        if (setRes.ok) {
          var settings = await setRes.json();
          if (settings && settings.company_name) {
            profileName = settings.company_name;
          }
          if (settings && settings.instagram_url) {
            instagram = settings.instagram_url;
          }
          if (settings && settings.google_business_url) {
            google_business = settings.google_business_url;
          }
        }
      } catch (e) {
        console.error('Failed to load company name for whatsapp', e);
      }

      var subtotal = Number(inv.subtotal || inv.amount || 0);
      var taxRate = Number(inv.tax_rate || 18);
      var gst = Number(inv.tax_amount || (subtotal * (taxRate / 100)));
      var discount = Number(inv.discount_amount || 0);
      var total = Number(inv.amount || (subtotal + gst - discount));

      var msg = 'Hello ' + inv.customer + ',\n\n' +
                'Here is your invoice from *' + profileName + '*:\n\n';
                
      var pdfLink = window.location.origin + '/invoice/' + inv.id + '/view';
      msg += '*View & Download Invoice PDF:* ' + pdfLink;
      
      if (instagram) msg += '\n\n*Follow us on Instagram:* ' + instagram;
      if (google_business) msg += '\n\n*Review us on Google:* ' + google_business;
      
      msg += '\n\nThank you for your business!';

      window.open('https://wa.me/' + digits + '?text=' + encodeURIComponent(msg), '_blank', 'noopener');
    });
  }

  var pdfBtn = document.getElementById('previewPdfBtn');
  if (pdfBtn) {
    pdfBtn.addEventListener('click', function () {
      var inv = findInvoice(selectedId);
      if (!inv) return;
      window.open('/invoice/' + inv.id + '/view', '_blank');
    });
  }

    var applyDiscountBtn = document.getElementById('applyDiscountBtn');
  if (applyDiscountBtn) {
    applyDiscountBtn.addEventListener('click', async function() {
      var inv = findInvoice(selectedId);
      if (!inv) return;
      var type = document.getElementById('discountTypeSelect').value;
      var input = Number(document.getElementById('discountInput').value) || 0;
      var subtotal = Number(inv.subtotal) || 0;
      if (subtotal === 0 && inv.amount > 0 && !inv.tax_amount && !inv.discount_amount) subtotal = inv.amount;
      
      var discountAmount = 0;
      if (type === 'percent') {
        discountAmount = Math.round(subtotal * (input / 100));
      } else {
        discountAmount = input;
      }

      try {
        var res = await fetch('/api/invoices/' + inv.id, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ _method: 'PUT', discount_amount: discountAmount })
        });
        if (res.ok) {
          window.SW.showToast('Discount applied');
          await loadInvoices();
          selectInvoice(inv.id);
        } else {
          alert('Failed to apply discount');
        }
      } catch (err) { console.error(err); }
    });
  }

  var markAsPaidBtn = document.getElementById('markAsPaidBtn');
  if (markAsPaidBtn) {
    markAsPaidBtn.addEventListener('click', async function () {
      var inv = findInvoice(selectedId);
      if (!inv) return;
      var method = document.getElementById('payMethodSelect').value;
      
      var payload = {
        _method: 'PUT',
        status: 'paid',
        method: method
      };
      
      if (method === 'upi_cash') {
        var sc = Number(document.getElementById('splitCashInput').value) || 0;
        var su = Number(document.getElementById('splitUpiInput').value) || 0;
        if (sc + su !== Number(inv.amount)) {
          document.getElementById('splitErrorMsg').style.display = 'block';
          return;
        }
        payload.split_cash = sc;
        payload.split_upi = su;
      }

      try {
        var res = await fetch('/api/invoices/' + inv.id, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(payload)
        });

        if (res.ok) {
          window.SW.showToast('Invoice marked as Paid via ' + method.toUpperCase());
          await loadInvoices();
          selectInvoice(inv.id);
        } else {
          var err = await res.json();
          alert('Error: ' + (err.message || 'Could not update invoice.'));
        }
      } catch (err) {
        console.error(err);
        alert('A connection error occurred.');
      }
    });
  }

  async function populateServices() {
    var dropdown = document.getElementById('invServiceName');
    if (!dropdown) return;
    try {
      var res = await fetch('/api/services?t=' + new Date().getTime());
      if (res.ok) {
        var services = await res.json();
        dropdown.innerHTML = '<option value="">Select a service</option>';
        services.forEach(function (s) {
          var opt = document.createElement('option');
          opt.value = s.name;
          opt.textContent = s.name + ' (₹' + s.price + ')';
          dropdown.appendChild(opt);
        });
      }
    } catch (e) {
      console.error(e);
    }
  }

  async function populateBranches(selectedId) {
    var dropdown = document.getElementById('invBranch');
    if (!dropdown) return;
    try {
      var res = await fetch('/api/global?t=' + new Date().getTime());
      if (!res.ok) return;
      var data = await res.json();
      var branches = data.branches || [];
      BRANCHES = branches;
      dropdown.innerHTML = '<option value="">Select branch</option>';
      branches.forEach(function (branch, index) {
        var opt = document.createElement('option');
        opt.value = branch.id;
        opt.textContent = (branch.code ? branch.code + ' · ' : '') + branch.name + (branch.location ? ' · ' + branch.location : '');
        if ((selectedId && String(selectedId) === String(branch.id)) || (!selectedId && index === 0)) {
          opt.selected = true;
        }
        dropdown.appendChild(opt);
      });
    } catch (e) {
      console.error(e);
    }
  }

  var gstToggleEl = document.getElementById('invGstToggle');
  var taxRateInputEl = document.getElementById('invTaxRate');
  if (gstToggleEl && taxRateInputEl) {
    gstToggleEl.addEventListener('change', function () {
      if (this.checked) {
        taxRateInputEl.value = '18';
        taxRateInputEl.disabled = false;
      } else {
        taxRateInputEl.value = '0';
        taxRateInputEl.disabled = true;
      }
    });
  }

  function csvField(value) {
    var str = String(value == null ? '' : value);
    return /[",\r\n]/.test(str) ? '"' + str.replace(/"/g, '""') + '"' : str;
  }

  if (exportBtn) {
    exportBtn.addEventListener('click', function () {
      var filtered = getFiltered();
      var lines = ['Invoice,Customer,Phone,Service,Amount,Method,Status,Date'];
      filtered.forEach(function (inv) {
        lines.push([
          csvField(inv.id),
          csvField(inv.customer),
          csvField(inv.phone),
          csvField(inv.service),
          csvField(inv.amount),
          csvField(inv.status === 'pending' ? '' : METHOD_LABEL[inv.method] || inv.method),
          csvField(inv.status),
          csvField(inv.date)
        ].join(','));
      });

      var blob = new Blob([lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
      var url = URL.createObjectURL(blob);
      var link = document.createElement('a');
      link.href = url;
      link.download = 'invoices-' + new Date().toLocaleDateString('en-CA') + '.csv';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);

      if (window.SW) window.SW.showToast('Invoices exported');
    });
  }

  if (createBtn) {
    createBtn.addEventListener('click', async function () {
      await populateBranches();
      await populateServices();
      var taxRate = document.getElementById('invTaxRate');
      var gstToggle = document.getElementById('invGstToggle');
      if (gstToggle) gstToggle.checked = false;
      if (taxRate) {
        taxRate.value = '0';
        taxRate.disabled = true;
      }
      var discount = document.getElementById('invDiscount');
      if (discount && !discount.value) discount.value = '0';
      var dueAt = document.getElementById('invDueAt');
      if (dueAt && !dueAt.value) dueAt.value = new Date(Date.now() + 7 * 86400000).toISOString().slice(0, 10);
      window.SW.openModal('invoiceModal');
    });
  }

  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      var customer = document.getElementById('invCustomer').value.trim();
      var phone = document.getElementById('invPhone').value.trim() || 'Not provided';
      var service = document.getElementById('invServiceName').value;
      var subtotal = Number(document.getElementById('invAmount').value) || 0;
      var branchId = document.getElementById('invBranch') ? document.getElementById('invBranch').value : '';
      var gstToggle = document.getElementById('invGstToggle');
      var isGstEnabled = gstToggle ? gstToggle.checked : true;
      var taxRate = isGstEnabled ? (Number(document.getElementById('invTaxRate').value) || 0) : 0;
      var discountAmount = Number(document.getElementById('invDiscount').value) || 0;
      var dueAt = document.getElementById('invDueAt').value || null;
      var method = document.getElementById('invMethod').value;
      var status = method === 'pending' ? 'pending' : 'paid';

      try {
        var res = await fetch('/api/invoices', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            customer: customer,
            phone: phone,
            service: service,
            subtotal: subtotal,
            tax_rate: taxRate,
            discount_amount: discountAmount,
            branch_id: branchId || null,
            due_at: dueAt,
            method: method,
            status: status
          })
        });

        if (res.ok) {
          var newInv = await res.json();
          if (filterSelect) filterSelect.value = 'all';
          if (customRangeChip) customRangeChip.classList.remove('active');
          if (invDateRangeMenu) invDateRangeMenu.classList.remove('open');
          activeFilter = 'all';
          if (searchInput) searchInput.value = '';
          currentPage = 1;

          await loadInvoices();
          selectInvoice(newInv.id);

          window.SW.closeModal('invoiceModal');
          window.SW.showToast('Invoice created: ' + newInv.id);
          form.reset();
        } else {
          var err = await res.json();
          alert('Error: ' + (err.message || 'Could not create invoice.'));
        }
      } catch (err) {
        console.error(err);
        alert('A connection error occurred.');
      }
    });
  }

  async function loadInvoices() {
    try {
      var res = await fetch('/api/invoices?t=' + new Date().getTime());
      if (res.ok) {
        INVOICES = await res.json();
        var focusId = localStorage.getItem('sw_focus_invoice');
        if (focusId && INVOICES.some(function (i) { return i.id === focusId; })) {
          selectedId = focusId;
          localStorage.removeItem('sw_focus_invoice');
        }
        renderTable();
        if (INVOICES.length > 0) {
          var exists = INVOICES.some(function (i) { return i.id === selectedId; });
          if (!exists) {
            selectedId = INVOICES[0].id;
          }
          if (selectedId) {
            selectInvoice(selectedId);
          }
        } else {
          clearPreview();
        }
      }
    } catch (err) {
      console.error('Failed to load invoices:', err);
    }
  }
  
  var editInvoiceBtn = document.getElementById('previewEditBtn');
  var editInvoiceForm = document.getElementById('editInvoiceForm');
  
  if (editInvoiceBtn) {
    editInvoiceBtn.addEventListener('click', function () {
      var inv = findInvoice(selectedId);
      if (!inv) return;
      document.getElementById('editInvoiceId').value = inv.id;
      document.getElementById('editInvoiceDate').value = inv.date || '';
      document.getElementById('editItemName').value = inv.item_name || '';
      document.getElementById('editItemPrice').value = inv.item_price || '';
      window.SW.openModal('editInvoiceModal');
    });
  }

  if (editInvoiceForm) {
    editInvoiceForm.addEventListener('submit', async function (evt) {
      evt.preventDefault();
      var id = document.getElementById('editInvoiceId').value;
      if (!id) return;
      var dateVal = document.getElementById('editInvoiceDate').value.trim();
      var itemNameVal = document.getElementById('editItemName').value.trim();
      var itemPriceVal = document.getElementById('editItemPrice').value.trim();
      
      var payload = {};
      if (dateVal) payload.date = dateVal;
      if (itemNameVal) payload.item_name = itemNameVal;
      if (itemPriceVal) payload.item_price = parseInt(itemPriceVal) || 0;

      payload._method = 'PUT';
      try {
        var res = await fetch('/api/invoices/' + id, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify(payload)
        });
        if (res.ok) {
          window.SW.closeModal('editInvoiceModal');
          window.SW.showToast('Invoice updated');
          await loadInvoices();
          selectInvoice(id);
        } else {
          alert('Failed to update invoice.');
        }
      } catch (err) { console.error(err); }
    });
  }
  loadInvoices();
})();
