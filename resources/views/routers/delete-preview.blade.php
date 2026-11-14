@extends('layouts.app')
@section('title', 'Tinjau Hapus Router')
@section('content')
@include('routers.partials.theme')
@php
    $counts = $plan['counts'];
    $summary = [
        ['customers', 'Pelanggan', 'users'],
        ['invoices', 'Invoice', 'invoice'],
        ['payments', 'Pembayaran invoice', 'payment'],
        ['internet_packages', 'Paket dihapus', 'package'],
    ];
    $details = [
        'installation_payments' => 'Pembayaran pasang baru',
        'installation_cancellations' => 'Riwayat pembatalan',
        'installations' => 'Pasang baru',
        'credit_balance_transactions' => 'Transaksi saldo',
        'package_sync_states' => 'Catatan sync',
        'internet_package_router' => 'Hubungan paket dan router',
        'routers' => 'Pendaftaran router',
    ];
@endphp
<div class="router-preview-page mx-auto max-w-4xl space-y-6">
    <a href="{{ route('routers.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-cyan-700 dark:text-slate-300">
        @include('routers.partials.icon', ['name' => 'back', 'iconClass' => 'h-4 w-4']) Kembali ke MikroTik
    </a>
    <header>
        <p class="text-xs font-semibold uppercase tracking-widest text-rose-700 dark:text-rose-400">Zona penghapusan</p>
        <h1 class="mt-1 text-2xl font-black text-slate-900 dark:text-white">Hapus router {{ $router->name }}</h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Tinjau dampaknya sebelum melanjutkan. Membuka halaman ini tidak menghapus data.</p>
    </header>
    @if ($errors->any())
    <div role="alert" class="rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900 dark:border-rose-800 dark:bg-rose-950 dark:text-rose-200">
        @foreach ($errors->all() as $message)<p>{{ $message }}</p>@endforeach
    </div>
    @endif

    <section class="space-y-4">
        <h2 class="text-base font-bold text-slate-900 dark:text-white">1. Periksa data yang akan dihapus</h2>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            @foreach ($summary as [$key, $label, $icon])
            <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                <div class="mb-3 text-slate-400">@include('routers.partials.icon', ['name' => $icon])</div>
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($counts[$key] ?? 0, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $label }}</p>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400">Semua angka menunjukkan jumlah baris data, bukan nilai uang.</p>
        <details class="rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
            <summary class="cursor-pointer p-4 text-sm font-semibold text-slate-700 dark:text-slate-200">Lihat rincian data lainnya</summary>
            <dl class="divide-y divide-slate-100 px-4 pb-3 dark:divide-slate-800">
                @foreach ($details as $key => $label)
                <div class="flex items-center justify-between gap-4 py-3 text-sm">
                    <dt class="text-slate-600 dark:text-slate-300">{{ $label }}</dt>
                    <dd class="font-semibold text-slate-900 dark:text-white">{{ number_format($counts[$key] ?? 0, 0, ',', '.') }}</dd>
                </div>
                @endforeach
            </dl>
        </details>
    </section>

    <div class="flex gap-3 rounded-xl border border-cyan-200 bg-cyan-50 p-4 text-sm text-cyan-900 dark:border-cyan-900 dark:bg-cyan-950 dark:text-cyan-200">
        @include('routers.partials.icon', ['name' => 'shield'])
        <div class="space-y-1">
            <p class="font-semibold">{{ $plan['preserved_packages'] }} paket terkait tetap disimpan</p>
            <p>Paket yang masih dipakai atau dirujuk router lain tidak ikut dihapus. Konfigurasi dan akun PPPoE pada perangkat MikroTik tidak disentuh.</p>
        </div>
    </div>

    <section class="overflow-hidden rounded-2xl border border-rose-300 bg-white dark:border-rose-900 dark:bg-slate-900">
        <div class="flex gap-3 border-b border-rose-200 bg-rose-50 p-5 text-rose-900 dark:border-rose-900 dark:bg-rose-950 dark:text-rose-200">
            @include('routers.partials.icon', ['name' => 'alert'])
            <div>
                <h2 class="font-bold">2. Konfirmasi penghapusan permanen</h2>
                <p class="mt-1 text-sm">Data pada daftar di atas akan dihapus. Tidak tersedia tombol Undo. Pemulihan membutuhkan restore backup.</p>
            </div>
        </div>
        <form method="post" action="{{ route('routers.destroy', $router) }}" class="space-y-5 p-5"
              onsubmit="return confirm('Hapus permanen router dan seluruh data yang tercantum?')">
            @csrf
            @method('DELETE')
            <input type="hidden" name="fingerprint" value="{{ $plan['fingerprint'] }}">
            <input type="hidden" name="approval" value="{{ $approval }}">
            <input type="hidden" name="expires" value="{{ $expires }}">
            <label class="block text-sm text-slate-700 dark:text-slate-200">
                Ketik <span class="rounded bg-slate-100 px-2 py-1 font-mono font-semibold dark:bg-slate-800">{{ $router->name }}</span> untuk melanjutkan
                <input name="confirm_name" type="text" required maxlength="100" autocomplete="off" spellcheck="false"
                       placeholder="Nama router, harus sama persis"
                       class="mt-3 w-full rounded-xl border border-slate-300 bg-white p-3 text-slate-900 outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-200 dark:border-slate-600 dark:bg-slate-950 dark:text-white">
            </label>
            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 text-sm text-slate-700 dark:border-slate-700 dark:text-slate-200">
                <input type="checkbox" name="acknowledge" value="1" required class="mt-1 h-4 w-4 shrink-0">
                <span>Saya memahami pelanggan, invoice, pembayaran, saldo, dan riwayat terkait pada daftar ini akan dihapus permanen dari database.</span>
            </label>
            <p class="flex items-start gap-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                @include('routers.partials.icon', ['name' => 'shield', 'iconClass' => 'h-4 w-4 shrink-0'])
                <span>Backup wajib berhasil sebelum penghapusan. Konfirmasi berlaku 10 menit. Jika data berubah, tinjau ulang halaman ini.</span>
            </p>
            <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-5 dark:border-slate-800">
                <a href="{{ route('routers.index') }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 dark:border-slate-600 dark:text-slate-200">Batal, kembali</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-rose-700 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-800 focus:ring-2 focus:ring-rose-400">
                    @include('routers.partials.icon', ['name' => 'trash']) Backup dan hapus permanen
                </button>
            </div>
        </form>
    </section>
    <p class="text-xs leading-relaxed text-slate-500 dark:text-slate-400">Berkas unggahan bukti bayar tidak ikut dihapus. Riwayat yang sudah kehilangan seluruh penanda kepemilikan tidak dihapus berdasarkan tebakan.</p>
</div>
@endsection
