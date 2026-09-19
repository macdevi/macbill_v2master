@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-page">
        @php
            $loginName = auth()->user()->name ?? 'Pengguna';
        @endphp

        <header class="dashboard-header">
            <div>
                <h1 class="dashboard-header__title" id="dashboard-greeting">
                    Selamat Datang, {{ $loginName }}
                </h1>

                <p class="dashboard-header__time" id="dashboard-local-time">
                    Memuat waktu lokal...
                </p>
            </div>
        </header>

        @php
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
                $networkHealthLabel = 'SEHAT';
                $networkHealthClass = 'network-monitoring-card__health--healthy';
            } elseif ($networkOnlinePercent >= 80) {
                $networkHealthLabel = 'PERHATIAN';
                $networkHealthClass = 'network-monitoring-card__health--warning';
            } else {
                $networkHealthLabel = 'BURUK';
                $networkHealthClass = 'network-monitoring-card__health--critical';
            }

            $networkDonutStyle = sprintf(
                'background: conic-gradient(#10b981 0%% %.4f%%, #f43f5e %.4f%% %.4f%%, #f59e0b %.4f%% 100%%);',
                $networkOnlineEnd,
                $networkOnlineEnd,
                $networkOfflineEnd,
                $networkOfflineEnd
            );
        @endphp

        <section class="network-monitoring-card network-monitoring-card--reference" aria-labelledby="network-monitoring-title">
                        <header class="network-monitoring-card__header">
                <div class="network-monitoring-card__header-left">
                    <p class="network-monitoring-card__subtitle">
                        Status koneksi PPPoE secara real-time
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

                    <div
                        class="network-monitoring-card__donut"
                        style="{{ $networkDonutStyle }}"
                        role="img"
                        aria-label="{{ number_format($networkOnlinePercent, 1, ',', '.') }} persen pelanggan online">
                        <div class="network-monitoring-card__donut-center">
                            <span class="network-monitoring-card__wifi" aria-hidden="true">◉</span>

                            <strong>{{ number_format($networkTotal, 0, ',', '.') }}</strong>

                            <span class="network-monitoring-card__donut-label">
                                Total Pelanggan
                            </span>

                            <span class="network-monitoring-card__health {{ $networkHealthClass }}">
                                {{ $networkHealthLabel }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="network-monitoring-card__status-area" aria-label="Rincian status pelanggan">
                    <p class="network-monitoring-card__status-heading">Status pelanggan</p>

                    <button
                        type="button"
                        class="network-monitoring-card__status network-monitoring-card__status--online"
                        data-customer-status="online"
                        aria-label="Lihat daftar pelanggan online">
                        <span class="network-monitoring-card__status-left">
                            <span class="network-monitoring-card__status-icon" aria-hidden="true">●</span>

                            <span class="network-monitoring-card__status-text">
                                <strong>Online</strong>
                                <small>Terhubung ke MikroTik</small>
                            </span>
                        </span>

                        <span class="network-monitoring-card__status-right">
                            <strong>{{ number_format($networkOnline, 0, ',', '.') }}</strong>
                            <span class="network-monitoring-card__view-text">Lihat Online &gt;</span>
                        </span>
                    </button>

                    <button
                        type="button"
                        class="network-monitoring-card__status network-monitoring-card__status--offline"
                        data-customer-status="offline"
                        aria-label="Lihat daftar pelanggan offline">
                        <span class="network-monitoring-card__status-left">
                            <span class="network-monitoring-card__status-icon" aria-hidden="true">●</span>

                            <span class="network-monitoring-card__status-text">
                                <strong>Offline</strong>
                                <small>Tidak terhubung</small>
                            </span>
                        </span>

                        <span class="network-monitoring-card__status-right">
                            <strong>{{ number_format($networkOffline, 0, ',', '.') }}</strong>
                            <span class="network-monitoring-card__view-text">Lihat Offline &gt;</span>
                        </span>
                    </button>

                    <button
                        type="button"
                        class="network-monitoring-card__status network-monitoring-card__status--isolated"
                        data-customer-status="isolated"
                        aria-label="Lihat daftar pelanggan terisolir">
                        <span class="network-monitoring-card__status-left">
                            <span class="network-monitoring-card__status-icon" aria-hidden="true">!</span>

                            <span class="network-monitoring-card__status-text">
                                <strong>Terisolir</strong>
                                <small>Akses dibatasi billing</small>
                            </span>
                        </span>

                        <span class="network-monitoring-card__status-right">
                            <strong>{{ number_format($networkIsolated, 0, ',', '.') }}</strong>
                            <span class="network-monitoring-card__view-text">Lihat Isolir &gt;</span>
                        </span>
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
                    <p class="financial-summary-card__eyebrow">KEUANGAN</p>
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
                            Positif dari arus kas bulan ini
                        @elseif ($netProfit < 0)
                            Pengeluaran lebih besar dari pemasukan bulan ini
                        @else
                            Arus kas bulan ini masih seimbang
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

                        <span class="finance-flow__detail">
                            Pembayaran terverifikasi
                        </span>

                        <div
                            class="finance-flow__bar finance-flow__bar--income"
                            style="--finance-bar-width: {{ $monthlyIncome > 0 ? 100 : 0 }}%;"
                            aria-hidden="true"
                        ></div>
                    </article>

                    <article class="finance-flow finance-flow--expense">
                        <div class="finance-flow__heading">
                            <span class="finance-flow__icon" aria-hidden="true">↙</span>
                            <span class="finance-flow__label">Pengeluaran</span>
                        </div>

                        <strong class="finance-flow__value">
                            Rp {{ number_format($monthlyExpense, 0, ',', '.') }}
                        </strong>

                        <span class="finance-flow__detail">
                            Pengeluaran tercatat
                        </span>

                        @php
                            $expenseBarWidth = $monthlyIncome > 0
                                ? min(round(($monthlyExpense / $monthlyIncome) * 100, 1), 100)
                                : ($monthlyExpense > 0 ? 100 : 0);
                        @endphp

                        <div
                            class="finance-flow__bar finance-flow__bar--expense"
                            style="--finance-bar-width: {{ $expenseBarWidth }}%;"
                            aria-hidden="true"
                        ></div>
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
                                {{ number_format($pendingInvoiceCount, 0, ',', '.') }}
                                invoice belum dibayar
                            </span>
                        </div>

                        <strong class="finance-support-row__value">
                            Rp {{ number_format($pendingRevenue, 0, ',', '.') }}
                        </strong>
                    </article>
                </div>
            </div>
        </section>

        <section class="financial-summary-card financial-summary-card--activity" aria-labelledby="finance-activity-title">
            <header class="financial-summary-card__header">
                <div>
                    <p class="financial-summary-card__eyebrow">KEUANGAN</p>
                    <h2 id="finance-activity-title" class="financial-summary-card__title">
                        Riwayat Aktivitas Terbaru
                    </h2>
                    <p class="financial-summary-card__period">
                        5 transaksi terakhir
                    </p>
                </div>
                <a href="{{ route('finance.activity') }}" class="financial-summary-card__badge" style="text-decoration:none;">
                    <span class="financial-summary-card__badge-dot"></span>
                    Lihat Semua
                </a>
            </header>
            <div class="financial-summary-card__body">
                <div class="finance-support-list">
                    @forelse ($recentFinanceActivity as $activity)
                        <article class="finance-support-row {{ $activity->activity_type === 'income' ? 'finance-support-row--estimate' : 'finance-support-row--pending' }}">
                            <span class="finance-support-row__icon" aria-hidden="true">
                                {{ $activity->activity_type === 'income' ? '+' : '-' }}
                            </span>
                            <div class="finance-support-row__content">
                                <span class="finance-support-row__label">{{ $activity->activity_title }}</span>
                                <span class="finance-support-row__detail">
                                    {{ \Carbon\Carbon::parse($activity->activity_date)->translatedFormat('d M Y, H:i') }}
                                    @if ($activity->activity_reference)
                                        &middot; {{ $activity->activity_reference }}
                                    @endif
                                </span>
                            </div>
                            <strong class="finance-support-row__value">
                                {{ $activity->activity_type === 'income' ? '+ ' : '- ' }}Rp {{ number_format($activity->activity_amount, 0, ',', '.') }}
                            </strong>
                        </article>
                    @empty
                        <p style="padding: 1rem; color:#94a3b8; text-align:center;">Belum ada aktivitas terbaru.</p>
                    @endforelse
                </div>
            </div>
        </section>

    </div>



        @if (auth()->user()?->isSuperAdmin() && $areaFinancialSummaries->isNotEmpty())
            <section class="financial-summary-card financial-summary-card--area" aria-labelledby="area-financial-summary-title">
                <header class="financial-summary-card__header">
                    <div>
                        <p class="financial-summary-card__eyebrow">SUPER ADMIN</p>
                        <h2 id="area-financial-summary-title" class="financial-summary-card__title">
                            Rincian Keuangan per Wilayah
                        </h2>
                        <p class="financial-summary-card__period">
                            Periode {{ $financialMonthLabel }}
                        </p>
                    </div>

                    <div class="financial-summary-card__badge">
                        <span class="financial-summary-card__badge-dot"></span>
                        Per wilayah
                    </div>
                </header>

                <div class="financial-summary-card__body">
                    <p class="area-financial-table__scroll-hint" aria-hidden="true">Geser tabel untuk melihat seluruh metrik →</p>
                    <div class="area-financial-table-wrapper">
                        <table class="area-financial-table">
                            <thead>
                                <tr>
                                    <th scope="col">Wilayah</th>
                                    <th scope="col" class="area-financial-table__number">Pelanggan Aktif</th>
                                    <th scope="col" class="area-financial-table__number">Pembayaran Diterima</th>
                                    <th scope="col" class="area-financial-table__number">Pengeluaran</th>
                                    <th scope="col" class="area-financial-table__number">Laba Bersih Kas</th>
                                    <th scope="col" class="area-financial-table__number">Piutang Aktif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($areaFinancialSummaries as $areaSummary)
                                    <tr>
                                        <td>
                                            <strong>{{ $areaSummary['name'] }}</strong>
                                            @if (!empty($areaSummary['code']))
                                                <span class="area-financial-table__code">{{ $areaSummary['code'] }}</span>
                                            @endif
                                        </td>
                                        <td class="area-financial-table__number">
                                            {{ number_format($areaSummary['customer_count'], 0, ',', '.') }}
                                        </td>
                                        <td class="area-financial-table__number area-financial-table__income">
                                            Rp {{ number_format($areaSummary['income'], 0, ',', '.') }}
                                        </td>
                                        <td class="area-financial-table__number area-financial-table__expense">
                                            Rp {{ number_format($areaSummary['expense'], 0, ',', '.') }}
                                        </td>
                                        <td class="area-financial-table__number {{ $areaSummary['net_profit'] < 0 ? 'area-financial-table__negative' : 'area-financial-table__profit' }}">
                                            {{ $areaSummary['net_profit'] < 0 ? '- ' : '' }}Rp {{ number_format(abs($areaSummary['net_profit']), 0, ',', '.') }}
                                        </td>
                                        <td class="area-financial-table__number area-financial-table__pending">
                                            Rp {{ number_format($areaSummary['pending_revenue'], 0, ',', '.') }}
                                            @if ($areaSummary['pending_invoice_count'] > 0)
                                                <span class="area-financial-table__invoice-count">
                                                    {{ number_format($areaSummary['pending_invoice_count'], 0, ',', '.') }} invoice
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        @endif

    {{-- Modal daftar pelanggan berdasarkan status koneksi --}}
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
                    <p id="customer-status-modal-kicker" class="customer-status-modal__kicker">
                        MONITORING JARINGAN
                    </p>
                    <h2 id="customer-status-modal-title" class="customer-status-modal__title">
                        Pelanggan
                    </h2>
                    <p id="customer-status-modal-count" class="customer-status-modal__count"></p>
                </div>

                <button
                    type="button"
                    class="customer-status-modal__close"
                    data-modal-close
                    aria-label="Tutup daftar pelanggan"
                >
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
                            : '<span>Username PPPoE belum diisi</span>';

                        const router = customer.router_name
                            ? `<span>Router: ${escapeHtml(customer.router_name)}</span>`
                            : '<span>Router belum ditentukan</span>';

                        const phone = customer.phone
                            ? `<span>${escapeHtml(customer.phone)}</span>`
                            : '';

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
@endsection
