const fs = require('fs');
const file = 'public/js/staff.js';
let content = fs.readFileSync(file, 'utf8');

// Replace the attendance modal rows rendering
let oldRow = `'<div class="attendance-segmented" data-staff-id="' + s.id + '">' +
            '<label class="present-label ' + (s.attendance === 'present' ? 'active-present' : '') + '">' +
              '<input type="radio" name="att_' + s.id + '" value="present" ' + (s.attendance === 'present' ? 'checked' : '') + '> P' +
            '</label>' +
            '<label class="late-label ' + (s.attendance === 'late' ? 'active-late' : '') + '">' +
              '<input type="radio" name="att_' + s.id + '" value="late" ' + (s.attendance === 'late' ? 'checked' : '') + '> L' +
            '</label>' +
            '<label class="absent-label ' + (s.attendance === 'absent' ? 'active-absent' : '') + '">' +
              '<input type="radio" name="att_' + s.id + '" value="absent" ' + (s.attendance === 'absent' ? 'checked' : '') + '> A' +
            '</label>' +
            '<label class="leave-label ' + (s.attendance === 'half-day' ? 'active-leave' : '') + '">' +
              '<input type="radio" name="att_' + s.id + '" value="half-day" ' + (s.attendance === 'half-day' ? 'checked' : '') + '> HD' +
            '</label>' +
          '</div>'`;

let newRow = `'<div class="attendance-controls" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; justify-content: flex-end;">' +
            '<div class="attendance-segmented" data-staff-id="' + s.id + '">' +
              '<label class="present-label ' + (s.attendance === 'present' ? 'active-present' : '') + '">' +
                '<input type="radio" name="att_' + s.id + '" value="present" ' + (s.attendance === 'present' ? 'checked' : '') + '> P' +
              '</label>' +
              '<label class="late-label ' + (s.attendance === 'late' ? 'active-late' : '') + '">' +
                '<input type="radio" name="att_' + s.id + '" value="late" ' + (s.attendance === 'late' ? 'checked' : '') + '> L' +
              '</label>' +
              '<label class="absent-label ' + (s.attendance === 'absent' ? 'active-absent' : '') + '">' +
                '<input type="radio" name="att_' + s.id + '" value="absent" ' + (s.attendance === 'absent' ? 'checked' : '') + '> A' +
              '</label>' +
              '<label class="leave-label ' + (s.attendance === 'half-day' ? 'active-leave' : '') + '">' +
                '<input type="radio" name="att_' + s.id + '" value="half-day" ' + (s.attendance === 'half-day' ? 'checked' : '') + '> HD' +
              '</label>' +
            '</div>' +
            '<div style="display:flex; gap:5px; align-items:center;">' +
              '<div style="display:flex; flex-direction:column; font-size:10px; color:#667085;">' +
                '<span>In</span>' +
                '<input type="time" class="time-in-input" data-staff-id="' + s.id + '" value="' + (s.check_in_time ? s.check_in_time.substring(0,5) : '') + '" style="padding:2px 4px; border:1px solid #e2e8f0; border-radius:4px;">' +
              '</div>' +
              '<div style="display:flex; flex-direction:column; font-size:10px; color:#667085;">' +
                '<span>Out</span>' +
                '<input type="time" class="time-out-input" data-staff-id="' + s.id + '" value="' + (s.check_out_time ? s.check_out_time.substring(0,5) : '') + '" style="padding:2px 4px; border:1px solid #e2e8f0; border-radius:4px;">' +
              '</div>' +
            '</div>' +
          '</div>'`;

content = content.replace(oldRow, newRow);

// Update submission logic
let oldSubmit = `var checkedRadio = ctrl.querySelector('input[type="radio"]:checked');
        if (checkedRadio) {
          attendanceData.push({ id: id, status: checkedRadio.value });
        }`;

let newSubmit = `var checkedRadio = ctrl.querySelector('input[type="radio"]:checked');
        var timeIn = document.querySelector('.time-in-input[data-staff-id="' + id + '"]');
        var timeOut = document.querySelector('.time-out-input[data-staff-id="' + id + '"]');
        if (checkedRadio) {
          attendanceData.push({ 
            id: id, 
            status: checkedRadio.value,
            check_in_time: timeIn ? timeIn.value : null,
            check_out_time: timeOut ? timeOut.value : null
          });
        }`;

content = content.replace(oldSubmit, newSubmit);

fs.writeFileSync(file, content);
console.log("Updated staff.js for attendance check-in/out.");
