@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
@php
    $tab = $tab ?? 'active';
    $hasFilter = !empty($search) || !empty($status) || !empty($period);

    $tabItems = [
        'active' => 'Tagihan Aktif',
        'history' => 'Riwayat Pembayaran',
        'all' => 'Semua',
    ];
@endphp

<div class="space-y-4">
    <div class="space-y-3">
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">Pembayaran</h1>

        <div class="flex flex-wrap gap-2" role="tablist" aria-label="Kategori pembayaran">
            @foreach ($tabItems as $tabKey => $tabLabel)
                <a href="{{ route('invoices.index', ['tab' => $tabKey]) }}"
                   role="tab"
                   aria-selected="{{ $tab === $tabKey ? 'true' : 'false' }}"
                   class="{{ $tab === $tabKey
                       ? 'bg-cyan-600 text-white shadow-sm hover:bg-cyan-700 dark:bg-cyan-500 dark:hover:bg-cyan-400'
                       : 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700' }} inline-flex items-center justify-center rounded-xl px-3.5 py-2 text-sm font-semibold transition">
                    {{ $tabLabel }}
                </a>
            @endforeach
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            <div class="font-semibold">Terjadi kesalahan:</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET"
          action="{{ route('invoices.index') }}"
          id="invoice-search-form"
          class="flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center">
          <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="relative min-w-0 flex-1">
            <label for="invoice-live-search" class="sr-only">Cari tagihan</label>

            <input type="search"
                   id="invoice-live-search"
                   name="search"
                   value="{{ $search ?? '' }}"
                   autocomplete="off"
                   placeholder="Cari invoice atau pelanggan..."
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 pr-10 text-sm font-medium text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20">

            <button type="button"
                    id="clear-invoice-live-search"
                    class="{{ !empty($search) ? 'inline-flex' : 'hidden' }} absolute inset-y-0 right-0 items-center justify-center px-3 text-lg text-slate-400 transition hover:text-rose-600 dark:text-slate-500 dark:hover:text-rose-400"
                    aria-label="Hapus pencarian tagihan">
                ×
            </button>
        </div>

        <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center">
            <select id="invoice-status-filter"
                    name="status"
                    aria-label="Filter status tagihan"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20 sm:w-40">
                <option value="">Semua Status</option>
                <option value="unpaid" {{ ($status ?? '') === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="paid" {{ ($status ?? '') === 'paid' ? 'selected' : '' }}>Lunas</option>
                <option value="isolated" {{ ($status ?? '') === 'isolated' ? 'selected' : '' }}>Terisolir</option>
            </select>

            <input type="month"
                   id="invoice-period-filter"
                   name="period"
                   value="{{ $period ?? '' }}"
                   aria-label="Filter periode tagihan"
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20 sm:w-40">
        </div>

        @if ($hasFilter)
            <a href="{{ route('invoices.index', ['tab' => $tab]) }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                Reset
            </a>
        @endif
    </form>

    <div class="flex items-center justify-between px-1 text-xs text-slate-500 dark:text-slate-400">
        <span>
            @if ($hasFilter)
                Hasil filter
            @else
                Semua tagihan
            @endif
        </span>
        <span class="font-semibold">
            @if ($hasFilter)
                {{ $invoices->total() }} ditemukan
            @else
                {{ $invoices->total() }} total
            @endif
        </span>
    </div>

    @if ($hasFilter && $invoices->count() === 0)
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-10 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="text-base font-bold text-slate-800 dark:text-slate-100">
                Tagihan tidak ditemukan
            </div>
            <p class="mx-auto mt-1 max-w-md text-sm text-slate-500 dark:text-slate-400">
                Tidak ada tagihan yang sesuai dengan kata kunci atau filter yang dipilih.
            </p>

            <a href="{{ route('invoices.index', ['tab' => $tab]) }}"
               class="mt-4 inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                Reset Filter
            </a>
        </div>
    @endif

    @if ($invoices->count() > 0)
        <div class="space-y-2.5 lg:hidden">
            @foreach ($invoices as $invoice)
                @php
                    $statusClass = match($invoice->status) {
                        'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300',
                        'isolated' => 'bg-rose-100 text-rose-700 dark:bg-rose-400/15 dark:text-rose-300',
                        default => 'bg-amber-100 text-amber-700 dark:bg-amber-400/15 dark:text-amber-300',
                    };

                    $amountClass = match($invoice->status) {
                        'paid' => 'text-emerald-600 dark:text-emerald-400',
                        'isolated' => 'text-rose-600 dark:text-rose-400',
                        default => 'text-amber-600 dark:text-amber-400',
                    };

                    $phoneDigits = preg_replace('/\\D+/', '', (string) ($invoice->customer->phone ?? ''));
                    $whatsappNumber = str_starts_with($phoneDigits, '0')
                        ? '62' . substr($phoneDigits, 1)
                        : $phoneDigits;

                    $canSendInvoiceWhatsApp = in_array($invoice->status, ['unpaid', 'isolated'], true)
                        && strlen($whatsappNumber) >= 10;

                    $creditUsed = (float) ($invoice->credit_used ?? 0);
                    $grossAmount = (float) (($invoice->gross_amount ?? 0) > 0
                        ? $invoice->gross_amount
                        : $invoice->amount);

                    $businessName = \App\Models\Setting::value('business_name', 'MACBILLING');
                    $businessPhone = \App\Models\Setting::value('business_phone', '');
                    $template = \App\Models\Setting::value('whatsapp_invoice_template', 'Halo Bapak/Ibu {customer_name},\n\nBerikut tagihan internet dari {business_name}.\n\nNo. Invoice: {invoice_number}\n{credit_summary}Sisa Tagihan: {amount}\nJatuh Tempo: {due_date}\n\nInvoice: {invoice_url}\n\nTerima kasih,\n{business_name}\nWhatsApp: {business_phone}');

                    $creditSummary = $creditUsed > 0
                        ? "Total tagihan awal: Rp " . number_format($grossAmount, 0, ',', '.') . "\n"
                          . "Titip saldo digunakan: Rp " . number_format($creditUsed, 0, ',', '.') . "\n"
                        : '';

                    $invoiceMessage = strtr($template, [
                        '{customer_name}' => (string) $invoice->customer->name,
                        '{customer_code}' => (string) ($invoice->customer->customer_code ?? ''),
                        '{invoice_number}' => (string) $invoice->invoice_number,
                        '{amount}' => "Rp " . number_format((float) $invoice->amount, 0, ',', '.'),
                        '{gross_amount}' => "Rp " . number_format($grossAmount, 0, ',', '.'),
                        '{credit_used}' => "Rp " . number_format($creditUsed, 0, ',', '.'),
                        '{credit_summary}' => $creditSummary,
                        '{due_date}' => $invoice->due_date->format('d/m/Y'),
                        '{billing_period}' => $invoice->billing_date->format('F Y'),
                        '{invoice_url}' => route('invoices.print', $invoice),
                        '{business_name}' => (string) $businessName,
                        '{business_phone}' => (string) $businessPhone,
                    ]);
                @endphp

                <article class="rounded-2xl border border-slate-200 bg-white px-3.5 py-3.5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="truncate text-sm font-bold text-slate-900 dark:text-white">
                                {{ $invoice->customer->name }}
                            </h2>
                            <p class="mt-0.5 truncate text-[11px] text-slate-500 dark:text-slate-400">
                                {{ $invoice->invoice_number }}
                            </p>
                        </div>

                        <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $statusClass }}">
                            {{ $invoice->status }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-end justify-between gap-3">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 dark:text-slate-500">
                                Sisa Tagihan
                            </div>
                            <div class="mt-0.5 text-lg font-black {{ $amountClass }}">
                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </div>

                            @if ($creditUsed > 0)
                                <div class="mt-0.5 text-[10px] font-medium text-emerald-700 dark:text-emerald-300">
                                    Dari total Rp {{ number_format($grossAmount, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        <div class="text-right">
                            <div class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 dark:text-slate-500">
                                Jatuh Tempo
                            </div>
                            <div class="mt-0.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                {{ $invoice->due_date->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                        <a href="{{ route('invoices.show', $invoice) }}"
                           class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                            Detail
                        </a>

                        @if ($canSendInvoiceWhatsApp)
                            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ rawurlencode($invoiceMessage) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex h-10 w-10 items-center justify-center rounded-xl transition"
                               style="display:inline-flex;background:#25D366;color:#ffffff;border:1px solid #25D366;box-shadow:0 1px 2px rgba(0,0,0,.08);text-decoration:none;"
                               aria-label="Kirim tagihan via WhatsApp"
                               title="Kirim tagihan via WhatsApp">
                                <img src="https://cdn.simpleicons.org/whatsapp/FFFFFF"
                                     alt=""
                                     class="h-4 w-4"
                                     width="16"
                                     height="16"
                                     loading="lazy">
                            </a>
                        @endif

                        @if ($invoice->status !== 'paid')
                            <a href="{{ route('invoices.pay', $invoice) }}"
                               class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-xl px-3 py-2 text-xs font-bold transition"
                               style="display:inline-flex;background:#0f766e;color:#ffffff;border:1px solid #0f766e;box-shadow:0 1px 2px rgba(0,0,0,.08);text-decoration:none;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M8 12h8"></path>
                                </svg>
                                Bayar
                            </a>
                        @else
                            <span class="inline-flex min-h-10 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                                Lunas
                            </span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:block">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[940px] text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-4 py-3">Invoice</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3">Pelanggan</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3">Tanggal</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3">Jatuh Tempo</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 text-right">Sisa Tagihan</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3">Status</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3">Kredit</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($invoices as $invoice)
                            @php
                                $statusClass = match($invoice->status) {
                                    'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300',
                                    'isolated' => 'bg-amber-100 text-amber-700 dark:bg-amber-400/15 dark:text-amber-300',
                                    default => 'bg-rose-100 text-rose-700 dark:bg-rose-400/15 dark:text-rose-300',
                                };

                                $phoneDigits = preg_replace('/\\D+/', '', (string) ($invoice->customer->phone ?? ''));
                                $whatsappNumber = str_starts_with($phoneDigits, '0')
                                    ? '62' . substr($phoneDigits, 1)
                                    : $phoneDigits;

                                $canSendInvoiceWhatsApp = in_array($invoice->status, ['unpaid', 'isolated'], true)
                                    && strlen($whatsappNumber) >= 10;

                                $creditUsed = (float) ($invoice->credit_used ?? 0);
                                $grossAmount = (float) (($invoice->gross_amount ?? 0) > 0
                                    ? $invoice->gross_amount
                                    : $invoice->amount);

                                $businessName = \App\Models\Setting::value('business_name', 'MACBILLING');
                                $businessPhone = \App\Models\Setting::value('business_phone', '');
                                $template = \App\Models\Setting::value('whatsapp_invoice_template', 'Halo Bapak/Ibu {customer_name},\n\nBerikut tagihan internet dari {business_name}.\n\nNo. Invoice: {invoice_number}\n{credit_summary}Sisa Tagihan: {amount}\nJatuh Tempo: {due_date}\n\nInvoice: {invoice_url}\n\nTerima kasih,\n{business_name}\nWhatsApp: {business_phone}');

                                $creditSummary = $creditUsed > 0
                                    ? "Total tagihan awal: Rp " . number_format($grossAmount, 0, ',', '.') . "\n"
                                      . "Titip saldo digunakan: Rp " . number_format($creditUsed, 0, ',', '.') . "\n"
                                    : '';

                                $invoiceMessage = strtr($template, [
                                    '{customer_name}' => (string) $invoice->customer->name,
                                    '{customer_code}' => (string) ($invoice->customer->customer_code ?? ''),
                                    '{invoice_number}' => (string) $invoice->invoice_number,
                                    '{amount}' => "Rp " . number_format((float) $invoice->amount, 0, ',', '.'),
                                    '{gross_amount}' => "Rp " . number_format($grossAmount, 0, ',', '.'),
                                    '{credit_used}' => "Rp " . number_format($creditUsed, 0, ',', '.'),
                                    '{credit_summary}' => $creditSummary,
                                    '{due_date}' => $invoice->due_date->format('d/m/Y'),
                                    '{billing_period}' => $invoice->billing_date->format('F Y'),
                                    '{invoice_url}' => route('invoices.print', $invoice),
                                    '{business_name}' => (string) $businessName,
                                    '{business_phone}' => (string) $businessPhone,
                                ]);
                            @endphp

                            <tr class="border-t border-slate-200 align-middle dark:border-slate-800">
                                <td class="whitespace-nowrap px-4 py-3.5">
                                    <div class="font-semibold text-slate-900 dark:text-white">
                                        {{ $invoice->invoice_number }}
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-900 dark:text-white">
                                        {{ $invoice->customer->name }}
                                    </div>

                                    @if (!empty($invoice->customer->customer_code))
                                        <div class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                            {{ $invoice->customer->customer_code }}
                                        </div>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-600 dark:text-slate-300">
                                    {{ $invoice->billing_date->format('d/m/Y') }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-3.5 text-right">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                    </div>

                                    @if ($creditUsed > 0)
                                        <div class="mt-0.5 text-[10px] font-medium text-emerald-700 dark:text-emerald-300">
                                            Dari total Rp {{ number_format($grossAmount, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-4 py-3.5">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $statusClass }}">
                                        {{ $invoice->status }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-4 py-3.5">
                                    @if (($invoice->customer->credit_balance ?? 0) > 0)
                                        <span class="font-semibold text-amber-700 dark:text-amber-300">
                                            Rp {{ number_format($invoice->customer->credit_balance, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500">-</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-4 py-3.5 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('invoices.show', $invoice) }}"
                                           class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                                            Detail
                                        </a>

                                        @if ($canSendInvoiceWhatsApp)
                                            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ rawurlencode($invoiceMessage) }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition"
                                               style="display:inline-flex;background:#25D366;color:#ffffff;border:1px solid #25D366;box-shadow:0 1px 2px rgba(0,0,0,.08);text-decoration:none;"
                                               aria-label="Kirim tagihan via WhatsApp"
                                               title="Kirim tagihan via WhatsApp">
                                                <img src="https://cdn.simpleicons.org/whatsapp/FFFFFF"
                                                     alt=""
                                                     class="h-4 w-4"
                                                     width="16"
                                                     height="16"
                                                     loading="lazy">
                                                WhatsApp
                                            </a>
                                        @endif

                                        @if ($invoice->status !== 'paid')
                                            <a href="{{ route('invoices.pay', $invoice) }}"
                                               class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition"
                                               style="display:inline-flex;background:#0f766e;color:#ffffff;border:1px solid #0f766e;box-shadow:0 1px 2px rgba(0,0,0,.08);text-decoration:none;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <path d="M8 12h8"></path>
                                                </svg>
                                                Bayar
                                            </a>
                                        @else
                                            <span class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                                                Lunas
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif (! $hasFilter)
        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
            Belum ada tagihan yang tersedia.
        </div>
    @endif

    @if ($invoices->hasPages())
        <div>
            {{ $invoices->links() }}
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('invoice-search-form');
        const searchInput = document.getElementById('invoice-live-search');
        const statusInput = document.getElementById('invoice-status-filter');
        const periodInput = document.getElementById('invoice-period-filter');
        const clearButton = document.getElementById('clear-invoice-live-search');

        let searchTimer = null;
        let lastSubmittedState = '';

        function getFormState() {
            return [
                searchInput.value.trim(),
                statusInput.value,
                periodInput.value,
            ].join('|');
        }

        function submitFilters() {
            const currentState = getFormState();

            if (currentState === lastSubmittedState) {
                return;
            }

            lastSubmittedState = currentState;
            form.submit();
        }

        searchInput.addEventListener('input', function () {
            const hasSearchValue = Boolean(searchInput.value.trim());

            clearButton.classList.toggle('hidden', !hasSearchValue);
            clearButton.classList.toggle('inline-flex', hasSearchValue);

            clearTimeout(searchTimer);

            searchTimer = setTimeout(function () {
                submitFilters();
            }, 600);
        });

        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                clearTimeout(searchTimer);
                lastSubmittedState = getFormState();
                form.submit();
            }
        });

        statusInput.addEventListener('change', function () {
            clearTimeout(searchTimer);
            submitFilters();
        });

        periodInput.addEventListener('change', function () {
            clearTimeout(searchTimer);
            submitFilters();
        });

        clearButton.addEventListener('click', function () {
            searchInput.value = '';
            clearButton.classList.remove('inline-flex');
            clearButton.classList.add('hidden');
            form.submit();
        });
    });
</script>
@endsection
