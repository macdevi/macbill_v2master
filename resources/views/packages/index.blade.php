@extends('layouts.app')

@section('title', 'Paket Internet')

@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-cyan-600 dark:text-cyan-400">
                Manajemen Layanan
            </div>
            <h1 class="mt-1 text-2xl font-black text-slate-900 dark:text-white sm:text-3xl">
                Paket Internet
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Atur harga, kecepatan, dan profile MikroTik pelanggan.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @if ($routers->count())
                <a href="{{ route('packages.import', $routers->first()) }}"
                   class="inline-flex min-h-11 items-center justify-center rounded-xl border border-cyan-300 bg-cyan-50 px-4 py-2.5 text-sm font-bold text-cyan-700 shadow-sm transition hover:bg-cyan-100 dark:border-cyan-500/30 dark:bg-cyan-400/10 dark:text-cyan-300 dark:hover:bg-cyan-400/20">
                    Import MikroTik
                </a>
            @endif

            <a href="{{ route('packages.create') }}"
               class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-black text-white shadow-sm transition hover:brightness-110"
               style="background:#0891b2;box-shadow:0 4px 12px rgba(8,145,178,.22);">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                Tambah Paket
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('sync'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            {{ $errors->first('sync') }}
        </div>
    @endif

    <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">
            Total <span class="font-black text-slate-900 dark:text-white">{{ $packages->total() }}</span> paket
        </div>

        @if ($routers->count())
            <div class="text-xs text-slate-500 dark:text-slate-400">
                {{ $routers->count() }} router aktif
            </div>
        @endif
    </div>


    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($packages as $package)
            @php
                $hasBurst = filled($package->burst_limit)
                    || filled($package->burst_threshold)
                    || filled($package->burst_time);
            @endphp

            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="truncate text-base font-black text-slate-900 dark:text-white">
                            {{ $package->name }}
                        </h2>

                        <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">
                            {{ $package->mikrotik_profile ?: 'Profile belum diatur' }}
                        </p>
                    </div>

                    <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide {{ $package->active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                        {{ $package->active ? 'Aktif' : 'Off' }}
                    </span>
                </div>

                <div class="mt-3 flex items-end justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
                            Rp {{ number_format((float) $package->monthly_price, 0, ',', '.') }}
                        </div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">
                            per bulan
                        </div>
                    </div>

                    <div class="flex shrink-0 overflow-hidden rounded-xl border border-slate-200 text-center text-xs dark:border-slate-700">
                        <div class="min-w-15 bg-cyan-50 px-2 py-1.5 dark:bg-cyan-400/10">
                            <div class="font-black text-cyan-700 dark:text-cyan-300">
                                ↓ {{ $package->download_speed }}
                            </div>
                            <div class="mt-0.5 text-[9px] font-bold uppercase tracking-wide text-cyan-600/80 dark:text-cyan-300/80">
                                Mbps
                            </div>
                        </div>

                        <div class="min-w-15 border-l border-slate-200 bg-violet-50 px-2 py-1.5 dark:border-slate-700 dark:bg-violet-400/10">
                            <div class="font-black text-violet-700 dark:text-violet-300">
                                ↑ {{ $package->upload_speed }}
                            </div>
                            <div class="mt-0.5 text-[9px] font-bold uppercase tracking-wide text-violet-600/80 dark:text-violet-300/80">
                                Mbps
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                    <details class="group min-w-0">
                        <summary class="cursor-pointer list-none text-xs font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                            <span class="inline-flex items-center gap-1">
                                Selengkapnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </span>
                        </summary>

                        <div class="mt-2 w-56 space-y-1.5 rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-[11px] text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <div class="flex justify-between gap-3">
                                <span>Priority</span>
                                <span class="font-bold">{{ $package->priority ?? 8 }}</span>
                            </div>

                            @if ($hasBurst)
                                <div class="border-t border-slate-200 pt-1.5 dark:border-slate-700">
                                    @if (filled($package->burst_limit))
                                        <div class="flex justify-between gap-3">
                                            <span>Burst</span>
                                            <span class="max-w-[65%] break-all text-right font-bold">{{ $package->burst_limit }}</span>
                                        </div>
                                    @endif

                                    @if (filled($package->burst_threshold))
                                        <div class="mt-1 flex justify-between gap-3">
                                            <span>Threshold</span>
                                            <span class="max-w-[65%] break-all text-right font-bold">{{ $package->burst_threshold }}</span>
                                        </div>
                                    @endif

                                    @if (filled($package->burst_time))
                                        <div class="mt-1 flex justify-between gap-3">
                                            <span>Waktu</span>
                                            <span class="max-w-[65%] break-all text-right font-bold">{{ $package->burst_time }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </details>

                    <div class="flex shrink-0 items-center gap-1">
                        <a href="{{ route('packages.edit', $package) }}"
                           class="inline-flex h-8 items-center justify-center rounded-lg border border-cyan-200 bg-cyan-50 px-2.5 text-[11px] font-bold text-cyan-700 transition hover:bg-cyan-100 dark:border-cyan-500/30 dark:bg-cyan-400/10 dark:text-cyan-300 dark:hover:bg-cyan-400/20">
                            Edit
                        </a>

                        @if ($routers->count())
                            <form method="POST" action="{{ route('packages.sync', $package) }}">
                                @csrf
                                <input type="hidden" name="router_id" value="{{ $routers->first()->id }}">

                                <button type="submit"
                                        title="Sinkronkan ke {{ $routers->count() }} router aktif"
                                        class="inline-flex h-8 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300 dark:hover:bg-emerald-500/20">
                                    Sync
                                </button>
                            </form>
                        @endif

                        <form method="POST"
                              action="{{ route('packages.destroy', $package) }}"
                              onsubmit="return confirm('Hapus paket {{ addslashes($package->name) }}?')">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    title="Hapus paket"
                                    class="inline-flex h-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 px-2.5 text-[11px] font-bold text-rose-700 transition hover:bg-rose-100 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-10 text-center dark:border-slate-700 dark:bg-slate-900 sm:col-span-2 xl:col-span-3">
                <h2 class="text-base font-black text-slate-900 dark:text-white">
                    Belum ada paket internet
                </h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Tambahkan paket baru atau import profile dari MikroTik.
                </p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <a href="{{ route('packages.create') }}"
                       class="inline-flex min-h-10 items-center justify-center rounded-xl bg-cyan-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-cyan-700">
                        Tambah Paket
                    </a>

                    @if ($routers->count())
                        <a href="{{ route('packages.import', $routers->first()) }}"
                           class="inline-flex min-h-10 items-center justify-center rounded-xl border border-cyan-300 bg-cyan-50 px-4 py-2 text-sm font-bold text-cyan-700 transition hover:bg-cyan-100 dark:border-cyan-500/30 dark:bg-cyan-400/10 dark:text-cyan-300">
                            Import MikroTik
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>


    @if ($packages->hasPages())
        <div class="pt-1">
            {{ $packages->links() }}
        </div>
    @endif
</div>
@endsection
