@extends('layouts.app')

@section('title', 'Expenses')

@section('page-id', 'expenses')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/expenses.css') }}?v={{ filemtime(public_path('css/expenses.css')) }}">
@endsection

@section('content')
<section class="view" id="expenses">
  <div class="view-header">
    <div class="exp-header-left">
    
      <div>
        <div class="view-title">Expenses</div>
       
      </div>
    </div>
    <button class="btn btn-accent" id="newExpenseBtn"><i class="ti ti-plus"></i>New Expense</button>
  </div>

  <div class="fixedScrolldivCommon">
  <div class="exp-kpi-grid">
    <div class="exp-kpi">
      <div class="exp-kpi-icon" style="background:#EFF6FF;color:#2563EB;"><i class="ti ti-cash"></i></div>
      <div class="exp-kpi-body">
        <div class="exp-kpi-label">Total Expenses</div>
        <div class="exp-kpi-value" id="expKpiTotal">₹0</div>
        <div class="exp-kpi-sub">All time</div>
      </div>
    </div>
    <div class="exp-kpi">
      <div class="exp-kpi-icon" style="background:#ECFDF5;color:#059669;"><i class="ti ti-tags"></i></div>
      <div class="exp-kpi-body">
        <div class="exp-kpi-label">Total Categories</div>
        <div class="exp-kpi-value" id="expKpiCategories">0</div>
        <div class="exp-kpi-sub">Active</div>
      </div>
    </div>
    <div class="exp-kpi">
      <div class="exp-kpi-icon" style="background:#F5F3FF;color:#7C3AED;"><i class="ti ti-chart-line"></i></div>
      <div class="exp-kpi-body">
        <div class="exp-kpi-label">This Month</div>
        <div class="exp-kpi-value" id="expKpiThisMonth">₹0</div>
        <div class="exp-kpi-sub" id="expKpiThisMonthSub">0 Expenses</div>
      </div>
    </div>
    <div class="exp-kpi">
      <div class="exp-kpi-icon" style="background:#FFF7ED;color:#C2410C;"><i class="ti ti-calendar-stats"></i></div>
      <div class="exp-kpi-body">
        <div class="exp-kpi-label">Last Month</div>
        <div class="exp-kpi-value" id="expKpiLastMonth">₹0</div>
        <div class="exp-kpi-sub" id="expKpiLastMonthSub">0 Expenses</div>
      </div>
    </div>
  </div>

  <div class="table-wrap">
    <div class="toolbar exp-toolbar">
      <div class="exp-search-box">
        <i class="ti ti-search"></i>
        <input id="expSearchInput" aria-label="Search expense" placeholder="Search expenses by name or note..." autocomplete="off">
      </div>
      <div class="exp-filter-field">
        <select id="expCategoryFilter" aria-label="Filter by category">
          <option value="all">All categories</option>
          <option value="emi">EMI</option>
          <option value="rent">Rent</option>
          <option value="staff_salary">Staff Salary</option>
          <option value="inventory">Inventory</option>
          <option value="utilities">Utilities</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="dropdown exp-date-range" id="expDateRangeDropdown">
        <div class="exp-date-range-btn" id="expDateRangeBtn"><i class="ti ti-calendar-event"></i><span id="expDateRangeLabel">Select date range</span><i class="ti ti-chevron-down"></i></div>
        <div class="dropdown-menu" id="expDateRangeMenu">
          <div class="dropdown-header">Custom Range</div>
          <div class="field"><label for="expDateFrom">From</label><input type="date" id="expDateFrom"></div>
          <div class="field"><label for="expDateTo">To</label><input type="date" id="expDateTo"></div>
          <button type="button" class="btn btn-accent" id="expDateApplyBtn" style="width:100%;margin-top:4px;">Apply</button>
        </div>
      </div>
    </div>
    <div class="table-scroll">
      <table>
        <thead>
          <tr>
            <th class="exp-sortable" data-sort="expense_date">Date <svg class="exp-sort-icon" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 9l4-4 4 4"/><path d="M16 15l-4 4-4-4"/></svg></th>
            <th class="exp-sortable" data-sort="name">Name / Details <svg class="exp-sort-icon" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 9l4-4 4 4"/><path d="M16 15l-4 4-4-4"/></svg></th>
            <th class="exp-sortable" data-sort="category">Category <svg class="exp-sort-icon" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 9l4-4 4 4"/><path d="M16 15l-4 4-4-4"/></svg></th>
            <th class="exp-sortable" data-sort="branch">Branch <svg class="exp-sort-icon" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 9l4-4 4 4"/><path d="M16 15l-4 4-4-4"/></svg></th>
            <th class="exp-sortable" data-sort="amount">Amount <svg class="exp-sort-icon" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 9l4-4 4 4"/><path d="M16 15l-4 4-4-4"/></svg></th>
            <th class="exp-sortable" data-sort="notes">Notes <svg class="exp-sort-icon" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 9l4-4 4 4"/><path d="M16 15l-4 4-4-4"/></svg></th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="expTableBody"></tbody>
      </table>
      <div class="dropdown-empty" id="expEmptyState" style="display:none;">No expenses match your filters.</div>
    </div>
    <div class="table-pagination">
      <span id="expPageInfo"></span>
      <div class="pagination-btns">
        <button type="button" class="btn" id="expPrevBtn"><i class="ti ti-chevron-down" style="transform:rotate(90deg);display:inline-block;"></i></button>
        <div class="exp-page-numbers" id="expPageNumbers"></div>
        <button type="button" class="btn" id="expNextBtn"><i class="ti ti-chevron-down" style="transform:rotate(-90deg);display:inline-block;"></i></button>
      </div>
    </div>
  </div>
  </div>
