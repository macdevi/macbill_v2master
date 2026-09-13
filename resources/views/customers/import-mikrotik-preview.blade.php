@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-[1800px] px-4 py-6 sm:px-6 lg:px-8">
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

            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-200">
                <strong>Keamanan:</strong> Password PPPoE terlihat pada halaman ini untuk kebutuhan import administrator. Gunakan HTTPS dan batasi akses aplikasi hanya kepada petugas berwenang.
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

            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <table class="min-w-[1700px] w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                        <tr>
                            <th class="p-4">Pilih</th>
                            <th class="p-4">Username PPPoE</th>
                            <th class="p-4">Password PPPoE *</th>
                            <th class="p-4">Profil MikroTik</th>
                            <th class="p-4">Status Router</th>
                            <th class="p-4">Nama Pelanggan *</th>
                            <th class="p-4">Telepon / WhatsApp</th>
                            <th class="p-4">Alamat</th>
                            <th class="p-4">Paket *</th>
                            <th class="p-4">Jatuh Tempo *</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($records as $i => $record)
                            @php($exists = !empty($record['existing_customer_id']))

                            <tr class="{{ $exists ? 'bg-slate-50/80 dark:bg-slate-800/30' : '' }}">
                                <td class="p-4 align-top">
                                    @if($exists)
                                        <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-bold text-slate-600 dark:bg-slate-700 dark:text-slate-300">Sudah ada</span>
                                    @else
                                        <input type="checkbox"
                                               name="customers[{{ $i }}][selected]"
                                               value="1"
                                               checked
                                               data-import-checkbox
                                               class="mt-1 h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-950">
                                    @endif
                                </td>

                                <td class="p-4 align-top">
                                    <div class="font-bold text-slate-800 dark:text-slate-100">{{ $record['username'] }}</div>
                                    <input type="hidden" name="customers[{{ $i }}][pppoe_username]" value="{{ $record['username'] }}">

                                    @if($exists)
                                        <a href="{{ route('customers.edit', $record['existing_customer_id']) }}"
                                           class="mt-1 inline-block text-xs font-bold text-cyan-600 hover:underline dark:text-cyan-400">
                                            Lihat pelanggan
                                        </a>
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
                                           placeholder="Wajib diisi manual"
                                           {{ $exists ? 'disabled' : 'required' }}
                                           class="min-w-52 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 disabled:cursor-not-allowed disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:disabled:bg-slate-800">
                                </td>

                                <td class="p-4 align-top">
                                    <input type="text"
                                           name="customers[{{ $i }}][phone]"
                                           placeholder="08..."
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
                                            <option value="{{ $package->id }}" {{ $record['profile'] === $package->mikrotik_profile ? 'selected' : '' }}>
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
@endsection
