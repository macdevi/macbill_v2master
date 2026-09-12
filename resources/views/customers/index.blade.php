@extends('layouts.app')

@section('title', 'Pelanggan')

@section('content')
<div class="space-y-5"
     x-data="{ detailOpen: false, detailId: null, search: '' }"
     @keydown.escape.window="detailOpen = false">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-cyan-500/10 text-xl text-cyan-600 ring-1 ring-cyan-500/20 dark:bg-cyan-400/10 dark:text-cyan-300">
                👥
            </div>
            <div class="min-w-0">
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Pelanggan</h1>
                <p class="mt-0.5 truncate text-sm text-slate-500 dark:text-slate-400">Kelola data pelanggan dan akun PPPoE.</p>
            </div>
        </div>

        <div class="flex w-full items-center gap-2 sm:w-auto">
            <details class="relative min-w-0 flex-1 sm:flex-none">
                <summary class="flex h-10 w-full cursor-pointer list-none items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-cyan-700 dark:hover:bg-slate-800 dark:hover:text-cyan-300 sm:w-auto">
                    <span>⇅</span>
                    <span>Import / Export</span>
                    <span class="text-[10px]">▼</span>
                </summary>
                <div class="absolute right-0 z-50 mt-2 w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900">
                    <a href="{{ route('customers.import.mikrotik') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-cyan-400/10 dark:hover:text-cyan-300">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-cyan-100 text-lg dark:bg-cyan-400/15">📡</span>
                        <span class="min-w-0">
                            <span class="block">Import dari MikroTik</span>
                            <span class="mt-0.5 block truncate text-xs font-normal text-slate-400">Ambil akun PPPoE dari router</span>
                        </span>
                    </a>
                    <div class="my-1.5 border-t border-slate-100 dark:border-slate-800"></div>
                    <a href="{{ route('customers.import.excel') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-slate-800">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-cyan-100 text-lg text-cyan-700 dark:bg-cyan-950/50 dark:text-cyan-300">↑</span>
                        <span class="min-w-0">
                            <span class="block">Import dari Excel</span>
                            <span class="mt-0.5 block truncate text-xs font-normal text-slate-400">Upload data pelanggan</span>
                        </span>
                    </a>
                    <a href="{{ route('customers.export.excel') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-cyan-400/10 dark:hover:text-cyan-300">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg dark:bg-slate-800">↓</span>
                        <span class="min-w-0">
                            <span class="block">Export ke Excel</span>
                            <span class="mt-0.5 block truncate text-xs font-normal text-slate-400">Unduh seluruh data pelanggan</span>
                        </span>
                    </a>
                    <a href="{{ route('customers.template.excel') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-cyan-400/10 dark:hover:text-cyan-300">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg dark:bg-slate-800">▦</span>
                        <span class="min-w-0">
                            <span class="block">Download Template Excel</span>
                            <span class="mt-0.5 block truncate text-xs font-normal text-slate-400">Format untuk import pelanggan</span>
                        </span>
                    </a>
                </div>
            </details>

            <a href="{{ route('customers.create') }}"
               class="inline-flex h-10 shrink-0 items-center justify-center gap-1 rounded-xl bg-cyan-500 px-3 text-sm font-black text-slate-950 shadow-sm shadow-cyan-500/20 transition hover:bg-cyan-400 focus:outline-none focus:ring-4 focus:ring-cyan-300/40">
                <span class="text-base leading-none">+</span>
                <span class="hidden xs:inline">Tambah</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-200">
            <span class="text-base">✓</span><span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-200">
            <span class="text-base">!</span><span>{{ session('error') }}</span>
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-100 p-4 dark:border-slate-800">
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-black text-slate-800 dark:text-slate-100">Total Pelanggan: {{ $customers->count() }}</h2>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Cari berdasarkan nama, kode, atau akun PPPoE.</p>
                </div>
            </div>

            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-slate-400" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                </span>
                <input id="customer-livefind"
                       type="search"
                       x-model="search"
                       placeholder="Cari nama, kode, atau PPPoE"
                       class="block h-11 w-full rounded-xl border border-slate-200 bg-slate-50 py-0 pl-11 pr-11 text-left text-sm font-medium text-slate-700 outline-none transition placeholder:text-left placeholder:font-normal placeholder:text-slate-400 focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
                <button type="button"
                        x-cloak
                        x-show="search.length > 0"
                        @click="search = ''"
                        class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-lg font-bold leading-none text-slate-400 transition hover:text-slate-700 focus:outline-none dark:hover:text-slate-100"
                        aria-label="Hapus pencarian">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg transition hover:bg-slate-200 dark:hover:bg-slate-700">×</span>
                </button>
            </div>
        </div>

        <div class="space-y-3 bg-slate-50 p-3 dark:bg-slate-950/40">
            @forelse($customers as $c)
                @php
                    $packageName = optional($c->internetPackage)->name ?: 'Belum ada paket';
                    $effectivePrice = $c->effective_monthly_price;
                    $isActive = $c->status === 'active';
                    $isIsolated = $c->status === 'isolated';
                    $isInactive = $c->status === 'inactive';
                @endphp

                <article x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())"
                         x-transition.opacity
                         class="rounded-2xl border p-4 shadow-sm transition
                            {{ $isIsolated
                                ? 'border-rose-200 bg-rose-50/30 hover:border-rose-300 dark:border-rose-400/20 dark:bg-rose-400/[0.04]'
                                : ($isInactive
                                    ? 'border-slate-200 bg-slate-100/70 hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900/60'
                                    : 'border-slate-200 bg-white hover:border-cyan-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-cyan-500/50') }}">
                    <div class="flex gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                            {{ $isIsolated ? 'bg-rose-500/10 text-rose-600 dark:text-rose-300' : ($isInactive ? 'bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400' : 'bg-cyan-500/10 text-cyan-700 dark:text-cyan-300') }}">
                            <span class="text-base font-black">{{ strtoupper(substr($c->name ?: '?', 0, 1)) }}</span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex min-w-0 items-start gap-2">
                                <div class="min-w-0 flex-1">
                                    <h2 class="truncate text-sm font-black text-slate-800 dark:text-slate-100">{{ $c->name ?: '-' }}</h2>
                                    <p class="mt-0.5 truncate font-mono text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                        {{ $c->customer_code ?: '-' }}
                                        <span class="px-1 text-slate-300 dark:text-slate-600">•</span>
                                        {{ $c->pppoe_username ?: '-' }}
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-start gap-1.5">
                                    <div class="pt-0.5 text-right">
                                        @if($isIsolated)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-500/10 px-2 py-0.5 text-[10px] font-bold text-rose-600 ring-1 ring-rose-500/20 dark:text-rose-400"><span class="h-1 w-1 rounded-full bg-rose-500"></span>Terisolir</span>
                                        @elseif($isInactive)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-500/10 px-2 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-500/20 dark:text-slate-300"><span class="h-1 w-1 rounded-full bg-slate-400"></span>Nonaktif</span>
                                        @elseif($c->realtime_online)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold text-emerald-600 ring-1 ring-emerald-500/20 dark:text-emerald-400"><span class="h-1 w-1 rounded-full bg-emerald-500"></span>Online</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-500/10 px-2 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-500/20 dark:text-slate-300"><span class="h-1 w-1 rounded-full bg-slate-400"></span>Offline</span>
                                        @endif
                                    </div>

                                    <div class="relative"
                                         x-data="{ open: false }"
                                         @click.outside="open = false"
                                         @keydown.escape.window="open = false">
                                        <button type="button"
                                                @click="open = !open"
                                                :aria-expanded="open.toString()"
                                                aria-haspopup="menu"
                                                aria-label="Buka aksi pelanggan"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg text-lg font-bold leading-none text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-300/30 dark:hover:bg-slate-800 dark:hover:text-slate-200 dark:focus:ring-cyan-400/15">⋮</button>

                                        <div x-cloak
                                             x-show="open"
                                             x-transition.origin.top.right
                                             role="menu"
                                             class="absolute right-0 z-50 mt-1 w-44 overflow-hidden rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                                            <button type="button"
                                                    @click="detailId = {{ $c->id }}; detailOpen = true; open = false"
                                                    role="menuitem"
                                                    class="flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-slate-800">Detail</button>
                                            <a href="{{ route('customers.edit', $c) }}"
                                               role="menuitem"
                                               class="flex items-center rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-slate-800">Edit</a>

                                            @if($isActive)
                                                <form method="POST" action="{{ route('customers.isolate', $c) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            role="menuitem"
                                                            onclick="return confirm('Isolir pelanggan ini secara manual?')"
                                                            class="flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/30">Isolir</button>
                                                </form>
                                            @elseif($isIsolated)
                                                <form method="POST" action="{{ route('customers.activate', $c) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            role="menuitem"
                                                            onclick="return confirm('Aktifkan kembali pelanggan terisolir ini?')"
                                                            class="flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-emerald-600 transition hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-950/30">Aktifkan</button>
                                                </form>
                                            @else
                                                <div class="px-3 py-2.5 text-xs font-semibold text-slate-400">Status nonaktif perlu ditinjau melalui Edit.</div>
                                            @endif

                                            <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                                            <form method="POST" action="{{ route('customers.destroy', $c) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        role="menuitem"
                                                        onclick="return confirm('Hapus pelanggan ini dari billing? Data terkait dapat ikut terhapus dan tidak dapat dipulihkan.')"
                                                        class="flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm font-bold text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/30">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-1.5 text-[11px]">
                                <span class="rounded-md bg-cyan-500/10 px-2 py-1 font-bold text-cyan-700 dark:bg-cyan-400/10 dark:text-cyan-300">{{ $packageName }}</span>
                                <span class="rounded-md bg-slate-100 px-2 py-1 font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Rp {{ number_format((float) $effectivePrice, 0, ',', '.') }}/bln</span>
                                @if($c->monthly_price_override !== null)
                                    <span class="rounded-md bg-amber-100 px-2 py-1 font-bold text-amber-800 dark:bg-amber-400/15 dark:text-amber-200">Harga khusus</span>
                                @endif
                                <span class="text-slate-300 dark:text-slate-600">•</span>
                                <span class="font-semibold text-slate-500 dark:text-slate-400">Tagihan tgl {{ $c->due_day ?: '-' }}</span>
                                @if($isActive && $c->realtime_online && $c->realtime_uptime)
                                    <span class="text-slate-300 dark:text-slate-600">•</span>
                                    <span class="font-mono font-semibold text-slate-500 dark:text-slate-400">↑ {{ $c->realtime_uptime }}</span>
                                @endif
                            </div>

                            @if($c->realtime_online && $c->realtime_address)
                                <p class="mt-2 truncate font-mono text-[11px] text-slate-400 dark:text-slate-500">IP aktif: {{ $c->realtime_address }}</p>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="px-5 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-500/10 text-2xl">👥</div>
                    <h2 class="mt-4 font-bold text-slate-800 dark:text-slate-100">Belum ada pelanggan</h2>
                    <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">Tambahkan pelanggan secara manual atau import akun PPPoE dari MikroTik.</p>
                    <a href="{{ route('customers.create') }}"
                       class="mt-5 inline-flex rounded-xl bg-cyan-500 px-4 py-2.5 text-sm font-bold text-slate-950 transition hover:bg-cyan-400">+ Tambah Pelanggan</a>
                </div>
            @endforelse
        </div>
    </section>

    <div x-cloak
         x-show="detailOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         role="dialog"
         aria-modal="true"
         aria-label="Detail pelanggan">
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="detailOpen = false"></div>
        <div class="relative z-10 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
            @foreach($customers as $c)
                <div x-show="detailId === {{ $c->id }}" x-cloak>
                    <div class="flex items-start justify-between border-b border-slate-100 p-5 dark:border-slate-800">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-cyan-500/10 text-lg font-black text-cyan-700 dark:text-cyan-300">{{ strtoupper(substr($c->name ?: '?', 0, 1)) }}</div>
                            <div class="min-w-0">
                                <h2 class="truncate text-lg font-black text-slate-900 dark:text-white">{{ $c->name ?: '-' }}</h2>
                                <p class="mt-0.5 truncate text-xs text-slate-400">{{ $c->customer_code ?: '-' }} · {{ $c->pppoe_username ?: '-' }}</p>
                            </div>
                        </div>
                        <button type="button"
                                @click="detailOpen = false"
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg font-bold text-slate-500 transition hover:bg-rose-100 hover:text-rose-600 dark:bg-slate-800 dark:text-slate-300"
                                aria-label="Tutup detail pelanggan">×</button>
                    </div>

                    <div class="p-5">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Paket Internet</div>
                                <div class="mt-1.5 text-sm font-bold text-slate-700 dark:text-slate-100">{{ optional($c->internetPackage)->name ?: '-' }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Biaya Ditagihkan</div>
                                <div class="mt-1.5 text-sm font-bold text-cyan-700 dark:text-cyan-300">Rp {{ number_format((float) $c->effective_monthly_price, 0, ',', '.') }}</div>
                                @if($c->monthly_price_override !== null)
                                    <div class="mt-1 text-[11px] font-semibold text-amber-700 dark:text-amber-300">Harga khusus</div>
                                @endif
                            </div>
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Status Akun</div>
                                <div class="mt-1.5 text-sm font-bold text-slate-700 dark:text-slate-100">{{ ucfirst($c->status ?: 'nonaktif') }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Koneksi Realtime</div>
                                <div class="mt-1.5 text-sm font-bold text-slate-700 dark:text-slate-100">
                                    @if($c->status === 'isolated')
                                        Terisolir
                                    @elseif($c->realtime_online)
                                        Online{{ $c->realtime_uptime ? ' · ' . $c->realtime_uptime : '' }}
                                    @else
                                        Offline
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100">Informasi Lengkap</h3>
                            <dl class="mt-3 overflow-hidden rounded-2xl border border-slate-100 text-sm dark:border-slate-800">
                                <div class="grid grid-cols-3 gap-3 border-b border-slate-100 px-4 py-3 dark:border-slate-800">
                                    <dt class="text-slate-400">Router</dt>
                                    <dd class="col-span-2 font-semibold text-slate-700 dark:text-slate-200">{{ optional($c->router)->name ?: '-' }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-3 border-b border-slate-100 px-4 py-3 dark:border-slate-800">
                                    <dt class="text-slate-400">Telepon</dt>
                                    <dd class="col-span-2 break-words font-semibold text-slate-700 dark:text-slate-200">{{ $c->phone ?: '-' }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-3 border-b border-slate-100 px-4 py-3 dark:border-slate-800">
                                    <dt class="text-slate-400">Alamat</dt>
                                    <dd class="col-span-2 whitespace-pre-line break-words font-semibold text-slate-700 dark:text-slate-200">{{ $c->address ?: '-' }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-3 border-b border-slate-100 px-4 py-3 dark:border-slate-800">
                                    <dt class="text-slate-400">Jatuh Tempo</dt>
                                    <dd class="col-span-2 font-semibold text-slate-700 dark:text-slate-200">Tanggal {{ $c->due_day ?: '-' }} setiap bulan</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-3 border-b border-slate-100 px-4 py-3 dark:border-slate-800">
                                    <dt class="text-slate-400">IP Realtime</dt>
                                    <dd class="col-span-2 break-all font-mono font-semibold text-slate-700 dark:text-slate-200">{{ $c->realtime_address ?: '-' }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-3 px-4 py-3">
                                    <dt class="text-slate-400">Dibuat</dt>
                                    <dd class="col-span-2 font-semibold text-slate-700 dark:text-slate-200">{{ optional($c->created_at)->format('d M Y H:i') ?: '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-slate-100 p-5 dark:border-slate-800">
                        <button type="button"
                                @click="detailOpen = false"
                                class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">Tutup</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
