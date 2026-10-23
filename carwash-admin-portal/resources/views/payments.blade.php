@extends('layouts.app')

@section('title', 'Payments and Invoicing')

@section('page-id', 'payments')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/payments.css') }}">
@endsection

@section('content')
<section class="view" id="payments">
  <div class="view-header">
    <div class="view-title">Payments and invoicing</div>
    <button class="btn" id="exportInvoicesBtn"><i class="ti ti-download"></i>Export</button>
  </div>
  <div class="paymentsCommonMainAll">
  <div class="tablefiletrFlex ">
    <div class="toolbar">
        <input id="invoiceSearchInput" aria-label="Search Invoice Or Customer" placeholder="Search Invoice Or Customer" autocomplete="off">
        <div class="pay-filter-field">
          <label for="invoiceFilterSelect">Filter:</label>
          <select id="invoiceFilterSelect">
            <option value="all">All</option>
            <option value="cash">Cash</option>
            <option value="upi">UPI</option>
            <option value="card">Card</option>
            <option value="pending">Pending</option>
          </select>
        </div>
        <div class="dropdown" id="customRangeDropdown">
          <div class="chip" data-filter="custom" id="customRangeChip">Custom Range <i class="ti ti-chevron-down"></i></div>
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
          <thead><tr><th>Invoice</th><th>Customer</th><th>Amount</th><th>Method</th><th>Status</th></tr></thead>
          <tbody id="invoiceTableBody"></tbody>
        </table>
        <div class="dropdown-empty" id="invoiceEmptyState" style="display:none;">No invoices match your search.</div>
      </div>
      <div class="table-pagination">
        <span id="invoicePageInfo"></span>
        <div class="pagination-btns">
          <button type="button" class="btn" id="invoicePrevBtn">Prev</button>
          <button type="button" class="btn" id="invoiceNextBtn">Next</button>
        </div>
      </div>
    </div>
  </div>
    <div class="card">
      <div class="card-title">Invoice Preview <span class="link">GST 18%</span></div>
      <div class="inv-preview-meta" id="previewMeta">INV-2026-0842 · 26 Jul 2026</div>

      <div class="inv-preview-box">
        <div class="inv-preview-row"><span id="previewService">Premium detail</span><span class="mono" id="previewAmount">₹699.00</span></div>
        <div class="inv-preview-row"><span>GST (18%)</span><span class="mono" id="previewGst">₹125.82</span></div>
        <div class="inv-preview-total"><span>Total</span><span class="mono" id="previewTotal">₹824.82</span></div>
      </div>

      <div class="inv-preview-actions">
        <button class="btn"  id="previewPdfBtn"><i class="ti ti-file-download"></i>PDF</button>
        <button class="btn inv-preview-delete" id="previewDeleteBtn" title="Delete Invoice">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
        </button>
      </div>

      <div class="inv-preview-payment" id="payActionContainer" style="display:none;">
        <label for="payMethodSelect">Receive Payment</label>
        <div style="display:flex;gap:8px;">
          <select id="payMethodSelect" style="flex:1;height:36px;border-radius:10px;border:1px solid #C9D3E2;background:#fff;color:#172033;padding:0 8px;font-size:13px;">
            <option value="cash">Cash</option>
            <option value="upi">UPI</option>
            <option value="card">Card</option>
          </select>
          <button class="btn btn-accent" id="markAsPaidBtn" style="height:36px;"><i class="ti ti-check"></i>Mark as Paid</button>
        </div>
      </div>
    </div>
  </div>
</section>

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
  <script src="{{ asset('js/payments.js') }}"></script>
@endsection
