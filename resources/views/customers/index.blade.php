@extends('layouts.app')

@section('title', 'Pelanggan')

@section('content')
<div class="space-y-6"
     x-data="{ detailOpen: false, detailId: null, selectedId: null, search: '' }"
     @keydown.escape.window="detailOpen = false">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 flex-1 items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-cyan-500/10 text-xl text-cyan-600 ring-1 ring-cyan-500/20 dark:bg-cyan-400/10 dark:text-cyan-300">
                👥
            </div>

            <div class="min-w-0">
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Pelanggan</h1>
                <p class="mt-0.5 truncate text-sm text-slate-500 dark:text-slate-400">
                    Kelola data pelanggan dan akun PPPoE.
                </p>
            </div>

            <a href="{{ route('customers.create') }}"
               class="ml-auto inline-flex shrink-0 items-center justify-center gap-1 rounded-lg bg-cyan-500 px-3 py-1.5 text-xs font-bold text-slate-950 shadow-sm shadow-cyan-500/20 transition hover:bg-cyan-400 focus:outline-none focus:ring-4 focus:ring-cyan-300/40">
                <span class="text-base leading-none">+</span>
                Tambah
            </a>
        </div>

        <div class="flex shrink-0 flex-col gap-2 sm:flex-row sm:items-center">
            <details class="relative">
                <summary class="flex cursor-pointer list-none items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-cyan-700 dark:hover:bg-slate-800 dark:hover:text-cyan-300">
                    <span>⇅</span>
                    Import / Export
                    <span class="text-[10px]">▼</span>
                </summary>

                <div class="absolute right-0 z-30 mt-2 w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900">
                    <a href="{{ route('customers.import.mikrotik') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-cyan-400/10 dark:hover:text-cyan-300">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-100 text-lg dark:bg-cyan-400/15">📡</span>
                        <span>
                            <span class="block">Import dari MikroTik</span>
                            <span class="mt-0.5 block text-xs font-normal text-slate-400">Ambil akun PPPoE dari router</span>
                        </span>
                    </a>

                    <div class="my-1.5 border-t border-slate-100 dark:border-slate-800"></div>

                    <a href="{{ route('customers.import.excel') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-slate-800">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-100 text-lg text-cyan-700 dark:bg-cyan-950/50 dark:text-cyan-300">↑</span>
                        <span><span class="block font-semibold">Import dari Excel</span><span class="mt-0.5 block text-xs text-slate-400 dark:text-slate-500">Upload data pelanggan</span></span>
                    </a>

<a href="{{ route('customers.export.excel') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-cyan-400/10 dark:hover:text-cyan-300">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-lg dark:bg-slate-800">↓</span>
<span><span class="block">Export ke Excel</span><span class="mt-0.5 block text-xs font-normal text-slate-400">Unduh seluruh data pelanggan</span></span>
                    </a>

