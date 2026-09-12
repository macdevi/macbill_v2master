<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #172033; max-width: 680px; margin: 32px auto; padding: 0 18px; font-size: 14px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; border-bottom: 2px solid #0ea5e9; padding-bottom: 16px; margin-bottom: 20px; }
        .brand { margin: 0; font-size: 24px; letter-spacing: 1.2px; }
        .invoice-label { margin: 4px 0 0; color: #64748b; font-size: 12px; }
        .business-contact { margin: 8px 0 0; color: #475569; font-size: 12px; line-height: 1.5; }
        .invoice-number { text-align: right; font-weight: bold; }
        .status { margin-top: 5px; color: #64748b; font-size: 12px; text-transform: uppercase; }
        h2 { font-size: 13px; margin: 24px 0 8px; text-transform: uppercase; letter-spacing: .5px; color: #475569; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 9px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        td:first-child { width: 55%; color: #475569; }
        td:last-child { text-align: right; font-weight: 600; }
        .muted { color: #64748b; font-size: 12px; font-weight: normal; }
        .total-row td { border-top: 2px solid #94a3b8; font-size: 17px; font-weight: bold; color: #0f172a; }
        .remaining-row td { border-top: 1px solid #94a3b8; font-size: 19px; font-weight: bold; color: #0f172a; }
        .credit td { color: #047857; }
        .footer { margin-top: 28px; border-top: 1px solid #e2e8f0; padding-top: 14px; color: #64748b; font-size: 12px; line-height: 1.6; white-space: pre-line; }
        @media print { body { margin: 0; max-width: none; padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
@php
    $businessName = \App\Models\Setting::value('business_name', 'MACBILLING');
    $businessOwner = \App\Models\Setting::value('business_owner', '');
    $businessPhone = \App\Models\Setting::value('business_phone', '');
    $businessEmail = \App\Models\Setting::value('business_email', '');
    $businessAddress = \App\Models\Setting::value('business_address', '');
    $businessCity = \App\Models\Setting::value('business_city', '');
    $invoiceFooter = \App\Models\Setting::value('invoice_footer', 'Terima kasih telah menggunakan layanan kami.');
    $isTaxSnapshotAvailable = (float) $invoice->gross_amount > 0;
    $serviceAmount = $isTaxSnapshotAvailable ? (float) $invoice->service_amount : (float) $invoice->amount;
    $subtotal = $isTaxSnapshotAvailable ? (float) $invoice->subtotal : $serviceAmount;
    $taxAmount = $isTaxSnapshotAvailable ? (float) $invoice->tax_amount : 0;
    $taxRate = $isTaxSnapshotAvailable ? (float) $invoice->tax_rate : 0;
    $grossAmount = $isTaxSnapshotAvailable ? (float) $invoice->gross_amount : (float) $invoice->amount;
    $creditUsed = $isTaxSnapshotAvailable ? (float) $invoice->credit_used : 0;
    $verifiedPaid = isset($invoice->payments) ? (float) $invoice->payments->where('status', 'verified')->sum('amount') : 0;
    $amountAfterCredit = max($grossAmount - $creditUsed, 0);
    $remainingAmount = max((float) $invoice->amount, 0);
    $taxLabel = trim((string) $invoice->tax_name) ?: 'Pajak';
    $taxModeLabel = match($invoice->tax_mode) { 'inclusive' => 'termasuk harga', 'exclusive' => 'ditambahkan', default => 'tidak dikenakan' };
    $businessLocation = trim(implode(', ', array_filter([$businessAddress, $businessCity])));
@endphp
<div class="header">
    <div>
        <h1 class="brand">{{ $businessName }}</h1>
        <p class="invoice-label">Tagihan layanan internet</p>
        @if ($businessOwner || $businessPhone || $businessEmail || $businessLocation)
            <div class="business-contact">
                @if ($businessOwner) Pemilik: {{ $businessOwner }}<br> @endif
                @if ($businessPhone) WhatsApp: {{ $businessPhone }}<br> @endif
                @if ($businessEmail) Email: {{ $businessEmail }}<br> @endif
                @if ($businessLocation) {{ $businessLocation }} @endif
            </div>
        @endif
    </div>
    <div><div class="invoice-number">{{ $invoice->invoice_number }}</div><div class="status">Status: {{ $invoice->status }}</div></div>
</div>
<h2>Informasi Pelanggan</h2>
<table>
    <tr><td>Pelanggan</td><td>{{ $invoice->customer->name }}</td></tr>
    <tr><td>PPPoE</td><td>{{ $invoice->customer->pppoe_username }}</td></tr>
    <tr><td>Periode</td><td>{{ $invoice->billing_date->format('F Y') }}</td></tr>
    <tr><td>Jatuh Tempo</td><td>{{ $invoice->due_date->format('d/m/Y') }}</td></tr>
</table>
<h2>Rincian Tagihan</h2>
<table>
    <tr><td>Harga layanan</td><td>Rp {{ number_format($serviceAmount, 0, ',', '.') }}</td></tr>
    @if ($isTaxSnapshotAvailable && $invoice->tax_mode !== 'none' && $taxRate > 0)
        <tr><td>Dasar harga sebelum pajak</td><td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>{{ $taxLabel }} {{ rtrim(rtrim(number_format($taxRate, 4, '.', ''), '0'), '.') }}% <span class="muted">({{ $taxModeLabel }})</span></td><td>Rp {{ number_format($taxAmount, 0, ',', '.') }}</td></tr>
    @endif
    <tr class="total-row"><td>Total tagihan awal</td><td>Rp {{ number_format($grossAmount, 0, ',', '.') }}</td></tr>
    @if ($creditUsed > 0)
        <tr class="credit"><td>Titip saldo digunakan</td><td>- Rp {{ number_format($creditUsed, 0, ',', '.') }}</td></tr>
          <tr><td>Tagihan setelah titip saldo</td><td>Rp {{ number_format($amountAfterCredit, 0, ',', '.') }}</td></tr>
    @endif

    @if ($verifiedPaid > 0)
        <tr class="credit"><td>Pembayaran tercatat</td><td>- Rp {{ number_format($verifiedPaid, 0, ',', '.') }}</td></tr>
    @endif
    <tr class="remaining-row"><td>Sisa yang harus dibayar</td><td>Rp {{ number_format($remainingAmount, 0, ',', '.') }}</td></tr>
</table>
<div class="footer">{{ $invoiceFooter }}</div>
<script>window.print();</script>
</body>
</html>
