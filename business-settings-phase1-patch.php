<?php

declare(strict_types=1);

$basePath = __DIR__;

$targetFiles = [
    'controller' => $basePath . '/app/Http/Controllers/BillingSettingController.php',
    'settings_view' => $basePath . '/resources/views/settings/billing.blade.php',
    'invoice_index' => $basePath . '/resources/views/invoices/index.blade.php',
    'print_view' => $basePath . '/resources/views/invoices/print.blade.php',
];

foreach ($targetFiles as $label => $file) {
    if (! is_file($file)) {
        fwrite(STDERR, "GAGAL: File {$label} tidak ditemukan: {$file}\n");
        exit(1);
    }
}

$invoiceIndex = file_get_contents($targetFiles['invoice_index']);
if ($invoiceIndex === false) {
    fwrite(STDERR, "GAGAL: File invoice index tidak dapat dibaca.\n");
    exit(1);
}

if (str_contains($invoiceIndex, "strtr(\$template")) {
    fwrite(STDOUT, "INFO: Integrasi template WhatsApp sudah tampak diterapkan. Patch tidak dijalankan.\n");
    exit(0);
}

$eol = str_contains($invoiceIndex, "\r\n") ? "\r\n" : "\n";
$lines = preg_split('/\R/', $invoiceIndex);
if ($lines === false) {
    fwrite(STDERR, "GAGAL: File invoice index tidak dapat diproses.\n");
    exit(1);
}
if (end($lines) === '') {
    array_pop($lines);
}

$starts = [];
foreach ($lines as $number => $line) {
    if (str_contains($line, '$creditUsed = (float) ($invoice->credit_used ?? 0);')) {
        $starts[] = $number;
    }
}

if (count($starts) !== 2) {
    fwrite(STDERR, 'GAGAL: Blok WhatsApp invoice ditemukan ' . count($starts) . " kali; seharusnya 2. File tidak diubah.\n");
    exit(1);
}

foreach (array_reverse($starts) as $start) {
    $end = null;
    for ($number = $start; $number < min(count($lines), $start + 35); $number++) {
        if (str_contains($lines[$number], '. "Terima kasih.";')) {
            $end = $number;
            break;
        }
    }

    if ($end === null) {
        fwrite(STDERR, 'GAGAL: Akhir blok pesan WhatsApp tidak ditemukan dekat baris ' . ($start + 1) . ". File tidak diubah.\n");
        exit(1);
    }

    $indent = substr($lines[$start], 0, strlen($lines[$start]) - strlen(ltrim($lines[$start], " \t")));
    $replacement = [
        $indent . '$creditUsed = (float) ($invoice->credit_used ?? 0);',
        $indent . '$grossAmount = (float) (($invoice->gross_amount ?? 0) > 0',
        $indent . '    ? $invoice->gross_amount',
        $indent . '    : $invoice->amount);',
        '',
        $indent . '$businessName = \App\Models\Setting::value(\'business_name\', \'MACBILLING\');',
        $indent . '$businessPhone = \App\Models\Setting::value(\'business_phone\', \'\');',
        $indent . '$template = \App\Models\Setting::value(\'whatsapp_invoice_template\', \"Halo Bapak/Ibu {customer_name},\\n\\nBerikut tagihan internet dari {business_name}.\\n\\nNo. Invoice: {invoice_number}\\n{credit_summary}Sisa Tagihan: {amount}\\nJatuh Tempo: {due_date}\\n\\nInvoice: {invoice_url}\\n\\nTerima kasih,\\n{business_name}\\nWhatsApp: {business_phone}\");',
        '',
        $indent . '$creditSummary = $creditUsed > 0',
        $indent . '    ? "Total tagihan awal: Rp " . number_format($grossAmount, 0, \',\', \'.\') . "\\n"',
        $indent . '      . "Titip saldo digunakan: Rp " . number_format($creditUsed, 0, \',\', \'.\') . "\\n"',
        $indent . '    : \'\';',
        '',
        $indent . '$invoiceMessage = strtr($template, [',
        $indent . '    \'{customer_name}\' => (string) $invoice->customer->name,',
        $indent . '    \'{customer_code}\' => (string) ($invoice->customer->customer_code ?? \'\'),',
        $indent . '    \'{invoice_number}\' => (string) $invoice->invoice_number,',
        $indent . '    \'{amount}\' => "Rp " . number_format((float) $invoice->amount, 0, \',\', \'.\'),',
        $indent . '    \'{gross_amount}\' => "Rp " . number_format($grossAmount, 0, \',\', \'.\'),',
        $indent . '    \'{credit_used}\' => "Rp " . number_format($creditUsed, 0, \',\', \'.\'),',
        $indent . '    \'{credit_summary}\' => $creditSummary,',
        $indent . '    \'{due_date}\' => $invoice->due_date->format(\'d/m/Y\'),',
        $indent . '    \'{billing_period}\' => $invoice->billing_date->format(\'F Y\'),',
        $indent . '    \'{invoice_url}\' => route(\'invoices.print\', $invoice),',
        $indent . '    \'{business_name}\' => (string) $businessName,',
        $indent . '    \'{business_phone}\' => (string) $businessPhone,',
        $indent . ']);',
    ];

    array_splice($lines, $start, $end - $start + 1, $replacement);
}

