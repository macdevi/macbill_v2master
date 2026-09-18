@extends('layouts.app')

@section('title', 'Dashboard Operasional')

@section('content')
@include('partials.dashboard-styles')

<div class="dashboard-page">
    <header class="dashboard-header">
        <div>
            <h1 class="dashboard-header__title" id="dashboard-greeting">
                Selamat Datang, {{ $user->name }}
            </h1>
            <p class="dashboard-header__time" id="dashboard-local-time">
                Memuat waktu lokal...
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2">
            @foreach($areas as $area)
                <span class="inline-flex items-center gap-1.5 rounded-full border border-violet-200 bg-violet-50 px-3 py-1.5 text-xs font-bold text-violet-700 dark:border-violet-400/20 dark:bg-violet-400/10 dark:text-violet-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>
                    {{ $area->name }}
                </span>
            @endforeach
        </div>
    </header>

@php
    $networkTotal = max((int) $totalCustomers, 0);
    $networkOnline = max((int) $onlineCustomers, 0);
    $networkOffline = max((int) $offlineCustomers, 0);
    $networkIsolated = max((int) $isolatedCustomers, 0);
    $networkOnlinePercent = $networkTotal > 0 ? min(($networkOnline / $networkTotal) * 100, 100) : 0;
    $networkOfflinePercent = $networkTotal > 0 ? min(($networkOffline / $networkTotal) * 100, 100) : 0;
    $networkOnlineEnd = $networkOnlinePercent;
    $networkOfflineEnd = min($networkOnlinePercent + $networkOfflinePercent, 100);
    if ($networkOnlinePercent >= 95) {
        $networkHealthLabel = "SEHAT";
        $networkHealthClass = "network-monitoring-card__health--healthy";
    } elseif ($networkOnlinePercent >= 80) {
        $networkHealthLabel = "PERHATIAN";
        $networkHealthClass = "network-monitoring-card__health--warning";
    } else {
        $networkHealthLabel = "BURUK";
        $networkHealthClass = "network-monitoring-card__health--critical";
    }
    $networkDonutStyle = sprintf("background: conic-gradient(#10b981 0%% %.4f%%, #f43f5e %.4f%% %.4f%%, #f59e0b %.4f%% 100%%);", $networkOnlineEnd, $networkOnlineEnd, $networkOfflineEnd, $networkOfflineEnd);
@endphp
<section class="network-monitoring-card network-monitoring-card--reference" aria-labelledby="network-monitoring-title">
        <header class="network-monitoring-card__header">
        <div class="network-monitoring-card__header-left">
            <p class="network-monitoring-card__subtitle">
                Status koneksi PPPoE wilayah secara real-time
            </p>
        </div>
        <span class="network-monitoring-card__live" aria-label="Monitoring jaringan aktif">
            <i></i>
            Network Live
        </span>
    </header>
    <div class="network-monitoring-card__body">
        <div class="network-monitoring-card__donut-area">
            <span class="network-monitoring-card__signal-ring" aria-hidden="true"></span>
            <span class="network-monitoring-card__pulse-ring" aria-hidden="true"></span>
            <span class="network-monitoring-card__pulse-ring network-monitoring-card__pulse-ring--two" aria-hidden="true"></span>
            <span class="network-monitoring-card__pulse-ring network-monitoring-card__pulse-ring--three" aria-hidden="true"></span>
            <div class="network-monitoring-card__donut" style="{{ $networkDonutStyle }}" role="img" aria-label="{{ number_format($networkOnlinePercent, 1, ",", ".") }} persen pelanggan online">
                <div class="network-monitoring-card__donut-center">
                    <span class="network-monitoring-card__wifi" aria-hidden="true">◉</span>
                    <strong>{{ number_format($networkTotal, 0, ",", ".") }}</strong>
                    <span class="network-monitoring-card__donut-label">Total Pelanggan</span>
                    <span class="network-monitoring-card__health {{ $networkHealthClass }}">{{ $networkHealthLabel }}</span>
                </div>
            </div>
        </div>
        <div class="network-monitoring-card__status-area" aria-label="Rincian status pelanggan">
            <p class="network-monitoring-card__status-heading">Status pelanggan</p>
            <button type="button" class="network-monitoring-card__status network-monitoring-card__status--online" data-customer-status="online" aria-label="Lihat daftar pelanggan online">
                <span class="network-monitoring-card__status-left"><span class="network-monitoring-card__status-icon" aria-hidden="true">●</span><span class="network-monitoring-card__status-text"><strong>Online</strong><small>Terhubung ke MikroTik</small></span></span>
                <span class="network-monitoring-card__status-right"><strong>{{ number_format($networkOnline, 0, ",", ".") }}</strong><span class="network-monitoring-card__view-text">Lihat Online &gt;</span></span>
            </button>
            <button type="button" class="network-monitoring-card__status network-monitoring-card__status--offline" data-customer-status="offline" aria-label="Lihat daftar pelanggan offline">
                <span class="network-monitoring-card__status-left"><span class="network-monitoring-card__status-icon" aria-hidden="true">●</span><span class="network-monitoring-card__status-text"><strong>Offline</strong><small>Tidak terhubung</small></span></span>
                <span class="network-monitoring-card__status-right"><strong>{{ number_format($networkOffline, 0, ",", ".") }}</strong><span class="network-monitoring-card__view-text">Lihat Offline &gt;</span></span>
            </button>
            <button type="button" class="network-monitoring-card__status network-monitoring-card__status--isolated" data-customer-status="isolated" aria-label="Lihat daftar pelanggan terisolir">
                <span class="network-monitoring-card__status-left"><span class="network-monitoring-card__status-icon" aria-hidden="true">!</span><span class="network-monitoring-card__status-text"><strong>Terisolir</strong><small>Akses dibatasi billing</small></span></span>
                <span class="network-monitoring-card__status-right"><strong>{{ number_format($networkIsolated, 0, ",", ".") }}</strong><span class="network-monitoring-card__view-text">Lihat Isolir &gt;</span></span>
            </button>
        </div>
    </div>
    <footer class="network-monitoring-card__footer network-monitoring-card__footer--router">
        <span class="network-monitoring-card__footer-live">
            <i></i>
            MikroTik <b>{{ $mikrotikConnected ?? 'Disconnected' }}</b>
        </span>
        <span class="network-monitoring-card__router-metric">
            CPU <b>{{ isset($mikrotikCpu) ? $mikrotikCpu . '%' : '—' }}</b>
        </span>
        <span class="network-monitoring-card__router-metric">
            Uptime <b>{{ $mikrotikUptime ?? '—' }}</b>
        </span>
    </footer>
