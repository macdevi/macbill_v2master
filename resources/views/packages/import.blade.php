@extends('layouts.app')

@section('title', 'Impor Paket MikroTik')

@section('content')
@include('packages.partials.catalog-theme')
<div class="package-catalog package-import">
@php
    $oldPackages = old('packages', []);
    $hasOldInput = old('packages') !== null;
    $packagesByProfile = $availablePackages->groupBy('mikrotik_profile');
@endphp

<div class="mb-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                <span class="pc-heading-icon">@include('packages.partials.catalog-icon',['name'=>'download'])</span> Impor paket dari MikroTik
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Pilih profile yang ingin dijadikan paket internet pada sistem billing.
            </p>
        </div>

        <a
            href="{{ route('packages.index') }}"
            class="inline-flex w-fit items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
        >
            ← Kembali ke Paket
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-red-300 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900/70 dark:bg-red-950/30 dark:text-red-200">
        <p class="font-bold">Import belum berhasil. Periksa data berikut:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- router-source-navigation --}}
<div class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
    <p class="text-sm font-bold text-slate-900 dark:text-white">
        Pilih MikroTik sumber
    </p>
    <div class="mt-3 flex flex-wrap gap-2">
        @foreach ($routers as $sourceRouter)
            <a href="{{ route('packages.import', $sourceRouter) }}"
               @if ($sourceRouter->id === $router->id) aria-current="page" @endif
               class="rounded-xl border px-4 py-2 text-sm font-semibold {{ $sourceRouter->id === $router->id ? 'border-cyan-600 bg-cyan-600 text-white' : 'border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-200' }}">
                {{ $sourceRouter->name }}
            </a>
        @endforeach
    </div>
    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
        Berganti sumber akan membuka ulang halaman. Pilihan paket yang belum disimpan tidak ikut dibawa.
    </p>
</div>

<div class="mb-5 rounded-xl border border-cyan-200 bg-cyan-50 p-3 text-sm text-cyan-900 dark:border-cyan-800 dark:bg-cyan-950 dark:text-cyan-200">
    Impor hanya menyimpan data billing dan hubungan router. Tidak ada pengaturan yang dikirim ke MikroTik.
    Profil yang sudah terdaftar pada router sumber akan dipertahankan.
</div>

