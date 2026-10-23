<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoice {{ $invoice->invoice_number ?: $invoice->id }}</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; background: #f7f7f7; color: #101010; font: 11px/1.4 Arial, Helvetica, sans-serif; }
    .page { width: 210mm; min-height: 297mm; margin: 12px auto; padding: 9mm; background: #fff; }
    .invoice { border: 1.5px solid #111; }

    /* ---- header ---- */
    .hdr { display: flex; align-items: center; border-bottom: 2px solid #111; }
    .hdr > div { padding: 14px 16px; }
    .hdr-brand { display: flex; align-items: center; justify-content: center; gap: 10px; flex: 1; }
    .hdr-name { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; line-height: 1.1; }
    .hdr-name img{ width: 100px; display: block; }

    .hdr-name span { display: block; font-size: 12px; font-weight: 800; letter-spacing: 1px; }
    .hdr-tagline { margin-top: 4px; font-size: 8px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #333; }
    .hdr-contact { flex: 1.7; border-left: 2px solid #111; font-size: 10px; font-weight: 700; text-transform: uppercase; }
    .hdr-contact .row { display: flex; align-items: flex-start; gap: 6px; margin-bottom: 5px; }
    .hdr-contact .row svg { flex-shrink: 0; margin-top: 1px; }
    .hdr-services { margin-top: 6px; font-size: 9px; letter-spacing: 1px; color: #333; }
    .hdr-slogan { flex: 1; border-left: 2px solid #111; display: flex; align-items: center; justify-content: center; text-align: center; font-style: italic; font-weight: 700; font-size: 13px; line-height: 1.35; color: #222; }

    /* ---- title + bill box ---- */
    .titlebar { display: flex; justify-content: space-between; align-items: flex-end; padding: 14px 16px 10px; }
    .titlebar .doc-title { font-size: 26px; font-weight: 800; letter-spacing: 1px; }
    .titlebar .doc-sub { font-size: 9px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #444; margin-top: 2px; }
    .billbox { border: 1.5px solid #111; min-width: 260px; }
    .billbox .r { display: flex; padding: 5px 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #ccc; }
    .billbox .r:last-child { border-bottom: 0; }
    .billbox .r span.lbl { width: 62px; color: #444; }
    .billbox .r span.sep { width: 12px; }
    .status-pill { background: #eee; border: 1px solid #999; border-radius: 3px; padding: 1px 8px; font-size: 9px; }

    /* ---- detail cards ---- */
    .cards { display: flex; gap: 12px; padding: 0 16px 12px; }
    .card { flex: 1; border: 1px solid #111; }
    .card .card-hd { display: flex; align-items: center; gap: 6px; background: #eeeeee; border-bottom: 1px solid #111; padding: 5px 10px; font-size: 10px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; }
    .card .card-body { padding: 8px 10px; font-size: 10.5px; }
    .card .card-body .r { display: flex; margin-bottom: 3px; }
    .card .card-body .r span.lbl { width: 62px; font-weight: 700; }
    .card .card-body .r span.sep { width: 10px; }

    /* ---- items table ---- */
    table.items { width: 100%; border-collapse: collapse; margin: 0 0 0; }
    table.items th { background: #111; color: #fff; font-size: 9.5px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 7px 8px; text-align: left; }
    table.items td { border-bottom: 1px solid #ccc; border-right: 1px solid #ccc; padding: 7px 8px; font-size: 10.5px; vertical-align: top; }
    table.items th:last-child, table.items td:last-child { border-right: 0; text-align: right; }
    table.items th.num, table.items td.num { width: 4%; text-align: center; }
    table.items th.desc, table.items td.desc { width: 58%; font-weight: 700; text-transform: uppercase; }
    table.items th.rate, table.items td.rate { width: 14%; text-align: right; }
    table.items th.qty, table.items td.qty { width: 8%; text-align: center; }
    table.items th.amount, table.items td.amount { width: 16%; }
    table.items tfoot td { border-top: 1.5px solid #111; border-right: 0; padding: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase; background: #f2f2f2; }
    table.items tfoot td.total-value { text-align: right; }
    table.items tfoot tr.summary td { border-top: 1px solid #ccc; background: #fff; font-weight: 700; font-size: 10.5px; padding: 6px 8px; }
    table.items tfoot tr.summary:first-child td { border-top: 1.5px solid #111; }
    table.items tfoot tr.summary + tr:not(.summary) td { border-top: 1.5px solid #111; }

    .words { margin: 0 16px; border: 1px solid #111; border-top: 0; padding: 8px 10px; font-size: 10px; font-weight: 800; letter-spacing: 0.3px; text-transform: uppercase; }

    /* ---- account / note ---- */
    .lower { display: flex; gap: 12px; padding: 12px 16px 0; }
    .acct { flex: 1.2; border: 1px solid #111; }
    .acct .card-hd, .note .card-hd { display: flex; align-items: center; gap: 6px; background: #eeeeee; border-bottom: 1px solid #111; padding: 5px 10px; font-size: 10px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; }
    .acct-body { display: flex; gap: 10px; padding: 10px; }
    .acct-body .fields { flex: 1; font-size: 10.5px; }
    .acct-body .fields .r { display: flex; margin-bottom: 4px; }
    .acct-body .fields .r span.lbl { width: 80px; font-weight: 700; text-transform: uppercase; }
    .acct-body .fields .r span.sep { width: 10px; }
    .acct-body .qr { text-align: center; font-size: 8px; font-weight: 700; }
    .acct-body .qr img { width: 92px; height: auto; display: block; margin: 0 auto; }
    .note { flex: 1; border: 1px solid #111; }
    .note-body { padding: 10px; font-size: 10px; }
    .note-body ol { margin: 0 0 14px; padding-left: 16px; }
    .note-body ol li { margin-bottom: 4px; }
    .sign { text-align: right; font-size: 10px; }
    .sign .line { border-top: 1px solid #111; width: 160px; margin: 22px 0 4px auto; }
    .sign .for { font-weight: 700; }

    .footer { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin: 14px 16px 0; border-top: 1.5px solid #111; padding: 10px 0 14px; }
    .footer .thanks { display: flex; align-items: center; gap: 8px; }
    .footer .thanks .t1 { font-size: 11px; font-weight: 800; text-transform: uppercase; }
    .footer .thanks .t2 { font-size: 8px; letter-spacing: 1px; text-transform: uppercase; color: #444; }
    .footer .right { text-align: right; font-size: 9px; }
    .footer .right .cg { font-weight: 800; text-transform: uppercase; }
    .footer .right .cg-sub { color: #444; max-width: 220px; }

    @page { size: A4; margin: 0; }
    @media print { body { background: #fff; } .page { margin: 0; width: 210mm; min-height: 297mm; } }
  </style>
</head>
<body>
  <main class="page"><section class="invoice">

    <header class="hdr">
      <div class="hdr-brand">
        <div style="display:flex; align-items:center; gap:12px;">
          @if($settings->company_logo)
            <img src="{{ asset($settings->company_logo) }}" style="max-width:70px; max-height:70px; object-fit:contain;">
          @endif
          <div class="hdr-name" style="text-align:left;">
            {{ $settings->company_name }}
          </div>
        </div>
      </div>
      <div class="hdr-contact">
        @if($settings->company_address)
        <div class="row">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"/></svg>
          <span>{!! nl2br(e($settings->company_address)) !!}</span>
        </div>
        @endif
        @if($settings->company_phone)
        <div class="row">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/></svg>
          <span>{{ $settings->company_phone }}</span>
        </div>
        @endif
        @if($settings->instagram_url)
        <div class="row">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="4" ry="4"></rect><circle cx="12" cy="12" r="3"></circle><line x1="16.5" y1="7.5" x2="16.5" y2="7.5"></line></svg>
          <span style="word-break: break-all;">{{ $settings->instagram_url }}</span>
        </div>
        @endif
        @if($settings->google_business_url)
        <div class="row">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
          <span style="word-break: break-all;">{{ $settings->google_business_url }}</span>
        </div>
        @endif
      </div>
      @if($settings->company_tagline)
      <div class="hdr-slogan">{!! nl2br(e($settings->company_tagline)) !!}</div>
      @endif
    </header>

    <div class="titlebar">
      <div>
        <div class="doc-title">INVOICE</div>
        <div class="doc-sub">Drive Cleaner. Live Better.</div>
      </div>
      <div class="billbox">
        <div class="r"><span class="lbl">Bill No</span><span class="sep">:</span><b>{{ $invoice->invoice_number ?: $invoice->id }}</b></div>
        <div class="r"><span class="lbl">Date</span><span class="sep">:</span>{{ $date }}</div>
        <div class="r"><span class="lbl">Status</span><span class="sep">:</span><span class="status-pill">{{ strtoupper($invoice->status ?: 'pending') }}</span></div>
      </div>
    </div>

    <div class="cards">
      <div class="card">
        <div class="card-hd">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/></svg>
          Customer Details
        </div>
        <div class="card-body">
          <div class="r"><span class="lbl">Name</span><span class="sep">:</span>{{ strtoupper($invoice->customer) }}</div>
          <div class="r"><span class="lbl">Mobile</span><span class="sep">:</span>{{ $invoice->phone ?: 'N/A' }}</div>
        </div>
      </div>
      <div class="card">
        <div class="card-hd">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6m-6 -6h15m-6 0v-5"/></svg>
          Vehicle Details
        </div>
        <div class="card-body">
          <div class="r"><span class="lbl">Vehicle</span><span class="sep">:</span>{{ strtoupper($invoice->vehicle ?: 'N/A') }}</div>
        </div>
      </div>
    </div>

    <table class="items">
      <thead>
        <tr><th class="num">#</th><th class="desc">Particulars</th><th class="rate">Rate (&#8377;)</th><th class="qty">Qty</th><th class="amount">Amount (&#8377;)</th></tr>
      </thead>
      <tbody>
        <tr><td class="num">1</td><td class="desc">{{ $invoice->service }}</td><td class="rate">{{ number_format($subtotal, 2) }}</td><td class="qty">1</td><td class="amount">{{ number_format($subtotal, 2) }}</td></tr>
        @if($item_price > 0 && !empty($invoice->item_name))
        <tr><td class="num">2</td><td class="desc">{{ $invoice->item_name }}</td><td class="rate">{{ number_format($item_price, 2) }}</td><td class="qty">1</td><td class="amount">{{ number_format($item_price, 2) }}</td></tr>
        @endif
      </tbody>
      <tfoot>
        @if($taxAmount > 0 || $discount > 0)
        <tr class="summary"><td colspan="4">Sub Total</td><td class="total-value">&#8377;{{ number_format($subtotal + $item_price, 2) }}</td></tr>
        @endif
        @if($taxAmount > 0)
        <tr class="summary"><td colspan="4">Tax</td><td class="total-value">+ &#8377;{{ number_format($taxAmount, 2) }}</td></tr>
        @endif
        @if($discount > 0)
        <tr class="summary discount"><td colspan="4">Discount</td><td class="total-value">- &#8377;{{ number_format($discount, 2) }}</td></tr>
        @endif
        <tr><td colspan="4">Total Amount</td><td class="total-value">&#8377;{{ number_format($total, 2) }}</td></tr>
      </tfoot>
    </table>

    <div class="words">Amount Chargeable (in Words) : Rupees {{ ucfirst(\Illuminate\Support\Number::spell((int) round($total))) }} Only</div>

    <div class="lower">
      <div class="acct">
        <div class="card-hd">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21l18 0"/><path d="M3 10l18 0"/><path d="M5 6l7 -3l7 3"/><path d="M4 10l0 11"/><path d="M20 10l0 11"/><path d="M8 14l0 3"/><path d="M12 14l0 3"/><path d="M16 14l0 3"/></svg>
          Account Details (For Online Payment)
        </div>
        <div class="acct-body">
          <div class="fields">
            <div class="r"><span class="lbl">A/C Name</span><span class="sep">:</span>{{ $settings->bank_account_name }}</div>
            <div class="r"><span class="lbl">Bank</span><span class="sep">:</span>{{ $settings->bank_name }}</div>
            <div class="r"><span class="lbl">A/C No</span><span class="sep">:</span>{{ $settings->bank_account_number }}</div>
            <div class="r"><span class="lbl">IFSC Code</span><span class="sep">:</span>{{ $settings->bank_ifsc }}</div>

          </div>
          @if($settings->upi_qr_code)
          <div class="qr">
            <img src="{{ asset($settings->upi_qr_code) }}" alt="Scan to pay via UPI">
          </div>
          @endif
        </div>
      </div>
      <div class="note">
        <div class="card-hd">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 20l7 -7"/><path d="M13 20v-6a1 1 0 0 1 1 -1h6v-7a2 2 0 0 0 -2 -2h-12a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7z"/><path d="M9 8l4 0"/><path d="M9 12l2 0"/></svg>
          Note
        </div>
        <div class="note-body">
          <ol>
            <li>Payment to be made as per the above account details.</li>
            <li>This is a computer generated invoice and does not require a physical signature.</li>
            <li>For any queries, please contact us.</li>
   
          </ol>
          <div class="sign">
            @if($settings->signature)
            <img src="{{ asset($settings->signature) }}" alt="Signature" style="max-height: 50px; display: block; margin: 0 auto 5px auto;">
            @else
            <div class="line"></div>
            @endif
            Authorised Signature<br><span class="for">For {{ $settings->company_name }}</span>
          </div>
        </div>
      </div>
    </div>

    <footer class="footer">
      <div class="thanks">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M9 12l2 2l4 -4"/></svg>
        <div>
          <div class="t1">Thank you for choosing {{ $settings->company_name }}.</div>
          <div class="t2">We look forward to serving you again.</div>
        </div>
      </div>
      <div class="right">
        <div class="cg">Computer Generated Bill</div>
        <div class="cg-sub">This is a system generated invoice and does not require a physical signature.</div>
      </div>
    </footer>

  </section></main>
  <script>window.onload = function () { window.print(); };</script>
</body>
</html>
