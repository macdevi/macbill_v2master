@extends('layouts.app')

@section('title', 'Import MikroTik')

@section('content')
<div class="mx-auto max-w-2xl">
    <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-cyan-100 bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700 dark:border-cyan-400/15 dark:bg-cyan-400/10 dark:text-cyan-300">
                <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                MikroTik
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                Import Pelanggan
            </h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Ambil daftar akun PPPoE dari MikroTik, lalu konfirmasi data pelanggan sebelum disimpan.
            </p>
        </div>

        <a href="{{ route('customers.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
            <span aria-hidden="true">←</span>
            Kembali
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-slate-800 dark:bg-slate-800/40 sm:px-6">
            <h2 class="text-sm font-extrabold text-slate-800 dark:text-slate-100">Pilih Router</h2>
            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">
                Sistem hanya membaca daftar akun PPPoE. Password PPPoE tidak ditampilkan dan tidak diimpor.
            </p>
        </div>

        <form method="POST" action="{{ route('customers.import.mikrotik.preview') }}" class="p-5 sm:p-6">
            @csrf

            <div>
                <label for="router_id" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
                    Router MikroTik
                </label>
                <select id="router_id" name="router_id" required
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm font-medium text-slate-800 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('router_id') border-rose-400 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    <option value="">Pilih router aktif</option>
                    @foreach($routers as $router)
                        <option value="{{ $router->id }}" {{ old('router_id') == $router->id ? 'selected' : '' }}>
                            {{ $router->name }} — {{ $router->host }}:{{ $router->port }}
                        </option>
                    @endforeach
                </select>
                @error('router_id')
                    <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-5">
                <label for="area_id" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
                    Wilayah Tujuan
                </label>
                <select id="area_id" name="area_id" required
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm font-medium text-slate-800 shadow-sm outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100 @error('area_id') border-rose-400 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    <option value="">Pilih wilayah tujuan import</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>
                            {{ $area->name }} — {{ $area->code }}
                        </option>
                    @endforeach
                </select>
                @error('area_id')
                    <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-slate-400">
                    Semua pelanggan pada batch ini akan disimpan ke wilayah yang dipilih.
                </p>
            </div>

            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-200">
                <strong>Catatan:</strong> Username PPPoE hanya menjadi identitas koneksi. Nama pelanggan wajib diisi manual pada halaman konfirmasi berikutnya.
            </div>

            <div class="mt-7 flex justify-end border-t border-slate-100 pt-5 dark:border-slate-800">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-cyan-300/40 dark:bg-cyan-500 dark:hover:bg-cyan-400">
                    Ambil Akun PPPoE
                    <span aria-hidden="true">→</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