<form method="POST" action="{{ route('packages.import.store') }}" id="import-packages-form">
    @csrf

    <input type="hidden" name="router_id" value="{{ $router->id }}">

    <div class="mb-5 rounded-2xl border bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-bold text-slate-900 dark:text-white">
                    Router sumber: {{ $router->name }}
                </p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Ditemukan <span class="font-semibold">{{ $profiles->count() }}</span> profile MikroTik.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    id="select-all-packages"
                    class="rounded-xl border border-cyan-300 bg-cyan-50 px-4 py-2 text-sm font-bold text-cyan-800 transition hover:bg-cyan-100 dark:border-cyan-800 dark:bg-cyan-950/40 dark:text-cyan-200 dark:hover:bg-cyan-950/70"
                >
                    Pilih semua profil
                </button>

                <button
                    type="button"
                    id="clear-all-packages"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                >
                    Kosongkan pilihan
                </button>
            </div>
        </div>
    </div>

    @if ($profiles->isNotEmpty())
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($profiles as $index => $p)
                @php
                    $defaultChecked = $hasOldInput
                        ? array_key_exists($index, $oldPackages)
                        : false;

                    $candidatePackages = $packagesByProfile->get(
                        $p['name'],
                        collect()
                    );

                    $linkedPackage = $candidatePackages->first(
                        fn ($candidate) => $candidate->routers
                            ->contains('id', $router->id)
                    );
                @endphp

                <article
                    class="pc-import-card package-card block rounded-2xl border bg-white p-5 shadow-sm transition hover:border-cyan-400 hover:shadow-md dark:border-slate-800 dark:bg-slate-900"
                    data-package-card
                >
                    <div class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            class="package-checkbox mt-1 h-5 w-5 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500"
                            data-package-checkbox
                            data-index="{{ $index }}"
                            @checked($defaultChecked)
                        >

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="break-words text-lg font-black text-slate-900 dark:text-white">
                                        {{ $p['name'] ?: 'Profile tanpa nama' }}
                                    </h2>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        Profile MikroTik
                                    </p>
                                </div>

                                <span
                                    class="package-status rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-300"
                                    data-package-status
                                >
                                    Belum dipilih
                                </span>
                            </div>

                            <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800/70">
                                    <dt class="text-xs text-slate-500 dark:text-slate-400">Download</dt>
                                    <dd class="mt-1 font-bold text-slate-900 dark:text-white">
                                        {{ $p['download'] }} Mbps
                                    </dd>
                                </div>

                                <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800/70">
                                    <dt class="text-xs text-slate-500 dark:text-slate-400">Upload</dt>
                                    <dd class="mt-1 font-bold text-slate-900 dark:text-white">
                                        {{ $p['upload'] }} Mbps
                                    </dd>
                                </div>
                            </dl>

                            <div class="mt-3 rounded-xl border border-slate-100 p-3 text-xs dark:border-slate-800">
                                <span class="text-slate-500 dark:text-slate-400">Rate limit MikroTik:</span>
                                <span class="ml-1 break-all font-semibold text-slate-700 dark:text-slate-200">
                                    {{ $p['rate'] ?: '-' }}
                                </span>
                            </div>

                            @if ($p['burst'])
                                <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                    Burst: <span class="font-semibold">{{ $p['burst'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div data-package-fields></div>

                    <template data-package-template>
                        <input type="hidden" name="packages[{{ $index }}][name]" value="{{ $p['name'] }}">
                        <input type="hidden" name="packages[{{ $index }}][mikrotik_profile]" value="{{ $p['name'] }}">
                        <input type="hidden" name="packages[{{ $index }}][download_speed]" value="{{ $p['download'] }}">
                        <input type="hidden" name="packages[{{ $index }}][upload_speed]" value="{{ $p['upload'] }}">
                        <input type="hidden" name="packages[{{ $index }}][burst_limit]" value="{{ $p['burst'] }}">
                        <input type="hidden" name="packages[{{ $index }}][burst_threshold]" value="{{ $p['burst_threshold'] }}">
                        <input type="hidden" name="packages[{{ $index }}][burst_time]" value="{{ $p['burst_time'] }}">
                        <input type="hidden" name="packages[{{ $index }}][priority]" value="8">

                        {{-- package-import-action --}}
                        @if ($linkedPackage)
                            <input type="hidden"
                                   name="packages[{{ $index }}][import_action]"
                                   value="link:{{ $linkedPackage->id }}">

                            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">
                                <p class="font-bold">
                                    Sudah terdaftar: {{ $linkedPackage->name }}
                                </p>
                                <p class="mt-1">
                                    Harga Rp {{ number_format((float) $linkedPackage->monthly_price, 0, ',', '.') }} dan pengaturan lama akan dipertahankan.
                                </p>
                                @if (!$linkedPackage->active)
                                    <p class="mt-1">Paket ini nonaktif. Impor tidak akan mengaktifkannya kembali.</p>
                                @endif
                            </div>
                        @else
                            <div class="mt-4 space-y-3 border-t border-slate-200 pt-4 dark:border-slate-700">
                                <label class="block">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                        Cara menyimpan paket
                                    </span>
                                    <select name="packages[{{ $index }}][import_action]"
                                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                                            required>
                                        <option value="new"
                                            @selected(old("packages.$index.import_action", 'new') === 'new')>
                                            Buat paket terpisah untuk {{ $router->name }}
                                        </option>
                                        @foreach ($candidatePackages->where('active', true) as $candidate)
                                            <option value="link:{{ $candidate->id }}"
                                                @selected(old("packages.$index.import_action") === 'link:'.$candidate->id)>
                                                Gunakan paket bersama: {{ $candidate->name }} (ID {{ $candidate->id }}) | {{ $candidate->routers->pluck('name')->implode(', ') ?: 'Belum dipetakan' }} | Rp {{ number_format((float) $candidate->monthly_price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="block">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                        Harga bulanan paket baru (Rp)
                                    </span>
                                    <input type="number"
                                           name="packages[{{ $index }}][monthly_price]"
                                           value="{{ old("packages.$index.monthly_price") }}"
                                           min="0"
                                           step="0.01"
                                           inputmode="decimal"
                                           placeholder="Isi harga, atau 0 jika gratis"
                                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                </label>

                                <p class="text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                                    Jika memakai paket bersama, harga dan pengaturan paket tujuan tetap digunakan. Input harga di atas diabaikan.
                                </p>
                            </div>
                        @endif
                    </template>
                </article>
            @endforeach
        </div>

        <div class="sticky bottom-4 z-10 mt-6 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-lg backdrop-blur dark:border-slate-800 dark:bg-slate-900/95">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-bold text-slate-900 dark:text-white">
                        <span id="selected-count">0</span> paket dipilih
                    </p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Hanya profil yang dicentang yang diproses. Paket yang sudah terdaftar tetap dipertahankan.
                    </p>
                </div>

                <button
                    type="submit"
                    id="submit-import"
                    disabled
                    class="inline-flex items-center justify-center rounded-xl bg-cyan-500 px-5 py-3 font-black text-slate-950 transition hover:bg-cyan-400 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500 dark:disabled:bg-slate-700 dark:disabled:text-slate-400"
                >
                    Import Paket Terpilih
                </button>
            </div>
        </div>
    @else
        <div class="rounded-2xl border bg-white p-6 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="font-bold text-slate-900 dark:text-white">
                Belum ada profile MikroTik yang dapat diimpor.
            </p>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Periksa koneksi router serta pastikan profile PPPoE sudah tersedia di MikroTik.
            </p>
        </div>
    @endif
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = Array.from(document.querySelectorAll('[data-package-checkbox]'));
    const selectAllButton = document.getElementById('select-all-packages');
    const clearAllButton = document.getElementById('clear-all-packages');
    const selectedCount = document.getElementById('selected-count');
    const submitButton = document.getElementById('submit-import');

    function configureImportAction(card) {
        const target = card.querySelector('[data-package-fields]');
        const select = target.querySelector('select[name$="[import_action]"]');
        const price = target.querySelector('input[name$="[monthly_price]"]');
        if (!select || !price) return;
        const apply = function () {
            const shared = select.value.startsWith('link:');
            price.disabled = shared;
            price.required = !shared;
            const label = price.closest('label');
            if (label) label.hidden = shared;
        };
        if (select.dataset.pcActionReady !== '1') {
            select.dataset.pcActionReady = '1';
            select.addEventListener('change', apply);
        }
        apply();
    }

    function syncPackageFields(checkbox) {
        const card = checkbox.closest('[data-package-card]');
        card.dataset.selected = checkbox.checked ? 'true' : 'false';
        const fieldsTarget = card.querySelector('[data-package-fields]');
        const template = card.querySelector('[data-package-template]');
        const status = card.querySelector('[data-package-status]');

        if (checkbox.checked) {
            if (!fieldsTarget.children.length) {
                fieldsTarget.appendChild(template.content.cloneNode(true));
            }

            card.classList.add('border-cyan-500', 'ring-2', 'ring-cyan-200', 'dark:ring-cyan-900');
            card.classList.remove('border-slate-200', 'dark:border-slate-800');

            status.textContent = 'Dipilih';
            status.classList.remove('bg-slate-100', 'text-slate-500', 'dark:bg-slate-800', 'dark:text-slate-300');
            status.classList.add('bg-cyan-100', 'text-cyan-800', 'dark:bg-cyan-950', 'dark:text-cyan-200');
        } else {
            fieldsTarget.replaceChildren();

            card.classList.remove('border-cyan-500', 'ring-2', 'ring-cyan-200', 'dark:ring-cyan-900');
            card.classList.add('border-slate-200', 'dark:border-slate-800');

            status.textContent = 'Belum dipilih';
            status.classList.remove('bg-cyan-100', 'text-cyan-800', 'dark:bg-cyan-950', 'dark:text-cyan-200');
            status.classList.add('bg-slate-100', 'text-slate-500', 'dark:bg-slate-800', 'dark:text-slate-300');
        }
        configureImportAction(card);
    }

    function updateSelection() {
        let total = 0;

        checkboxes.forEach(function (checkbox) {
            syncPackageFields(checkbox);

            if (checkbox.checked) {
                total++;
            }
        });

        if (selectedCount) {
            selectedCount.textContent = total;
        }

        if (submitButton) {
            submitButton.disabled = total === 0;
            submitButton.textContent = total > 0
                ? 'Import ' + total + ' Paket Terpilih'
                : 'Import Paket Terpilih';
        }
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateSelection);
    });

    if (selectAllButton) {
        selectAllButton.addEventListener('click', function () {
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = true;
            });

            updateSelection();
        });
    }

    if (clearAllButton) {
        clearAllButton.addEventListener('click', function () {
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });

            updateSelection();
        });
    }

    updateSelection();
});
</script>
</div>
@endsection
