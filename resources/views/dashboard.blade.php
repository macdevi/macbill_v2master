@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $currentUser = auth()->user();
    $loginName = $currentUser?->name ?? 'Pengguna';

    $dashboardScope = $dashboardScope ?? 'global';
    $dashboardAreas = collect($dashboardAreas ?? []);
    $isAreaDashboard = $dashboardScope === 'area';

    $networkTotal = max((int) $totalCustomers, 0);
    $networkOnline = max((int) $onlineCustomers, 0);
    $networkOffline = max((int) $offlineCustomers, 0);
    $networkIsolated = max((int) $isolatedCustomers, 0);

    $networkOnlinePercent = $networkTotal > 0 ? min(($networkOnline / $networkTotal) * 100, 100) : 0;
    $networkOfflinePercent = $networkTotal > 0 ? min(($networkOffline / $networkTotal) * 100, 100) : 0;
    $networkIsolatedPercent = $networkTotal > 0 ? min(($networkIsolated / $networkTotal) * 100, 100) : 0;

    $networkOnlineEnd = $networkOnlinePercent;
    $networkOfflineEnd = min($networkOnlinePercent + $networkOfflinePercent, 100);

    if ($networkOnlinePercent >= 95) {
        $networkHealthLabel = 'Sangat sehat';
        $networkHealthClass = 'db2-health--good';
    } elseif ($networkOnlinePercent >= 80) {
        $networkHealthLabel = 'Perlu perhatian';
        $networkHealthClass = 'db2-health--warn';
    } else {
        $networkHealthLabel = 'Perlu tindakan';
        $networkHealthClass = 'db2-health--bad';
    }

    $networkDonutStyle = sprintf(
        'background: conic-gradient(#22c55e 0%% %.4f%%, #f43f5e %.4f%% %.4f%%, #f59e0b %.4f%% 100%%);',
        $networkOnlineEnd,
        $networkOnlineEnd,
        $networkOfflineEnd,
        $networkOfflineEnd
    );

    $expenseBarWidth = $monthlyIncome > 0
        ? min(round(($monthlyExpense / $monthlyIncome) * 100, 1), 100)
        : ($monthlyExpense > 0 ? 100 : 0);
@endphp

