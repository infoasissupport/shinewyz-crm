@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-id', 'dashboard')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
@endsection

@section('content')
<section class="view" id="dashboard">
  <div class="view-header">
    <div class="view-title">Dashboard</div>
    <button class="btn btn-accent" id="newBookingBtn"><i class="ti ti-plus"></i>New wash</button>
  </div>
  <div class="fixedScrolldivCommon">

  <div class="dash-stats-grid">
    <div class="dash-stat-card">
      <div class="dash-stat-icon dash-badge-purple">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none">
          <rect x="3" y="4.5" width="18" height="17" rx="3" stroke="currentColor" stroke-width="1.8"/>
          <path d="M7.5 2.5v4M16.5 2.5v4M3.5 9h17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <path d="M7.5 12.5h.01M12 12.5h.01M16.5 12.5h.01M7.5 16.5h.01M12 16.5h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="dash-stat-body">
        <div class="dash-stat-label">Today's washes</div>
        <div class="dash-stat-value" id="kpiTodayBookings">—</div>
        <div class="dash-stat-sub">Today</div>
      </div>
    </div>
    <div class="dash-stat-card">
      <div class="dash-stat-icon dash-badge-amber">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20" fill="none">
          <path d="M5 18h22v5H5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
          <path d="M8 18l2-7h10l4 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M12 11V8h7v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <circle cx="10" cy="24" r="2.5" stroke="currentColor" stroke-width="1.8"/>
          <circle cx="22" cy="24" r="2.5" stroke="currentColor" stroke-width="1.8"/>
          <path d="M2.5 7h5M2.5 10h3M24.5 7h5M26.5 10h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="dash-stat-body">
        <div class="dash-stat-label">Cars in queue</div>
        <div class="dash-stat-value" id="kpiCarsWaiting">—</div>
        <div class="dash-stat-sub">Live</div>
      </div>
    </div>
    <div class="dash-stat-card">
      <div class="dash-stat-icon dash-badge-green">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none">
          <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
          <path d="m8 12 2.6 2.6L16.5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="dash-stat-body">
        <div class="dash-stat-label">Completed today</div>
        <div class="dash-stat-value" id="kpiCompletedToday">—</div>
        <div class="dash-stat-sub">Today</div>
      </div>
    </div>
    <div class="dash-stat-card">
      <div class="dash-stat-icon dash-badge-blue">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none">
          <rect x="3" y="4" width="18" height="16" rx="3" stroke="currentColor" stroke-width="1.8"/>
          <path d="M7 16v-3M11 16V9M15 16v-5M19 16v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <path d="M7 8h2M15 7h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="dash-stat-body">
        <div class="dash-stat-label">Revenue today</div>
        <div class="dash-stat-value" id="kpiRevenueToday">—</div>
        <div class="dash-stat-sub">Today</div>
      </div>
    </div>
  </div>
  <div class="dash-stats-grid">
    <div class="dash-stat-card">
      <div class="dash-stat-icon dash-badge-teal">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none">
          <rect x="3" y="4" width="18" height="16" rx="3" stroke="currentColor" stroke-width="1.8"/>
          <path d="M7 16v-3M11 16V9M15 16v-5M19 16v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <path d="M7 8h2M15 7h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="dash-stat-body">
        <div class="dash-stat-label">Revenue this week</div>
        <div class="dash-stat-value" id="kpiRevenueWeekly">—</div>
        <div class="dash-stat-sub">This week</div>
      </div>
    </div>
    <div class="dash-stat-card">
      <div class="dash-stat-icon dash-badge-pink">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none">
          <rect x="3" y="4" width="18" height="16" rx="3" stroke="currentColor" stroke-width="1.8"></rect>
          <path d="M7 16v-3M11 16V9M15 16v-5M19 16v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
          <path d="M7 8h2M15 7h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
        </svg>
      </div>
      <div class="dash-stat-body">
        <div class="dash-stat-label">Revenue this month</div>
        <div class="dash-stat-value" id="kpiRevenueMonthly">—</div>
        <div class="dash-stat-sub">This month</div>
      </div>
    </div>
    <div class="dash-stat-card">
      <div class="dash-stat-icon dash-badge-red">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none">
          <rect x="3" y="5" width="18" height="14" rx="3" stroke="currentColor" stroke-width="1.8"/>
          <path d="M3 9h18" stroke="currentColor" stroke-width="1.8"/>
          <circle cx="12" cy="14" r="3" stroke="currentColor" stroke-width="1.8"/>
          <path d="M12 12.5v1.7l1.2.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="dash-stat-body">
        <div class="dash-stat-label">Pending payments</div>
        <div class="dash-stat-value" id="kpiPendingPayments">—</div>
        <div class="dash-stat-sub"><span id="kpiPendingDeltaText">0 invoices</span></div>
      </div>
    </div>
  </div>

  <div class="grid-2">
    <div class="card">
      <div class="card-title">Revenue — last 7 days
        <span class="card-title-actions chart-actions">
          <label class="chart-type-select" for="revenueChartType"><i class="ti ti-chart-bar"></i><span class="chart-type-value" id="revenueChartTypeLabel">Bar</span><select id="revenueChartType" aria-label="Chart type"><option value="column">Bar</option><option value="line">Line</option><option value="area">Area</option></select><i class="ti ti-chevron-down"></i></label>
          <button type="button" class="chart-icon-btn" id="revenueFullscreenBtn" title="View full screen" aria-label="View full screen"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg></button>
          <a class="chart-report-link chart-report-link-inline" href="/reports">View report</a>
        </span>
      </div>
      <div id="revenueChart"></div>
      <a class="chart-report-link chart-report-link-mobile" href="/reports">View report</a>
    </div>
    <div class="card">
      <div class="card-title">Live queue<a class="chart-report-link" href="/queue">Open queue</a></div>

      <div class="flexScrollingMainall">
      <div id="dashboardQueueRows"><div class="dropdown-empty">No cars in queue right now.</div>
      </div>
