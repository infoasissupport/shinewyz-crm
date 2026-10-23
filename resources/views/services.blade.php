@extends('layouts.app')

@section('title', 'Services')

@section('page-id', 'services')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/services.css') }}?v={{ filemtime(public_path('css/services.css')) }}">
@endsection

@section('content')
<section class="view" id="services">



  <div class="svc-toolbar">
      <div class="view-header">

      <div class="view-title">Services</div>


  </div>
  
    <div class="sortedByFlex">
        <div class="svc-search-box">
      <i class="ti ti-search"></i>
      <input type="text" id="servicesSearchInput" placeholder="Search services..." aria-label="Search services">
    </div>
    <div class="svc-view-toggle">
      <button type="button" class="svc-view-btn active" data-view="grid" title="Grid view" aria-label="Grid view">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>
      </button>
      <button type="button" class="svc-view-btn" data-view="list" title="List view" aria-label="List view">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="4" y1="18" x2="20" y2="18"></line></svg>
      </button>
    </div>
    <button class="btn btn-accent" id="addServiceBtn"><i class="ti ti-plus"></i>Add Service</button>
    </div>
 
  </div>
<div class="scrollingMainall1">
    <div class="svc-grid"  id="servicesGrid"></div>
</div>

  <div class="svc-empty" id="servicesEmptyState" style="display:none;">No services match your filters.</div>


</section>

<div class="modal-overlay" id="serviceModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="serviceModalTitle">Add service</div>
      <button class="icon-btn" onclick="window.SW.closeModal('serviceModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <form id="serviceForm">
      <div class="modal-body">
        <div class="field"><label for="svcName">Service name</label><input type="text" id="svcName" required placeholder="e.g. Foam wash"></div>
        <div class="field"><label for="svcDesc">Description</label><input type="text" id="svcDesc" placeholder="e.g. Foam wash and rinse, hand dry"></div>
        <div class="field"><label for="svcCategory">Category</label>
          <select id="svcCategory">
            <option value="Exterior">Exterior</option>
            <option value="Interior">Interior</option>
            <option value="Bundle">Bundle</option>
            <option value="Detailing">Detailing</option>
            <option value="Premium">Premium</option>
            <option value="Add-on">Add-on</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div class="modal-row-3">
          <div class="field"><label for="svcPrice">Price (₹)</label><input type="number" id="svcPrice" min="0" required placeholder="e.g. 299"></div>
          <div class="field"><label for="svcDuration">Duration</label><input type="text" id="svcDuration" required placeholder="e.g. 20 min"></div>
          <div class="field"><label for="svcStaff">Staff required</label><input type="number" id="svcStaff" min="1" value="1" required></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn danger" id="deleteServiceBtn" style="display:none;margin-right:auto;"><i class="ti ti-x"></i>Delete service</button>
        <button type="button" class="btn" onclick="window.SW.closeModal('serviceModal')">Cancel</button>
        <button type="submit" class="btn btn-accent" id="serviceSubmitBtn"><i class="ti ti-plus"></i>Add service</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
  <script src="{{ asset('js/services.js') }}?v={{ time() }}"></script>
@endsection
