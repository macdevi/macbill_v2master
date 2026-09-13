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

    <style>
        .dashboard-page {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
        }

        .dashboard-header {
            margin-bottom: 1.5rem;
        }

        .dashboard-header__title {
            margin: 0;
            color: #0f172a;
            font-size: clamp(1.55rem, 2.2vw, 2.12rem);
            font-weight: 900;
            letter-spacing: -0.045em;
            line-height: 1.15;
        }

        .dashboard-header__time {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin: 0.52rem 0 0;
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1.4;
        }

        .dashboard-header__time span {
            color: #cbd5e1;
        }

        .customer-summary-card {
            position: relative;
            overflow: hidden;
            border: 1px solid #dbe5f2;
            border-radius: 22px;
            background:
                radial-gradient(circle at 100% 0%, rgba(59, 130, 246, 0.11), transparent 32%),
                linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
            box-shadow:
                0 18px 46px rgba(15, 23, 42, 0.09),
                0 2px 7px rgba(15, 23, 42, 0.04);
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .customer-summary-card::before {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #2563eb 0%, #38bdf8 48%, #22c55e 100%);
            content: "";
        }

        .customer-summary-card:hover {
            transform: translateY(-2px);
            box-shadow:
                0 23px 52px rgba(15, 23, 42, 0.12),
                0 4px 12px rgba(37, 99, 235, 0.08);
        }

        .customer-summary-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            padding: 1.65rem 1.7rem 1.35rem;
        }

        .customer-summary-card__title {
            display: flex;
            align-items: center;
            gap: 0.62rem;
            margin: 0;
            color: #0f172a;
            font-size: 1.22rem;
            font-weight: 900;
            letter-spacing: -0.028em;
        }

        .customer-summary-card__title::before {
            display: block;
            width: 10px;
            height: 10px;
            flex: 0 0 auto;
            border-radius: 50%;
            background: #2563eb;
            box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.12);
            content: "";
        }

        .customer-summary-card__description {
            display: none;
        }

        .customer-summary-card__total {
            position: relative;
            min-width: 142px;
            overflow: hidden;
            padding: 0.82rem 1rem;
            border: 1px solid rgba(147, 197, 253, 0.62);
            border-radius: 15px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            text-align: right;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
        }

        .customer-summary-card__total::after {
            position: absolute;
            right: -17px;
            bottom: -24px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.1);
            content: "";
        }

        .customer-summary-card__total span {
            position: relative;
            z-index: 1;
            display: block;
            color: #3b5c91;
            font-size: 0.67rem;
            font-weight: 850;
            letter-spacing: 0.02em;
        }

        .customer-summary-card__total strong {
            position: relative;
            z-index: 1;
            display: block;
            margin-top: 0.28rem;
            color: #1d4ed8;
            font-size: 1.85rem;
            font-weight: 950;
            letter-spacing: -0.06em;
            line-height: 1;
        }

        .customer-summary-card__divider {
            height: 1px;
            margin: 0 1.7rem;
            background: linear-gradient(90deg, transparent, #dbe5f2 10%, #dbe5f2 90%, transparent);
        }

        .customer-summary-card__stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            padding: 1.3rem 1.7rem 1.15rem;
        }

        .customer-status {
            position: relative;
            overflow: hidden;
            min-height: 145px;
            padding: 1rem 1.1rem;
            border: 1px solid transparent;
            border-radius: 16px;
            transition: transform 170ms ease, box-shadow 170ms ease;
        }

        .customer-status:hover {
            transform: translateY(-2px);
        }

        .customer-status::after {
            position: absolute;
            right: -20px;
            bottom: -35px;
            width: 105px;
            height: 105px;
            border-radius: 50%;
            content: "";
        }

        .customer-status--online {
            border-color: #bbf7d0;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            box-shadow: 0 7px 18px rgba(22, 163, 74, 0.07);
        }

        .customer-status--online::after {
            background: rgba(34, 197, 94, 0.1);
        }

        .customer-status--offline {
            border-color: #fecaca;
            background: linear-gradient(135deg, #fff7f7 0%, #fff1f2 100%);
            box-shadow: 0 7px 18px rgba(239, 68, 68, 0.065);
        }

        .customer-status--offline::after {
            background: rgba(239, 68, 68, 0.09);
        }

        .customer-status__header,
        .customer-status__value,
        .customer-status__caption,
        .customer-status__isolated {
            position: relative;
            z-index: 1;
        }

        .customer-status__header {
            display: flex;
            align-items: center;
            gap: 0.52rem;
        }

        .customer-status__indicator {
            width: 9px;
            height: 9px;
            flex: 0 0 auto;
            border-radius: 999px;
        }

        .customer-status__indicator--online {
            background: #22c55e;
            box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.15);
        }

        .customer-status__indicator--offline {
            background: #ef4444;
            box-shadow: 0 0 0 5px rgba(239, 68, 68, 0.13);
        }

        .customer-status__label {
            color: #475569;
            font-size: 0.77rem;
            font-weight: 900;
            letter-spacing: 0.01em;
        }

        .customer-status__value {
            display: block;
            margin-top: 0.85rem;
            color: #0f172a;
            font-size: clamp(2.15rem, 4vw, 2.8rem);
            font-weight: 950;
            letter-spacing: -0.07em;
            line-height: 0.92;
        }

        .customer-status__caption {
            display: block;
            margin-top: 0.65rem;
            color: #64748b;
            font-size: 0.71rem;
            font-weight: 750;
            line-height: 1.35;
        }

        .customer-status__isolated {
            display: inline-flex;
            align-items: center;
            margin-top: 0.6rem;
            padding: 0.32rem 0.56rem;
            border: 1px solid #fed7aa;
            border-radius: 999px;
            background: #fff7ed;
            color: #c2410c;
            font-size: 0.66rem;
            font-weight: 850;
            line-height: 1;
            box-shadow: 0 1px 1px rgba(194, 65, 12, 0.05);
        }

        .customer-summary-card__progress-wrap {
            padding: 0.15rem 1.7rem 1.55rem;
        }

        .customer-summary-card__progress {
            display: flex;
            width: 100%;
            height: 14px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 999px;
            background: #e5e7eb;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .customer-summary-card__progress-online {
            display: block;
            min-width: 0;
            height: 100%;
            background: linear-gradient(90deg, #16a34a 0%, #4ade80 100%);
            box-shadow: inset -1px 0 0 rgba(255, 255, 255, 0.4);
            transition: width 300ms ease;
        }

        .customer-summary-card__progress-offline {
            display: block;
            min-width: 0;
            height: 100%;
            background: linear-gradient(90deg, #fb7185 0%, #ef4444 100%);
            transition: width 300ms ease;
        }

        .dark .dashboard-header__title {
            color: #f8fafc;
        }

        .dark .dashboard-header__time {
            color: #94a3b8;
        }

        .dark .dashboard-header__time span {
            color: #475569;
        }

        .dark .customer-summary-card {
            border-color: rgba(71, 85, 105, 0.72);
            background:
                radial-gradient(circle at 100% 0%, rgba(59, 130, 246, 0.16), transparent 33%),
                linear-gradient(145deg, #172033 0%, #111827 100%);
            box-shadow: 0 18px 46px rgba(0, 0, 0, 0.24);
        }

        .dark .customer-summary-card__title,
        .dark .customer-status__value {
            color: #f8fafc;
        }

        .dark .customer-summary-card__total {
            border-color: rgba(96, 165, 250, 0.25);
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.35), rgba(30, 58, 138, 0.26));
        }

        .dark .customer-summary-card__total span {
            color: #bfdbfe;
        }

        .dark .customer-summary-card__total strong {
            color: #93c5fd;
        }

        .dark .customer-summary-card__divider {
            background: linear-gradient(90deg, transparent, #334155 10%, #334155 90%, transparent);
        }

        .dark .customer-status--online {
            border-color: rgba(34, 197, 94, 0.24);
            background: linear-gradient(135deg, rgba(20, 83, 45, 0.42), rgba(6, 78, 59, 0.24));
        }

        .dark .customer-status--offline {
            border-color: rgba(248, 113, 113, 0.24);
            background: linear-gradient(135deg, rgba(127, 29, 29, 0.32), rgba(136, 19, 55, 0.2));
        }

        .dark .customer-status__label {
            color: #cbd5e1;
        }

        .dark .customer-status__caption {
            color: #94a3b8;
        }

        .dark .customer-status__isolated {
            border-color: rgba(251, 146, 60, 0.27);
            background: rgba(154, 52, 18, 0.24);
            color: #fdba74;
        }

        .dark .customer-summary-card__progress {
            border-color: rgba(71, 85, 105, 0.65);
            background: #334155;
        }

        @media (max-width: 720px) {
            .dashboard-header {
                margin-bottom: 1.15rem;
            }

            .customer-summary-card {
                border-radius: 17px;
            }

            .customer-summary-card__top {
                align-items: flex-start;
                flex-direction: column;
                gap: 1rem;
                padding: 1.25rem 1.12rem 1.05rem;
            }

            .customer-summary-card__total {
                width: 100%;
                text-align: left;
            }

            .customer-summary-card__divider {
                margin: 0 1.12rem;
            }

            .customer-summary-card__stats {
                grid-template-columns: 1fr;
                gap: 0.75rem;
                padding: 1rem 1.12rem;
            }

            .customer-status {
                min-height: 130px;
                padding: 0.92rem 1rem;
            }

            .customer-summary-card__progress-wrap {
                padding: 0.1rem 1.12rem 1.2rem;
            }
        }

        /* Concept D — empat metrik dengan aksen vertikal */
        .customer-summary-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.07);
        }

        .customer-summary-card::before,
        .customer-summary-card::after {
            display: none;
        }

        .customer-summary-card__accent {
            grid-row: 1 / span 2;
            width: 7px;
            background: linear-gradient(180deg, #2563eb 0%, #38bdf8 48%, #22c55e 100%);
        }

        .customer-summary-card__heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.35rem 1.45rem 1rem;
        }

        .customer-summary-card__eyebrow {
            margin: 0 0 0.28rem;
            color: #64748b;
            font-size: 0.65rem;
            font-weight: 850;
            letter-spacing: 0.12em;
        }

        .customer-summary-card__title {
            margin: 0;
            color: #0f172a;
            font-size: 1.15rem;
            font-weight: 900;
            letter-spacing: -0.03em;
        }

        .customer-summary-card__title::before {
            display: none;
        }

        .customer-summary-card__live {
            display: inline-flex;
            align-items: center;
            gap: 0.42rem;
            padding: 0.42rem 0.62rem;
            border: 1px solid #bbf7d0;
            border-radius: 999px;
            background: #f0fdf4;
            color: #15803d;
            font-size: 0.67rem;
            font-weight: 850;
        }

        .customer-summary-card__live i {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
        }

        .customer-summary-card__metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            border-top: 1px solid #eef2f7;
        }

        .customer-metric {
            position: relative;
            min-width: 0;
            padding: 1.1rem 1.45rem 1.25rem;
        }

        .customer-metric + .customer-metric {
            border-left: 1px solid #eef2f7;
        }

        .customer-metric__label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .customer-metric__dot {
            width: 8px;
            height: 8px;
            flex: 0 0 auto;
            border-radius: 50%;
            background: currentColor;
        }

        .customer-metric__value {
            display: block;
            margin-top: 0.58rem;
            color: #0f172a;
            font-size: clamp(1.8rem, 3.1vw, 2.45rem);
            font-weight: 950;
            letter-spacing: -0.065em;
            line-height: 1;
        }

        .customer-metric__detail {
            display: block;
            margin-top: 0.48rem;
            color: #94a3b8;
            font-size: 0.67rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .customer-metric--total {
            background: linear-gradient(180deg, rgba(239, 246, 255, 0.78), rgba(255, 255, 255, 0));
        }

        .customer-metric--total .customer-metric__value {
            color: #2563eb;
        }

        .customer-metric--online .customer-metric__label {
            color: #16a34a;
        }

        .customer-metric--online .customer-metric__value {
            color: #15803d;
        }

        .customer-metric--offline .customer-metric__label {
            color: #dc2626;
        }

        .customer-metric--offline .customer-metric__value {
            color: #b91c1c;
        }

        .customer-metric--isolated .customer-metric__label {
            color: #d97706;
        }

        .customer-metric--isolated .customer-metric__value {
            color: #b45309;
        }

        .dark .customer-summary-card {
            border-color: #334155;
            background: #111827;
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.24);
        }

        .dark .customer-summary-card__heading,
        .dark .customer-metric__value {
            color: #f8fafc;
        }

        .dark .customer-summary-card__eyebrow,
        .dark .customer-metric__detail {
            color: #94a3b8;
        }

        .dark .customer-summary-card__title {
            color: #f8fafc;
        }

        .dark .customer-summary-card__metrics,
        .dark .customer-metric + .customer-metric {
            border-color: #334155;
        }

        .dark .customer-metric--total {
            background: linear-gradient(180deg, rgba(30, 64, 175, 0.2), rgba(17, 24, 39, 0));
        }

        .dark .customer-metric--total .customer-metric__value {
            color: #93c5fd;
        }

        .dark .customer-metric--online .customer-metric__value {
            color: #86efac;
        }

        .dark .customer-metric--offline .customer-metric__value {
            color: #fca5a5;
        }

        .dark .customer-metric--isolated .customer-metric__value {
            color: #fcd34d;
        }

        @media (max-width: 920px) {
            .customer-summary-card__metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .customer-metric:nth-child(3) {
                border-left: 0;
                border-top: 1px solid #eef2f7;
            }

            .customer-metric:nth-child(4) {
                border-top: 1px solid #eef2f7;
            }

            .dark .customer-metric:nth-child(3),
            .dark .customer-metric:nth-child(4) {
                border-top-color: #334155;
            }
        }

        @media (max-width: 560px) {
            .customer-summary-card {
                grid-template-columns: 5px minmax(0, 1fr);
                border-radius: 15px;
            }

            .customer-summary-card__accent {
                width: 5px;
            }

            .customer-summary-card__heading {
                align-items: flex-start;
                flex-direction: column;
                padding: 1.1rem 1rem 0.9rem;
            }

            .customer-summary-card__metrics {
                grid-template-columns: 1fr;
            }

            .customer-metric {
                padding: 0.9rem 1rem;
            }

            .customer-metric + .customer-metric,
            .customer-metric:nth-child(3),
            .customer-metric:nth-child(4) {
                border-top: 1px solid #eef2f7;
                border-left: 0;
            }

            .dark .customer-metric + .customer-metric,
            .dark .customer-metric:nth-child(3),
            .dark .customer-metric:nth-child(4) {
                border-top-color: #334155;
            }
        }


        /* Mobile: empat metrik tetap horizontal dan dapat digeser bila layar sempit */
        @media (max-width: 920px) {
            .customer-summary-card__metrics {
                display: flex;
                overflow-x: auto;
                grid-template-columns: none;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 transparent;
            }

            .customer-summary-card__metrics::-webkit-scrollbar {
                height: 5px;
            }

            .customer-summary-card__metrics::-webkit-scrollbar-thumb {
                border-radius: 999px;
                background: #cbd5e1;
            }

            .customer-metric {
                min-width: 145px;
                flex: 1 0 145px;
            }

            .customer-metric + .customer-metric,
            .customer-metric:nth-child(3),
            .customer-metric:nth-child(4) {
                border-top: 0;
                border-left: 1px solid #eef2f7;
            }

            .dark .customer-metric + .customer-metric,
            .dark .customer-metric:nth-child(3),
            .dark .customer-metric:nth-child(4) {
                border-top-color: transparent;
                border-left-color: #334155;
            }
        }

        @media (max-width: 560px) {
            .customer-summary-card__metrics {
                grid-template-columns: none;
            }

            .customer-metric {
                min-width: 132px;
                flex-basis: 132px;
                padding: 0.9rem 0.85rem 1rem;
            }

            .customer-metric__label {
                font-size: 0.66rem;
            }

            .customer-metric__value {
                font-size: 1.95rem;
            }

            .customer-metric__detail {
                font-size: 0.61rem;
            }

            .customer-metric + .customer-metric,
            .customer-metric:nth-child(3),
            .customer-metric:nth-child(4) {
                border-top: 0;
                border-left: 1px solid #eef2f7;
            }

            .dark .customer-metric + .customer-metric,
            .dark .customer-metric:nth-child(3),
            .dark .customer-metric:nth-child(4) {
                border-top-color: transparent;
                border-left-color: #334155;
            }
        }


        /* Total pelanggan di kanan header, khusus tampilan mobile */
        .customer-summary-card__heading-main {
            min-width: 0;
        }

        .customer-summary-card__mobile-total {
            display: none;
        }

        @media (max-width: 560px) {
            .customer-summary-card__heading {
                align-items: flex-start;
                flex-direction: row;
                justify-content: space-between;
                gap: 0.75rem;
            }

            .customer-summary-card__live {
                margin-top: 0.65rem;
            }

            .customer-summary-card__mobile-total {
                display: flex;
                min-width: 96px;
                min-height: 56px;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 0.45rem 0.65rem;
                border: 1px solid #bfdbfe;
                border-radius: 11px;
                background: #eff6ff;
                text-align: center;
            }

            .customer-summary-card__mobile-total span {
                display: block;
                color: #3b5c91;
                font-size: 0.58rem;
                font-weight: 850;
                line-height: 1.2;
                white-space: nowrap;
            }

            .customer-summary-card__mobile-total strong {
                display: block;
                margin-top: 0.18rem;
                color: #1d4ed8;
                font-size: 1.45rem;
                font-weight: 950;
                letter-spacing: -0.06em;
                line-height: 1;
            }

            .customer-metric--total {
                display: none;
            }

            .customer-summary-card__metrics {
                grid-template-columns: none;
            }

            .dark .customer-summary-card__mobile-total {
                border-color: rgba(96, 165, 250, 0.28);
                background: rgba(30, 64, 175, 0.28);
            }

            .dark .customer-summary-card__mobile-total span {
                color: #bfdbfe;
            }

            .dark .customer-summary-card__mobile-total strong {
                color: #93c5fd;
            }
        }




        /* Border hijau halus: aman, tanpa pseudo-element dan tanpa mask */
        .customer-summary-card {
            border: 2px solid transparent;
            background:
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(
                    90deg,
                    #16a34a 0%,
                    #86efac 20%,
                    #dcfce7 40%,
                    #22c55e 55%,
                    #15803d 75%,
                    #86efac 100%
                ) border-box;
            background-size: 100% 100%, 300% 100%;
            background-position: 0 0, 0 0;
            animation: customer-card-green-flow 5s linear infinite;
        }

        @keyframes customer-card-green-flow {
            from {
                background-position: 0 0, 0 0;
            }
            to {
                background-position: 0 0, 300% 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .customer-summary-card {
                animation: none;
            }
        }



        /* customer-card-layout-repair:
           Hapus sisa layout aksen kiri agar isi card kembali sejajar */
        .customer-summary-card {
            grid-template-columns: minmax(0, 1fr) !important;
        }

        .customer-summary-card__accent {
            display: none !important;
        }

        /* Header desktop/tablet: konten kiri dan status tetap rapi */
        .customer-summary-card__heading {
            grid-column: 1 !important;
        }

        .customer-summary-card__metrics {
            grid-column: 1 !important;
        }

        /* Mobile: judul/status di kiri, total pelanggan di kanan */
        @media (max-width: 560px) {
            .customer-summary-card {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .customer-summary-card__heading {
                display: flex;
                flex-direction: row;
                align-items: flex-start;
                justify-content: space-between;
                gap: 0.75rem;
                padding: 1.1rem 1rem 0.9rem;
            }

            .customer-summary-card__heading-main {
                flex: 1 1 auto;
                min-width: 0;
            }

            .customer-summary-card__mobile-total {
                flex: 0 0 96px;
                min-width: 96px;
                margin-left: auto;
            }

            .customer-summary-card__metrics {
                display: flex;
                grid-template-columns: none;
                overflow-x: auto;
            }
        }


        /* Card status dapat diklik untuk membuka daftar pelanggan */
        .customer-metric--clickable {
            position: relative;
            cursor: pointer;
            transition: background-color 180ms ease, transform 180ms ease;
            -webkit-tap-highlight-color: transparent;
        }

        .customer-metric--clickable:hover {
            background-color: rgba(248, 250, 252, 0.92);
        }

        .customer-metric--clickable:focus-visible {
            z-index: 1;
            outline: 3px solid rgba(34, 197, 94, 0.4);
            outline-offset: -3px;
        }

        .customer-metric--clickable:active {
            transform: scale(0.985);
        }

        .customer-metric__action {
            display: block;
            margin-top: 0.55rem;
            color: #2563eb;
            font-size: 0.64rem;
            font-weight: 850;
            letter-spacing: 0.01em;
        }

        .customer-metric--online .customer-metric__action {
            color: #15803d;
        }

        .customer-metric--offline .customer-metric__action {
            color: #b91c1c;
        }

        .customer-metric--isolated .customer-metric__action {
            color: #b45309;
        }

        .dark .customer-metric--clickable:hover {
            background-color: rgba(30, 41, 59, 0.72);
        }

        /* Modal pelanggan */
        .customer-status-modal {
            position: fixed;
            z-index: 9999;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            visibility: hidden;
            opacity: 0;
            transition: opacity 180ms ease, visibility 180ms ease;
        }

        .customer-status-modal.is-open {
            visibility: visible;
            opacity: 1;
        }

        .customer-status-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.58);
            backdrop-filter: blur(3px);
        }

        .customer-status-modal__panel {
            position: relative;
            z-index: 1;
            display: flex;
            width: min(100%, 620px);
            max-height: min(80vh, 680px);
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.34);
            transform: translateY(10px) scale(0.98);
            transition: transform 180ms ease;
        }

        .customer-status-modal.is-open .customer-status-modal__panel {
            transform: translateY(0) scale(1);
        }

        .customer-status-modal__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.2rem 1.25rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .customer-status-modal__kicker {
            margin: 0 0 0.28rem;
            color: #16a34a;
            font-size: 0.66rem;
            font-weight: 900;
            letter-spacing: 0.11em;
        }

        .customer-status-modal__title {
            margin: 0;
            color: #0f172a;
            font-size: 1.2rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .customer-status-modal__count {
            margin: 0.35rem 0 0;
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .customer-status-modal__close {
            display: inline-flex;
            width: 2.2rem;
            height: 2.2rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 10px;
            background: #f1f5f9;
            color: #334155;
            cursor: pointer;
            font-size: 1.55rem;
            line-height: 1;
        }

        .customer-status-modal__close:hover {
            background: #e2e8f0;
        }

        .customer-status-modal__list {
            min-height: 110px;
            overflow-y: auto;
            padding: 0.75rem 1.25rem;
        }

        .customer-status-modal__customer {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            padding: 0.9rem 0;
            border-bottom: 1px solid #eef2f7;
        }

        .customer-status-modal__customer:last-child {
            border-bottom: 0;
        }

        .customer-status-modal__avatar {
            display: flex;
            width: 2.45rem;
            height: 2.45rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #15803d;
            font-size: 0.95rem;
            font-weight: 900;
        }

        .customer-status-modal__customer-info {
            min-width: 0;
        }

        .customer-status-modal__customer-info h3 {
            margin: 0;
            overflow: hidden;
            color: #0f172a;
            font-size: 0.91rem;
            font-weight: 850;
            line-height: 1.35;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .customer-status-modal__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.28rem 0.55rem;
            margin-top: 0.33rem;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 650;
            line-height: 1.45;
        }

        .customer-status-modal__meta span:not(:last-child)::after {
            margin-left: 0.55rem;
            color: #cbd5e1;
            content: "•";
        }

        .customer-status-modal__empty {
            display: grid;
            min-height: 150px;
            place-items: center;
            padding: 1rem;
            color: #64748b;
            font-size: 0.84rem;
            font-weight: 700;
            text-align: center;
        }

        .customer-status-modal__footer {
            display: flex;
            justify-content: flex-end;
            padding: 0.9rem 1.25rem;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .customer-status-modal__button {
            border: 0;
            border-radius: 9px;
            padding: 0.6rem 1rem;
            background: #16a34a;
            color: #ffffff;
            cursor: pointer;
            font-size: 0.78rem;
            font-weight: 850;
        }

        .customer-status-modal__button:hover {
            background: #15803d;
        }

        .customer-status-modal-open {
            overflow: hidden;
        }

        .dark .customer-status-modal__panel {
            border-color: #334155;
            background: #111827;
        }

        .dark .customer-status-modal__header,
        .dark .customer-status-modal__footer,
        .dark .customer-status-modal__customer {
            border-color: #334155;
        }

        .dark .customer-status-modal__footer {
            background: #0f172a;
        }

        .dark .customer-status-modal__title,
        .dark .customer-status-modal__customer-info h3 {
            color: #f8fafc;
        }

        .dark .customer-status-modal__count,
        .dark .customer-status-modal__meta,
        .dark .customer-status-modal__empty {
            color: #94a3b8;
        }

        .dark .customer-status-modal__close {
            background: #1e293b;
            color: #e2e8f0;
        }

        @media (max-width: 560px) {
            .customer-status-modal {
                align-items: flex-end;
                padding: 0;
            }

            .customer-status-modal__panel {
                width: 100%;
                max-height: 82vh;
                border-right: 0;
                border-bottom: 0;
                border-left: 0;
                border-radius: 18px 18px 0 0;
            }

            .customer-status-modal__header {
                padding: 1rem;
            }

            .customer-status-modal__list {
                padding: 0.55rem 1rem;
            }

            .customer-status-modal__footer {
                padding: 0.8rem 1rem;
            }

            .customer-status-modal__customer {
                gap: 0.72rem;
                padding: 0.82rem 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .customer-metric--clickable,
            .customer-status-modal,
            .customer-status-modal__panel {
                transition: none;
            }
        }



        /* customer-card-dark-green-border-fix:
           Dark mode tetap memakai dua layer background agar border hijau hidup terlihat */
        .dark .customer-summary-card {
            border: 2px solid transparent;
            background:
                linear-gradient(#111827, #111827) padding-box,
                linear-gradient(
                    90deg,
                    #16a34a 0%,
                    #86efac 20%,
                    #14532d 40%,
                    #22c55e 55%,
                    #15803d 75%,
                    #86efac 100%
                ) border-box;
            background-size: 100% 100%, 300% 100%;
            background-position: 0 0, 0 0;
            animation: customer-card-green-flow 5s linear infinite;
        }



        /* dashboard-greeting-cute-icon: ikon sapaan setelah nama login */
        #dashboard-greeting {
            display: inline-flex;
            align-items: center;
            gap: 0.42rem;
        }

        #dashboard-greeting::after {
            display: inline-flex;
            width: 1.55rem;
            height: 1.55rem;
            flex: 0 0 1.55rem;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.18);
            content: "👋";
            font-size: 1rem;
            line-height: 1;
            animation: dashboard-greeting-wave 2.6s ease-in-out infinite;
            transform-origin: 72% 72%;
        }

        @keyframes dashboard-greeting-wave {
            0%, 58%, 100% {
                transform: rotate(0deg);
            }
            63% {
                transform: rotate(14deg);
            }
            68% {
                transform: rotate(-11deg);
            }
            73% {
                transform: rotate(12deg);
            }
            78% {
                transform: rotate(-7deg);
            }
            83% {
                transform: rotate(0deg);
            }
        }

        .dark #dashboard-greeting::after {
            background: linear-gradient(135deg, #78350f, #a16207);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.26);
        }

        @media (prefers-reduced-motion: reduce) {
            #dashboard-greeting::after {
                animation: none;
            }
        }



        /* Ringkasan Keuangan — Model A: Fokus Laba Bersih */
        .financial-summary-card {
            margin-top: 1.25rem;
            overflow: hidden;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.07);
        }

        .financial-summary-card__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem 1.25rem 0.95rem;
            border-bottom: 1px solid #eaf0f7;
            background: linear-gradient(115deg, rgba(239, 246, 255, 0.86), rgba(255, 255, 255, 0));
        }

        .financial-summary-card__eyebrow {
            margin: 0;
            color: #2563eb;
            font-size: 0.67rem;
            font-weight: 900;
            letter-spacing: 0.12em;
            line-height: 1.2;
        }

        .financial-summary-card__title {
            margin: 0.3rem 0 0;
            color: #0f172a;
            font-size: 1.15rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .financial-summary-card__period {
            margin: 0.35rem 0 0;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .financial-summary-card__badge {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid #bbf7d0;
            border-radius: 999px;
            padding: 0.38rem 0.65rem;
            background: #f0fdf4;
            color: #15803d;
            font-size: 0.66rem;
            font-weight: 850;
            white-space: nowrap;
        }

        .financial-summary-card__badge-dot {
            display: block;
            width: 0.45rem;
            height: 0.45rem;
            border-radius: 999px;
            background: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
        }

        .financial-summary-card__body {
            padding: 1.1rem 1.25rem 1.25rem;
        }

        /* Fokus utama: laba bersih */
        .finance-profit-hero {
            padding: 1.15rem 1.2rem;
            border: 1px solid #bbf7d0;
            border-radius: 15px;
            background:
                radial-gradient(circle at 100% 0%, rgba(134, 239, 172, 0.46), transparent 42%),
                linear-gradient(135deg, #f0fdf4, #ecfdf5);
        }

        .finance-profit-hero--negative {
            border-color: #fecaca;
            background:
                radial-gradient(circle at 100% 0%, rgba(252, 165, 165, 0.42), transparent 42%),
                linear-gradient(135deg, #fff1f2, #fef2f2);
        }

        .finance-profit-hero__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .finance-profit-hero__label {
            color: #15803d;
            font-size: 0.66rem;
            font-weight: 950;
            letter-spacing: 0.1em;
            line-height: 1.2;
        }

        .finance-profit-hero--negative .finance-profit-hero__label {
            color: #b91c1c;
        }

        .finance-profit-hero__icon {
            display: inline-flex;
            width: 2rem;
            height: 2rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #16a34a;
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 950;
        }

        .finance-profit-hero--negative .finance-profit-hero__icon {
            background: #dc2626;
        }

        .finance-profit-hero__value {
            display: block;
            margin-top: 0.45rem;
            color: #047857;
            font-size: clamp(1.55rem, 5.4vw, 2.1rem);
            font-weight: 950;
            letter-spacing: -0.06em;
            line-height: 1.1;
        }

        .finance-profit-hero--negative .finance-profit-hero__value {
            color: #b91c1c;
        }

        .finance-profit-hero__note {
            margin: 0.45rem 0 0;
            color: #15803d;
            font-size: 0.74rem;
            font-weight: 750;
            line-height: 1.35;
        }

        .finance-profit-hero--negative .finance-profit-hero__note {
            color: #b91c1c;
        }

        /* Perbandingan pemasukan dengan pengeluaran */
        .finance-flow-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
            margin-top: 0.95rem;
        }

        .finance-flow {
            min-width: 0;
            padding: 0.95rem;
            border: 1px solid #e2e8f0;
            border-radius: 13px;
            background: #ffffff;
        }

        .finance-flow__heading {
            display: flex;
            align-items: center;
            gap: 0.42rem;
        }

        .finance-flow__icon {
            display: inline-flex;
            width: 1.55rem;
            height: 1.55rem;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            font-size: 0.9rem;
            font-weight: 950;
        }

        .finance-flow__label {
            color: #475569;
            font-size: 0.69rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .finance-flow__value {
            display: block;
            margin-top: 0.68rem;
            overflow: hidden;
            font-size: clamp(0.98rem, 3.2vw, 1.22rem);
            font-weight: 950;
            letter-spacing: -0.045em;
            line-height: 1.15;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .finance-flow__detail {
            display: block;
            min-height: 1.9em;
            margin-top: 0.34rem;
            color: #94a3b8;
            font-size: 0.62rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .finance-flow__bar {
            position: relative;
            height: 0.42rem;
            margin-top: 0.72rem;
            overflow: hidden;
            border-radius: 999px;
            background: #e2e8f0;
        }

        .finance-flow__bar::after {
            display: block;
            width: var(--finance-bar-width);
            height: 100%;
            border-radius: inherit;
            content: "";
            transition: width 420ms ease;
        }

        .finance-flow--income .finance-flow__icon {
            background: #dcfce7;
            color: #15803d;
        }

        .finance-flow--income .finance-flow__value {
            color: #15803d;
        }

        .finance-flow__bar--income::after {
            background: linear-gradient(90deg, #22c55e, #86efac);
        }

        .finance-flow--expense .finance-flow__icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .finance-flow--expense .finance-flow__value {
            color: #b91c1c;
        }

        .finance-flow__bar--expense::after {
            background: linear-gradient(90deg, #ef4444, #fca5a5);
        }

        /* Informasi pendukung: estimasi dan tagihan tertunda */
        .finance-support-list {
            margin-top: 1rem;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 13px;
            background: #ffffff;
        }

        .finance-support-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.82rem 0.9rem;
        }

        .finance-support-row + .finance-support-row {
            border-top: 1px solid #eef2f7;
        }

        .finance-support-row__icon {
            display: inline-flex;
            width: 1.85rem;
            height: 1.85rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 950;
        }

        .finance-support-row__content {
            min-width: 0;
            flex: 1 1 auto;
        }

        .finance-support-row__label,
        .finance-support-row__detail {
            display: block;
        }

        .finance-support-row__label {
            color: #334155;
            font-size: 0.72rem;
            font-weight: 900;
            line-height: 1.25;
        }

        .finance-support-row__detail {
            margin-top: 0.18rem;
            overflow: hidden;
            color: #94a3b8;
            font-size: 0.61rem;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .finance-support-row__value {
            flex: 0 0 auto;
            color: #0f172a;
            font-size: clamp(0.78rem, 2.7vw, 0.98rem);
            font-weight: 950;
            letter-spacing: -0.04em;
            text-align: right;
            white-space: nowrap;
        }

        .finance-support-row--estimate .finance-support-row__icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .finance-support-row--estimate .finance-support-row__value {
            color: #1d4ed8;
        }

        .finance-support-row--pending .finance-support-row__icon {
            background: #fef3c7;
            color: #b45309;
        }

        .finance-support-row--pending .finance-support-row__value {
            color: #b45309;
        }

        /* Dark mode */
        .dark .financial-summary-card {
            border-color: #334155;
            background: #111827;
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.24);
        }

        .dark .financial-summary-card__header {
            border-color: #334155;
            background: linear-gradient(115deg, rgba(30, 64, 175, 0.16), rgba(17, 24, 39, 0));
        }

        .dark .financial-summary-card__title {
            color: #f8fafc;
        }

        .dark .financial-summary-card__period {
            color: #94a3b8;
        }

        .dark .finance-profit-hero {
            border-color: rgba(34, 197, 94, 0.36);
            background:
                radial-gradient(circle at 100% 0%, rgba(22, 163, 74, 0.22), transparent 44%),
                linear-gradient(135deg, rgba(20, 83, 45, 0.55), rgba(17, 24, 39, 0.92));
        }

        .dark .finance-profit-hero--negative {
            border-color: rgba(248, 113, 113, 0.35);
            background:
                radial-gradient(circle at 100% 0%, rgba(220, 38, 38, 0.22), transparent 44%),
                linear-gradient(135deg, rgba(127, 29, 29, 0.52), rgba(17, 24, 39, 0.92));
        }

        .dark .finance-profit-hero__label,
        .dark .finance-profit-hero__note {
            color: #bbf7d0;
        }

        .dark .finance-profit-hero__value {
            color: #86efac;
        }

        .dark .finance-profit-hero--negative .finance-profit-hero__label,
        .dark .finance-profit-hero--negative .finance-profit-hero__note {
            color: #fecaca;
        }

        .dark .finance-profit-hero--negative .finance-profit-hero__value {
            color: #fca5a5;
        }

        .dark .finance-flow,
        .dark .finance-support-list {
            border-color: #334155;
            background: #0f172a;
        }

        .dark .finance-flow__label,
        .dark .finance-support-row__label {
            color: #e2e8f0;
        }

        .dark .finance-flow__detail,
        .dark .finance-support-row__detail {
            color: #94a3b8;
        }

        .dark .finance-flow--income .finance-flow__icon {
            background: rgba(20, 83, 45, 0.48);
            color: #86efac;
        }

        .dark .finance-flow--income .finance-flow__value {
            color: #86efac;
        }

        .dark .finance-flow--expense .finance-flow__icon {
            background: rgba(127, 29, 29, 0.44);
            color: #fca5a5;
        }

        .dark .finance-flow--expense .finance-flow__value {
            color: #fca5a5;
        }

        .dark .finance-flow__bar {
            background: #334155;
        }

        .dark .finance-support-row + .finance-support-row {
            border-color: #334155;
        }

        .dark .finance-support-row--estimate .finance-support-row__icon {
            background: rgba(30, 64, 175, 0.34);
            color: #93c5fd;
        }

        .dark .finance-support-row--estimate .finance-support-row__value {
            color: #93c5fd;
        }

        .dark .finance-support-row--pending .finance-support-row__icon {
            background: rgba(120, 53, 15, 0.45);
            color: #fcd34d;
        }

        .dark .finance-support-row--pending .finance-support-row__value {
            color: #fcd34d;
        }

        @media (max-width: 560px) {
            .financial-summary-card {
                margin-top: 1rem;
                border-radius: 15px;
            }

            .financial-summary-card__header {
                padding: 1.05rem 1rem 0.88rem;
            }

            .financial-summary-card__title {
                font-size: 1.03rem;
            }

            .financial-summary-card__badge {
                padding: 0.34rem 0.55rem;
                font-size: 0.6rem;
            }

            .financial-summary-card__body {
                padding: 0.9rem 1rem 1rem;
            }

            .finance-profit-hero {
                padding: 1rem;
                border-radius: 13px;
            }

            .finance-flow-grid {
                gap: 0.65rem;
                margin-top: 0.75rem;
            }

            .finance-flow {
                padding: 0.8rem;
                border-radius: 11px;
            }

            .finance-flow__value {
                font-size: 0.92rem;
            }

            .finance-support-list {
                margin-top: 0.8rem;
                border-radius: 11px;
            }

            .finance-support-row {
                gap: 0.6rem;
                padding: 0.75rem;
            }

            .finance-support-row__value {
                font-size: 0.76rem;
            }
        }

        @media (max-width: 380px) {
            .financial-summary-card__badge {
                display: none;
            }

            .finance-flow-grid {
                grid-template-columns: 1fr;
            }

            .finance-flow__detail {
                min-height: auto;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .finance-flow__bar::after {
                transition: none;
            }
        }



        /* finance-color-distinction-palette:
           Keuangan memakai identitas indigo; warna status tetap semantik. */

        /* Header finansial: indigo agar berbeda dari monitoring jaringan */
        .financial-summary-card {
            border-color: #ddd6fe;
            box-shadow: 0 14px 32px rgba(79, 70, 229, 0.09);
        }

        .financial-summary-card__header {
            border-bottom-color: #e9e7ff;
            background:
                radial-gradient(circle at 100% 0%, rgba(196, 181, 253, 0.38), transparent 40%),
                linear-gradient(120deg, #f5f3ff, #ffffff 72%);
        }

        .financial-summary-card__eyebrow {
            color: #6d28d9;
        }

        .financial-summary-card__badge {
            border-color: #c4b5fd;
            background: #f5f3ff;
            color: #6d28d9;
        }

        .financial-summary-card__badge-dot {
            background: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.14);
        }

        /* Laba bersih: indigo sebagai wadah, hijau hanya untuk hasil positif */
        .finance-profit-hero {
            border-color: #c7d2fe;
            background:
                radial-gradient(circle at 100% 0%, rgba(167, 139, 250, 0.48), transparent 43%),
                linear-gradient(135deg, #eef2ff, #f5f3ff);
        }

        .finance-profit-hero__label {
            color: #4338ca;
        }

        .finance-profit-hero__icon {
            background: #4f46e5;
            box-shadow: 0 7px 16px rgba(79, 70, 229, 0.26);
        }

        .finance-profit-hero__value {
            color: #047857;
        }

        .finance-profit-hero__note {
            color: #475569;
        }

        /* Kondisi laba negatif tetap jelas memakai merah */
        .finance-profit-hero--negative {
            border-color: #fecaca;
            background:
                radial-gradient(circle at 100% 0%, rgba(252, 165, 165, 0.42), transparent 43%),
                linear-gradient(135deg, #fff1f2, #fef2f2);
        }

        .finance-profit-hero--negative .finance-profit-hero__label,
        .finance-profit-hero--negative .finance-profit-hero__note,
        .finance-profit-hero--negative .finance-profit-hero__value {
            color: #b91c1c;
        }

        .finance-profit-hero--negative .finance-profit-hero__icon {
            background: #dc2626;
            box-shadow: 0 7px 16px rgba(220, 38, 38, 0.22);
        }

        /* Pendapatan: teal, supaya tidak menyatu dengan hijau monitoring */
        .finance-flow--income .finance-flow__icon {
            background: #ccfbf1;
            color: #0f766e;
        }

        .finance-flow--income .finance-flow__value {
            color: #0f766e;
        }

        .finance-flow__bar--income::after {
            background: linear-gradient(90deg, #0d9488, #5eead4);
        }

        /* Pengeluaran: coral/merah lembut */
        .finance-flow--expense .finance-flow__icon {
            background: #ffe4e6;
            color: #e11d48;
        }

        .finance-flow--expense .finance-flow__value {
            color: #be123c;
        }

        .finance-flow__bar--expense::after {
            background: linear-gradient(90deg, #e11d48, #fda4af);
        }

        /* Estimasi: biru informasi */
        .finance-support-row--estimate .finance-support-row__icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .finance-support-row--estimate .finance-support-row__value {
            color: #1d4ed8;
        }

        /* Tertunda: amber sebagai tindakan yang perlu diperhatikan */
        .finance-support-row--pending .finance-support-row__icon {
            background: #fef3c7;
            color: #b45309;
        }

        .finance-support-row--pending .finance-support-row__value {
            color: #b45309;
        }

        /* Dark mode: indigo slate tetap menjadi identitas keuangan */
        .dark .financial-summary-card {
            border-color: #4338ca;
            background: #111827;
            box-shadow: 0 16px 38px rgba(49, 46, 129, 0.28);
        }

        .dark .financial-summary-card__header {
            border-color: #3730a3;
            background:
                radial-gradient(circle at 100% 0%, rgba(109, 40, 217, 0.28), transparent 42%),
                linear-gradient(120deg, rgba(49, 46, 129, 0.34), #111827 72%);
        }

        .dark .financial-summary-card__eyebrow {
            color: #c4b5fd;
        }

        .dark .financial-summary-card__badge {
            border-color: rgba(167, 139, 250, 0.45);
            background: rgba(76, 29, 149, 0.28);
            color: #ddd6fe;
        }

        .dark .financial-summary-card__badge-dot {
            background: #a78bfa;
            box-shadow: 0 0 0 4px rgba(167, 139, 250, 0.16);
        }

        .dark .finance-profit-hero {
            border-color: rgba(129, 140, 248, 0.44);
            background:
                radial-gradient(circle at 100% 0%, rgba(109, 40, 217, 0.32), transparent 44%),
                linear-gradient(135deg, rgba(49, 46, 129, 0.58), rgba(30, 27, 75, 0.54));
        }

        .dark .finance-profit-hero__label {
            color: #c7d2fe;
        }

        .dark .finance-profit-hero__icon {
            background: #6366f1;
            box-shadow: 0 7px 17px rgba(99, 102, 241, 0.3);
        }

        .dark .finance-profit-hero__value {
            color: #6ee7b7;
        }

        .dark .finance-profit-hero__note {
            color: #cbd5e1;
        }

        .dark .finance-profit-hero--negative {
            border-color: rgba(248, 113, 113, 0.38);
            background:
                radial-gradient(circle at 100% 0%, rgba(220, 38, 38, 0.24), transparent 44%),
                linear-gradient(135deg, rgba(127, 29, 29, 0.5), rgba(69, 10, 10, 0.38));
        }

        .dark .finance-profit-hero--negative .finance-profit-hero__label,
        .dark .finance-profit-hero--negative .finance-profit-hero__note,
        .dark .finance-profit-hero--negative .finance-profit-hero__value {
            color: #fecaca;
        }

        .dark .finance-flow--income .finance-flow__icon {
            background: rgba(15, 118, 110, 0.36);
            color: #5eead4;
        }

        .dark .finance-flow--income .finance-flow__value {
            color: #5eead4;
        }

        .dark .finance-flow--expense .finance-flow__icon {
            background: rgba(159, 18, 57, 0.38);
            color: #fda4af;
        }

        .dark .finance-flow--expense .finance-flow__value {
            color: #fda4af;
        }

        .dark .finance-support-row--estimate .finance-support-row__icon {
            background: rgba(30, 64, 175, 0.36);
            color: #93c5fd;
        }

        .dark .finance-support-row--estimate .finance-support-row__value {
            color: #93c5fd;
        }

        .dark .finance-support-row--pending .finance-support-row__icon {
            background: rgba(120, 53, 15, 0.48);
            color: #fcd34d;
        }

        .dark .finance-support-row--pending .finance-support-row__value {
            color: #fcd34d;
        }

</style>
@endsection
