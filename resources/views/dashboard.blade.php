@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-id', 'dashboard')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
@endsection

@section('content')
<section class="view" id="dashboard">
  <div class="view-header">
    <!-- <div class="view-title dash-page-title">Dashboard</div> -->
    <button class="btn btn-accent" id="newBookingBtn"><i class="ti ti-plus"></i>New Appointment</button>
  </div>
  <div class="fixedScrolldivCommon">

  <div class="dash-stats-grid">
    <div class="dash-stat-card is-link" data-href="/bookings" role="link" tabindex="0" aria-label="View today's bookings">
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
    <div class="dash-stat-card is-link" data-href="/queue#waitlist" role="link" tabindex="0" aria-label="View cars in the queue">
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
    <div class="dash-stat-card is-link" data-href="/queue#completed" role="link" tabindex="0" aria-label="View washes completed today">
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
    <div class="dash-stat-card is-link" data-href="/reports?timeframe=daily" role="link" tabindex="0" aria-label="View today's revenue report">
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
  <div class="dash-stats-grid1">
    <div class="dash-stat-card is-link" data-href="/reports?timeframe=weekly" role="link" tabindex="0" aria-label="View this week's revenue report">
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
    <div class="dash-stat-card is-link" data-href="/reports?timeframe=monthly" role="link" tabindex="0" aria-label="View this month's revenue report">
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
    <div class="dash-stat-card is-link" data-href="/payments?filter=pending" role="link" tabindex="0" aria-label="View pending payments">
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
      <div class="card-title"><span class="dash-chart-heading"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5.2 10.2 6.9 5.6A2.4 2.4 0 0 1 9.1 4h5.8a2.4 2.4 0 0 1 2.2 1.6l1.7 4.6A2.6 2.6 0 0 1 21 12.8V17a1 1 0 0 1-1 1h-1v1.2a1.3 1.3 0 0 1-2.6 0V18H7.6v1.2a1.3 1.3 0 0 1-2.6 0V18H4a1 1 0 0 1-1-1v-4.2a2.6 2.6 0 0 1 2.2-2.6zm2.1-.2h9.4l-1.3-3.6a.9.9 0 0 0-.8-.6H9.4a.9.9 0 0 0-.8.6L7.3 10zM7 15.2a1.3 1.3 0 1 0 0-2.6 1.3 1.3 0 0 0 0 2.6zm10 0a1.3 1.3 0 1 0 0-2.6 1.3 1.3 0 0 0 0 2.6z"/></svg><span>Live Queue</span></span><a class="chart-report-link dash-open-queue" href="/queue">Open Queue <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 8h14l-4-4M20 16H6l4 4"/></svg></a></div>

      <div class="flexScrollingMainall">
      <div id="dashboardQueueRows"><div class="dash-queue-empty"><span class="dash-queue-empty-icon"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5.2 10.2 6.9 5.6A2.4 2.4 0 0 1 9.1 4h5.8a2.4 2.4 0 0 1 2.2 1.6l1.7 4.6A2.6 2.6 0 0 1 21 12.8V17a1 1 0 0 1-1 1h-1v1.2a1.3 1.3 0 0 1-2.6 0V18H7.6v1.2a1.3 1.3 0 0 1-2.6 0V18H4a1 1 0 0 1-1-1v-4.2a2.6 2.6 0 0 1 2.2-2.6zm2.1-.2h9.4l-1.3-3.6a.9.9 0 0 0-.8-.6H9.4a.9.9 0 0 0-.8.6L7.3 10zM7 15.2a1.3 1.3 0 1 0 0-2.6 1.3 1.3 0 0 0 0 2.6zm10 0a1.3 1.3 0 1 0 0-2.6 1.3 1.3 0 0 0 0 2.6z"/></svg><svg class="dash-queue-empty-spark" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l2.2 7.8L22 12l-7.8 2.2L12 22l-2.2-7.8L2 12l7.8-2.2z"/></svg></span><div class="dash-queue-empty-title">No cars in queue right now</div><div class="dash-queue-empty-sub">New appointments will appear here</div></div>
      </div>
