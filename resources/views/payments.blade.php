@extends('layouts.app')

@section('title', 'Payments and Invoicing')

@section('page-id', 'payments')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/payments.css') }}?v={{ filemtime(public_path('css/payments.css')) }}">
@endsection

@section('content')
<section class="view" id="payments">
  <div class="view-header">
    <div class="view-title">Payments and invoicing</div>
    <div class="pay-header-actions">
      <div class="pay-filter-field">
        <label for="invoiceDateFilterSelect">Timeframe</label>
        <select id="invoiceDateFilterSelect" autocomplete="off">
          <option value="all">All Time</option>
          <option value="daily" selected>Daily</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
      </div>
      <button class="btn" id="exportInvoicesBtn"><i class="ti ti-download"></i>Export</button>
    </div>
  </div>
   <div class="pay-kpi-grid">
    <div class="pay-kpi">
      <div class="pay-kpi-icon pay-kpi--green"><i class="ti ti-cash"></i></div>
      <div class="pay-kpi-body">
        <div class="pay-kpi-label">Total Cash</div>
        <div class="pay-kpi-value" id="payKpiCash">₹0</div>
        <div class="pay-kpi-sub" id="payKpiCashSub">0 Invoices</div>
      </div>
    </div>
    <div class="pay-kpi">
      <div class="pay-kpi-icon pay-kpi--blue"><i class="ti ti-device-mobile"></i></div>
      <div class="pay-kpi-body">
        <div class="pay-kpi-label">Total UPI</div>
        <div class="pay-kpi-value" id="payKpiUpi">₹0</div>
        <div class="pay-kpi-sub" id="payKpiUpiSub">0 Invoices</div>
      </div>
    </div>
    <div class="pay-kpi">
      <div class="pay-kpi-icon pay-kpi--purple"><i class="ti ti-wallet"></i></div>
      <div class="pay-kpi-body">
        <div class="pay-kpi-label">Total Cash + UPI</div>
        <div class="pay-kpi-value" id="payKpiTotal">₹0</div>
        <div class="pay-kpi-sub" id="payKpiTotalSub">0 Invoices</div>
      </div>
    </div>
    <div class="pay-kpi">
      <div class="pay-kpi-icon pay-kpi--amber"><i class="ti ti-report-money"></i></div>
      <div class="pay-kpi-body">
        <div class="pay-kpi-label">Total Amount</div>
        <div class="pay-kpi-value" id="payKpiGrandTotal">₹0</div>
        <div class="pay-kpi-sub" id="payKpiGrandTotalSub">0 Invoices</div>
      </div>
    </div>
  </div>
  <div class="paymentsCommonMainAll">
  <div class="tablefiletrFlex ">
 
    <div class="toolbar">
        <input id="invoiceSearchInput" aria-label="Search invoice, customer, vehicle number or model" placeholder="Search Invoice, Customer, Vehicle No. Or Model" autocomplete="off">
        <div class="pay-filter-field">
          <label for="invoiceFilterSelect">Filter</label>
          <select id="invoiceFilterSelect">
            <option value="all">All</option>
            <option value="cash">Cash</option>
            <option value="upi">UPI</option>
            <option value="card">Card</option>
            <option value="pending">Pending</option>
          </select>
        </div>
        <div class="dropdown pay-filter-field" id="customRangeDropdown">
          <span class="pay-filter-label">Date Range</span>
          <div class="chip" data-filter="custom" id="customRangeChip"><svg class="pay-cal-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4.5" width="18" height="17" rx="2.5"/><path d="M8 2.5v4M16 2.5v4M3 10h18"/></svg>Custom Range <i class="ti ti-chevron-down"></i></div>
          <div class="dropdown-menu" id="invDateRangeMenu">
            <div class="dropdown-header">Custom Range</div>
            <div class="field"><label for="invDateFrom">From</label><input type="date" id="invDateFrom"></div>
            <div class="field"><label for="invDateTo">To</label><input type="date" id="invDateTo"></div>
            <button type="button" class="btn btn-accent" id="invDateApplyBtn" style="width:100%;margin-top:4px;">Apply</button>
          </div>
        </div>
      </div>
    <div class="table-wrap">
      
      <div class="table-scroll">
        <table>
          <thead><tr><th class="col-num">#</th><th>Invoice</th><th>Customer</th><th>Vehicle No.</th><th>Vehicle Model</th><th>Amount</th><th>Method</th><th>Status</th><th>Last Wash Detail</th></tr></thead>
          <tbody id="invoiceTableBody"></tbody>
        </table>
        <div class="dropdown-empty" id="invoiceEmptyState" style="display:none;">No invoices match your search.</div>
      </div>
      <div class="table-pagination">
        <span id="invoicePageInfo"></span>
        <div class="pagination-btns">
          <button type="button" class="btn" id="invoicePrevBtn"><i class="ti ti-chevron-left"></i>Prev</button>
          <button type="button" class="btn" id="invoiceNextBtn">Next<i class="ti ti-chevron-right"></i></button>
        </div>
      </div>
    </div>
  </div>
    <div class="card">
      <div class="card-title">
        <span class="inv-preview-heading"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/><path d="M9 9h1M9 13h6M13 17h2"/></svg>Invoice Preview</span>
        <span class="inv-gst-badge">GST 18%</span>
      </div>
      <div class="inv-preview-meta" id="previewMeta">INV-2026-0842 · 26 Jul 2026</div>

      <div class="inv-preview-party">
        <div class="inv-party">
          <span class="inv-party-avatar inv-party-avatar--customer"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg></span>
          <div class="inv-party-text"><div class="inv-party-name" id="previewCustomerName">—</div><div class="inv-party-sub">Customer</div></div>
        </div>
        <div class="inv-party">
          <span class="inv-party-avatar inv-party-avatar--vehicle"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 17h-1a1 1 0 0 1-1-1v-4l2-5h14l2 5v4a1 1 0 0 1-1 1h-1"/><path d="M3 12h18"/><circle cx="7.5" cy="17" r="2"/><circle cx="16.5" cy="17" r="2"/><path d="M9.5 17h5"/></svg></span>
          <div class="inv-party-text"><div class="inv-party-name" id="previewVehiclePlate">—</div><div class="inv-party-sub" id="previewVehicleModel">Vehicle</div></div>
        </div>
      </div>

      <div class="inv-preview-box">
        <div class="inv-preview-row"><span id="previewService">Premium detail</span><span class="mono" id="previewAmount">₹699.00</span></div>
        <div class="inv-preview-row"><span>GST (18%)</span><span class="mono" id="previewGst">₹125.82</span></div>
        <div class="inv-preview-row" id="previewItemRow" style="display:none;"><span id="previewItemName">Additional Item</span><span class="mono" id="previewItemPrice">₹0.00</span></div>
        <div class="inv-preview-row" id="discountRow" style="display:none;"><span>Discount</span><span class="mono" id="previewDiscount">-₹0.00</span></div>
                <div class="inv-preview-total"><span>Total</span><span class="mono" id="previewTotal">₹824.82</span></div>
        <div class="inv-preview-row" id="splitCashRow" style="display:none; color:#475569; font-size:13px; margin-top:8px;"><span>Paid via Cash</span><span class="mono" id="previewSplitCash">₹0.00</span></div>
        <div class="inv-preview-row" id="splitUpiRow" style="display:none; color:#475569; font-size:13px;"><span>Paid via UPI</span><span class="mono" id="previewSplitUpi">₹0.00</span></div>
      </div>

      <div class="inv-preview-actions">
        <button class="btn inv-btn-whatsapp" id="previewWhatsappBtn"><i class="ti ti-brand-whatsapp"></i>WhatsApp</button>
        <button class="btn inv-btn-pdf" id="previewPdfBtn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/><path d="M9 17h6M9 13h6"/></svg>PDF</button>
        <button class="btn inv-btn-edit" id="previewEditBtn"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>Edit</button>
      </div>

      <div class="inv-preview-payment" id="payActionContainer" style="display:none;">
                <label>Apply Discount</label>
        <div style="display:flex;gap:8px;margin-bottom:16px;">
          <select id="discountTypeSelect" style="width:80px;height:36px;border-radius:10px;border:1px solid #C9D3E2;background:#fff;color:#172033;padding:0 8px;font-size:13px;">
            <option value="flat">₹ Flat</option>
            <option value="percent">% Pct</option>
          </select>
          <input type="number" id="discountInput" placeholder="Amount" style="flex:1;height:36px;border-radius:10px;border:1px solid #C9D3E2;background:#fff;color:#172033;padding:0 8px;font-size:13px;" min="0">
          <button type="button" class="btn" id="applyDiscountBtn" style="height:36px;padding:0 12px;">Apply</button>
        </div>
        <label for="payMethodSelect">Receive Payment</label>
        <div style="display:flex;gap:8px;margin-bottom:8px;">
          <select id="payMethodSelect" onchange="document.getElementById('splitPaymentFields').style.display = (this.value === 'upi_cash') ? 'flex' : 'none'; document.getElementById('splitErrorMsg').style.display = 'none';" style="flex:1;height:36px;border-radius:10px;border:1px solid #C9D3E2;background:#fff;color:#172033;padding:0 8px;font-size:13px;">
            <option value="cash">Cash</option>
            <option value="upi">UPI</option>
            <option value="card">Card</option>
            <option value="upi_cash">UPI + Cash</option>
          </select>
          <button class="btn btn-accent" id="markAsPaidBtn" style="height:36px;"><i class="ti ti-check"></i>Mark as Paid</button>
        </div>
        
        <div id="splitPaymentFields" style="display:none; gap:8px; margin-bottom:8px;">
          <input type="number" id="splitCashInput" placeholder="Cash Amount" style="flex:1;height:36px;border-radius:10px;border:1px solid #C9D3E2;background:#fff;color:#172033;padding:0 8px;font-size:13px;" min="0">
          <input type="number" id="splitUpiInput" placeholder="UPI Amount" style="flex:1;height:36px;border-radius:10px;border:1px solid #C9D3E2;background:#fff;color:#172033;padding:0 8px;font-size:13px;" min="0">
        </div>
        <div id="splitErrorMsg" style="display:none; color:#EF4444; font-size:12px; margin-bottom:8px;">Total does not match the invoice amount.</div>
      </div>
    </div>
  </div>