<a href="{{ route('customers.template.excel') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-cyan-400/10 dark:hover:text-cyan-300">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-lg dark:bg-slate-800">▦</span>
<span><span class="block">Download Template Excel</span><span class="mt-0.5 block text-xs font-normal text-slate-400">Format untuk import pelanggan</span></span>
                    </a>
                </div>
            </details>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
            <div class="mb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-black text-slate-800 dark:text-slate-100">
                        Total Pelanggan: {{ $customers->count() }}
                    </h2>
                </div>
            </div>

            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 z-10 flex items-center pl-4">
                    <svg class="h-5 w-5 text-slate-400"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                </div>

                <input id="customer-livefind"
                       type="search"
                       x-model="search"
                       placeholder="Cari nama pelanggan"
                       class="block h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-12 py-0 text-center text-sm font-medium leading-none text-slate-700 outline-none transition placeholder:text-center placeholder:font-normal placeholder:text-slate-400 focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">

                <button type="button"
                        x-cloak
                        x-show="search.length > 0"
                        @click="search = ''"
                        class="absolute inset-y-0 right-0 z-10 flex w-12 items-center justify-center text-lg font-bold leading-none text-slate-400 transition hover:text-slate-700 focus:outline-none dark:hover:text-slate-100"
                        aria-label="Hapus pencarian">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg transition hover:bg-slate-200 dark:hover:bg-slate-700">×</span>
                </button>
            </div>
        </div>

        <div class="max-h-[calc(100vh-250px)] overflow-auto">
            <table class="min-w-[860px] w-full text-sm">
                <thead>
                    <tr class="sticky top-0 z-10 border-b border-slate-100 bg-slate-50 text-left text-[11px] font-extrabold uppercase tracking-wider text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-800 dark:text-slate-400">
                        <th class="px-5 py-3.5">Nama Pelanggan</th>
                        <th class="px-5 py-3.5">Paket &amp; Bulanan</th>
                        <th class="px-5 py-3.5">Status Internet</th>
                        <th class="px-5 py-3.5">Uptime</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($customers as $c)
                        <tr x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())"
                            x-transition.opacity
                            @click="if (!$event.target.closest('a, button, form, input, select, textarea')) selectedId = {{ $c->id }}"
                            @mouseenter="selectedId = {{ $c->id }}"
                            :style="selectedId === {{ $c->id }} ? 'background-color: rgba(6, 182, 212, 0.14); box-shadow: inset 4px 0 0 #06b6d4;' : ''"
                            class="cursor-pointer transition-colors duration-150">
                            <td class="px-5 py-4 transition-colors duration-150"
                                :style="selectedId === {{ $c->id }} ? 'background-color: rgba(6, 182, 212, 0.14);' : ''">
                                <div class="truncate font-bold text-slate-800 dark:text-slate-100">
                                    {{ $c->name ?: '-' }}
                                </div>
                            </td>

                            <td class="px-5 py-4 transition-colors duration-150"
                                :style="selectedId === {{ $c->id }} ? 'background-color: rgba(6, 182, 212, 0.14);' : ''">
                                  @php
                                      $packageName = optional($c->internetPackage)->name ?: '-';
                                      $packagePrice = optional($c->internetPackage)->monthly_price;
                                      $specialPrice = $c->monthly_price_override;
                                      $effectivePrice = $c->effective_monthly_price;
                                  @endphp

                                  <div class="font-bold leading-tight text-slate-800 dark:text-slate-100">
                                      {{ $packageName }}
                                  </div>

                                  @if($packagePrice !== null || $specialPrice !== null)
                                      <div class="mt-1 text-xs font-bold leading-none text-cyan-600 dark:text-cyan-300">
                                          Rp.{{ number_format((float) $effectivePrice, 0, ',', '.') }}
                                      </div>

                                      @if($specialPrice !== null)
                                          <div class="mt-1 inline-flex items-center rounded-md bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-800 dark:bg-amber-400/15 dark:text-amber-200">
                                              Harga khusus
                                          </div>
                                      @endif
                                  @endif
                            </td>

                            <td class="px-5 py-4 transition-colors duration-150"
                                :style="selectedId === {{ $c->id }} ? 'background-color: rgba(6, 182, 212, 0.14);' : ''">
                                @if($c->status === 'isolated')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-2.5 py-1 text-xs font-bold text-rose-600 ring-1 ring-rose-500/20 dark:text-rose-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                        Terisolir
                                    </span>
                                @elseif($c->realtime_online)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-bold text-emerald-600 ring-1 ring-emerald-500/20 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Online
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-500/10 px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-500/20 dark:text-slate-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        Offline
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 transition-colors duration-150"
                                :style="selectedId === {{ $c->id }} ? 'background-color: rgba(6, 182, 212, 0.14);' : ''">
                                @if($c->realtime_online && $c->realtime_uptime)
                                    <span class="font-mono text-xs font-bold text-slate-600 dark:text-slate-300">
                                        {{ $c->realtime_uptime }}
                                    </span>
                                @else
                                    <span class="font-mono text-xs font-semibold text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="px-5 py-4 transition-colors duration-150"
                                :style="selectedId === {{ $c->id }} ? 'background-color: rgba(6, 182, 212, 0.14);' : ''">
                                <details class="relative inline-block text-left">
                                    <summary class="flex cursor-pointer list-none items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                        Aksi <span class="text-[10px]">▼</span>
                                    </summary>
                                    <div class="absolute right-0 z-40 mt-2 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                                        <button type="button"
                                                @click="detailId = {{ $c->id }}; detailOpen = true"
                                                class="flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                            Detail
                                        </button>
                                        <a href="{{ route('customers.edit', $c) }}"
                                           class="flex items-center rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                            Edit
                                        </a>
                                        @if($c->status === 'active')
                                            <form method="POST" action="{{ route('customers.isolate', $c) }}">
                                                @csrf
                                                <button type="submit"
                                                        onclick="return confirm('Isolir pelanggan ini?')"
                                                        class="flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/30">
                                                    Isolir
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('customers.activate', $c) }}">
                                                @csrf
                                                <button type="submit"
                                                        onclick="return confirm('Aktifkan pelanggan ini?')"
                                                        class="flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-emerald-600 transition hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-950/30">
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @endif
                                        <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                                        <form method="POST" action="{{ route('customers.destroy', $c) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Hapus pelanggan ini dari billing? Data terkait dapat ikut terhapus dan tidak dapat dipulihkan.')"
                                                    class="flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm font-bold text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/30">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </details>
                            </td>
</tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-14 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-500/10 text-2xl">👥</div>
                                    <h3 class="mt-4 font-bold text-slate-800 dark:text-slate-100">Belum ada pelanggan</h3>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                        Tambahkan pelanggan secara manual atau import akun PPPoE dari MikroTik.
                                    </p>
                                    <a href="{{ route('customers.create') }}"
                                       class="mt-5 rounded-xl bg-cyan-500 px-4 py-2.5 text-sm font-bold text-slate-950 transition hover:bg-cyan-400">
                                        + Tambah Pelanggan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


    </div>

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
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-500/10 text-lg font-black text-cyan-700 dark:text-cyan-300">
                                {{ strtoupper(substr($c->name ?: '?', 0, 1)) }}
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-900 dark:text-white">{{ $c->name ?: '-' }}</h2>
                                <p class="mt-0.5 text-xs text-slate-400">Informasi pelanggan dan koneksi PPPoE</p>
                            </div>
                        </div>

                        <button type="button"
                                @click="detailOpen = false"
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-lg font-bold text-slate-500 transition hover:bg-rose-100 hover:text-rose-600 dark:bg-slate-800 dark:text-slate-300"
                                aria-label="Tutup detail pelanggan">
                            ×
                        </button>
                    </div>

                    <div class="p-5">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Akun PPPoE</div>
                                <div class="mt-1.5 break-all font-mono text-sm font-bold text-slate-700 dark:text-slate-100">{{ $c->pppoe_username ?: '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Paket Internet</div>
                                <div class="mt-1.5 text-sm font-bold text-slate-700 dark:text-slate-100">{{ optional($c->internetPackage)->name ?: '-' }}</div>
                            </div>

                              <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                                  <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                                      Biaya Ditagihkan
                                  </div>

                                  <div class="mt-1.5 text-sm font-bold text-cyan-700 dark:text-cyan-300">
                                      Rp {{ number_format((float) $c->effective_monthly_price, 0, ',', '.') }}
                                  </div>

                                  @if($c->monthly_price_override !== null)
                                      <div class="mt-2 border-t border-slate-200 pt-2 text-[11px] dark:border-slate-700">
                                          <div class="flex justify-between gap-3 text-slate-500 dark:text-slate-400">
                                              <span>Harga paket</span>
                                              <span>Rp {{ number_format((float) (optional($c->internetPackage)->monthly_price ?? 0), 0, ',', '.') }}</span>
                                          </div>

                                          <div class="mt-1 flex justify-between gap-3 font-bold text-amber-700 dark:text-amber-300">
                                              <span>Harga khusus</span>
                                              <span>Rp {{ number_format((float) $c->monthly_price_override, 0, ',', '.') }}</span>
                                          </div>
                                      </div>
                                  @else
                                      <p class="mt-1 text-[11px] text-slate-400">
                                          Mengikuti harga paket.
                                      </p>
                                  @endif
                              </div>

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Status Akun</div>
                                <div class="mt-1.5 text-sm font-bold text-slate-700 dark:text-slate-100">{{ ucfirst($c->status ?: 'Nonaktif') }}</div>
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
                                    <dd class="col-span-2 font-semibold text-slate-700 dark:text-slate-200">{{ $c->phone ?: '-' }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-3 border-b border-slate-100 px-4 py-3 dark:border-slate-800">
                                    <dt class="text-slate-400">Alamat</dt>
                                    <dd class="col-span-2 whitespace-pre-line font-semibold text-slate-700 dark:text-slate-200">{{ $c->address ?: '-' }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-3 border-b border-slate-100 px-4 py-3 dark:border-slate-800">
                                    <dt class="text-slate-400">Jatuh Tempo</dt>
                                    <dd class="col-span-2 font-semibold text-slate-700 dark:text-slate-200">Tanggal {{ $c->due_day ?: '-' }} setiap bulan</dd>
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
                                class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            Tutup
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
