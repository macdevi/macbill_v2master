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

        <section class="customer-summary-card" aria-label="Kondisi pelanggan">
            

            <div class="customer-summary-card__heading">
                <div class="customer-summary-card__heading-main">
                    <p class="customer-summary-card__eyebrow">MONITORING JARINGAN</p>

                    <span class="customer-summary-card__live">
                        <i></i>
                        Real-time
                    </span>
                </div>

                <div class="customer-summary-card__mobile-total">
                    <span>Total Pelanggan</span>
                    <strong>{{ number_format($totalCustomers, 0, ',', '.') }}</strong>
                </div>
            </div>

            <div class="customer-summary-card__metrics">
                <article class="customer-metric customer-metric--total">
                    <span class="customer-metric__label">Total Pelanggan</span>
                    <strong class="customer-metric__value">
                        {{ number_format($totalCustomers, 0, ',', '.') }}
                    </strong>
                    <span class="customer-metric__detail">Terdaftar di billing</span>
                </article>

                <article
                    class="customer-metric customer-metric--online customer-metric--clickable"
                    data-customer-status="online"
                    role="button"
                    tabindex="0"
                    aria-label="Lihat daftar pelanggan online">
                    <span class="customer-metric__label">
                        <i class="customer-metric__dot"></i>
                        Online
                    </span>
                    <strong class="customer-metric__value">
                        {{ number_format($onlineCustomers, 0, ',', '.') }}
                    </strong>
                    <span class="customer-metric__detail">
                        {{ number_format($onlinePercentage, 1, ',', '.') }}% pelanggan
                    </span>
                    <span class="customer-metric__action">Lihat pelanggan →</span>
                </article>

                <article
                    class="customer-metric customer-metric--offline customer-metric--clickable"
                    data-customer-status="offline"
                    role="button"
                    tabindex="0"
                    aria-label="Lihat daftar pelanggan offline">
                    <span class="customer-metric__label">
                        <i class="customer-metric__dot"></i>
                        Offline
                    </span>
                    <strong class="customer-metric__value">
                        {{ number_format($offlineCustomers, 0, ',', '.') }}
                    </strong>
                    <span class="customer-metric__detail">
                        {{ number_format($offlinePercentage, 1, ',', '.') }}% pelanggan
                    </span>
                    <span class="customer-metric__action">Lihat pelanggan →</span>
                </article>

                <article
                    class="customer-metric customer-metric--isolated customer-metric--clickable"
                    data-customer-status="isolated"
                    role="button"
                    tabindex="0"
                    aria-label="Lihat daftar pelanggan terisolir">
                    <span class="customer-metric__label">
                        <i class="customer-metric__dot"></i>
                        Terisolir
                    </span>
                    <strong class="customer-metric__value">
                        {{ number_format($isolatedCustomers, 0, ',', '.') }}
                    </strong>
                    <span class="customer-metric__detail">Akses dibatasi</span>
                    <span class="customer-metric__action">Lihat pelanggan →</span>
                </article>
            </div>
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

    </div>


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