</section>

    <section class="financial-summary-card financial-summary-card--model-a" aria-labelledby="financial-summary-title">
        <header class="financial-summary-card__header">
            <div>
                <p class="financial-summary-card__eyebrow">KEUANGAN WILAYAH</p>
                <h2 id="financial-summary-title" class="financial-summary-card__title">
                    Ringkasan Bulan Ini
                </h2>
                <p class="financial-summary-card__period">
                    Periode {{ $financialMonthLabel }}
                </p>
            </div>
            <div class="financial-summary-card__badge">
                <span class="financial-summary-card__badge-dot"></span>
                Bulan berjalan
            </div>
        </header>

        <div class="financial-summary-card__body">
            <section class="finance-profit-hero {{ $netProfit < 0 ? 'finance-profit-hero--negative' : '' }}">
                <div class="finance-profit-hero__top">
                    <span class="finance-profit-hero__label">LABA BERSIH BULAN INI</span>
                    <span class="finance-profit-hero__icon" aria-hidden="true">
                        {{ $netProfit < 0 ? '↓' : '↑' }}
                    </span>
                </div>
                <strong class="finance-profit-hero__value">
                    {{ $netProfit < 0 ? '- ' : '' }}Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
                </strong>
                <p class="finance-profit-hero__note">
                    @if ($netProfit > 0)
                        Positif dari arus kas wilayah bulan ini
                    @elseif ($netProfit < 0)
                        Pengeluaran wilayah lebih besar dari pemasukan
                    @else
                        Arus kas wilayah bulan ini masih seimbang
                    @endif
                </p>
            </section>

            <div class="finance-flow-grid">
                <article class="finance-flow finance-flow--income">
                    <div class="finance-flow__heading">
                        <span class="finance-flow__icon" aria-hidden="true">↗</span>
                        <span class="finance-flow__label">Pendapatan</span>
                    </div>
                    <strong class="finance-flow__value">
                        Rp {{ number_format($monthlyIncome, 0, ',', '.') }}
                    </strong>
                    <span class="finance-flow__detail">Pembayaran terverifikasi</span>
                    <div class="finance-flow__bar finance-flow__bar--income"
                         style="--finance-bar-width: {{ $monthlyIncome > 0 ? 100 : 0 }}%;"
                         aria-hidden="true"></div>
                </article>

                <article class="finance-flow finance-flow--expense">
                    <div class="finance-flow__heading">
                        <span class="finance-flow__icon" aria-hidden="true">↙</span>
                        <span class="finance-flow__label">Pengeluaran</span>
                    </div>
                    <strong class="finance-flow__value">
                        Rp {{ number_format($monthlyExpense, 0, ',', '.') }}
                    </strong>
                    <span class="finance-flow__detail">Pengeluaran wilayah tercatat</span>
                    @php
                        $expenseBarWidth = $monthlyIncome > 0
                            ? min(round(($monthlyExpense / $monthlyIncome) * 100, 1), 100)
                            : ($monthlyExpense > 0 ? 100 : 0);
                    @endphp
                    <div class="finance-flow__bar finance-flow__bar--expense"
                         style="--finance-bar-width: {{ $expenseBarWidth }}%;"
                         aria-hidden="true"></div>
                </article>
            </div>

            <div class="finance-support-list">
                <article class="finance-support-row finance-support-row--estimate">
                    <span class="finance-support-row__icon" aria-hidden="true">◌</span>
                    <div class="finance-support-row__content">
                        <span class="finance-support-row__label">Estimasi Pendapatan</span>
                        <span class="finance-support-row__detail">
                            Tagihan periode {{ $financialMonthLabel }}
                        </span>
                    </div>
                    <strong class="finance-support-row__value">
                        Rp {{ number_format($estimatedRevenue, 0, ',', '.') }}
                    </strong>
                </article>

                <article class="finance-support-row finance-support-row--pending">
                    <span class="finance-support-row__icon" aria-hidden="true">◷</span>
                    <div class="finance-support-row__content">
                        <span class="finance-support-row__label">Pendapatan Tertunda</span>
                        <span class="finance-support-row__detail">
                            {{ number_format($pendingInvoiceCount, 0, ',', '.') }} invoice belum dibayar
                        </span>
                    </div>
                    <strong class="finance-support-row__value">
                        Rp {{ number_format($pendingRevenue, 0, ',', '.') }}
                    </strong>
                </article>
            </div>
        </div>
    </section>
