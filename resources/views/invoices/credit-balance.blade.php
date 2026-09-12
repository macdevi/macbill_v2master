@extends('layouts.app')

@section('title', 'Titip Saldo')

@section('content')
@php
    $customersWithCredit = $customers->filter(
        fn ($customer) => (float) $customer->credit_balance > 0
    );

    $customerSearchData = $customers->map(fn ($customer) => [
        'id' => $customer->id,
        'name' => $customer->name,
        'customer_code' => $customer->customer_code ?? '',
        'phone' => $customer->phone ?? '',
        'credit_balance' => (float) $customer->credit_balance,
        'action' => route('customers.credit-balance', $customer),
    ])->values();
@endphp

<div class="space-y-5">
    <div>
        <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-cyan-600 dark:text-cyan-400">
            Billing Pelanggan
        </div>
        <h1 class="mt-1 text-2xl font-black text-slate-900 dark:text-white">
            Titip Saldo
        </h1>
        <p class="mt-1 max-w-3xl text-sm text-slate-500 dark:text-slate-400">
            Catat pembayaran lebih awal sebagai saldo kredit pelanggan.
        </p>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            <div class="font-semibold">Titip saldo belum dapat disimpan.</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-5 xl:grid-cols-[minmax(0,390px)_minmax(0,1fr)]">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-800 sm:px-5">
                <h2 class="text-base font-black text-slate-900 dark:text-white">
                    Input Titip Saldo
                </h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Cari pelanggan lalu masukkan nominal dana yang diterima.
                </p>
            </div>

            <form method="POST"
                  action="{{ old('customer_id') ? route('customers.credit-balance', old('customer_id')) : '' }}"
                  id="credit-balance-form"
                  class="space-y-4 p-4 sm:p-5">
                @csrf

                <input type="hidden"
                       id="customer_id"
                       name="customer_id"
                       value="{{ old('customer_id') }}">

                <div class="relative">
                    <label for="customer_search" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Pelanggan
                    </label>

                    <div class="relative">
                        <input type="text"
                               id="customer_search"
                               autocomplete="off"
                               placeholder="Ketik nama, kode, atau nomor telepon..."
                               class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 pr-11 text-sm font-medium text-slate-900 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
                               role="combobox"
                               aria-autocomplete="list"
                               aria-expanded="false"
                               aria-controls="customer-search-results"
                               aria-activedescendant="">

                        <button type="button"
                                id="clear-customer-search"
                                class="absolute inset-y-0 right-0 hidden items-center justify-center px-3 text-slate-400 transition hover:text-rose-600 dark:text-slate-500 dark:hover:text-rose-400"
                                aria-label="Hapus pelanggan terpilih">
                            ×
                        </button>
                    </div>

                    <div id="customer-search-results"
                         class="absolute z-30 mt-2 hidden max-h-72 w-full overflow-y-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900"
                         role="listbox"
                         aria-label="Hasil pencarian pelanggan">
                    </div>

                    <div id="customer-search-empty"
                         class="absolute z-30 mt-2 hidden w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500 shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                        Pelanggan tidak ditemukan.
                    </div>

                    <div id="selected-customer-info"
                         class="mt-3 hidden rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800 dark:border-cyan-500/20 dark:bg-cyan-500/10 dark:text-cyan-200">
                        <div class="text-xs font-bold uppercase tracking-wide text-cyan-600 dark:text-cyan-400">
                            Pelanggan Terpilih
                        </div>
                        <div id="selected-customer-name" class="mt-1 font-bold"></div>
                        <div id="selected-customer-meta" class="mt-1 text-xs text-cyan-700 dark:text-cyan-300"></div>
                    </div>

                    @error('customer_id')
                        <div class="mt-2 text-sm font-medium text-rose-600 dark:text-rose-400">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div>
                    <label for="amount" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Nominal Titip Saldo
                    </label>

                    <input type="number"
                           id="amount"
                           name="amount"
                           min="1"
                           step="0.01"
                           inputmode="decimal"
                           value="{{ old('amount') }}"
                           placeholder="Contoh: 150000"
                           class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
                           required>

                    @error('amount')
                        <div class="mt-2 text-sm font-medium text-rose-600 dark:text-rose-400">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-xs text-slate-600 dark:bg-slate-800/70 dark:text-slate-300">
                    Saldo akan ditambahkan ke kredit pelanggan. Sistem mencatat aktivitas penambahan saldo untuk keperluan audit.
                </div>

                <button type="submit"
                        id="save-credit-balance"
                        class="inline-flex w-full items-center justify-center rounded-2xl px-4 py-3 text-sm font-bold transition"
                        style="display:inline-flex;background:#0891b2;color:#ffffff;border:1px solid #0891b2;box-shadow:0 1px 2px rgba(0,0,0,.08);text-decoration:none;">
                    Simpan Titip Saldo
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-2 border-b border-slate-200 px-4 py-4 dark:border-slate-800 sm:flex-row sm:items-end sm:justify-between sm:px-5">
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">
                        Pelanggan dengan Saldo Kredit
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Hanya pelanggan yang masih memiliki saldo kredit.
                    </p>
                </div>

                <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400 dark:text-slate-500">
                    {{ $customersWithCredit->count() }} pelanggan
                </div>
            </div>

            <div class="space-y-3 p-4 lg:hidden">
                @forelse ($customersWithCredit as $customer)
                    @php
                        $statusClass = match($customer->status) {
                            'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
                            'isolated' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
                            'inactive' => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
                            default => 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300',
                        };
                    @endphp

                    <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $customer->name }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $customer->customer_code ?? '-' }}
                                    @if ($customer->phone)
                                        · {{ $customer->phone }}
                                    @endif
                                </div>
                            </div>

                            <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $statusClass }}">
                                {{ ucfirst($customer->status ?? 'unknown') }}
                            </span>
                        </div>

                        <div class="mt-3 rounded-xl bg-slate-50 px-3 py-2 dark:bg-slate-800/70">
                            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Saldo Kredit
                            </div>
                            <div class="mt-1 text-lg font-black text-slate-900 dark:text-white">
                                Rp {{ number_format((float) $customer->credit_balance, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                        Belum ada pelanggan yang memiliki saldo kredit.
                    </div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full min-w-[760px] text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Pelanggan</th>
                            <th class="px-4 py-3 font-semibold">Kode</th>
                            <th class="px-4 py-3 font-semibold">Telepon</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 text-right font-semibold">Saldo Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customersWithCredit as $customer)
                            @php
                                $statusClass = match($customer->status) {
                                    'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
                                    'isolated' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
                                    'inactive' => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
                                    default => 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300',
                                };
                            @endphp

                            <tr class="border-t border-slate-200 dark:border-slate-800">
                                <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $customer->name }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                    {{ $customer->customer_code ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                    {{ $customer->phone ?: '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">
                                        {{ ucfirst($customer->status ?? 'unknown') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-black text-slate-900 dark:text-white">
                                    Rp {{ number_format((float) $customer->credit_balance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Belum ada pelanggan yang memiliki saldo kredit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const customers = @json($customerSearchData);
        const form = document.getElementById('credit-balance-form');
        const hiddenCustomerId = document.getElementById('customer_id');
        const searchInput = document.getElementById('customer_search');
        const results = document.getElementById('customer-search-results');
        const emptyResult = document.getElementById('customer-search-empty');
        const clearButton = document.getElementById('clear-customer-search');
        const selectedInfo = document.getElementById('selected-customer-info');
        const selectedName = document.getElementById('selected-customer-name');
        const selectedMeta = document.getElementById('selected-customer-meta');

        let filteredCustomers = [];
        let activeIndex = -1;
        let selectedCustomer = null;

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function closeResults() {
            results.classList.add('hidden');
            emptyResult.classList.add('hidden');
            searchInput.setAttribute('aria-expanded', 'false');
            searchInput.removeAttribute('aria-activedescendant');
            activeIndex = -1;
        }

        function showSelectedCustomer(customer) {
            selectedCustomer = customer;
            hiddenCustomerId.value = customer.id;
            form.action = customer.action;
            searchInput.value = customer.name;
            selectedName.textContent = customer.name;

            const detail = [
                customer.customer_code || '-',
                customer.phone || 'Tanpa nomor telepon',
            ];

            selectedMeta.textContent = detail.join(' · ');
            selectedInfo.classList.remove('hidden');
            clearButton.classList.remove('hidden');
            closeResults();
        }

        function clearSelectedCustomer() {
            selectedCustomer = null;
            hiddenCustomerId.value = '';
            form.action = '';
            searchInput.value = '';
            selectedInfo.classList.add('hidden');
            clearButton.classList.add('hidden');
            closeResults();
            searchInput.focus();
        }

        function renderResults() {
            const query = searchInput.value.trim().toLowerCase();

            if (!query) {
                filteredCustomers = [];
                results.innerHTML = '';
                closeResults();
                return;
            }

            filteredCustomers = customers.filter(function (customer) {
                const haystack = [
                    customer.name,
                    customer.customer_code,
                    customer.phone,
                ].join(' ').toLowerCase();

                return haystack.includes(query);
            }).slice(0, 12);

            if (!filteredCustomers.length) {
                results.classList.add('hidden');
                emptyResult.classList.remove('hidden');
                searchInput.setAttribute('aria-expanded', 'false');
                activeIndex = -1;
                return;
            }

            emptyResult.classList.add('hidden');
            results.innerHTML = filteredCustomers.map(function (customer, index) {
                const isActive = index === activeIndex;

                return `
                    <button type="button"
                            id="customer-option-${customer.id}"
                            data-index="${index}"
                            role="option"
                            aria-selected="${isActive ? 'true' : 'false'}"
                            class="customer-search-option flex w-full items-start justify-between gap-3 rounded-xl px-3 py-3 text-left transition ${isActive ? 'bg-cyan-50 text-cyan-900 dark:bg-cyan-500/15 dark:text-cyan-100' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800'}">
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-bold">${escapeHtml(customer.name)}</span>
                            <span class="mt-1 block truncate text-xs text-slate-500 dark:text-slate-400">
                                ${escapeHtml(customer.customer_code || '-')} · ${escapeHtml(customer.phone || 'Tanpa nomor')}
                            </span>
                        </span>
                        <span class="shrink-0 text-xs font-semibold text-slate-400 dark:text-slate-500">
                            Pilih
                        </span>
                    </button>
                `;
            }).join('');

            results.classList.remove('hidden');
            searchInput.setAttribute('aria-expanded', 'true');

            if (activeIndex >= 0 && filteredCustomers[activeIndex]) {
                searchInput.setAttribute(
                    'aria-activedescendant',
                    'customer-option-' + filteredCustomers[activeIndex].id
                );
            } else {
                searchInput.removeAttribute('aria-activedescendant');
            }
        }

        function moveActiveOption(direction) {
            if (!filteredCustomers.length) {
                if (searchInput.value.trim()) {
                    renderResults();
                }

                return;
            }

            activeIndex += direction;

            if (activeIndex < 0) {
                activeIndex = filteredCustomers.length - 1;
            }

            if (activeIndex >= filteredCustomers.length) {
                activeIndex = 0;
            }

            renderResults();

            const activeOption = document.getElementById(
                'customer-option-' + filteredCustomers[activeIndex].id
            );

            if (activeOption) {
                activeOption.scrollIntoView({ block: 'nearest' });
            }
        }

        searchInput.addEventListener('input', function () {
            if (selectedCustomer && searchInput.value !== selectedCustomer.name) {
                selectedCustomer = null;
                hiddenCustomerId.value = '';
                form.action = '';
                selectedInfo.classList.add('hidden');
                clearButton.classList.add('hidden');
            }

            activeIndex = -1;
            renderResults();
        });

        searchInput.addEventListener('focus', function () {
            if (searchInput.value.trim() && !selectedCustomer) {
                renderResults();
            }
        });

        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                moveActiveOption(1);
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                moveActiveOption(-1);
            }

            if (event.key === 'Enter' && activeIndex >= 0 && filteredCustomers[activeIndex]) {
                event.preventDefault();
                showSelectedCustomer(filteredCustomers[activeIndex]);
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                closeResults();
            }
        });

        results.addEventListener('click', function (event) {
            const option = event.target.closest('.customer-search-option');

            if (!option) {
                return;
            }

            const customer = filteredCustomers[Number(option.dataset.index)];

            if (customer) {
                showSelectedCustomer(customer);
            }
        });

        clearButton.addEventListener('click', clearSelectedCustomer);

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.relative')) {
                closeResults();
            }
        });

        form.addEventListener('submit', function (event) {
            if (!hiddenCustomerId.value) {
                event.preventDefault();
                searchInput.focus();
                searchInput.setCustomValidity('Pilih pelanggan terlebih dahulu.');
                searchInput.reportValidity();
                searchInput.setCustomValidity('');
            }
        });

        const oldCustomerId = String(hiddenCustomerId.value || '');

        if (oldCustomerId) {
            const oldCustomer = customers.find(function (customer) {
                return String(customer.id) === oldCustomerId;
            });

            if (oldCustomer) {
                showSelectedCustomer(oldCustomer);
            }
        }
    });
</script>
@endsection
