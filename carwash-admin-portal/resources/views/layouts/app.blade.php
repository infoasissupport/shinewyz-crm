<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shinewise — @yield('title', 'Sparkle Auto Care')</title>
<link rel="stylesheet" href="{{ asset('css/icons.css') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/common.css') }}?v={{ filemtime(public_path('css/common.css')) }}">
@yield('styles')
</head>
<body data-page="@yield('page-id')">
<div class="app">

<!-- ============ SIDEBAR ============ -->
<aside class="sidebar">
  <div class="brand">
    <a class="innerLogoMain" href="/dashboard">
      <img src="{{ asset('images/shinewyz-logo-blacl-desktop.png') }}" alt="Shinewise" class="brand-logo-img">
    </a>
    <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close navigation menu">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </button>
  </div>

  <div class="sidebar-menu">
    <div class="nav-group">
      <div class="nav-label">Overview</div>
      <a class="nav-item @if(Request::is('dashboard')) active @endif" data-page="dashboard" href="/dashboard"><i class="ti ti-layout-dashboard icon-purple"></i>Dashboard</a>
      <a class="nav-item @if(Request::is('queue')) active @endif" data-page="queue" href="/queue"><i class="ti ti-clock-hour-4 icon-amber"></i>Queue</a>
    </div>
  <div class="nav-group">
    <div class="nav-label">Customers</div>
    <a class="nav-item @if(Request::is('crm') || Request::is('vehicles')) active @endif" data-page="crm" href="/crm"><i class="ti ti-users icon-blue"></i>Customers</a>
  </div>
  <div class="nav-group">
    <div class="nav-label">Operations</div>
    <!-- <a class="nav-item @if(Request::is('bookings')) active @endif" data-page="bookings" href="/bookings"><i class="ti ti-calendar-event icon-green"></i>Bookings</a> -->
    <a class="nav-item @if(Request::is('jobcards')) active @endif" data-page="jobcards" href="/jobcards"><i class="ti ti-clipboard-check icon-pink"></i>Job cards</a>
    <a class="nav-item @if(Request::is('services')) active @endif" data-page="services" href="/services"><i class="ti ti-wash icon-cyan"></i>Services</a>
    <a class="nav-item @if(Request::is('staff')) active @endif" data-page="staff" href="/staff"><i class="ti ti-id-badge-2 icon-indigo"></i>Staff</a>
    <!-- <a class="nav-item @if(Request::is('inventory')) active @endif" data-page="inventory" href="/inventory"><i class="ti ti-package icon-orange"></i>Inventory</a> -->
  </div>
  <div class="nav-group">
    <div class="nav-label">Revenue</div>
    <a class="nav-item @if(Request::is('payments')) active @endif" data-page="payments" href="/payments"><i class="ti ti-credit-card icon-gold"></i>Payments & invoicing</a>
    <a class="nav-item @if(Request::is('expenses')) active @endif" data-page="expenses" href="/expenses"><i class="ti ti-report-money icon-red"></i>Expenses</a>
    <a class="nav-item @if(Request::is('reports')) active @endif" data-page="reports" href="/reports"><i class="ti ti-chart-bar icon-violet"></i>Reports</a>
  </div>
  <div class="nav-group">
    <div class="nav-label">Admin</div>
    <a class="nav-item @if(Request::is('branches')) active @endif" data-page="branches" href="/branches"><i class="ti ti-building-store icon-mint"></i>Branches</a>
  </div>
  </div>

  <div class="sidebar-account">
    <div class="dropdown" id="userDropdown">
      <button type="button" class="sidebar-account-toggle" id="userToggle" aria-label="Open account menu">
        <span class="avatar">SA</span>
        <span class="sidebar-account-details"><strong id="userNameLabel">Admin User</strong><small id="userEmailLabel">admin@carwash.com</small></span>
     <i class="ti ti-chevron-down" style="font-size:17px;"></i>
      </button>
      <div class="dropdown-menu sidebar-user-menu" id="userMenu">
        <div class="dropdown-user"><div class="avatar">SA</div><div><div class="dropdown-user-name">Admin User</div><div class="dropdown-user-email">admin@carwash.com</div></div></div>
        <div class="dropdown-divider"></div>
        <div class="dropdown-item" id="profileBtn" ><i class="ti ti-id-badge-2"></i>My profile</div>
        <div class="dropdown-divider"></div>
        <div class="dropdown-item danger" id="logoutBtn"><i class="ti ti-x"></i>Log out</div>
      </div>
    </div>
  </div>