</div>
    </div>
  </div>
  </div>
</section>

<div class="modal-overlay" id="newBookingModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">New booking</div>
      <div class="modal-close" onclick="window.SW.closeModal('newBookingModal')"><i class="ti ti-x"></i></div>
    </div>
    <form id="newBookingForm">
      <div class="modal-body">
        <div class="modal-row-3">
          <div class="field"><label for="nbCustomer">Customer name</label><input type="text" id="nbCustomer" required placeholder="Customer Name"></div>
     
          <div class="field"><label for="nbPhone">Phone</label><input type="tel" id="nbPhone" inputmode="numeric" pattern="[0-9]*" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Phone"></div>
         <div class="field"><label for="nbVehicle">Vehicle Name</label><input type="text" id="nbVehicle" placeholder="Vehicle Name"></div>
        </div>
        <div class="modal-row-3">
        
          <div class="field"><label for="nbVehiclePlate">Vehicle Number</label><input type="text" id="nbVehiclePlate" placeholder="Vehicle Number"></div>
    <div class="field"><label for="nbVehicleType">Vehicle Type</label>
            <select id="nbVehicleType">
              <option value="hatchback">Hatchback</option>
              <option value="sedan" selected>Sedan</option>
              <option value="suv">SUV</option>
              <option value="luxury">Luxury</option>
              <option value="two-wheeler">2-Wheeler</option>
            </select>
          </div>
                <div class="field"><label for="nbSource">Source</label>
            <select id="nbSource">
              <option value="walkin">Walk-in</option>
              <option value="phone">Phone</option>
            </select>
          </div>
        </div>
        <div class="modal-row-3">
         
    
          <div class="field"><label for="nbDate">Date</label><input type="date" id="nbDate"></div>
            <div class="field"><label for="nbTime">Time</label><input type="time" id="nbTime"></div>
        </div>
        <div class="field" >
          <label style="margin-bottom:6px;display:block;">Select Services (Select one or more)</label>
          <div class="svc-select" id="nbSvcSelect">
            <div class="svc-select-control" id="nbSvcControl">
              <div class="svc-chip-list" id="nbSvcChips">
                <span class="svc-placeholder" id="nbSvcPlaceholder">Search and select services…</span>
              </div>
              <i class="ti ti-chevron-down svc-caret"></i>
            </div>
            <div class="svc-dropdown" id="nbSvcDropdown">
              <div class="svc-dropdown-search">
              
                <input type="text" id="nbServiceSearch" class="svc-search-input" placeholder="Search services…" aria-label="Search services" autocomplete="off">
              </div>
              <div id="nbServicesContainer" class="svc-options">
                <!-- Dynamic checkboxes -->
              </div>
              <div class="svc-empty" id="nbSvcEmpty" style="display:none;">No services match your search.</div>
              <div class="svc-add-row">
                <button type="button" id="nbAddServiceToggle" class="btn btn-secondary" style="height:32px;padding:6px 12px;font-size:12px;width:100%;justify-content:center;"><i class="ti ti-plus"></i>Add new service</button>
                <div id="nbAddServiceForm" style="display:none;gap:8px;margin-top:10px;align-items:center;">
                  <input type="text" id="nbNewServiceName" placeholder="Service name" style="flex:1 1 auto;min-width:0;">
                  <input type="number" id="nbNewServicePrice" placeholder="Price (₹)" min="0" style="flex:0 0 100px;">
                  <button type="button" id="nbNewServiceSave" class="btn btn-accent" style="height:36px;padding:0 14px;font-size:12px;">Add</button>
                  <button type="button" id="nbNewServiceCancel" class="btn" style="height:36px;padding:0 14px;font-size:12px;">Cancel</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      
        <div class="field"><label for="nbNotes">Notes</label><textarea id="nbNotes" placeholder="Notes"></textarea></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="window.SW.closeModal('newBookingModal')">Cancel</button>
        <button type="submit" class="btn btn-accent"><i class="ti ti-plus"></i>Create booking</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="{{ asset('js/dashboard.js') }}?v={{ filemtime(public_path('js/dashboard.js')) }}"></script>
@endsection