<div class="db2">
    <header class="db2-welcome db2-welcome-card">
        <div>
            <p class="db2-eyebrow">DASHBOARD OPERASIONAL</p>
            <h1 id="dashboard-greeting" class="db2-welcome__title">
                Selamat Datang, {{ $loginName }}
            </h1>
            <p id="dashboard-local-time" class="db2-welcome__time">
                Memuat waktu lokal...
            </p>
            @if ($isAreaDashboard && $dashboardAreas->isNotEmpty())
                <div class="db2-area-chips" aria-label="Wilayah operasional">
                    @foreach ($dashboardAreas as $areaName)
                        <span class="db2-area-chip">{{ $areaName }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </header>
    <section class="db2-grid db2-grid--top" aria-label="Ringkasan operasional">
        <article class="db2-card db2-network">
            <header class="db2-card__header db2-network-v2__header">
                <div>
                    <p class="db2-network-v2__eyebrow">STATUS JARINGAN</p>
                </div>

                <span class="db2-network-v2__live"><i></i>LIVE</span>
            </header>

            <section class="db2-network-v2__router" aria-label="Status MikroTik">
                <span class="db2-network-v2__router-icon" aria-hidden="true">
                    <img src="https://img.icons8.com/fluency/48/router.png" alt="" width="48" height="48" decoding="async">
</span>

<div class="db2-network-v2__router-copy">
    <strong>MikroTik Gateway</strong>
    <span>
        {{ ($mikrotikConnected ?? '') === 'Connected'
            ? 'Terhubung ke ' . ($mikrotikIdentity ?: 'MikroTik')
            : 'Router belum terhubung' }}
    </span>
</div>
            </section>

<section class="db2-network-v2__telemetry" aria-label="Telemetry router">
    <div class="db2-network-v2__metric">
                    <span class="db2-network-v2__metric-label">Status</span>
                    <strong class="db2-network-v2__metric-value {{ ($mikrotikConnected ?? '') === 'Connected' ? 'is-good' : '' }}">
                        {{ ($mikrotikConnected ?? '') === 'Connected' ? 'Online' : 'Offline' }}
                    </strong>
                </div>

                <div class="db2-network-v2__metric">
                    <span class="db2-network-v2__metric-label">CPU</span>
                    <strong class="db2-network-v2__metric-value">{{ isset($mikrotikCpu) ? $mikrotikCpu . '%' : '[-]' }}</strong>
                    @if (isset($mikrotikCpu))
                        <span class="db2-network-v2__cpu-bar">
                            <i style="width: {{ min(max((float) $mikrotikCpu, 0), 100) }}%"></i>
                        </span>
                    @endif
                </div>
                <div class="db2-network-v2__metric">
                    <span class="db2-network-v2__metric-label">Uptime</span>
                    <strong class="db2-network-v2__metric-value">{{ $mikrotikUptime ?? '-' }}</strong>
                </div>

            </section>
            <div class="db2-network-v2__customer-heading">
                <strong>MONITORING PELANGGAN</strong>
                <span>Klik status untuk detail</span>
            </div>

            <section class="db2-network-v2__customers" aria-label="Ringkasan status pelanggan">
                <div
                    class="db2-network-v2__donut"
                    style="{{ $networkDonutStyle }}"
                    role="img"
                    aria-label="{{ number_format($networkOnlinePercent, 1, ',', '.') }} persen pelanggan online"
                >
                    <div class="db2-network-v2__donut-inner">
                        <strong>{{ number_format($networkTotal, 0, ',', '.') }}</strong>
                        <span>Total pelanggan</span>
                    </div>
                </div>

                <div class="db2-network-v2__status-list">
                    <button type="button" class="db2-network-v2__status db2-network-v2__status--online" data-customer-status="online" aria-label="Lihat daftar pelanggan online">
                        <span class="db2-network-v2__status-dot" aria-hidden="true"></span>
                        <span class="db2-network-v2__status-label">Online</span>
                        <strong class="db2-network-v2__status-count">{{ number_format($networkOnline, 0, ',', '.') }}</strong>
                        <span class="db2-network-v2__status-action">Lihat ›</span>
                    </button>

                    <button type="button" class="db2-network-v2__status db2-network-v2__status--offline" data-customer-status="offline" aria-label="Lihat daftar pelanggan offline">
                        <span class="db2-network-v2__status-dot" aria-hidden="true"></span>
                        <span class="db2-network-v2__status-label">Offline</span>
                        <strong class="db2-network-v2__status-count">{{ number_format($networkOffline, 0, ',', '.') }}</strong>
                        <span class="db2-network-v2__status-action">Lihat ›</span>
                    </button>

                    <button type="button" class="db2-network-v2__status db2-network-v2__status--isolated" data-customer-status="isolated" aria-label="Lihat daftar pelanggan terisolir">
                        <span class="db2-network-v2__status-dot" aria-hidden="true"></span>
                        <span class="db2-network-v2__status-label">Terisolir</span>
                        <strong class="db2-network-v2__status-count">{{ number_format($networkIsolated, 0, ',', '.') }}</strong>
                        <span class="db2-network-v2__status-action">Lihat ›</span>
                    </button>
                </div>
            </section>

            <p class="db2-network-v2__updated">Ether1 · [DOWN] <strong>{{ $traffic["download"] ?? "0 bps" }}</strong> · [UP] <strong>{{ $traffic["upload"] ?? "0 bps" }}</strong></p>
        </article>

        <article class="db2-card db2-profit db2-finance-v5 {{ $netProfit < 0 ? 'db2-profit--negative' : '' }}">
    <header class="db2-card__header db2-finance-v6__header">
        <h2 class="db2-finance-v6__title">CATATAN KEUANGAN</h2>

        <div class="db2-finance-v6__date">
            <img src="{{ asset('icons/calendar-local.png') }}" alt="" width="20" height="20" decoding="async">
            <span>{{ $financialMonthLabel }}</span>
        </div>
    </header>

    <section class="db2-profit__hero db2-finance-v6__profit-hero">
        <div class="db2-finance-v6__profit-top">
            <span class="db2-finance-v6__profit-label"><img src="{{ asset('icons/profit.png') }}" alt="" width="20" height="20" decoding="async">
                <img src="https://img.icons8.com/fluency/48/profit.png" alt="" width="48" height="48" decoding="async" onerror="this.remove()">
                Laba bersih
            </span>

            <span class="db2-finance-v6__period-chip"><img src="{{ asset('icons/calendar-local.png') }}" alt="" width="18" height="18" decoding="async">
                
                Bulan ini
            </span>
        </div>

        <div class="db2-finance-v6__profit-main">
            <div>
                <strong class="db2-profit__amount db2-finance-v6__profit-amount">
                    {{ $netProfit < 0 ? '- ' : '' }}Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
                </strong>

                <p class="db2-finance-v6__profit-copy">
                    @if ($netProfit > 0)
                        Setelah seluruh pengeluaran tercatat
                    @elseif ($netProfit < 0)
                        Pengeluaran melampaui pendapatan bulan ini
                    @else
                        Pendapatan dan pengeluaran masih seimbang
                    @endif
                </p>
            </div>

            <span class="db2-finance-v6__profit-status {{ $netProfit < 0 ? 'is-negative' : ($netProfit > 0 ? 'is-positive' : 'is-neutral') }}">
                <img
                    src="https://img.icons8.com/fluency/48/{{ $netProfit < 0 ? 'long-arrow-down' : ($netProfit> 0 ? 'long-arrow-up' : 'horizontal-line') }}.png"
                    alt=""
                    width="48"
                    height="48"
                    decoding="async"
                >
                {{ $netProfit < 0 ? 'Perlu perhatian' : ($netProfit > 0 ? 'Positif' : 'Seimbang') }}
            </span>
        </div>

        <div class="db2-finance-v6__chart" aria-hidden="true">
            <svg viewBox="0 0 360 42" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path class="db2-finance-v6__chart-guide" d="M0 34H360M0 20H360M0 7H360" />
                <rect class="db2-finance-v6__chart-bar" x="8" y="25" width="19" height="17" rx="3"/>
                <rect class="db2-finance-v6__chart-bar" x="52" y="20" width="19" height="22" rx="3"/>
                <rect class="db2-finance-v6__chart-bar" x="96" y="24" width="19" height="18" rx="3"/>
                <rect class="db2-finance-v6__chart-bar" x="140" y="15" width="19" height="27" rx="3"/>
                <rect class="db2-finance-v6__chart-bar" x="184" y="18" width="19" height="24" rx="3"/>
                <rect class="db2-finance-v6__chart-bar is-strong" x="228" y="10" width="19" height="32" rx="3"/>
                <rect class="db2-finance-v6__chart-bar is-strong" x="272" y="13" width="19" height="29" rx="3"/>
                <rect class="db2-finance-v6__chart-bar is-strong" x="316" y="4" width="19" height="38" rx="3"/>
                <path class="db2-finance-v6__chart-line" d="M17 24L61 18L105 22L149 12L193 15L237 7L281 10L325 2"/>
                <circle class="db2-finance-v6__chart-dot" cx="325" cy="2" r="3.5"/>
            </svg>
        </div>
    </section>

    <div class="db2-flow-grid db2-finance-v6__flow-grid">
        <article class="db2-flow db2-flow--income">
            <div class="db2-flow__top">
                <span class="db2-flow__icon" aria-hidden="true">
                    <img src="https://img.icons8.com/fluency/48/money-bag.png" alt="" width="48" height="48" decoding="async">
                </span>
                <span class="db2-flow__label">Pendapatan</span>
            </div>
            <strong>Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</strong>
        </article>

        <article class="db2-flow db2-flow--expense">
            <div class="db2-flow__top">
                <span class="db2-flow__icon" aria-hidden="true">
                    <img src="https://img.icons8.com/fluency/48/wallet.png" alt="" width="48" height="48" decoding="async">
                </span>
                <span class="db2-flow__label">Pengeluaran</span>
            </div>
            <strong>Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</strong>
        </article>
    </div>

    <div class="db2-finance-notes db2-finance-v6__connected-list">
        <button
            type="button"
            class="db2-finance-note db2-finance-note--action"
            data-pending-invoice-modal-open
            aria-label="Lihat daftar invoice belum dibayar"
        >
            <span class="db2-finance-note__icon" aria-hidden="true">
                <img src="https://img.icons8.com/fluency/48/bill.png" alt="" width="48" height="48" decoding="async">
            </span>

            <span class="db2-finance-note__copy">
                <strong>Tagihan belum bayar</strong>
            </span>

            <span class="db2-finance-note__invoice-summary">
                <strong>{{ number_format($pendingInvoiceCount, 0, ',', '.') }}</strong>
                <small>Tagihan</small>
            </span>

            <span class="db2-finance-note__action-label">Lihat <span aria-hidden="true">›</span></span>
        </button>

        <div class="db2-finance-note db2-finance-note--pending">
            <span class="db2-finance-note__icon" aria-hidden="true">
                <img src="https://img.icons8.com/fluency/48/hourglass.png" alt="" width="48" height="48" decoding="async">
            </span>

            <span class="db2-finance-note__copy">
                <strong>Pendapatan tertunda</strong>
            </span>

            <strong class="db2-finance-note__value">
                Rp {{ number_format($pendingRevenue, 0, ',', '.') }}
            </strong>
        </div>

        <div class="db2-finance-note db2-finance-note--estimate">
            <span class="db2-finance-note__icon" aria-hidden="true">
                <img src="https://img.icons8.com/fluency/48/combo-chart.png" alt="" width="48" height="48" decoding="async">
            </span>

            <span class="db2-finance-note__copy">
                <strong>Estimasi pendapatan</strong>
            </span>

            <strong class="db2-finance-note__value">
                Rp {{ number_format($estimatedRevenue, 0, ',', '.') }}
            </strong>
        </div>
    </div>
</article>

            </section>

    <section class="db2-card db2-activity">
        <header class="db2-card__header">
            <div>
                <p class="db2-eyebrow">RIWAYAT TRANSAKSI</p>
            </div>
            <a href="{{ route('finance.activity') }}" class="db2-link">Lihat semua <span aria-hidden="true">→</span></a>
        </header>

        <div class="db2-activity-list">
            @forelse ($recentFinanceActivity as $activity)
                <article class="db2-activity-row">
                    <span
                        class="db2-activity-row__icon {{ $activity->activity_type === 'income' ? 'is-income' : 'is-expense' }}"
                        aria-hidden="true"
                    >
                        <img
                            src="{{ asset($activity->activity_type === 'income' ? 'icons/arrow-down.png' : 'icons/arrow-up.png') }}"
                            class="db2-activity-row__icon-image"
                            alt=""
                        >
                    </span>

                    <div class="db2-activity-row__copy">
                        <strong>{{ $activity->activity_title }}</strong>
                        <span>
                            {{ \Carbon\Carbon::parse($activity->activity_date)->translatedFormat('d M Y, H:i') }}
                            @if ($activity->activity_reference)
                                · {{ $activity->activity_reference }}
                            @endif
                        </span>
                    </div>

                    <strong class="db2-activity-row__amount {{ $activity->activity_type === 'income' ? 'is-income' : 'is-expense' }}">
                        {{ $activity->activity_type === 'income' ? '+ ' : '- ' }}Rp {{ number_format($activity->activity_amount, 0, ',', '.') }}
                    </strong>
                </article>
            @empty
                <div class="db2-empty">Belum ada aktivitas keuangan terbaru.</div>
            @endforelse
        </div>
    </section>

    @if (auth()->user()?->isSuperAdmin() && $areaFinancialSummaries->isNotEmpty())
        <section class="db2-card db2-area" aria-labelledby="area-financial-summary-title">
            <header class="db2-card__header">
                <div>
                    <p class="db2-eyebrow">SUPER ADMIN</p>
                    <h2 id="area-financial-summary-title" class="db2-card__title">Keuangan per wilayah</h2>
                    <p class="db2-card__subtitle">Periode {{ $financialMonthLabel }}</p>
                </div>
                <span class="db2-period-chip">Per wilayah</span>
            </header>

            <div class="db2-table-scroll">
                <table class="db2-table">
                    <thead>
                        <tr>
                            <th scope="col">Wilayah</th>
                            <th scope="col">Pelanggan aktif</th>
                            <th scope="col">Pembayaran</th>
                            <th scope="col">Pengeluaran</th>
                            <th scope="col">Laba bersih</th>
                            <th scope="col">Piutang aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($areaFinancialSummaries as $areaSummary)
                            <tr>
                                <td>
                                    <strong>{{ $areaSummary['name'] }}</strong>
                                    @if (!empty($areaSummary['code']))
                                        <small>{{ $areaSummary['code'] }}</small>
                                    @endif
                                </td>
                                <td>{{ number_format($areaSummary['customer_count'], 0, ',', '.') }}</td>
                                <td class="is-income">Rp {{ number_format($areaSummary['income'], 0, ',', '.') }}</td>
                                <td class="is-expense">Rp {{ number_format($areaSummary['expense'], 0, ',', '.') }}</td>
                                <td class="{{ $areaSummary['net_profit'] < 0 ? 'is-expense' : 'is-income' }}">
                                    {{ $areaSummary['net_profit'] < 0 ? '- ' : '' }}Rp {{ number_format(abs($areaSummary['net_profit']), 0, ',', '.') }}
                                </td>
                                <td>
                                    Rp {{ number_format($areaSummary['pending_revenue'], 0, ',', '.') }}
                                    @if ($areaSummary['pending_invoice_count'] > 0)
                                        <small>{{ number_format($areaSummary['pending_invoice_count'], 0, ',', '.') }} invoice</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>

<div
    id="customer-status-modal"
    class="customer-status-modal"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-labelledby="customer-status-modal-title"
>
    <div class="customer-status-modal__backdrop" data-modal-close></div>
    <section class="customer-status-modal__panel" role="document">
        <header class="customer-status-modal__header">
            <div>
                <p id="customer-status-modal-kicker" class="customer-status-modal__kicker">MONITORING JARINGAN</p>
                <h2 id="customer-status-modal-title" class="customer-status-modal__title">Pelanggan</h2>
                <p id="customer-status-modal-count" class="customer-status-modal__count"></p>
            </div>
            <button type="button" class="customer-status-modal__close" data-modal-close aria-label="Tutup daftar pelanggan">×</button>
        </header>
        <div id="customer-status-modal-list" class="customer-status-modal__list"></div>
        <footer class="customer-status-modal__footer">
            <button type="button" class="customer-status-modal__button" data-modal-close>Tutup</button>
        </footer>
    </section>
</div>


<div
    id="pending-invoice-modal"
    class="pending-invoice-modal"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-labelledby="pending-invoice-modal-title"
>
    <div class="pending-invoice-modal__backdrop" data-pending-invoice-modal-close></div>

    <section class="pending-invoice-modal__panel" role="document">
        <header class="pending-invoice-modal__header">
            <div>
                <p class="pending-invoice-modal__kicker">TINDAKAN PENAGIHAN</p>
                <h2 id="pending-invoice-modal-title" class="pending-invoice-modal__title">
                    Invoice Belum Dibayar
                </h2>
                <p class="pending-invoice-modal__summary">
                    {{ number_format($pendingInvoiceCount, 0, ',', '.') }} invoice ·
                    Total Rp {{ number_format($pendingRevenue, 0, ',', '.') }}
                </p>
            </div>

            <button
                type="button"
                class="pending-invoice-modal__close"
                data-pending-invoice-modal-close
                aria-label="Tutup daftar invoice"
            >×</button>
        </header>

        <div class="pending-invoice-modal__list">
            @forelse ($pendingInvoiceList as $invoice)
                @php
                    $invoiceCustomerName = $invoice->customer?->name ?? 'Pelanggan tidak ditemukan';
                    $invoiceInitials = collect(preg_split('/s+/', trim($invoiceCustomerName)))
                        ->filter()
                        ->take(2)
                        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                        ->implode('');

                    if ($invoiceInitials === '') {
                        $invoiceInitials = '?';
                    }
                @endphp

                <article class="pending-invoice-modal__item">
                    <div class="pending-invoice-modal__avatar" aria-hidden="true">
                        {{ $invoiceInitials }}
                    </div>

                    <div class="pending-invoice-modal__info">
                        <h3>{{ $invoiceCustomerName }}</h3>

                        <div class="pending-invoice-modal__meta">
                            <span>{{ $invoice->invoice_number }}</span>

                            @if ($invoice->due_date)
                                <span>Jatuh tempo {{ $invoice->due_date->translatedFormat('d M Y') }}</span>
                            @else
                                <span>Tagihan {{ $invoice->billing_date?->translatedFormat('d M Y') ?? '[-]' }}</span>
                            @endif

                            @if (auth()->user()?->isSuperAdmin() && $invoice->customer?->area?->name)
                                <span>{{ $invoice->customer->area->name }}</span>
                            @endif

                            @if ($invoice->customer?->phone)
                                <span>{{ $invoice->customer->phone }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="pending-invoice-modal__finance">
                        <strong>Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}</strong>

                        <span class="pending-invoice-modal__status {{ $invoice->status === 'isolated' ? 'is-isolated' : '' }}">
                            {{ $invoice->status === 'isolated' ? 'Terisolir' : 'Belum dibayar' }}
                        </span>

                        <a href="{{ route('invoices.show', $invoice) }}">
                            Buka invoice <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="pending-invoice-modal__empty">
                    Tidak ada invoice belum dibayar saat ini.
                </div>
            @endforelse
        </div>

        @if ($pendingInvoiceCount > $pendingInvoiceList->count())
            <footer class="pending-invoice-modal__footer">
                Menampilkan {{ number_format($pendingInvoiceList->count(), 0, ',', '.') }}
                invoice dari total {{ number_format($pendingInvoiceCount, 0, ',', '.') }} invoice belum dibayar.
            </footer>
        @endif
    </section>
</div>

<script>
(function () {
    const customerStatusLists = @json($customerStatusLists);
    const statusConfig = {
        online: { title: 'Pelanggan Online', label: 'ONLINE', empty: 'Tidak ada pelanggan online saat ini.' },
        offline: { title: 'Pelanggan Offline', label: 'OFFLINE', empty: 'Tidak ada pelanggan offline saat ini.' },
        isolated: { title: 'Pelanggan Terisolir', label: 'TERISOLIR', empty: 'Tidak ada pelanggan terisolir saat ini.' },
    };

    const modal = document.getElementById('customer-status-modal');
    const modalTitle = document.getElementById('customer-status-modal-title');
    const modalKicker = document.getElementById('customer-status-modal-kicker');
    const modalCount = document.getElementById('customer-status-modal-count');
    const modalList = document.getElementById('customer-status-modal-list');
    const modalCloseElements = document.querySelectorAll('[data-modal-close]');
    const statusCards = document.querySelectorAll('[data-customer-status]');
    let lastFocusedElement = null;

    const escapeHtml = function (value) {
        const element = document.createElement('div');
        element.textContent = value ?? '';
        return element.innerHTML;
    };

    const openCustomerModal = function (status) {
        const config = statusConfig[status];
        const customers = customerStatusLists[status] || [];

        if (!config || !modal || !modalTitle || !modalList) {
            return;
        }

        lastFocusedElement = document.activeElement;
        modalTitle.textContent = config.title;
        modalKicker.textContent = config.label;
        modalCount.textContent = `${customers.length} pelanggan`;

        if (customers.length === 0) {
            modalList.innerHTML = `<div class="customer-status-modal__empty">${escapeHtml(config.empty)}</div>`;
        } else {
            modalList.innerHTML = customers.map(function (customer) {
                const code = customer.customer_code ? `<span>${escapeHtml(customer.customer_code)}</span>` : '';
                const username = customer.pppoe_username ? `<span>${escapeHtml(customer.pppoe_username)}</span>` : '<span>Username PPPoE belum diisi</span>';
                const router = customer.router_name ? `<span>Router: ${escapeHtml(customer.router_name)}</span>` : '<span>Router belum ditentukan</span>';
                const phone = customer.phone ? `<span>${escapeHtml(customer.phone)}</span>` : '';

                return `
                    <article class="customer-status-modal__customer">
                        <div class="customer-status-modal__avatar" aria-hidden="true">
                            ${escapeHtml((customer.name || '?').charAt(0).toUpperCase())}
                        </div>
                        <div class="customer-status-modal__customer-info">
                            <h3>${escapeHtml(customer.name || 'Tanpa nama')}</h3>
                            <div class="customer-status-modal__meta">
                                ${code}
                                ${username}
                                ${router}
                                ${phone}
                            </div>
                        </div>
                    </article>
                `;
            }).join('');
        }

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('customer-status-modal-open');

        const closeButton = modal.querySelector('.customer-status-modal__close');
        if (closeButton) {
            closeButton.focus();
        }
    };

    const closeCustomerModal = function () {
        if (!modal || !modal.classList.contains('is-open')) {
            return;
        }

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('customer-status-modal-open');

        if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
            lastFocusedElement.focus();
        }
    };

    statusCards.forEach(function (card) {
        card.addEventListener('click', function () {
            openCustomerModal(card.dataset.customerStatus);
        });
    });

    modalCloseElements.forEach(function (element) {
        element.addEventListener('click', closeCustomerModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeCustomerModal();
        }
    });


    const pendingInvoiceModal = document.getElementById('pending-invoice-modal');
    const pendingInvoiceOpenButtons = document.querySelectorAll('[data-pending-invoice-modal-open]');
    const pendingInvoiceCloseElements = document.querySelectorAll('[data-pending-invoice-modal-close]');
    let pendingInvoiceLastFocusedElement = null;

    const openPendingInvoiceModal = function () {
        if (!pendingInvoiceModal) {
            return;
        }

        pendingInvoiceLastFocusedElement = document.activeElement;
        pendingInvoiceModal.classList.add('is-open');
        pendingInvoiceModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('pending-invoice-modal-open');

        const closeButton = pendingInvoiceModal.querySelector('.pending-invoice-modal__close');
        if (closeButton) {
            closeButton.focus();
        }
    };

    const closePendingInvoiceModal = function () {
        if (!pendingInvoiceModal || !pendingInvoiceModal.classList.contains('is-open')) {
            return;
        }

        pendingInvoiceModal.classList.remove('is-open');
        pendingInvoiceModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('pending-invoice-modal-open');

        if (
            pendingInvoiceLastFocusedElement &&
            typeof pendingInvoiceLastFocusedElement.focus === 'function'
        ) {
            pendingInvoiceLastFocusedElement.focus();
        }
    };

    pendingInvoiceOpenButtons.forEach(function (button) {
        button.addEventListener('click', openPendingInvoiceModal);
    });

    pendingInvoiceCloseElements.forEach(function (element) {
        element.addEventListener('click', closePendingInvoiceModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closePendingInvoiceModal();
        }
    });

    const greetingElement = document.getElementById('dashboard-greeting');
    const timeElement = document.getElementById('dashboard-local-time');
    const loginName = @json($loginName);

    if (!greetingElement || !timeElement) {
        return;
    }

    const updateLocalTime = function () {
        const now = new Date();
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Asia/Jakarta';
        const hour = now.getHours();
        let greeting = 'Selamat Malam';

        if (hour >= 4 && hour < 11) {
            greeting = 'Selamat Pagi';
        } else if (hour >= 11 && hour < 15) {
            greeting = 'Selamat Siang';
        } else if (hour >= 15 && hour < 18) {
            greeting = 'Selamat Sore';
        }

        greetingElement.textContent = greeting + ', ' + loginName;

        const formattedDate = new Intl.DateTimeFormat('id-ID', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            timeZone: timezone,
        }).format(now);

        const formattedTime = new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
            timeZone: timezone,
            timeZoneName: 'short',
        }).format(now);

        timeElement.textContent = formattedDate + ' · ' + formattedTime;
    };

    updateLocalTime();
    window.setInterval(updateLocalTime, 30000);
})();
</script>

@include('partials.dashboard-styles')
@include('partials.dashboard-finance-final')
@include('partials.dashboard-network-final')
@endsection