</section>

<div class="modal-overlay" id="editInvoiceModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Invoice</div>
      <button class="icon-btn" onclick="window.SW.closeModal('editInvoiceModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <form id="editInvoiceForm">
      <input type="hidden" id="editInvoiceId">
      <div class="modal-body">
        <div class="field">
          <label class="label" for="editInvoiceDate">Invoice Date</label>
          <input type="text" id="editInvoiceDate" required placeholder="e.g. 09 Sep 2026">
        </div>
        <div class="field">
          <label class="label" for="editItemName">Additional Item Name</label>
          <input type="text" id="editItemName" placeholder="e.g. Microfiber Towel, Car Perfume">
        </div>
        <div class="field">
          <label class="label" for="editItemPrice">Additional Item Price (₹)</label>
          <input type="number" id="editItemPrice" placeholder="0" min="0">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="window.SW.closeModal('editInvoiceModal')">Cancel</button>
        <button type="submit" class="btn btn-accent" id="saveInvoiceEditBtn"><i class="ti ti-check"></i>Save Changes</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="invoiceModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Create invoice</div>
      <button class="icon-btn" onclick="window.SW.closeModal('invoiceModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <form id="invoiceForm">
      <div class="modal-body">
        <div class="field"><label for="invCustomer">Customer name</label><input type="text" id="invCustomer" required placeholder="e.g. Ravi Kumar"></div>
        <div class="modal-row">
          <div class="field"><label for="invBranch">Branch</label><select id="invBranch"><option value="">Select branch</option></select></div>
          <div class="field">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
              <label for="invTaxRate" style="margin-bottom:0;">GST rate (%)</label>
              <label style="font-size:11.5px;color:var(--accent);font-weight:600;display:inline-flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" id="invGstToggle" style="width:auto;"> Enable GST</label>
            </div>
            <input type="number" id="invTaxRate" min="0" max="100" step="0.01" value="0" disabled>
          </div>
        </div>
        <div class="modal-row">
          <div class="field"><label for="invPhone">Phone</label><input type="tel" id="invPhone" inputmode="numeric" pattern="[0-9]*" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="e.g. 9876543210"></div>
          <div class="field"><label for="invServiceName">Service</label>
            <select id="invServiceName" required>
              <option value="">Select a service</option>
            </select>
          </div>
        </div>
        <div class="modal-row">
          <div class="field"><label for="invAmount">Amount (₹, before GST)</label><input type="number" id="invAmount" min="0" required placeholder="e.g. 699"></div>
          <div class="field"><label for="invDiscount">Discount (₹)</label><input type="number" id="invDiscount" min="0" value="0"></div>
        </div>
        <div class="modal-row">
          <div class="field"><label for="invMethod">Payment method</label>
            <select id="invMethod">
              <option value="pending">Not paid yet</option>
              <option value="cash">Cash</option>
              <option value="upi">UPI</option>
              <option value="card">Card</option>
            </select>
          </div>
          <div class="field"><label for="invDueAt">Due date</label><input type="date" id="invDueAt"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="window.SW.closeModal('invoiceModal')">Cancel</button>
        <button type="submit" class="btn btn-accent"><i class="ti ti-plus"></i>Create invoice</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
  <script src="{{ asset('js/payments.js') }}?v={{ time() }}"></script>
@endsection