$updatedInvoiceIndex = implode($eol, $lines) . $eol;

$indexChecks = [
    "strtr(\$template" => 2,
    "Setting::value('whatsapp_invoice_template'" => 2,
    "'{credit_summary}'" => 2,
];
foreach ($indexChecks as $needle => $expected) {
    $found = substr_count($updatedInvoiceIndex, $needle);
    if ($found !== $expected) {
        fwrite(STDERR, "GAGAL: Validasi invoice index untuk {$needle} menghasilkan {$found}; seharusnya {$expected}. File tidak diubah.\n");
        exit(1);
    }
}

$controller = <<<'PHP'
<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingSettingController extends Controller
{
    private const DEFAULT_WHATSAPP_INVOICE_TEMPLATE = <<<'TEXT'
Halo Bapak/Ibu {customer_name},

Berikut tagihan internet dari {business_name}.

No. Invoice: {invoice_number}
{credit_summary}Sisa Tagihan: {amount}
Jatuh Tempo: {due_date}

Invoice: {invoice_url}

Terima kasih,
{business_name}
WhatsApp: {business_phone}
TEXT;

    public function edit(): View
    {
        return view('settings.billing', [
            'businessName' => Setting::value('business_name', 'MACBILLING'),
            'businessOwner' => Setting::value('business_owner', ''),
            'businessPhone' => Setting::value('business_phone', ''),
            'businessEmail' => Setting::value('business_email', ''),
            'businessAddress' => Setting::value('business_address', ''),
            'businessCity' => Setting::value('business_city', ''),
            'invoiceFooter' => Setting::value('invoice_footer', 'Terima kasih telah menggunakan layanan kami.'),
            'whatsappInvoiceTemplate' => Setting::value('whatsapp_invoice_template', self::DEFAULT_WHATSAPP_INVOICE_TEMPLATE),
            'taxEnabled' => filter_var(Setting::value('billing_tax_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'taxName' => Setting::value('billing_tax_name', 'PPN'),
            'taxRate' => Setting::value('billing_tax_rate', '11.00'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:100'],
            'business_owner' => ['nullable', 'string', 'max:100'],
            'business_phone' => ['nullable', 'string', 'max:30'],
            'business_email' => ['nullable', 'email', 'max:100'],
            'business_address' => ['nullable', 'string', 'max:500'],
            'business_city' => ['nullable', 'string', 'max:100'],
            'invoice_footer' => ['nullable', 'string', 'max:500'],
            'whatsapp_invoice_template' => ['required', 'string', 'max:3000'],
            'billing_tax_enabled' => ['nullable', 'boolean'],
            'billing_tax_name' => ['nullable', 'string', 'max:50'],
            'billing_tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'business_name.required' => 'Nama usaha wajib diisi.',
            'business_email.email' => 'Format email usaha belum benar.',
            'whatsapp_invoice_template.required' => 'Template pesan WhatsApp wajib diisi.',
            'billing_tax_rate.required' => 'Tarif pajak wajib diisi.',
            'billing_tax_rate.numeric' => 'Tarif pajak harus berupa angka.',
            'billing_tax_rate.min' => 'Tarif pajak tidak boleh kurang dari 0.',
            'billing_tax_rate.max' => 'Tarif pajak tidak boleh lebih dari 100%.',
        ]);

        $values = [
            'business_name' => trim((string) $data['business_name']),
            'business_owner' => trim((string) ($data['business_owner'] ?? '')),
            'business_phone' => trim((string) ($data['business_phone'] ?? '')),
            'business_email' => trim((string) ($data['business_email'] ?? '')),
            'business_address' => trim((string) ($data['business_address'] ?? '')),
            'business_city' => trim((string) ($data['business_city'] ?? '')),
            'invoice_footer' => trim((string) ($data['invoice_footer'] ?? '')),
            'whatsapp_invoice_template' => trim((string) $data['whatsapp_invoice_template']),
            'billing_tax_enabled' => $request->boolean('billing_tax_enabled') ? '1' : '0',
            'billing_tax_name' => trim((string) ($data['billing_tax_name'] ?? '')) ?: 'PPN',
            'billing_tax_rate' => number_format((float) $data['billing_tax_rate'], 4, '.', ''),
        ];

        foreach ($values as $key => $value) {
            Setting::put($key, $value);
        }

        return redirect()
            ->route('settings.billing.edit')
            ->with('success', 'Pengaturan usaha berhasil disimpan. Perubahan pajak hanya berlaku untuk invoice baru.');
    }
}
PHP;

$settingsView = <<<'BLADE'
@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div>
        <p class="text-sm font-semibold text-cyan-700 dark:text-cyan-300">Pengaturan</p>
        <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 dark:text-white">Pengaturan Usaha</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">Isi data usaha dan pesan tagihan di bawah ini. Informasi ini dipakai pada invoice cetak dan tombol WhatsApp pelanggan.</p>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
            <div class="font-bold">Data belum dapat disimpan.</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.billing.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <h2 class="text-base font-black text-slate-900 dark:text-white">1. Profil Usaha</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Data ini dapat tampil pada invoice dan pesan WhatsApp.</p>
            </div>
            <div class="grid gap-5 p-5 md:grid-cols-2">
                <div><label for="business_name" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Nama usaha <span class="text-rose-600">*</span></label><input id="business_name" name="business_name" type="text" maxlength="100" required value="{{ old('business_name', $businessName) }}" placeholder="Contoh: CV Internet Sejahtera" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
                <div><label for="business_owner" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Nama pemilik <span class="font-normal text-slate-400">(opsional)</span></label><input id="business_owner" name="business_owner" type="text" maxlength="100" value="{{ old('business_owner', $businessOwner) }}" placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
                <div><label for="business_phone" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Nomor WhatsApp usaha <span class="font-normal text-slate-400">(opsional)</span></label><input id="business_phone" name="business_phone" type="text" maxlength="30" value="{{ old('business_phone', $businessPhone) }}" placeholder="Contoh: 0812 3456 7890" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"><p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Boleh ditulis 0812..., +62812..., atau 62812....</p></div>
                <div><label for="business_email" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Email usaha <span class="font-normal text-slate-400">(opsional)</span></label><input id="business_email" name="business_email" type="email" maxlength="100" value="{{ old('business_email', $businessEmail) }}" placeholder="Contoh: admin@usahaanda.com" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
                <div class="md:col-span-2"><label for="business_address" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Alamat usaha <span class="font-normal text-slate-400">(opsional)</span></label><textarea id="business_address" name="business_address" rows="3" maxlength="500" placeholder="Contoh: Jl. Merdeka No. 10" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white">{{ old('business_address', $businessAddress) }}</textarea></div>
                <div><label for="business_city" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Kota / Kabupaten <span class="font-normal text-slate-400">(opsional)</span></label><input id="business_city" name="business_city" type="text" maxlength="100" value="{{ old('business_city', $businessCity) }}" placeholder="Contoh: Bandung" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h2 class="text-base font-black text-slate-900 dark:text-white">2. Pesan WhatsApp Tagihan</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pesan ini digunakan saat tombol WhatsApp pada daftar tagihan ditekan.</p></div>
            <div class="space-y-4 p-5">
                <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-4 text-sm text-cyan-900 dark:border-cyan-500/20 dark:bg-cyan-500/10 dark:text-cyan-100"><div class="font-bold">Kode yang dapat digunakan</div><p class="mt-1 leading-6"><code>{customer_name}</code>, <code>{invoice_number}</code>, <code>{amount}</code>, <code>{due_date}</code>, <code>{invoice_url}</code>, <code>{business_name}</code>, <code>{business_phone}</code>, dan <code>{credit_summary}</code>.</p><p class="mt-1 leading-6"><code>{credit_summary}</code> otomatis kosong bila pelanggan tidak memakai titip saldo.</p></div>
                <div><label for="whatsapp_invoice_template" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Isi pesan WhatsApp <span class="text-rose-600">*</span></label><textarea id="whatsapp_invoice_template" name="whatsapp_invoice_template" rows="13" maxlength="3000" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 font-mono text-sm leading-6 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white">{{ old('whatsapp_invoice_template', $whatsappInvoiceTemplate) }}</textarea></div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h2 class="text-base font-black text-slate-900 dark:text-white">3. Tampilan Invoice</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Teks ini tampil di bagian paling bawah invoice cetak.</p></div>
            <div class="p-5"><label for="invoice_footer" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Catatan bawah invoice <span class="font-normal text-slate-400">(opsional)</span></label><textarea id="invoice_footer" name="invoice_footer" rows="3" maxlength="500" placeholder="Contoh: Terima kasih telah menggunakan layanan kami." class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm leading-6 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white">{{ old('invoice_footer', $invoiceFooter) }}</textarea></div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h2 class="text-base font-black text-slate-900 dark:text-white">4. Pajak Invoice Baru</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Perubahan pajak hanya digunakan saat membuat invoice baru. Invoice yang sudah ada tidak berubah.</p></div>
            <div class="grid gap-5 p-5 md:grid-cols-2">
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 dark:border-slate-700"><input type="checkbox" name="billing_tax_enabled" value="1" @checked(old('billing_tax_enabled', $taxEnabled)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500"><span><span class="block text-sm font-bold text-slate-800 dark:text-slate-100">Gunakan pajak pada invoice baru</span><span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">Matikan bila usaha belum menerapkan pajak pada tagihan baru.</span></span></label>
                <div></div>
                <div><label for="billing_tax_name" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Nama pajak</label><input id="billing_tax_name" name="billing_tax_name" type="text" maxlength="50" value="{{ old('billing_tax_name', $taxName) }}" placeholder="Contoh: PPN" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
                <div><label for="billing_tax_rate" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Tarif pajak (%) <span class="text-rose-600">*</span></label><input id="billing_tax_rate" name="billing_tax_rate" type="number" min="0" max="100" step="0.0001" inputmode="decimal" required value="{{ old('billing_tax_rate', $taxRate) }}" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between"><p class="text-xs leading-5 text-slate-500 dark:text-slate-400">Field bertanda <span class="font-bold text-rose-600">*</span> wajib diisi.</p><button type="submit" class="inline-flex items-center justify-center rounded-xl bg-cyan-600 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-cyan-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Simpan Pengaturan</button></div>
    </form>
</div>
@endsection
BLADE;

$printView = <<<'BLADE'
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
    $remainingAmount = max((float) $invoice->amount - $verifiedPaid, 0);
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
        <tr><td>Tagihan setelah titip saldo</td><td>Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}</td></tr>
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
BLADE;

$timestamp = date('Ymd-His');
foreach ($targetFiles as $file) {
    $backup = $file . '.bak.business-settings.' . $timestamp;
    if (! copy($file, $backup)) {
        fwrite(STDERR, "GAGAL: Backup tidak dapat dibuat: {$backup}. Tidak ada file yang ditulis.\n");
        exit(1);
    }
}

$writes = [
    $targetFiles['controller'] => $controller . "\n",
    $targetFiles['settings_view'] => $settingsView . "\n",
    $targetFiles['invoice_index'] => $updatedInvoiceIndex,
    $targetFiles['print_view'] => $printView . "\n",
];

foreach ($writes as $file => $content) {
    if (file_put_contents($file, $content) === false) {
        fwrite(STDERR, "GAGAL: Tidak dapat menulis {$file}. Backup tersedia dengan akhiran .bak.business-settings.{$timestamp}\n");
        exit(1);
    }
}

fwrite(STDOUT, "SUKSES: Fase 1 Pengaturan Usaha diterapkan.\n");
fwrite(STDOUT, "Backup dibuat dengan akhiran: .bak.business-settings.{$timestamp}\n");