</section>

<!-- ============ NEW/EDIT EXPENSE MODAL ============ -->
<div class="modal-overlay" id="expenseModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="expenseModalTitle">New Expense</div>
      <button class="icon-btn" onclick="window.SW.closeModal('expenseModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <form id="expenseForm">
      <div class="modal-body">
        <div class="modal-row">
          <div class="field">
            <label for="expCategory">Category</label>
            <select id="expCategory" required>
              <option value="emi">EMI</option>
              <option value="rent">Rent</option>
              <option value="staff_salary">Staff Salary</option>
              <option value="inventory">Inventory Purchase</option>
              <option value="utilities">Utilities (Water, Electricity, etc.)</option>
              <option value="other" selected>Other Expense</option>
            </select>
          </div>
          <div class="field">
            <label for="expBranch">Branch</label>
            <select id="expBranch" required>
              <!-- Populated dynamically -->
            </select>
          </div>
        </div>

        <!-- Staff Member Dropdown (Visible only for Staff Salary Category) -->
        <div class="field" id="expStaffRow" style="display:none; margin-bottom: 14px;">
          <label for="expStaff">Select Staff Member</label>
          <select id="expStaff">
            <option value="">-- Select Staff Member --</option>
            <!-- Populated dynamically -->
          </select>
        </div>

        <!-- Inventory Item Dropdown (Visible only for Inventory Purchase Category) -->
        <div class="field" id="expInventoryRow" style="display:none; margin-bottom: 14px;">
          <label for="expInventory">Select Inventory Item</label>
          <select id="expInventory">
            <option value="">-- Select Inventory Item --</option>
            <!-- Populated dynamically -->
          </select>
        </div>

        <div class="field">
          <label for="expName">Expense Name / Title</label>
          <input type="text" id="expName" required placeholder="e.g. Monthly Shop Rent, Generator EMI">
        </div>

        <div class="modal-row">
          <div class="field">
            <label for="expAmount">Amount (₹)</label>
            <input type="number" id="expAmount" min="1" required placeholder="e.g. 5000">
          </div>
          <div class="field">
            <label for="expDate">Expense Date</label>
            <input type="date" id="expDate" required>
          </div>
        </div>

        <div class="field">
          <label for="expNotes">Notes / Description</label>
          <textarea id="expNotes" rows="3" placeholder="Additional details (receipt numbers, transaction references, etc.)"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="window.SW.closeModal('expenseModal')">Cancel</button>
        <button type="submit" class="btn btn-accent" id="expenseSubmitBtn"><i class="ti ti-check"></i>Save expense</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
  <script src="{{ asset('js/expenses.js') }}?v=2.0"></script>
@endsection