</div>
    </div>
       <div class="card">
      <div class="card-title">
        <span class="dash-chart-heading"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M4 20V11M9.3 20V6M14.6 20V9M20 20V4"/></svg><span>Revenue — <span id="revenueRangeTitle">Last 7 Days</span></span></span>
        <span class="card-title-actions chart-actions">
          <label class="chart-type-select" for="revenueChartType"><svg class="chart-select-icon chart-select-icon--accent" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M5 20v-8M12 20V5M19 20v-11"/></svg><span class="chart-type-value" id="revenueChartTypeLabel">Bar</span><select id="revenueChartType" aria-label="Chart type"><option value="column">Bar</option><option value="line">Line</option><option value="area">Area</option></select><i class="ti ti-chevron-down"></i></label>
          <label class="chart-type-select chart-range-select" for="revenueRange"><svg class="chart-select-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4.5" width="18" height="17" rx="2.5"/><path d="M8 2.5v4M16 2.5v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 17.5h.01M12 17.5h.01"/></svg><span class="chart-type-value" id="revenueRangeLabel">Last 7 Days</span><select id="revenueRange" aria-label="Date range"><option value="7">Last 7 Days</option><option value="14">Last 14 Days</option><option value="30">Last 30 Days</option></select><i class="ti ti-chevron-down"></i></label>
        </span>
      </div>
      <div id="revenueChart"></div>
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
          <div class="field" style="position:relative;">
            <label for="nbCustomer">Customer name</label>
            <input type="text" id="nbCustomer" required placeholder="Customer Name" autocomplete="off">
            <div id="nbCustomerDropdown" class="autocomplete-dropdown" style="display:none; position:absolute; top:100%; left:0; width:100%; background:#fff; border:1px solid #e2e8f0; z-index:100; max-height:200px; overflow-y:auto; border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.1); margin-top:4px;"></div>
          </div>
     
          <div class="field" style="position:relative;">
            <label for="nbPhone">Phone</label>
            <input type="tel" id="nbPhone" inputmode="numeric" pattern="[0-9]*" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Phone" autocomplete="off">
            <div id="nbPhoneDropdown" class="autocomplete-dropdown" style="display:none; position:absolute; top:100%; left:0; width:100%; background:#fff; border:1px solid #e2e8f0; z-index:100; max-height:200px; overflow-y:auto; border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.1); margin-top:4px;"></div>
          </div>
          
          <div class="field"><label for="nbVehicle">Vehicle Name</label><input type="text" id="nbVehicle" placeholder="Vehicle Name"></div>
        </div>
        <style>
          .autocomplete-item { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; }
          .autocomplete-item:hover { background: #f8fafc; }
          .autocomplete-item-name { font-weight: 600; color: #1e293b; }
          .autocomplete-item-desc { font-size: 12px; color: #64748b; margin-top: 2px; }
        </style>
        <div class="modal-row-3">
        
          <div class="field"><label for="nbVehiclePlate">Vehicle Number</label><input type="text" id="nbVehiclePlate" placeholder="Vehicle Number"><div id="nbVehicleLoyalty" style="font-size:12px;color:#10B981;margin-top:4px;display:none;font-weight:600;"></div></div>
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
         <div class="field">
          <label>Before Photos</label>
          <div class="jc-photo-grid">
            <div id="nbPhotosPreview" style="display:contents;"></div>
            <button type="button" class="jc-add-photo" id="nbAddBeforePhotoGallery" title="Upload from gallery">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
            </button>
            <button type="button" class="jc-add-photo" id="nbAddBeforePhotoCamera" title="Take photo directly">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7h1a2 2 0 0 0 2 -2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2" /><path d="M9 13a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
            </button>
          </div>
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
