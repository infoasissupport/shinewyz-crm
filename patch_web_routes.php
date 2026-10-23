<?php
$file = 'routes/web.php';
$content = file_get_contents($file);

$newRoute = <<<PHP
Route::get('/invoice/{id}/view', function (\$id) {
    \$invoice = \App\Models\Invoice::find(\$id);
    if (!\$invoice) return "Invoice not found";
    
    // Very simple HTML invoice matching the admin portal's style
    \$html = "<html><head><style>
        body { font-family: sans-serif; padding: 40px; color: #1e293b; }
        .head { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; }
        .details { margin-bottom: 40px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase; }
        .total { text-align: right; font-size: 20px; font-weight: bold; margin-top: 20px; }
        .paid { color: #10b981; font-weight: bold; }
    </style></head><body>
        <div class='head'>
            <div>
                <h2>SHINEWISE CAR WASH</h2>
                <p>Invoice #{\$invoice->invoice_number}</p>
            </div>
            <div style='text-align: right'>
                <p><b>Date:</b> {\$invoice->created_at->format('d M Y')}</p>
                <p><b>Status:</b> <span class='".(\$invoice->status == 'paid' ? 'paid' : '')."'>".strtoupper(\$invoice->status)."</span></p>
            </div>
        </div>
        <div class='details'>
            <p><b>Bill To:</b> {\$invoice->customer}</p>
            <p><b>Phone:</b> {\$invoice->phone}</p>
            <p><b>Vehicle:</b> {\$invoice->vehicle}</p>
        </div>
        <table>
            <tr><th>Description</th><th style='text-align:right'>Amount</th></tr>
            <tr><td>{\$invoice->service}</td><td style='text-align:right'>₹{\$invoice->amount}</td></tr>
        </table>
        <div class='total'>
            Total: ₹{\$invoice->amount}
        </div>
        <script>window.onload = function() { window.print(); }</script>
    </body></html>";
    return \$html;
});
PHP;

$content .= "\n" . $newRoute . "\n";
file_put_contents($file, $content);
echo "Added /invoice/{id}/view route.\n";
