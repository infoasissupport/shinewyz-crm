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

  var METHOD_LABEL = { cash: 'Cash', upi: 'UPI', card: 'Card' };
  var METHOD_STYLE = {
    cash: 'background:#f1f2f4;color:#475569;',
    upi: 'background:#EFF6FF;color:#1D4ED8;',
    card: 'background:#f1f2f4;color:#475569;'
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
      var res = await fetch('/api/global');
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
    return '<span class="tag" style="' + (METHOD_STYLE[inv.method] || '') + '">' + METHOD_LABEL[inv.method] + '</span>';
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
    if (activeFilter === 'custom') {
      var from = dateFromInput && dateFromInput.value ? new Date(dateFromInput.value + 'T00:00:00') : null;
      var to = dateToInput && dateToInput.value ? new Date(dateToInput.value + 'T23:59:59') : null;
      if (!from && !to) return true;
      var d = invoiceDate(inv);
      if (!d) return false;
      if (from && d < from) return false;
      if (to && d > to) return false;
      return true;
    }
    if (activeFilter === 'all') return true;
    if (activeFilter === 'pending') return inv.status === 'pending';
    return inv.method === activeFilter;
  }

  function matchesSearch(inv, query) {
    if (!query) return true;
    return (inv.id + ' ' + inv.customer).toLowerCase().indexOf(query) !== -1;
  }

  function getFiltered() {
    var query = searchInput ? searchInput.value.trim().toLowerCase() : '';
    return INVOICES.filter(function (i) { return matchesFilter(i) && matchesSearch(i, query); });
  }

  function renderTable() {
    var filtered = getFiltered();
    var totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    var start = (currentPage - 1) * PAGE_SIZE;
    var pageItems = filtered.slice(start, start + PAGE_SIZE);

    tableBody.innerHTML = '';
    pageItems.forEach(function (inv) {
      var tr = document.createElement('tr');
      tr.className = 'inv-row' + (inv.id === selectedId ? ' selected' : '');
      tr.innerHTML =
        '<td class="mono cell-primary">' + escapeHtml(inv.id) + '</td>' +
        '<td>' + escapeHtml(inv.customer) + '</td>' +
        '<td class="mono">₹' + inv.amount.toLocaleString('en-IN') + '</td>' +
        '<td>' + methodCell(inv) + '</td>' +
        '<td>' + statusCell(inv) + '</td>';
      tr.addEventListener('click', function () { selectInvoice(inv.id); });
      tableBody.appendChild(tr);
    });

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

  function selectInvoice(id) {
    var inv = findInvoice(id);
    if (!inv) return;
    selectedId = id;

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
    var total = Number(inv.amount || (subtotal + gst - discount));

    document.getElementById('previewMeta').textContent = inv.id + ' · ' + inv.date + (inv.branch_id ? ' · Branch #' + inv.branch_id : '');
    document.getElementById('previewService').textContent = inv.service;
    document.getElementById('previewAmount').textContent = formatMoney(subtotal);
    document.getElementById('previewGst').textContent = formatMoney(gst);
    document.getElementById('previewTotal').textContent = formatMoney(total);

    var payAction = document.getElementById('payActionContainer');
    if (payAction) {
      payAction.style.display = inv.status === 'pending' ? 'block' : 'none';
    }
  }

  function clearPreview() {
    document.getElementById('previewMeta').textContent = 'No invoice selected';
    document.getElementById('previewService').textContent = 'Create or complete a wash to see billing here';
    document.getElementById('previewAmount').textContent = '₹0.00';
    document.getElementById('previewGst').textContent = '₹0.00';
    document.getElementById('previewTotal').textContent = '₹0.00';
    var payAction = document.getElementById('payActionContainer');
    if (payAction) payAction.style.display = 'none';
  }

  if (filterSelect) {
    filterSelect.addEventListener('change', function () {
      if (invDateRangeMenu) invDateRangeMenu.classList.remove('open');
      if (customRangeChip) customRangeChip.classList.remove('active');
      activeFilter = filterSelect.value;
      currentPage = 1;
      renderTable();
    });
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
    whatsappBtn.addEventListener('click', function () {
      var inv = findInvoice(selectedId);
      if (!inv) return;
      var digits = inv.phone.replace(/\D/g, '');
      if (!digits) {
        alert('Customer phone number is not available.');
        return;
      }

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

      var msg = 'Hello ' + inv.customer + ',\n\n' +
                'Here is your invoice from *' + profileName + '*:\n\n' +
                '*Invoice ID:* ' + inv.id + '\n' +
                '*Date:* ' + inv.date + '\n' +
                '*Service:* ' + inv.service + '\n' +
                '*Subtotal:* ' + formatMoney(subtotal) + '\n' +
                '*GST (' + taxRate + '%):* ' + formatMoney(gst) + '\n' +
                (discount > 0 ? '*Discount:* -' + formatMoney(discount) + '\n' : '') +
                '*Total:* ' + formatMoney(total) + '\n' +
                '*Status:* ' + inv.status.toUpperCase() + '\n\n' +
                'Thank you for your business!';

      window.open('https://wa.me/' + digits + '?text=' + encodeURIComponent(msg), '_blank', 'noopener');
    });
  }

  var pdfBtn = document.getElementById('previewPdfBtn');
  if (pdfBtn) {
    pdfBtn.addEventListener('click', async function () {
      var inv = findInvoice(selectedId);
      if (!inv) return;

      await ensureBranchesLoaded();

      var branch = BRANCHES.filter(function (b) { return String(b.id) === String(inv.branch_id); })[0];
      var addressLine = branch ? [branch.location, branch.city, branch.state].filter(Boolean).join(', ') : '';
      var contactLine = (branch && branch.phone) ? branch.phone : COMPANY_PROFILE.phone;
      var gstLine = (branch && branch.gst_number) ? branch.gst_number : COMPANY_PROFILE.gstin;
      var addressLines = addressLine ? [addressLine] : COMPANY_PROFILE.addressLines;

      var subtotal = Number(inv.subtotal || inv.amount || 0);
      var taxRate = Number(inv.tax_rate || 18);
      var gst = Number(inv.tax_amount || (subtotal * (taxRate / 100)));
      var discount = Number(inv.discount_amount || 0);
      var rawTotal = Number(inv.amount || (subtotal + gst - discount));
      var grandTotal = Math.round(rawTotal);
      var roundOff = grandTotal - rawTotal;
      var cgst = gst / 2;
      var sgst = gst / 2;
      var amountInWords = 'Rupees ' + numberToWordsIndian(grandTotal) + ' Only';
      var bank = COMPANY_PROFILE.bank;

      var termsHtml = INVOICE_TERMS.map(function (term, i) {
        return '<div class="tinv-term">' +
          '<div class="tinv-term-badge">' + (i + 1) + '</div>' +
          '<div><strong>' + escapeHtml(term.title) + '</strong><span>' + escapeHtml(term.text) + '</span></div>' +
        '</div>';
      }).join('');

      var html =
        '<!DOCTYPE html>' +
        '<html>' +
        '<head>' +
          '<meta charset="utf-8">' +
          '<title>Invoice ' + escapeHtml(inv.invoice_number || inv.id) + '</title>' +
          '<style>' +
            '*{box-sizing:border-box;}' +
            'body{font-family:system-ui,-apple-system,Segoe UI,sans-serif;color:#1e293b;margin:0;padding:28px;background:#eef1f5;display:flex;justify-content:center;}' +
            '.tinv{width:100%;max-width:760px;background:#fff;padding:32px;border-radius:10px;}' +
            '.tinv-head{display:flex;justify-content:space-between;align-items:flex-start;gap:24px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid #e5e9f0;}' +
            '.tinv-brand{display:flex;align-items:center;gap:12px;}' +
            '.tinv-mark{width:46px;height:46px;border-radius:11px;background:linear-gradient(135deg,#ff761c,#ff3423);display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;font-weight:900;font-style:italic;flex-shrink:0;}' +
            '.tinv-brand-name{font-size:22px;font-weight:800;color:#101827;letter-spacing:-.02em;}' +
            '.tinv-brand-tagline{font-size:11px;color:#7a8798;letter-spacing:.08em;text-transform:uppercase;margin-top:2px;}' +
            '.tinv-company-info{font-size:11.5px;color:#475569;margin-top:14px;display:grid;gap:5px;}' +
            '.tinv-company-info div{display:flex;align-items:flex-start;gap:7px;}' +
            '.tinv-company-info svg{width:13px;height:13px;flex-shrink:0;margin-top:1px;color:#ff5925;}' +
            '.tinv-invoice-meta{text-align:right;min-width:230px;}' +
            '.tinv-invoice-title{display:inline-block;font-size:21px;font-weight:800;letter-spacing:.02em;color:#101827;padding-bottom:8px;border-bottom:2px solid #101827;margin-bottom:14px;}' +
            '.tinv-meta-row{display:flex;justify-content:space-between;gap:24px;font-size:12px;color:#7a8798;padding:3px 0;}' +
            '.tinv-meta-row b{color:#101827;font-weight:600;}' +
            '.tinv-cards{display:flex;margin-top:6px;}' +
            '.tinv-card{position:relative;border:1px solid #e5e9f0;border-radius:8px;padding:18px 16px 16px;width:100%;max-width:360px;margin-top:14px;}' +
            '.tinv-card-tab{position:absolute;top:-13px;left:14px;background:#171f2e;color:#fff;font-size:10.5px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:6px 14px;border-radius:6px;}' +
            '.tinv-card-name{font-size:16px;font-weight:700;color:#101827;margin:4px 0 10px;}' +
            '.tinv-card-row{display:flex;font-size:12.5px;padding:3px 0;color:#344054;}' +
            '.tinv-card-row .lbl{width:72px;flex-shrink:0;color:#7a8798;}' +
            '.tinv-card-row .cln{width:12px;flex-shrink:0;color:#7a8798;}' +
            '.tinv-card-row b{font-weight:600;}' +
            '.tinv-status-paid{color:#16a34a;}' +
            '.tinv-status-pending{color:#d97706;}' +
            'table.tinv-table{width:100%;border-collapse:collapse;margin-top:24px;font-size:12.5px;}' +
            'table.tinv-table thead th{background:#171f2e;color:#fff;text-align:left;padding:10px 14px;font-size:11px;text-transform:uppercase;letter-spacing:.05em;}' +
            'table.tinv-table thead th.num{text-align:right;}' +
            'table.tinv-table td{padding:12px 14px;border-bottom:1px solid #eef1f5;vertical-align:top;}' +
            'table.tinv-table td.num{text-align:right;white-space:nowrap;}' +
            'table.tinv-table .tinv-item-note{display:block;color:#7a8798;font-size:11px;margin-top:2px;}' +
            '.tinv-totals{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:20px;align-items:start;}' +
            '.tinv-totals-left{border:1px solid #e5e9f0;border-radius:9px;padding:14px 16px;font-size:12.5px;}' +
            '.tinv-totals-left .row{display:flex;padding:4px 0;color:#344054;}' +
            '.tinv-totals-left .row .lbl{width:90px;flex-shrink:0;color:#7a8798;}' +
            '.tinv-totals-left .row .cln{width:12px;flex-shrink:0;color:#7a8798;}' +
            '.tinv-totals-left .row b{font-weight:600;}' +
            '.tinv-totals-words{margin-top:10px;padding-top:10px;border-top:1px solid #eef1f5;font-size:11.5px;color:#475569;}' +
            '.tinv-totals-words b{display:block;color:#101827;margin-top:2px;}' +
            '.tinv-totals-right{border:1px solid #e5e9f0;border-radius:9px;padding:14px 16px;}' +
            '.tinv-totals-right .row{display:flex;justify-content:space-between;padding:6px 0;font-size:12.5px;color:#344054;}' +
            '.tinv-totals-right .grand{margin-top:6px;padding-top:12px;border-top:1px solid #e5e9f0;align-items:center;}' +
            '.tinv-totals-right .grand span{color:#101827;font-weight:700;font-size:14px;}' +
            '.tinv-totals-right .grand b{color:#ff5925;font-size:18px;font-weight:800;}' +
            '.tinv-account{margin-top:22px;border:1px solid #e5e9f0;border-radius:9px;padding:16px;display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;}' +
            '.tinv-account-icon{width:38px;height:38px;border-radius:50%;background:#171f2e;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;}' +
            '.tinv-account-icon svg{width:18px;height:18px;}' +
            '.tinv-account-title{font-size:11.5px;font-weight:700;color:#101827;letter-spacing:.03em;line-height:1.3;min-width:80px;}' +
            '.tinv-account-grid{flex:1;display:grid;grid-template-columns:1fr 1fr;gap:6px 20px;font-size:12px;}' +
            '.tinv-account-grid div{display:flex;color:#344054;}' +
            '.tinv-account-grid .lbl{width:80px;flex-shrink:0;color:#7a8798;}' +
            '.tinv-account-grid .cln{width:12px;flex-shrink:0;color:#7a8798;}' +
            '.tinv-thanks{margin-top:18px;background:#f7f8fa;border:1px solid #e5e9f0;border-radius:9px;padding:13px 16px;display:flex;align-items:center;gap:10px;font-size:12.5px;font-weight:600;color:#475569;}' +
            '.tinv-thanks svg{width:18px;height:18px;flex-shrink:0;color:#475569;}' +
            '.tinv-footer{margin-top:24px;border-top:1px solid #e5e9f0;padding-top:18px;display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap;font-size:12px;}' +
            '.tinv-pay b{display:block;font-size:12.5px;color:#101827;margin-bottom:4px;}' +
            '.tinv-pay-badges{display:flex;gap:8px;margin-top:8px;}' +
            '.tinv-pay-badges span{border-radius:6px;padding:5px 12px;font-size:11px;font-weight:700;}' +
            '.tinv-badge-gpay{background:#fff;border:1px solid #dadce0;color:#3c4043;}' +
            '.tinv-badge-paytm{background:#00baf2;color:#fff;}' +
            '.tinv-badge-phonepe{background:#5f259f;color:#fff;}' +
            '.tinv-follow b{display:block;font-size:12.5px;color:#101827;margin-bottom:6px;}' +
            '.tinv-follow div{display:flex;align-items:center;gap:7px;color:#475569;margin-top:4px;}' +
            '.tinv-follow svg{width:13px;height:13px;color:#ff5925;flex-shrink:0;}' +
            '.tinv-bottombar{margin-top:24px;border-top:1px solid #e5e9f0;padding-top:14px;text-align:center;font-size:11px;color:#94a3b8;}' +
            '@media print{body{padding:0;background:#fff;}.tinv{max-width:100%;border-radius:0;}}' +
          '</style>' +
        '</head>' +
        '<body>' +
          '<div class="tinv">' +

            '<div class="tinv-head">' +
              '<div>' +
                '<div class="tinv-brand">' +
                  '<div class="tinv-mark">S</div>' +
                  '<div><div class="tinv-brand-name">' + escapeHtml(COMPANY_PROFILE.name) + '</div><div class="tinv-brand-tagline">' + escapeHtml(branch ? branch.name : COMPANY_PROFILE.tagline) + '</div></div>' +
                '</div>' +
                '<div class="tinv-company-info">' +
                  '<div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21Z" stroke-linejoin="round"/><circle cx="12" cy="9.5" r="2.3"/></svg><span>' + addressLines.map(escapeHtml).join('<br>') + '</span></div>' +
                  '<div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3h3l2 5-2.5 1.5a12 12 0 0 0 6 6L16 13l5 2v3a2 2 0 0 1-2 2A16 16 0 0 1 4 5a2 2 0 0 1 2-2Z" stroke-linejoin="round"/></svg><span>' + escapeHtml(contactLine) + '</span></div>' +
                  '<div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6" stroke-linecap="round" stroke-linejoin="round"/></svg><span>' + escapeHtml(COMPANY_PROFILE.email) + '</span></div>' +
                  '<div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a13 13 0 0 1 0 18M12 3a13 13 0 0 0 0 18"/></svg><span>' + escapeHtml(COMPANY_PROFILE.website) + '</span></div>' +
                '</div>' +
              '</div>' +
              '<div class="tinv-invoice-meta">' +
                '<div class="tinv-invoice-title">TAX INVOICE</div>' +
                '<div class="tinv-meta-row"><span>GSTIN</span><b>' + escapeHtml(gstLine) + '</b></div>' +
                '<div class="tinv-meta-row"><span>Contact</span><b>' + escapeHtml(contactLine) + '</b></div>' +
                '<div class="tinv-meta-row"><span>Date</span><b>' + formatInvoiceDateTime(inv) + '</b></div>' +
                '<div class="tinv-meta-row"><span>Invoice No</span><b>' + escapeHtml(inv.invoice_number || inv.id) + '</b></div>' +
              '</div>' +
            '</div>' +

            '<div class="tinv-cards">' +
              '<div class="tinv-card">' +
                '<div class="tinv-card-tab">Bill To</div>' +
                '<div class="tinv-card-name">' + escapeHtml(inv.customer) + '</div>' +
                '<div class="tinv-card-row"><span class="lbl">Mob</span><span class="cln">:</span><b>' + escapeHtml(inv.phone || '—') + '</b></div>' +
                '<div class="tinv-card-row"><span class="lbl">Bill No</span><span class="cln">:</span><b>' + escapeHtml(inv.invoice_number || inv.id) + '</b></div>' +
                '<div class="tinv-card-row"><span class="lbl">Date</span><span class="cln">:</span><b>' + formatInvoiceDateTime(inv) + '</b></div>' +
                '<div class="tinv-card-row"><span class="lbl">Status</span><span class="cln">:</span><b class="' + (inv.status === 'paid' ? 'tinv-status-paid' : 'tinv-status-pending') + '">' + escapeHtml((inv.status || '').toUpperCase()) + '</b></div>' +
                '<div class="tinv-card-row"><span class="lbl">Vehicle</span><span class="cln">:</span><b>' + escapeHtml(inv.vehicle || '—') + '</b></div>' +
              '</div>' +
            '</div>' +

            '<table class="tinv-table">' +
              '<thead><tr><th>Description</th><th class="num">Rate (₹)</th><th class="num">Qty</th><th class="num">Amount (₹)</th></tr></thead>' +
              '<tbody><tr>' +
                '<td>' + escapeHtml(inv.service) + '<span class="tinv-item-note">GST @ ' + taxRate + '%</span></td>' +
                '<td class="num">' + formatMoney(subtotal) + '</td>' +
                '<td class="num">1</td>' +
                '<td class="num">' + formatMoney(subtotal) + '</td>' +
              '</tr></tbody>' +
            '</table>' +

            '<div class="tinv-totals">' +
              '<div class="tinv-totals-left">' +
                '<div class="row"><span class="lbl">Bill Amount</span><span class="cln">:</span><b>' + formatMoney(subtotal) + '</b></div>' +
                '<div class="row"><span class="lbl">Discount</span><span class="cln">:</span><b>' + formatMoney(discount) + '</b></div>' +
                '<div class="row"><span class="lbl">CGST (' + (taxRate / 2) + '%)</span><span class="cln">:</span><b>' + formatMoney(cgst) + '</b></div>' +
                '<div class="row"><span class="lbl">SGST (' + (taxRate / 2) + '%)</span><span class="cln">:</span><b>' + formatMoney(sgst) + '</b></div>' +
                (roundOff !== 0 ? '<div class="row"><span class="lbl">Round Off</span><span class="cln">:</span><b>' + (roundOff >= 0 ? '' : '-') + formatMoney(Math.abs(roundOff)) + '</b></div>' : '') +
                '<div class="tinv-totals-words">Amount in words<b>' + escapeHtml(amountInWords) + '</b></div>' +
              '</div>' +
              '<div class="tinv-totals-right">' +
                '<div class="row"><span>Sub Total</span><b>' + formatMoney(subtotal - discount) + '</b></div>' +
                '<div class="row"><span>Tax (' + taxRate + '%)</span><b>' + formatMoney(gst) + '</b></div>' +
                '<div class="row grand"><span>Grand Total</span><b>' + formatMoney(grandTotal) + '</b></div>' +
              '</div>' +
            '</div>' +

            '<div class="tinv-account">' +
              '<div class="tinv-account-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 10 12 4l9 6" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 10v9M19 10v9M9 10v9M15 10v9" stroke-linecap="round"/><path d="M3 19h18"/></svg></div>' +
              '<div class="tinv-account-title">Account<br>Details</div>' +
              '<div class="tinv-account-grid">' +
                '<div><span class="lbl">Bank Name</span><span class="cln">:</span><b>' + escapeHtml(bank.name) + '</b></div>' +
                '<div><span class="lbl">Branch</span><span class="cln">:</span><b>' + escapeHtml(bank.branch) + '</b></div>' +
                '<div><span class="lbl">A/C No</span><span class="cln">:</span><b>' + escapeHtml(bank.account) + '</b></div>' +
                '<div><span class="lbl">UPI ID</span><span class="cln">:</span><b>' + escapeHtml(bank.upi) + '</b></div>' +
                '<div><span class="lbl">IFSC Code</span><span class="cln">:</span><b>' + escapeHtml(bank.ifsc) + '</b></div>' +
              '</div>' +
            '</div>' +

            '<div class="tinv-thanks"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="m8 12.5 2.5 2.5L16 9" stroke-linecap="round" stroke-linejoin="round"/></svg>Thank you for choosing Shinewise. We look forward to serving you again.</div>' +



            '<div class="tinv-footer">' +
              '<div class="tinv-pay">' +
                '<b>Pay via UPI</b>' +
                'UPI ID: ' + escapeHtml(bank.upi) +
                '<div class="tinv-pay-badges"><span class="tinv-badge-gpay">G Pay</span><span class="tinv-badge-paytm">Paytm</span><span class="tinv-badge-phonepe">PhonePe</span></div>' +
              '</div>' +
              '<div class="tinv-follow">' +
                '<b>Contact</b>' +
                '<div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3h3l2 5-2.5 1.5a12 12 0 0 0 6 6L16 13l5 2v3a2 2 0 0 1-2 2A16 16 0 0 1 4 5a2 2 0 0 1 2-2Z" stroke-linejoin="round"/></svg>' + escapeHtml(contactLine) + '</div>' +
                '<div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6" stroke-linecap="round" stroke-linejoin="round"/></svg>' + escapeHtml(COMPANY_PROFILE.email) + '</div>' +
              '</div>' +
            '</div>' +

            '<div class="tinv-bottombar">This is a computer generated invoice and does not require a signature.</div>' +
          '</div>' +
          '<script>window.onload = function() { window.print(); }</script>' +
        '</body>' +
        '</html>';

      var printWindow = window.open('', '_blank');
      printWindow.document.write(html);
      printWindow.document.close();
    });
  }

  var markAsPaidBtn = document.getElementById('markAsPaidBtn');
  if (markAsPaidBtn) {
    markAsPaidBtn.addEventListener('click', async function () {
      var inv = findInvoice(selectedId);
      if (!inv) return;
      var method = document.getElementById('payMethodSelect').value;

      try {
        var res = await fetch('/api/invoices/' + inv.id, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            status: 'paid',
            method: method
          })
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

  var previewDeleteBtn = document.getElementById('previewDeleteBtn');
  if (previewDeleteBtn) {
    previewDeleteBtn.addEventListener('click', async function () {
      var inv = findInvoice(selectedId);
      if (!inv) return;

      if (!confirm('Are you sure you want to delete invoice "' + inv.id + '"?')) return;

      try {
        var res = await fetch('/api/invoices/' + inv.id, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json'
          }
        });

        if (res.ok) {
          window.SW.showToast('Invoice deleted successfully');
          selectedId = null;
          await loadInvoices();
        } else {
          alert('Could not delete invoice.');
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
      var res = await fetch('/api/services');
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
      var res = await fetch('/api/global');
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
      var res = await fetch('/api/invoices');
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
  loadInvoices();
})();