</div>

<div id="customer-status-modal"
     class="customer-status-modal"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="customer-status-modal-title">
    <div class="customer-status-modal__backdrop" data-modal-close></div>

    <section class="customer-status-modal__panel" role="document">
        <header class="customer-status-modal__header">
            <div>
                <p id="customer-status-modal-kicker" class="customer-status-modal__kicker">
                    MONITORING WILAYAH
                </p>
                <h2 id="customer-status-modal-title" class="customer-status-modal__title">
                    Pelanggan
                </h2>
                <p id="customer-status-modal-count" class="customer-status-modal__count"></p>
            </div>
            <button type="button"
                    class="customer-status-modal__close"
                    data-modal-close
                    aria-label="Tutup daftar pelanggan">
                ×
            </button>
        </header>

        <div id="customer-status-modal-list" class="customer-status-modal__list"></div>

        <footer class="customer-status-modal__footer">
            <button type="button" class="customer-status-modal__button" data-modal-close>
                Tutup
            </button>
        </footer>
    </section>
</div>

<script id="staff-dashboard-browser-time">
(function () {
    const timeElement = document.getElementById('dashboard-local-time');

    if (!timeElement) {
        return;
    }

    const browserTimeZone = Intl.DateTimeFormat()
        .resolvedOptions()
        .timeZone || '';

    const formatTime = function () {
        const now = new Date();

        const dateTime = now.toLocaleString('id-ID', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        });

        timeElement.textContent = browserTimeZone
            ? `${dateTime} · ${browserTimeZone}`
            : dateTime;
    };

    formatTime();
    window.setInterval(formatTime, 1000);
})();
</script>

<script>
(function () {
    const customerStatusLists = @json($customerStatusLists);

    const statusConfig = {
        online: {
            title: 'Pelanggan Online',
            label: 'ONLINE',
            empty: 'Tidak ada pelanggan online saat ini.',
        },
        offline: {
            title: 'Pelanggan Offline',
            label: 'OFFLINE',
            empty: 'Tidak ada pelanggan offline saat ini.',
        },
        isolated: {
            title: 'Pelanggan Terisolir',
            label: 'TERISOLIR',
            empty: 'Tidak ada pelanggan terisolir saat ini.',
        },
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
            modalList.innerHTML = `
                <div class="customer-status-modal__empty">
                    ${escapeHtml(config.empty)}
                </div>
            `;
        } else {
            modalList.innerHTML = customers.map(function (customer) {
                const code = customer.customer_code
                    ? `<span>${escapeHtml(customer.customer_code)}</span>`
                    : '';
                const username = customer.pppoe_username
                    ? `<span>${escapeHtml(customer.pppoe_username)}</span>`
                    : '';
                const router = customer.router_name
                    ? `<span>${escapeHtml(customer.router_name)}</span>`
                    : '';

                return `
                    <article class="customer-status-modal__customer">
                        <div class="customer-status-modal__avatar" aria-hidden="true">
                            ${escapeHtml((customer.name || '?').charAt(0).toUpperCase())}
                        </div>
                        <div class="customer-status-modal__customer-info">
                            <h3>${escapeHtml(customer.name || 'Pelanggan')}</h3>
                            <div class="customer-status-modal__meta">
                                ${code}${username}${router}
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

        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
    };

    statusCards.forEach(function (card) {
        card.addEventListener('click', function () {
            openCustomerModal(card.dataset.customerStatus);
        });

        card.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openCustomerModal(card.dataset.customerStatus);
            }
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
})();
</script>
@endsection
