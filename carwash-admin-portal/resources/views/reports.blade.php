@extends('layouts.app')

@section('title', 'Reports')

@section('page-id', 'reports')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/reports.css') }}?v={{ filemtime(public_path('css/reports.css')) }}">
@endsection

@section('content')
<section class="view" id="reports">
  <div class="view-header"><div><div class="view-title">Reports and analytics</div></div><button class="btn" id="exportReportBtn"><i class="ti ti-download"></i>Export report</button></div>
  <div class="fixedScrolldivCommon">
  <div class="kpi-grid">
    <div class="kpi kpi--green"><div class="kpi-label">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="4" width="18" height="16" rx="3" stroke="currentColor" stroke-width="1.8"/>
        <path d="M7 16v-3M11 16V9M15 16v-5M19 16v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <path d="M7 8h2M15 7h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      </svg>Revenue (this month)</div><div class="kpi-value" id="kpiRevenue">—</div><span class="kpi-delta up" id="kpiRevenueDelta"></span></div>
    <div class="kpi kpi--red"><div class="kpi-label">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="6" width="18" height="13" rx="3" stroke="currentColor" stroke-width="1.8"/>
        <path d="M3 10.5h18" stroke="currentColor" stroke-width="1.8"/>
        <path d="M8 15h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      </svg>Expenses</div><div class="kpi-value" id="kpiExpenses">—</div><span class="kpi-delta down" id="kpiExpensesDelta"></span></div>
    <div class="kpi kpi--blue"><div class="kpi-label">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
        <path d="M3 17l6-6 4 4 8-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M15 7h6v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>Profit</div><div class="kpi-value" id="kpiProfit">—</div><span class="kpi-delta up" id="kpiProfitDelta"></span></div>
    <div class="kpi kpi--purple"><div class="kpi-label">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
        <path d="M17 2l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M3 12v-2a4 4 0 0 1 4-4h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M7 22l-4-4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M21 12v2a4 4 0 0 1-4 4H3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>Repeat customers</div><div class="kpi-value" id="kpiRepeatPercentage">—</div><span class="kpi-delta" id="kpiRepeatDelta"></span></div>
  </div>
  <div class="grid-2">
    <div class="card">
      <div class="card-title">Daily Revenue (this month)
        <span class="card-title-actions chart-actions">
          <label class="chart-type-select" for="dailyRevenueChartType"><i class="ti ti-chart-bar"></i><span class="chart-type-value" id="dailyRevenueChartTypeLabel">Area</span><select id="dailyRevenueChartType" aria-label="Chart type"><option value="area" selected>Area</option><option value="line">Line</option><option value="column">Column</option></select><i class="ti ti-chevron-down"></i></label>
        </span>
      </div>
      <div id="dailyRevenueChart"></div>
    </div>
    <div class="card">
      <div class="card-title">Top Services
        <span class="card-title-actions chart-actions">
          <label class="chart-type-select" for="topServicesChartType"><i class="ti ti-chart-bar"></i><span class="chart-type-value" id="topServicesChartTypeLabel">Bar</span><select id="topServicesChartType" aria-label="Chart type"><option value="bar" selected>Bar</option><option value="column">Column</option></select><i class="ti ti-chevron-down"></i></label>
        </span>
      </div>
      <div id="topServicesChart"></div>
    </div>
  </div>
  <div class="grid-2">
    <div class="card">
      <div class="card-title">Peak hours
        <span class="card-title-actions chart-actions">
          <label class="chart-type-select" for="peakHoursChartType"><i class="ti ti-chart-bar"></i><span class="chart-type-value" id="peakHoursChartTypeLabel">Column</span><select id="peakHoursChartType" aria-label="Chart type"><option value="column" selected>Column</option><option value="line">Line</option></select><i class="ti ti-chevron-down"></i></label>
        </span>
      </div>
      <div id="peakHoursChart"></div>
    </div>
    <div class="card">
      <div class="card-title">Staff performance
        <span class="card-title-actions chart-actions">
          <label class="chart-type-select" for="staffPerformanceChartType"><i class="ti ti-chart-bar"></i><span class="chart-type-value" id="staffPerformanceChartTypeLabel">Bar</span><select id="staffPerformanceChartType" aria-label="Chart type"><option value="bar" selected>Bar</option><option value="column">Column</option></select><i class="ti ti-chevron-down"></i></label>
        </span>
      </div>
      <div id="staffPerformanceChart"></div>
    </div>
  </div>
  </div>
</section>
@endsection

@section('scripts')
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="{{ asset('js/reports.js') }}"></script>
@endsection
