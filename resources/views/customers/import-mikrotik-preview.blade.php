@extends('layouts.app')

@section('content')
<style>
.cc-import-preview{--ci-bg:#fff;--ci-soft:#f1f5f9;--ci-text:#0f172a;--ci-muted:#475569;--ci-border:#cbd5e1;--ci-focus:#ecfeff;--ci-first:56px;--ci-second:180px;width:100%;max-width:1800px;min-width:0;box-sizing:border-box}.dark .cc-import-preview{--ci-bg:#151e30;--ci-soft:#202c40;--ci-text:#edf2f7;--ci-muted:#b8c5d6;--ci-border:#41516a;--ci-focus:#173a4b}.ci-scroll-tools{display:flex;flex-wrap:wrap;align-items:center;gap:10px;padding:12px;border:1px solid var(--ci-border);border-bottom:0;border-radius:14px 14px 0 0;background:var(--ci-bg);color:var(--ci-text)}.ci-scroll-tools[hidden]{display:none!important}.ci-tool-note{font-size:11px;color:var(--ci-muted);line-height:1.5;flex:1 1 200px}.ci-scroll-controls{display:flex;align-items:center;gap:9px;flex:1 1 250px;min-width:0}.ci-scroll-controls input[type=range]{flex:1;min-width:60px;width:100%;height:36px;margin:0;accent-color:#0891b2;cursor:ew-resize}.ci-scroll-button{display:inline-flex;align-items:center;justify-content:center;min-width:40px;min-height:40px;padding:8px 11px;border:1px solid var(--ci-border);border-radius:9px;background:var(--ci-bg);color:var(--ci-text);font-size:12px;line-height:1.3;cursor:pointer}.ci-scroll-button:disabled{opacity:.4;cursor:default}.ci-scroll-button:focus-visible,.ci-scroll-viewport:focus-visible{outline:3px solid #22d3ee;outline-offset:2px}.ci-scroll-viewport{position:relative;isolation:isolate;min-width:0;width:100%;max-width:100%;max-height:65vh;max-height:65dvh;overflow:auto!important;overscroll-behavior-x:contain;scroll-padding-left:calc(var(--ci-first) + var(--ci-second) + 12px);scroll-padding-top:70px;-webkit-overflow-scrolling:touch;border:1px solid var(--ci-border);border-radius:0 0 14px 14px;background:var(--ci-bg);scrollbar-width:auto;scrollbar-color:#64748b var(--ci-soft)}.ci-scroll-viewport::-webkit-scrollbar{width:12px;height:12px}.ci-scroll-viewport::-webkit-scrollbar-track{background:var(--ci-soft)}.ci-scroll-viewport::-webkit-scrollbar-thumb{background:#64748b;border:3px solid var(--ci-soft);border-radius:20px}.ci-scroll-viewport::-webkit-scrollbar-corner{background:var(--ci-soft)}.ci-import-table{width:1580px!important;min-width:1580px!important;max-width:none!important;table-layout:fixed;border-collapse:separate!important;border-spacing:0;color:var(--ci-text)}.ci-import-table th,.ci-import-table td{padding:12px!important;box-sizing:border-box;border-bottom:1px solid var(--ci-border);background:var(--ci-bg);vertical-align:top;overflow-wrap:anywhere}.ci-import-table th{position:sticky;top:0;z-index:30;background:var(--ci-soft);color:var(--ci-muted);font-size:10px;line-height:1.5;letter-spacing:.02em;vertical-align:middle}.ci-import-table td{font-size:12px;line-height:1.5}.ci-import-table th:nth-child(1),.ci-import-table td:nth-child(1){position:sticky;left:0;z-index:12;width:var(--ci-first);padding-left:8px!important;padding-right:8px!important;text-align:center}.ci-import-table th:nth-child(2),.ci-import-table td:nth-child(2){position:sticky;left:var(--ci-first);z-index:11;width:var(--ci-second);box-shadow:2px 0 0 var(--ci-border)}.ci-import-table th:nth-child(1),.ci-import-table th:nth-child(2){z-index:40;background:var(--ci-soft)}.ci-import-table tr[data-ci-existing="1"] td{background:var(--ci-soft)}.ci-import-table tr:focus-within td{background:var(--ci-focus)}.ci-import-table td input:not([type=checkbox]):not([type=hidden]),.ci-import-table td select{box-sizing:border-box;width:100%!important;min-width:0!important;max-width:100%!important;min-height:40px;padding:9px 10px!important;font-size:13px;background:var(--ci-bg)!important;color:var(--ci-text)!important;border-color:var(--ci-border)!important}.ci-import-table td input:disabled,.ci-import-table td select:disabled{opacity:.65}.ci-import-table input[type=checkbox]{width:20px;height:20px;accent-color:#0891b2;cursor:pointer}.ci-import-table td:nth-child(2)>div{overflow-wrap:anywhere;word-break:normal;white-space:normal}.ci-import-table td:nth-child(1)>span{font-size:9px;line-height:1.4;padding:4px!important;white-space:normal}.cc-import-preview[data-ci-freeze="0"] .ci-import-table td:nth-child(2){position:static;box-shadow:none}.cc-import-preview[data-ci-freeze="0"] .ci-import-table th:nth-child(2){left:auto;z-index:30;box-shadow:none}.cc-import-preview[data-ci-freeze="0"] .ci-scroll-viewport{scroll-padding-left:calc(var(--ci-first) + 12px)}@media(max-width:640px){.cc-import-preview{--ci-first:44px;--ci-second:104px}.ci-scroll-viewport{max-height:62vh;max-height:62dvh}.ci-scroll-tools{padding:10px;gap:8px}.ci-tool-note{flex-basis:100%;font-size:10px}.ci-scroll-controls{flex-basis:calc(100% - 20px)}.ci-scroll-button{min-height:42px}.ci-import-table{width:1332px!important;min-width:1332px!important}.ci-import-table th,.ci-import-table td{padding:10px!important}.ci-import-table td input:not([type=checkbox]):not([type=hidden]),.ci-import-table td select{font-size:16px;min-height:42px}.ci-import-table th{font-size:9px}.ci-import-table td:nth-child(2){font-size:11px}.ci-import-table col:nth-child(1){width:44px!important}.ci-import-table col:nth-child(2){width:104px!important}.ci-import-table col:nth-child(3){width:160px!important}.ci-import-table col:nth-child(4){width:120px!important}.ci-import-table col:nth-child(5){width:112px!important}.ci-import-table col:nth-child(6){width:176px!important}.ci-import-table col:nth-child(7){width:152px!important}.ci-import-table col:nth-child(8){width:176px!important}.ci-import-table col:nth-child(9){width:176px!important}.ci-import-table col:nth-child(10){width:112px!important}}
</style>

<div class="cc-import-preview mx-auto py-4 sm:py-6" data-ci-freeze="1">
    <div class="mb-7 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-cyan-100 bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700 dark:border-cyan-400/15 dark:bg-cyan-400/10 dark:text-cyan-300">
                Import MikroTik
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Preview akun PPPoE</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Router: {{ $router->name }}</p>
        </div>

        <a href="{{ route('customers.import.mikrotik') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
            Kembali
        </a>
    </div>

    @if($records === [])
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm font-medium text-amber-800 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-200">
            Tidak ada akun PPPoE yang ditemukan pada router ini.
        </div>
    @else
        <form method="POST" action="{{ route('customers.import.mikrotik.store') }}">
            @csrf
            <input type="hidden" name="router_id" value="{{ $router->id }}">
            <input type="hidden" name="area_id" value="{{ $area->id }}">

            <div class="mb-5 flex flex-wrap items-center gap-2 rounded-xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm font-semibold text-violet-800 dark:border-violet-400/20 dark:bg-violet-400/10 dark:text-violet-200">
                <span class="text-violet-500" aria-hidden="true">⌖</span>
                Wilayah tujuan:
                <span class="font-black">{{ $area->name }}</span>
                <span class="font-mono text-xs opacity-75">{{ $area->code }}</span>
            </div>

            @error('customers')
                <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-700 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-300">
                    {{ $message }}
                </div>
            @enderror

            <div class="mb-5 rounded-xl border border-cyan-200 bg-cyan-50 p-4 text-sm leading-6 text-cyan-900 dark:border-cyan-800 dark:bg-cyan-950 dark:text-cyan-200">
                <h2 class="font-bold">Cara impor pelanggan</h2>
                <ol class="mt-2 list-decimal space-y-1 pl-5">
                    <li>Centang pelanggan yang ingin diimpor.</li>
                    <li>Periksa nama pelanggan yang sudah terisi otomatis. Ubah jika perlu.</li>
                    <li>Isi nomor HP dan alamat jika tersedia. Keduanya boleh dikosongkan.</li>
                    <li>Sesuaikan paket internet dan tanggal jatuh tempo.</li>
                    <li>Periksa kembali, lalu klik <span class="font-semibold">Konfirmasi &amp; Import Terpilih</span>.</li>
                </ol>
            </div>


            <div class="mb-4 flex flex-wrap gap-2">
                <button type="button" id="select-all-import"
                        class="inline-flex items-center justify-center rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-2 text-xs font-bold text-cyan-700 transition hover:bg-cyan-100 dark:border-cyan-400/20 dark:bg-cyan-400/10 dark:text-cyan-300">
                    Pilih Semua
                </button>
                <button type="button" id="clear-all-import"
                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Kosongkan Semua
                </button>
            </div>

            <div class="ci-scroll-tools" data-ci-tools hidden>
                <p class="ci-tool-note">Geser kolom di sini atau langsung pada tabel. Judul kolom, pilihan, dan username tetap terlihat saat dikunci.</p>
                <div class="ci-scroll-controls">
                    <button type="button" class="ci-scroll-button" data-ci-left aria-label="Geser tabel ke kiri">&#8592;</button>
                    <input type="range" min="0" max="100" value="0" step="1" data-ci-range aria-label="Posisi geser kolom tabel">
                    <button type="button" class="ci-scroll-button" data-ci-right aria-label="Geser tabel ke kanan">&#8594;</button>
                </div>
                <button type="button" class="ci-scroll-button" data-ci-toggle aria-pressed="true">Lepas kunci username</button>
            </div>
            <div class="ci-scroll-viewport" data-ci-viewport tabindex="0" role="region" aria-label="Tabel preview impor pelanggan, dapat digeser kanan kiri dan atas bawah">
                <table class="ci-import-table text-left text-sm">
                    <colgroup><col style="width:56px"><col style="width:180px"><col style="width:184px"><col style="width:144px"><col style="width:120px"><col style="width:200px"><col style="width:168px"><col style="width:200px"><col style="width:200px"><col style="width:128px"></colgroup>
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                        <tr>
                            <th class="p-4">Pilih</th>
                            <th class="p-4">Username PPPoE</th>
                            <th class="p-4">Password PPPoE *</th>
                            <th class="p-4">Profil MikroTik</th>
                            <th class="p-4">Status akun di MikroTik</th>
                            <th class="p-4">Nama Pelanggan *</th>
                            <th class="p-4">Nomor HP (opsional)</th>
                            <th class="p-4">Alamat</th>
                            <th class="p-4">Paket *</th>
                            <th class="p-4">Jatuh Tempo *</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($records as $i => $record)
                            @php
                            $exists = !empty($record['existing_customer_id']);
                            $profileMatches = $packages->filter(fn ($package) => $record['profile'] === $package->mikrotik_profile);
                            $autoPackageId = $profileMatches->count() === 1 ? $profileMatches->first()->id : null;
                            @endphp

                            <tr data-ci-existing="{{ $exists ? '1' : '0' }}" class="{{ $exists ? 'bg-slate-50/80 dark:bg-slate-800/30' : '' }}">
                                <td class="p-4 align-top">
                                    @if($exists)
                                        <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-bold text-slate-600 dark:bg-slate-700 dark:text-slate-300">Sudah ada</span>
                                    @else
                                        <input type="checkbox"
                                               name="customers[{{ $i }}][selected]"
                                               value="1"
                                               data-import-checkbox
                                               class="mt-1 h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-950">
                                    @endif
                                </td>

                                <td class="p-4 align-top">
                                    <div class="font-bold text-slate-800 dark:text-slate-100">{{ $record['username'] }}</div>
                                    <input type="hidden" name="customers[{{ $i }}][pppoe_username]" value="{{ $record['username'] }}">

                                    @if($exists)
                                        @if($record['existing_customer_id'] > 0)
                                        <a href="{{ route('customers.edit', $record['existing_customer_id']) }}"
                                           class="mt-1 inline-block text-xs font-bold text-cyan-600 hover:underline dark:text-cyan-400">
                                            Lihat pelanggan
                                        </a>
                                        @else
                                        <span class="mt-1 block text-xs text-slate-500">Sudah terdaftar di luar akses wilayah Anda.</span>
                                        @endif
                                    @endif
                                </td>

                                <td class="p-4 align-top">
                                    <input type="text"
                                           name="customers[{{ $i }}][pppoe_password]"
                                           value="{{ $record['password'] }}"
                                           {{ $exists ? 'disabled' : 'required' }}
                                           class="min-w-44 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 disabled:cursor-not-allowed disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:disabled:bg-slate-800">
                                </td>

                                <td class="p-4 align-top text-slate-600 dark:text-slate-300">{{ $record['profile'] ?: '-' }}</td>

                                <td class="p-4 align-top">
                                    @if($record['disabled'])
                                        <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-700 dark:bg-rose-400/10 dark:text-rose-300">Nonaktif</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300">Aktif</span>
                                    @endif
                                </td>

                                <td class="p-4 align-top">
                                    <input type="text"
                                           name="customers[{{ $i }}][name]"
                                           value="{{ old("customers.$i.name", $record['name'] ?? '') }}"
                                           placeholder="Nama pelanggan"
                                           {{ $exists ? 'disabled' : 'required' }}
                                           class="min-w-52 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 disabled:cursor-not-allowed disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:disabled:bg-slate-800">
                                </td>

                                <td class="p-4 align-top">
                                    <input type="text"
                                           name="customers[{{ $i }}][phone]"
                                           placeholder="Boleh dikosongkan"
                                           {{ $exists ? 'disabled' : '' }}
                                           class="min-w-36 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 disabled:cursor-not-allowed disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:disabled:bg-slate-800">
                                </td>

                                <td class="p-4 align-top">
                                    <input type="text"
                                           name="customers[{{ $i }}][address]"
                                           placeholder="Alamat pelanggan"
                                           {{ $exists ? 'disabled' : '' }}
                                           class="min-w-56 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 disabled:cursor-not-allowed disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:disabled:bg-slate-800">
                                </td>

                                <td class="p-4 align-top">
                                    <select name="customers[{{ $i }}][internet_package_id]"
                                            {{ $exists ? 'disabled' : 'required' }}
                                            class="min-w-48 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 disabled:cursor-not-allowed disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:disabled:bg-slate-800">
                                        <option value="">Pilih paket</option>
                                        @foreach($packages as $package)
                                            <option value="{{ $package->id }}" @selected((string) $autoPackageId === (string) $package->id)>
                                                {{ $package->name }}{{ $record['profile'] === $package->mikrotik_profile ? ' — sesuai profil' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="p-4 align-top">
                                    <input type="number"
                                           name="customers[{{ $i }}][due_day]"
                                           min="1"
                                           max="28"
                                           value="10"
                                           {{ $exists ? 'disabled' : 'required' }}
                                           class="w-28 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 disabled:cursor-not-allowed disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:disabled:bg-slate-800">
                                    <input type="hidden" name="customers[{{ $i }}][status]" value="{{ $record['disabled'] ? 'inactive' : 'active' }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs leading-5 text-slate-500 dark:text-slate-400">Hanya baris yang dicentang dan data wajibnya lengkap yang akan disimpan.</p>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-cyan-300/40 dark:bg-cyan-500 dark:hover:bg-cyan-400">
                    Konfirmasi &amp; Import Terpilih
                </button>
            </div>
        </form>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const boxes = Array.from(document.querySelectorAll('input[data-import-checkbox]'));
    const selectAllButton = document.getElementById('select-all-import');
    const clearAllButton = document.getElementById('clear-all-import');

    function syncRow(box) {
        const row = box.closest('tr');
        if (!row) return;

        const fields = row.querySelectorAll('input[type="text"], select, input[type="number"]');

        fields.forEach(function (field) {
            field.disabled = !box.checked;

            const requiredField =
                field.name.endsWith('[pppoe_password]') ||
                field.name.endsWith('[name]') ||
                field.name.endsWith('[internet_package_id]') ||
                field.name.endsWith('[due_day]');

            if (requiredField) {
                field.required = box.checked;
            }
        });
    }

    boxes.forEach(function (box) {
        syncRow(box);
        box.addEventListener('change', function () {
            syncRow(box);
        });
    });

    if (selectAllButton) {
        selectAllButton.addEventListener('click', function () {
            boxes.forEach(function (box) {
                box.checked = true;
                syncRow(box);
            });
        });
    }

    if (clearAllButton) {
        clearAllButton.addEventListener('click', function () {
            boxes.forEach(function (box) {
                box.checked = false;
                syncRow(box);
            });
        });
    }
});
</script>
<script>
(() => {
    function initImportScroll() {
        const root = document.querySelector('.cc-import-preview');
        if (!root) return;
        const viewport = root.querySelector('[data-ci-viewport]');
        if (!viewport) return;
        const tools = root.querySelector('[data-ci-tools]');
        const range = root.querySelector('[data-ci-range]');
        const left = root.querySelector('[data-ci-left]');
        const right = root.querySelector('[data-ci-right]');
        const toggle = root.querySelector('[data-ci-toggle]');
        const table = viewport.querySelector('table');
        let scheduled = false;
        function update() {
            scheduled = false;
            const max = Math.max(0, viewport.scrollWidth - viewport.clientWidth);
            const value = Math.max(0, Math.min(max, viewport.scrollLeft));
            range.max = String(Math.ceil(max));
            range.value = String(Math.round(value));
            range.disabled = max < 2;
            range.setAttribute('aria-valuetext', max < 2 ? 'Semua kolom terlihat' : Math.round(value / max * 100) + ' persen');
            left.disabled = value <= 1;
            right.disabled = value >= max - 1;
        }
        function schedule() {
            if (!scheduled) { scheduled = true; requestAnimationFrame(update); }
        }
        tools.hidden = false;
        range.addEventListener('input', () => { viewport.scrollLeft = Number(range.value); schedule(); });
        left.addEventListener('click', () => {viewport.scrollLeft -= Math.max(140, viewport.clientWidth * 0.65);schedule();});
        right.addEventListener('click', () => {viewport.scrollLeft += Math.max(140, viewport.clientWidth * 0.65);schedule();});
        toggle.addEventListener('click', () => {
            const frozen = root.dataset.ciFreeze !== '0';
            root.dataset.ciFreeze = frozen ? '0' : '1';
            toggle.setAttribute('aria-pressed', String(!frozen));
            toggle.textContent = frozen ? 'Kunci username' : 'Lepas kunci username';
        });
        viewport.addEventListener('scroll', schedule, {passive:true});
        window.addEventListener('resize', schedule, {passive:true});
        if ('ResizeObserver' in window) {
            const observer = new ResizeObserver(schedule);
            observer.observe(viewport);
            observer.observe(table);
        }
        update();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initImportScroll, {once:true});
    else initImportScroll();
})();
</script>

@endsection
