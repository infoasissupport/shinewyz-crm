@extends('layouts.app')

@section('title', 'Job Cards')

@section('page-id', 'jobcards')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/jobcards.css') }}?v=1.1">
@endsection

@section('content')
<section class="view" id="jobcards">
  <div class="view-header">

      <div class="view-title">Job Cards</div>

  

  </div>

  <div class="fixedScrolldivCommon" id="jcScrollArea">
    <div class="jc-toolbar">
      <div class="jc-search">
        <i class="ti ti-search"></i>
        <input type="text" id="jcSearchInput" placeholder="Search by Job Card, Vehicle or Customer..." aria-label="Search job cards">
      </div>
      <button type="button" class="btn jc-filter-toggle" id="jcFilterToggle">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="4 4 20 4 14 12.5 14 19 10 21 10 12.5 4 4"></polygon></svg>Filters<span class="jc-filter-badge" id="jcFilterBadge" style="display:none;"></span>
        <i class="ti ti-chevron-down jc-filter-chevron"></i>
      </button>
      <div class="jc-filter-fields" id="jcFilterFields">
        <select id="jcFilterBay" aria-label="Filter by bay"><option value="">All Bays</option></select>
        <select id="jcFilterService" aria-label="Filter by service"><option value="">All Services</option></select>
        <input type="date" id="jcFilterDate" aria-label="Filter by date">
        <button type="button" class="btn" id="jcClearFilters">Clear</button>
        <button type="button" class="btn btn-accent" id="jcApplyFilters">Apply</button>
      </div>
    </div>

    <div class="table-scroll" id="jcTableScroll">
      <table>
        <thead>
          <tr>
            <th>Job Card</th>
            <th>Vehicle &amp; Customer</th>
            <th>Service</th>
            <th>Bay</th>
            <th>Assigned To</th>
            <th>Start Time</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="jcTableRows"></tbody>
      </table>
    </div>

    <div class="table-pagination">
      <span id="jcPageInfo"></span>
      <div class="pagination-btns" id="jcPager"></div>
    </div>
  </div>
</section>

<div class="modal-overlay" id="jobCardModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="jobCardModalTitle">Job card</div>
      <button class="icon-btn" onclick="window.SW.closeModal('jobCardModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body" id="jobCardModalBody"></div>
    <div class="modal-footer" id="jobCardModalFooter"></div>
  </div>
</div>

<div class="modal-overlay" id="newJobCardModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">New job card</div>
      <button class="icon-btn" onclick="window.SW.closeModal('newJobCardModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <form id="newJobCardForm">
      <div class="modal-body">
        <div class="modal-row">
          <div class="field"><label for="njCustomer">Customer</label><input type="text" id="njCustomer" placeholder="e.g. Ravi Kumar"></div>
          <div class="field"><label for="njVehicle">Vehicle</label><input type="text" id="njVehicle" required placeholder="e.g. Honda City"></div>
        </div>
        <div class="modal-row">
          <div class="field"><label for="njService">Service</label>
            <select id="njService" required>
              <option value="">Select a service</option>
            </select>
          </div>
          <div class="field"><label for="njBay">Bay</label>
            <select id="njBay">
              <option>Bay 1</option>
              <option>Bay 2</option>
              <option>Bay 3</option>
              <option>Bay 4</option>
            </select>
          </div>
        </div>
        <div class="field"><label for="njStaff">Assigned staff</label>
          <select id="njStaff">
            <option value="">Select staff</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="window.SW.closeModal('newJobCardModal')">Cancel</button>
        <button type="submit" class="btn btn-accent"><i class="ti ti-plus"></i>Create job card</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="startJobModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Start Job</div>
      <button class="icon-btn" onclick="window.SW.closeModal('startJobModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="sjJobId">
      <p style="font-size:12.5px;color:var(--text-3);margin-bottom:12px;">Add a photo of the vehicle's starting condition before beginning work (optional).</p>
      <div class="jc-photo-grid" id="sjPhotoGrid">
        <button type="button" class="jc-add-photo" id="sjAddPhotoBtn" title="Add starting photo">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h3l1.5-2h7L17 8h3a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Z"/><circle cx="12" cy="13" r="3.5"/></svg>
        </button>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn" onclick="window.SW.closeModal('startJobModal')">Cancel</button>
      <button type="button" class="btn btn-accent" id="sjConfirmBtn"><i class="ti ti-check"></i>Start Job</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="editJobCardModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit job card</div>
      <button class="icon-btn" onclick="window.SW.closeModal('editJobCardModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <form id="editJobCardForm">
      <div class="modal-body">
        <input type="hidden" id="ejId">
        <div class="field"><label for="ejCustomer">Customer</label><input type="text" id="ejCustomer" placeholder="Customer name"></div>
        <div class="field"><label for="ejVehicle">Vehicle</label><input type="text" id="ejVehicle" required placeholder="e.g. Honda City"></div>
        <div class="field"><label for="ejService">Service</label><input type="text" id="ejService" required placeholder="e.g. Foam wash"></div>
        <div class="modal-row">
          <div class="field"><label for="ejBay">Bay</label>
            <select id="ejBay">
              <option value="">Select bay</option>
            </select>
          </div>
          <div class="field"><label for="ejStaff">Assigned staff</label>
            <select id="ejStaff" required>
              <option value="">Select staff</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="window.SW.closeModal('editJobCardModal')">Cancel</button>
        <button type="submit" class="btn btn-accent"><i class="ti ti-check"></i>Save changes</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
  <script src="{{ asset('js/jobcards.js') }}?v={{ time() }}"></script>
@endsection
