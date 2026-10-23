const fs = require('fs');
const file = 'public/js/staff.js';
let content = fs.readFileSync(file, 'utf8');

// SVG Icon
const CALENDAR_SVG = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';

// 1. Inject calendar button in table
let oldTableBtn = '<td><button type="button" class="stf-edit-btn" data-id="\' + s.id + \'" title="Edit staff" aria-label="Edit staff">';
let newTableBtn = '<td>' + 
  '<button type="button" class="stf-calendar-btn" data-id="\' + s.id + \'" title="View Attendance" aria-label="View Attendance" style="background:transparent; border:none; cursor:pointer; color:#667085; margin-right:8px;">' + CALENDAR_SVG + '</button>' +
  '<button type="button" class="stf-edit-btn" data-id="\' + s.id + \'" title="Edit staff" aria-label="Edit staff">';
content = content.replace(oldTableBtn, newTableBtn);

// 2. Inject calendar button in grid
let oldGridBtn = '<button type="button" class="emp-action-btn edit" data-action="edit" data-id="\' + s.id + \'" title="Edit employee">';
let newGridBtn = '<button type="button" class="emp-action-btn view-cal" data-action="calendar" data-id="\' + s.id + \'" title="View Attendance">' + CALENDAR_SVG + '</button>' +
  '<button type="button" class="emp-action-btn edit" data-action="edit" data-id="\' + s.id + \'" title="Edit employee">';
content = content.replace(oldGridBtn, newGridBtn);

// 3. Update table click listener
let oldTableListener = `tableBody.addEventListener('click', function (e) {
    var btn = e.target.closest('.stf-edit-btn');
    if (!btn) return;
    openEdit(Number(btn.getAttribute('data-id')));
  });`;
let newTableListener = `tableBody.addEventListener('click', function (e) {
    var calBtn = e.target.closest('.stf-calendar-btn');
    if (calBtn) {
      openCalendar(Number(calBtn.getAttribute('data-id')));
      return;
    }
    var btn = e.target.closest('.stf-edit-btn');
    if (!btn) return;
    openEdit(Number(btn.getAttribute('data-id')));
  });`;
content = content.replace(oldTableListener, newTableListener);

// 4. Update grid click listener
let oldGridListener = `if (action === 'add-new') {
          openCreate('staff');
        } else if (action === 'edit') {
          openEdit(Number(actionBtn.getAttribute('data-id')));
        } else if (action === 'delete') {`;
let newGridListener = `if (action === 'add-new') {
          openCreate('staff');
        } else if (action === 'calendar') {
          openCalendar(Number(actionBtn.getAttribute('data-id')));
        } else if (action === 'edit') {
          openEdit(Number(actionBtn.getAttribute('data-id')));
        } else if (action === 'delete') {`;
content = content.replace(oldGridListener, newGridListener);

// 5. Add Calendar Logic at the very end (before })(); )
let calendarLogic = `
  var currentCalStaffId = null;
  var currentCalDate = new Date();

  window.openCalendar = async function(id) {
    var staff = findStaff(id);
    if (!staff) return;
    currentCalStaffId = id;
    currentCalDate = new Date();
    document.getElementById('calendarModalTitle').textContent = staff.name + ' - Attendance';
    window.SW.openModal('calendarModal');
    renderCalendar();
  };

  async function renderCalendar() {
    var grid = document.getElementById('calendarDays');
    var display = document.getElementById('calendarMonthDisplay');
    var loading = document.getElementById('calendarLoading');
    if (!grid || !display || !loading) return;

    var monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    var year = currentCalDate.getFullYear();
    var month = currentCalDate.getMonth();
    var monthStr = year + '-' + String(month + 1).padStart(2, '0');
    display.textContent = monthNames[month] + ' ' + year;

    grid.style.display = 'none';
    loading.style.display = 'block';

    try {
      var res = await fetch('/api/staff/' + currentCalStaffId + '/attendance?month=' + monthStr);
      var records = [];
      if (res.ok) records = await res.json();
      
      var recordMap = {};
      records.forEach(function(r) { recordMap[r.date] = r; });

      var firstDay = new Date(year, month, 1).getDay();
      var daysInMonth = new Date(year, month + 1, 0).getDate();

      var html = '';
      for (var i = 0; i < firstDay; i++) {
        html += '<div></div>';
      }

      for (var d = 1; d <= daysInMonth; d++) {
        var dStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
        var r = recordMap[dStr];
        var bg = '#f8fafc';
        var color = '#64748b';
        var label = '-';
        var timeStr = '';
        if (r) {
          if (r.status === 'present') { bg = '#ecfdf5'; color = '#10b981'; label = 'P'; }
          else if (r.status === 'absent') { bg = '#fef2f2'; color = '#ef4444'; label = 'A'; }
          else if (r.status === 'late') { bg = '#fffbeb'; color = '#f59e0b'; label = 'L'; }
          else if (r.status === 'half-day') { bg = '#fff7ed'; color = '#ea580c'; label = 'HD'; }
          else if (r.status === 'on_leave') { bg = '#f3e8ff'; color = '#a855f7'; label = 'LV'; }
          
          if (r.check_in_time) {
            timeStr = '<div style="font-size:0.6rem; margin-top:2px; color:#64748b;">' + r.check_in_time.substring(0,5) + '</div>';
          }
        }
        
        var isToday = dStr === new Date().toLocaleDateString('en-CA');
        var todayStyle = isToday ? 'border:1px solid #1D4ED8;' : 'border:1px solid transparent;';
        
        html += '<div style="background:'+bg+'; color:'+color+'; padding:8px 4px; border-radius:6px; ' + todayStyle + '"><div style="font-size:0.7rem; font-weight:600; margin-bottom:4px; color:#0f172a;">' + d + '</div><div style="font-weight:bold; font-size:0.9rem;">' + label + '</div>' + timeStr + '</div>';
      }
      
      grid.innerHTML = html;
      loading.style.display = 'none';
      grid.style.display = 'grid';

    } catch (e) {
      console.error(e);
      loading.textContent = 'Error loading attendance.';
    }
  }

  var prevCalBtn = document.getElementById('prevMonthBtn');
  var nextCalBtn = document.getElementById('nextMonthBtn');
  if (prevCalBtn) prevCalBtn.addEventListener('click', function() { currentCalDate.setMonth(currentCalDate.getMonth() - 1); renderCalendar(); });
  if (nextCalBtn) nextCalBtn.addEventListener('click', function() { currentCalDate.setMonth(currentCalDate.getMonth() + 1); renderCalendar(); });

`;

let beforeEnd = "\n  loadStaff();\n})();";
content = content.replace(beforeEnd, calendarLogic + beforeEnd);

fs.writeFileSync(file, content);
console.log("Injected calendar JS.");
