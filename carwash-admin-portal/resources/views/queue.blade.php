@extends('layouts.app')

@section('title', 'Queue')

@section('page-id', 'queue')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/queue.css') }}?v={{ filemtime(public_path('css/queue.css')) }}">
@endsection

@section('content')
<section class="view" id="queue">
  <div class="view-header">
    <div class="view-title">Queue management</div>
   
  </div>
  <div class="fixedScrolldivCommon">
      <div class="card-title">
        <span>Wash Bays</span>
  
    
          <button class="btn btn-accent" id="addBayBtn"><i class="ti ti-plus"></i>Add Bay</button>
     
      </div>
      <div class="grid-3" id="queueBaysGrid"></div>

  <div class="card">
    <div class="card-title">
      <span>Waitlist</span>
      <div class="cw-search">
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path _ngcontent-ng-c389393698="" d="M14 14L11.1 11.1M12.6667 7.33333C12.6667 10.2789 10.2789 12.6667 7.33333 12.6667C4.38781 12.6667 2 10.2789 2 7.33333C2 4.38781 4.38781 2 7.33333 2C10.2789 2 12.6667 4.38781 12.6667 7.33333Z" stroke="#172033" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>
        <input type="text" id="waitlistSearchInput" placeholder="Search Vehicle, Customer, Service" aria-label="Search waitlist">
      </div>
    </div>
    <div class="table-scroll" id="waitlistTableScroll">
      <table>
        <thead>
          <tr>
            <th>Vehicle</th>
            <th>Customer</th>
            <th>Service</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="queueWaitlistRows"></tbody>
      </table>
    </div>
    <div class="table-pagination">
      <span id="waitlistPageInfo"></span>
      <div class="pagination-btns">
        <button type="button" class="btn" id="waitlistPrevBtn">Prev</button>
        <button type="button" class="btn" id="waitlistNextBtn">Next</button>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">
      <span>Completed Today <span class="tag" id="completedCountBadge" style="background:#E7F7EE;color:#166534;">0 washes</span></span>
      <div class="cw-search">
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M14 14L11.1 11.1M12.6667 7.33333C12.6667 10.2789 10.2789 12.6667 7.33333 12.6667C4.38781 12.6667 2 10.2789 2 7.33333C2 4.38781 4.38781 2 7.33333 2C10.2789 2 12.6667 4.38781 12.6667 7.33333Z" stroke="#172033" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>
        <input type="text" id="completedSearchInput" placeholder="Search Vehicle, Customer, Service" aria-label="Search completed washes">
      </div>
    </div>
    <div class="table-scroll" id="completedTableScroll">
      <table>
        <thead>
          <tr>
            <th>Vehicle</th>
            <th>Customer</th>
            <th>Service</th>
            <th>Staff</th>
            <th>Completed</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="queueCompletedRows"></tbody>
      </table>
    </div>
    <div class="table-pagination">
      <span id="completedPageInfo"></span>
      <div class="pagination-btns">
        <button type="button" class="btn" id="completedPrevBtn">Prev</button>
        <button type="button" class="btn" id="completedNextBtn">Next</button>
      </div>
    </div>
  </div>

  </div>

</section>

<div class="modal-overlay" id="assignBookingModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Start Wash / Check In</div>
      <button class="icon-btn" onclick="window.SW.closeModal('assignBookingModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <form id="assignBookingForm">
      <div class="modal-body">
        <input type="hidden" id="abBookingId">
        <input type="hidden" id="abService">
        <input type="hidden" id="abPhone">
        <input type="hidden" id="abBranchId">

        <div class="modal-row">
          <div class="field">
            <label class="label" for="abVehicle">Vehicle</label>
            <input type="text" id="abVehicle" readonly style="background:#f6f7f9;color:#475569;">
          </div>
          <div class="field">
            <label class="label" for="abCustomer">Customer</label>
            <input type="text" id="abCustomer" readonly style="background:#f6f7f9;color:#475569;">
          </div>
        </div>

        <div class="modal-row">
          <div class="field">
            <label class="label" for="abBay">Bay</label>
            <select id="abBay" required>
              <option value="">Select bay</option>
            </select>
          </div>
          <div class="field">
            <label class="label" for="abStaff">Assign Staff</label>
            <select id="abStaff" required>
              <option value="">Select staff</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="window.SW.closeModal('assignBookingModal')">Cancel</button>
        <button type="submit" class="btn btn-accent"><i class="ti ti-plus"></i>Start Wash</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="editWaitlistModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Waitlist Entry</div>
      <button class="icon-btn" onclick="window.SW.closeModal('editWaitlistModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <form id="editWaitlistForm">
      <div class="modal-body">
        <input type="hidden" id="ewId">
        <div class="modal-row">
          <div class="field">
            <label class="label" for="ewCustomer">Customer</label>
            <input type="text" id="ewCustomer" required placeholder="Customer Name">
          </div>
          <div class="field">
            <label class="label" for="ewPhone">Phone</label>
            <input type="tel" id="ewPhone" inputmode="numeric" pattern="[0-9]*" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Phone Number">
          </div>
        </div>
        <div class="modal-row">
          <div class="field">
            <label class="label" for="ewVehiclePlate">Vehicle Number</label>
            <input type="text" id="ewVehiclePlate" placeholder="Vehicle Number">
          </div>
          <div class="field">
            <label class="label" for="ewVehicle">Vehicle Model</label>
            <input type="text" id="ewVehicle" placeholder="Vehicle Model">
          </div>
        </div>
        <div class="field">
          <label class="label" for="ewService">Service</label>
          <input type="text" id="ewService" required placeholder="Service">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="window.SW.closeModal('editWaitlistModal')">Cancel</button>
        <button type="submit" class="btn btn-accent" id="editWaitlistSubmitBtn"><i class="ti ti-check"></i>Save changes</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="completedJobModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="completedJobModalTitle">Job card</div>
      <button class="icon-btn" onclick="window.SW.closeModal('completedJobModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body" id="completedJobModalBody"></div>
    <div class="modal-footer">
      <a class="btn" id="completedJobFullLink" href="/jobcards">Open full job card</a>
      <button type="button" class="btn btn-accent" onclick="window.SW.closeModal('completedJobModal')">Close</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="addBayModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Add Bay</div>
      <button class="icon-btn" onclick="window.SW.closeModal('addBayModal')" aria-label="Close modal"><i class="ti ti-x"></i></button>
    </div>
    <form id="addBayForm">
      <div class="modal-body">
        <div class="field">
          <label class="label" for="newBayName">Bay name</label>
          <input type="text" id="newBayName" required placeholder="Bay Name">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="window.SW.closeModal('addBayModal')">Cancel</button>
        <button type="submit" class="btn btn-accent"><i class="ti ti-plus"></i>Add Bay</button>
      </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')
  <script src="{{ asset('js/queue.js') }}?v=1788245435"></script>
@endsection