</aside>

<!-- ============ MAIN ============ -->
<div class="main">
  <div class="topbar">
    <button type="button" class="icon-btn" id="sidebarToggleBtn" style="display:none" aria-label="Toggle navigation menu">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="4" y1="18" x2="20" y2="18"></line></svg>
    </button>
    <a href="/dashboard" class="topbar-brand-mobile"><img src="{{ asset('images/logo.png') }}" alt="Shinewise"></a>
    <div class="topbar-search">
<svg _ngcontent-ng-c389393698="" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path _ngcontent-ng-c389393698="" d="M14 14L11.1 11.1M12.6667 7.33333C12.6667 10.2789 10.2789 12.6667 7.33333 12.6667C4.38781 12.6667 2 10.2789 2 7.33333C2 4.38781 4.38781 2 7.33333 2C10.2789 2 12.6667 4.38781 12.6667 7.33333Z" stroke="#fff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>
      <input type="text" id="globalSearchInput" aria-label="Search Customers, Vehicles" placeholder="Search Customers, Vehicles" autocomplete="off">
      <div class="search-results" id="searchResults"></div>
    </div>
    <div class="topbar-right">
      <div class="dropdown" id="branchDropdown">
        <div class="branch-pill" id="branchToggle"><i class="ti ti-map-pin"></i><span id="branchLabel">Select branch</span><i class="ti ti-chevron-down"></i></div>
        <div class="dropdown-menu" id="branchMenu">
          <!-- Loaded dynamically -->
        </div>
      </div>
      <div class="dropdown" id="waDropdown">
        <div class="icon-btn" id="waToggle"><i class="ti ti-brand-whatsapp"></i><span class="dot" id="waDot" style="display:none;background:red;"></span></div>
        <div class="dropdown-menu dropdown-menu-wide" id="waMenu">
          <div class="dropdown-header">WhatsApp CRM<span class="dropdown-header-actions"></span></div>
          <div class="notif-list" id="waList" style="max-height:300px;overflow-y:auto;padding:8px;"></div>
        </div>
      </div>
      <div class="dropdown" id="notifDropdown">
        <div class="icon-btn" id="notifToggle"><i class="ti ti-bell"></i><span class="dot" id="notifDot"></span></div>
        <div class="dropdown-menu dropdown-menu-wide" id="notifMenu">
          <div class="dropdown-header">Notifications<span class="dropdown-header-actions"><span class="link" id="markAllRead">Mark all read</span><span class="link link-danger" id="clearAllNotifs">Clear</span></span></div>
          <div class="notif-list" id="notifList"></div>
        </div>
      </div>

    </div>
  </div>

  @yield('content')

</div>
</div>

<footer class="app-footer">
  <span>© {{ date('Y') }} Shinewise. All rights reserved.</span>
  <span>Designed by <a herf=""><b>TechInfoasis</b> </a> </span>
</footer>

<div class="modal-overlay" id="profileModal">
  <div class="modal" style="max-width:400px;">
    <div class="modal-header">
      <div class="modal-title">My Profile</div>
    </div>
    <div class="modal-body profile-modal-body">
      <div class="profile-card">
        <div class="avatar profile-avatar">SA</div>
        <h3 class="profile-name" id="profileName">Admin User</h3>
        <p class="profile-role" id="profileRole">Administrator</p>
      </div>
      <div class="profile-details">
        <div class="profile-detail-row">
          <span class="profile-detail-label">Email Address</span>
          <span class="profile-detail-value" id="profileEmail">admin@carwash.com</span>
        </div>
        <div class="profile-detail-row">
          <span class="profile-detail-label">Member Since</span>
          <span class="profile-detail-value" id="profileJoined">July 2026</span>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn" onclick="window.SW.closeModal('profileModal')" >Close</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="photoLightboxModal">
  <div class="photo-lightbox">
    <button type="button" class="photo-lightbox-close" onclick="window.SW.closeModal('photoLightboxModal')" aria-label="Close">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </button>
    <img id="photoLightboxImg" src="" alt="Photo preview">
  </div>
</div>

<script src="{{ asset('js/common.js') }}?v={{ filemtime(public_path('js/common.js')) }}"></script>
@yield('scripts')
</body>
</html>
