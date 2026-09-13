@extends('layouts.app')

@section('title', 'Import Pelanggan Excel')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('customers.index') }}"
           class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
            ←
        </a>

        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                Import Pelanggan dari Excel
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Data disimpan ke billing saja. Tidak ada akun PPPoE yang dikirim ke MikroTik saat import.
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-300">
            <p class="font-bold">Import belum dapat diproses:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                <li>Gunakan template agar header kolom sesuai.</li>
                <li>Pilih <strong>Wilayah Tujuan Import</strong>; seluruh pelanggan dalam file akan disimpan ke wilayah tersebut.</li>
                <li>Router dan package harus sama persis dengan nama router/paket yang aktif.</li>
                <li><strong>monthly_price_override</strong> opsional; kosongkan untuk memakai harga paket, atau isi angka Rupiah tanpa pemisah, misalnya <code>150000</code>.</li>
                <li><strong>tax_mode</strong> wajib diisi dengan salah satu nilai: <code>none</code>, <code>inclusive</code>, atau <code>exclusive</code>.</li>
                <li>Password PPPoE minimal enam karakter dan tidak boleh hanya berupa tanda bintang.</li>
                <li>Username PPPoE yang sudah ada atau duplikat dalam file akan dilewati.</li>
                <li>Pelanggan berhasil import mendapat status <strong>Belum Push</strong>.</li>
                <li>Import ini tidak membuat atau mengubah PPPoE secret di MikroTik.</li>
            </ul>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200">
            <p class="font-bold">Baris yang tidak diimpor:</p>
            <ul class="mt-2 max-h-56 list-disc space-y-1 overflow-y-auto pl-5">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-6 rounded-xl border border-cyan-200 bg-cyan-50 p-4 text-sm text-cyan-800 dark:border-cyan-900/60 dark:bg-cyan-950/30 dark:text-cyan-200">
            <p class="font-bold">Aturan import</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                <li>Gunakan template agar header kolom sesuai.</li>
                <li>Pilih <strong>Wilayah Tujuan Import</strong>; seluruh pelanggan dalam file akan disimpan ke wilayah tersebut.</li>
                <li>Router dan package harus sama persis dengan nama router/paket yang aktif.</li>
                <li><strong>monthly_price_override</strong> opsional; kosongkan untuk memakai harga paket, atau isi angka Rupiah tanpa pemisah, misalnya <code>150000</code>.</li>
                <li><strong>tax_mode</strong> wajib diisi dengan salah satu nilai: <code>none</code>, <code>inclusive</code>, atau <code>exclusive</code>.</li>
                <li>Password PPPoE minimal enam karakter dan tidak boleh hanya berupa tanda bintang.</li>
                <li>Username PPPoE yang sudah ada atau duplikat dalam file akan dilewati.</li>
                <li>Pelanggan berhasil import mendapat status <strong>Belum Push</strong>.</li>
                <li>Import ini tidak membuat atau mengubah PPPoE secret di MikroTik.</li>
            </ul>
        </div>

        <form method="POST"
              action="{{ route('customers.import.excel.store') }}"
              enctype="multipart/form-data"
              class="space-y-5">
            @csrf

              <label class="block">
                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">
                    Wilayah Tujuan Import <span class="text-rose-500">*</span>
                </span>

                <select name="area_id"
                        required
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    <option value="">Pilih wilayah tujuan import</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}"
                                @selected((string) old('area_id') === (string) $area->id)>
                            {{ $area->code }} — {{ $area->name }}
                        </option>
                    @endforeach
                </select>

                <span class="mt-2 block text-xs text-slate-400">
                    Semua pelanggan dalam file akan disimpan ke wilayah yang dipilih.
                </span>
            </label>

            <label class="block">
                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">
                    File Excel
                </span>

                <input type="file"
                       name="file"
                       accept=".xlsx,.xls,.csv"
                       required
                       class="mt-2 block w-full cursor-pointer rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-cyan-500 file:px-4 file:py-2 file:text-sm file:font-bold file:text-slate-950 hover:file:bg-cyan-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">

                <span class="mt-2 block text-xs text-slate-400">
                    Mendukung .xlsx, .xls, dan .csv. Ukuran maksimum 5 MB.
                </span>
            </label>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('customers.template.excel') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-600 transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    ▦ Download Template Excel
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-cyan-500 px-5 py-3 text-sm font-black text-slate-950 shadow-sm shadow-cyan-500/20 transition hover:bg-cyan-400 focus:outline-none focus:ring-4 focus:ring-cyan-300/40">
                    ↑ Import ke Billing
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
