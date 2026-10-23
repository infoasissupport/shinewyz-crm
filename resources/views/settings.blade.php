@extends('layouts.app')
@section('title', 'Company Settings')
@section('page-id', 'settings')
@section('styles')
<style>
.settings-container {  margin: 0 auto; background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; }
.settings-section { margin-bottom: 32px; }
.settings-section h3 { font-size: 16px; color: #1e293b; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0; }
.form-row { display: flex; gap: 16px; margin-bottom: 16px; }
.form-row .field { flex: 1; }
.form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
.form-grid .field-full { grid-column: 1 / -1; }
@media (max-width: 1024px) { .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }
.field label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
.field input, .field textarea { width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
.field input:focus { border-color: #3b82f6; outline: none; }
.btn-save { background: #fa0100; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
.btn-save:hover { background: #000; }
/* Save + backup bar stays pinned to the bottom while the form scrolls */
.settings-footer { position: sticky; bottom: 0; z-index: 5; display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin: 0 -24px -24px; padding: 16px 24px; background: #fff; border-top: 1px solid #e2e8f0; border-radius: 0 0 12px 12px; box-shadow: 0 -6px 16px rgba(15,23,42,.06); }
.settings-footer-backup h3 { font-size: 16px; color: #1e293b; margin-bottom: 6px; }
.settings-footer-backup p { margin-bottom: 10px !important; }
@media (max-width: 768px) { .settings-footer { flex-direction: column-reverse; align-items: stretch; } }
</style>
@endsection
@section('content')
<section class="view" id="settings">
  <div class="view-header"><div class="view-title">Company Settings</div></div>
  <div class="fixedScrolldivCommon">
    <div class="settings-container">
      <form id="settingsForm">
        <div class="settings-section">
          <h3>Basic Details</h3>
          <div class="form-grid">
            <div class="field"><label>Company Name</label><input type="text" id="company_name"></div>
            <div class="field"><label>Tagline</label><input type="text" id="company_tagline"></div>
            <div class="field"><label>Email</label><input type="email" id="company_email"></div>
            <div class="field"><label>Phone</label><input type="text" id="company_phone"></div>
            <div class="field"><label>Website</label><input type="text" id="company_website"></div>
            <div class="field"><label>GSTIN</label><input type="text" id="company_gstin"></div>
            <div class="field"><label>Instagram URL</label><input type="url" id="instagram_url" placeholder="https://instagram.com/..."></div>
            <div class="field"><label>Google Business URL</label><input type="url" id="google_business_url" placeholder="https://g.page/..."></div>
            <div class="field field-full"><label>Address</label><textarea id="company_address" rows="3"></textarea></div>
          </div>
        </div>
        <div class="settings-section">
          <h3>Bank Details (For Invoices)</h3>
          <div class="form-grid">
            <div class="field"><label>Account Name</label><input type="text" id="bank_account_name"></div>
            <div class="field"><label>Account Number</label><input type="text" id="bank_account_number"></div>
            <div class="field"><label>Bank Name</label><input type="text" id="bank_name"></div>
            <div class="field"><label>Branch Name</label><input type="text" id="bank_branch"></div>
            <div class="field"><label>IFSC Code</label><input type="text" id="bank_ifsc"></div>
            <div class="field"><label>Upload Company Logo</label><input type="file" id="company_logo" accept="image/*"></div>
            <div class="field"><label>Upload UPI QR Code</label><input type="file" id="upi_qr_code" accept="image/*"></div>
            <div class="field"><label>Upload Signature Photo</label><input type="file" id="signature" accept="image/*"></div>
          </div>
        </div>
        <div class="settings-footer">
          <div class="settings-footer-backup">
            <h3>System Backup & Export</h3>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 12px;">Download a complete backup of all your data (Customers, Vehicles, Job Cards, Invoices, etc.) as an Excel file.</p>
            <button type="button" class="btn-save" style="background: #000;" id="exportAllDataBtn"><i class="ti ti-download" style="margin-right: 6px;"></i>Export All Data to Excel</button>
          </div>
          <button type="submit" class="btn-save">Save Settings</button>
        </div>

      </form>
    </div>
  </div>
</section>
@endsection
@section('scripts')
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
  try {
    const res = await fetch('/api/settings');
    if (res.ok) {
      const data = await res.json();
      document.getElementById('company_name').value = data.company_name || '';
      document.getElementById('company_tagline').value = data.company_tagline || '';
      document.getElementById('company_email').value = data.company_email || '';
      document.getElementById('company_phone').value = data.company_phone || '';
      document.getElementById('company_website').value = data.company_website || '';
      document.getElementById('company_gstin').value = data.company_gstin || '';
      document.getElementById('instagram_url').value = data.instagram_url || '';
      document.getElementById('google_business_url').value = data.google_business_url || '';
      document.getElementById('company_address').value = data.company_address || '';
      document.getElementById('bank_name').value = data.bank_name || '';
      document.getElementById('bank_branch').value = data.bank_branch || '';
      document.getElementById('bank_account_name').value = data.bank_account_name || '';
      document.getElementById('bank_account_number').value = data.bank_account_number || '';
      document.getElementById('bank_ifsc').value = data.bank_ifsc || '';
      
    }
  } catch (e) {}

  document.getElementById('settingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData();
    formData.append('company_name', document.getElementById('company_name').value);
    formData.append('company_tagline', document.getElementById('company_tagline').value);
    formData.append('company_email', document.getElementById('company_email').value);
    formData.append('company_phone', document.getElementById('company_phone').value);
    formData.append('company_website', document.getElementById('company_website').value);
    formData.append('company_gstin', document.getElementById('company_gstin').value);
    formData.append('instagram_url', document.getElementById('instagram_url').value);
    formData.append('google_business_url', document.getElementById('google_business_url').value);
    formData.append('company_address', document.getElementById('company_address').value);
    formData.append('bank_name', document.getElementById('bank_name').value);
    formData.append('bank_branch', document.getElementById('bank_branch').value);
    formData.append('bank_account_name', document.getElementById('bank_account_name').value);
    formData.append('bank_account_number', document.getElementById('bank_account_number').value);
    formData.append('bank_ifsc', document.getElementById('bank_ifsc').value);
    
    const logoInput = document.getElementById('company_logo');
    if (logoInput.files.length > 0) formData.append('company_logo', logoInput.files[0]);
    
    const qrInput = document.getElementById('upi_qr_code');
    if (qrInput.files.length > 0) formData.append('upi_qr_code', qrInput.files[0]);

    const sigInput = document.getElementById('signature');
    if (sigInput.files.length > 0) formData.append('signature', sigInput.files[0]);

    try {
      const res = await fetch('/api/settings', {
        method: 'POST',
        body: formData
      });
      if (res.ok) alert('Settings saved successfully!');
      else alert('Failed to save settings.');
    } catch (err) { alert('Error saving settings.'); }
  });

  const exportBtn = document.getElementById('exportAllDataBtn');
  if (exportBtn) {
    exportBtn.addEventListener('click', async function() {
      try {
        exportBtn.disabled = true;
        exportBtn.innerHTML = '<i class="ti ti-loader" style="margin-right: 6px;"></i>Exporting...';
        
        // Fetch all important datasets
        const endpoints = [
          { name: 'Customers', url: '/api/customers' },
          { name: 'Vehicles', url: '/api/vehicles' },
          { name: 'JobCards', url: '/api/jobcards' },
          { name: 'Invoices', url: '/api/invoices' },
          { name: 'Expenses', url: '/api/expenses' },
          { name: 'Services', url: '/api/services' },
          { name: 'Staff', url: '/api/staff' },
          { name: 'Inventory', url: '/api/inventory' },
          { name: 'Bays', url: '/api/bays' },
          { name: 'Branches', url: '/api/branches' }
        ];
        
        // Fetch all data first so we can cross-reference
        const allData = {};
        for (let ep of endpoints) {
          try {
            const res = await fetch(ep.url);
            if (res.ok) {
              let data = await res.json();
              allData[ep.name] = Array.isArray(data) ? data : [data];
            } else {
              allData[ep.name] = [];
            }
          } catch(err) {
            console.error('Failed to fetch ' + ep.name, err);
            allData[ep.name] = [];
          }
        }

        const wb = XLSX.utils.book_new();
        
        for (let ep of endpoints) {
          const data = allData[ep.name];
          
          const cleanData = data.map(row => {
            const newRow = {};
            for (let key in row) {
              // Exclude specific columns for Customers
              if (ep.name === 'Customers' && ['plan', 'points', 'loyalty_points', 'membership'].includes(key)) {
                continue;
              }
              
              if (row[key] !== null && typeof row[key] === 'object') {
                if (ep.name === 'Customers' && key === 'vehicles') continue; // Skip vehicles JSON dump
                newRow[key] = JSON.stringify(row[key]);
              } else {
                newRow[key] = row[key];
              }
            }
            
            // Add custom calculated columns
            if (ep.name === 'Customers') {
              const invoices = allData['Invoices'] || [];
              const washCount = invoices.filter(inv => inv.phone === row.phone && inv.status === 'paid').length;
              newRow['total_washes'] = washCount;
            }
            
            return newRow;
          });
          
          if (cleanData.length > 0) {
            const ws = XLSX.utils.json_to_sheet(cleanData);
            XLSX.utils.book_append_sheet(wb, ws, ep.name);
          } else {
            const ws = XLSX.utils.json_to_sheet([{ Message: 'No data found' }]);
            XLSX.utils.book_append_sheet(wb, ws, ep.name);
          }
        }
        
        // Create file name with current date
        const dateStr = new Date().toISOString().split('T')[0];
        const fileName = `Carwash_System_Backup_${dateStr}.xlsx`;
        
        // Trigger download
        XLSX.writeFile(wb, fileName);
        
        exportBtn.disabled = false;
        exportBtn.innerHTML = '<i class="ti ti-download" style="margin-right: 6px;"></i>Export All Data to Excel';
        alert('Export completed successfully!');
        
      } catch (err) {
        console.error(err);
        exportBtn.disabled = false;
        exportBtn.innerHTML = '<i class="ti ti-download" style="margin-right: 6px;"></i>Export All Data to Excel';
        alert('An error occurred during export.');
      }
    });
  }

});
</script>
@endsection
