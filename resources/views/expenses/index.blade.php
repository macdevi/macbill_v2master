@extends('layouts.app')

@section('title', 'Pengeluaran')

@section('content')
<div class="expense-page space-y-5" x-data="{ actionId: null }">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-rose-500/10 text-xl text-rose-600 ring-1 ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300">
                💸
            </div>
            <div class="min-w-0">
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Pengeluaran</h1>
                <p class="mt-0.5 truncate text-sm text-slate-500 dark:text-slate-400">
                    Catat dan pantau biaya operasional bisnis.
                </p>
            </div>
        </div>

        <a href="{{ route('expenses.create') }}"
           class="inline-flex shrink-0 items-center justify-center gap-1 rounded-xl bg-cyan-500 px-4 py-2.5 text-sm font-black text-slate-950 shadow-sm shadow-cyan-500/20 transition hover:bg-cyan-400 focus:outline-none focus:ring-4 focus:ring-cyan-300/40">
            <span class="text-base leading-none">+</span>
            Tambah Pengeluaran
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-200">
            <span class="text-base">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-200">
            <span class="text-base">!</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total bulan ini</div>
            <div class="mt-1.5 text-xl font-black tracking-tight text-rose-600 dark:text-rose-400">
                Rp {{ number_format($monthTotal, 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Transaksi tercatat</div>
            <div class="mt-1.5 text-xl font-black tracking-tight text-slate-900 dark:text-white">{{ number_format($monthCount, 0, ',', '.') }}</div>
            <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pengeluaran aktif bulan ini</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pengeluaran hari ini</div>
            <div class="mt-1.5 text-xl font-black tracking-tight text-slate-900 dark:text-white">
                Rp {{ number_format($todayTotal, 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ now()->translatedFormat('d M Y') }}</div>
        </div>
    </div>

    <details class="group rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900" @if(request()->hasAny(['search', 'category', 'payment_method']) || $status !== 'posted') open @endif>
        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3.5 text-sm font-bold text-slate-700 dark:text-slate-200">
            <span class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-sm dark:bg-slate-800">⌕</span>
                Filter pengeluaran
            </span>
            <span class="text-xs text-slate-400 transition group-open:rotate-180">▼</span>
        </summary>

        <form method="GET" action="{{ route('expenses.index') }}" class="grid gap-3 border-t border-slate-100 p-4 sm:grid-cols-2 lg:grid-cols-6 dark:border-slate-800">
            <div class="sm:col-span-2">
                <label for="expense-search" class="mb-1.5 block text-xs font-bold text-slate-500 dark:text-slate-400">Cari</label>
                <input id="expense-search" type="search" name="search" value="{{ $search }}"
                       placeholder="Judul, vendor, atau keterangan"
                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
            </div>

            <div>
                <label for="expense-month" class="mb-1.5 block text-xs font-bold text-slate-500 dark:text-slate-400">Bulan</label>
                <input id="expense-month" type="month" name="month" value="{{ $month }}"
                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
            </div>

            <div>
                <label for="expense-category" class="mb-1.5 block text-xs font-bold text-slate-500 dark:text-slate-400">Kategori</label>
                <select id="expense-category" name="category"
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
                    <option value="">Semua</option>
                    @foreach($categories as $item)
                        <option value="{{ $item }}" @selected($category === $item)>{{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="expense-method" class="mb-1.5 block text-xs font-bold text-slate-500 dark:text-slate-400">Metode</label>
                <select id="expense-method" name="payment_method"
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
                    <option value="">Semua</option>
                    @foreach($paymentMethods as $key => $label)
                        <option value="{{ $key }}" @selected($paymentMethod === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <div class="min-w-0 flex-1">
                    <label for="expense-status" class="mb-1.5 block text-xs font-bold text-slate-500 dark:text-slate-400">Status</label>
                    <select id="expense-status" name="status"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
                        <option value="posted" @selected($status === 'posted')>Tercatat</option>
                        <option value="voided" @selected($status === 'voided')>Dibatalkan</option>
                        <option value="all" @selected($status === 'all')>Semua</option>
                    </select>
                </div>
                <button type="submit"
                        class="rounded-xl bg-slate-900 px-3 py-2.5 text-sm font-bold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-300 dark:bg-cyan-500 dark:text-slate-950 dark:hover:bg-cyan-400 dark:focus:ring-cyan-400/30">
                    Cari
                </button>
            </div>
        </form>
    </details>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($expenses as $expense)
                <article class="relative p-4 transition hover:bg-cyan-50/40 dark:hover:bg-cyan-400/[0.03]">
                    <div class="flex gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $expense->status === 'posted' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-300' : 'bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                            💸
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex min-w-0 items-start gap-2 pr-8">
                                <div class="min-w-0 flex-1">
                                    <h2 class="truncate text-sm font-black text-slate-800 dark:text-slate-100">{{ $expense->title }}</h2>
                                    <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">
                                        {{ $expense->expense_date->translatedFormat('d M Y') }}
                                        @if($expense->vendor)
                                            <span class="px-1 text-slate-300 dark:text-slate-600">•</span>{{ $expense->vendor }}
                                        @endif
                                    </p>
                                </div>

                                <div class="shrink-0 text-right">
                                    <div class="text-sm font-black {{ $expense->status === 'posted' ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 line-through dark:text-slate-500' }}">
                                        Rp {{ number_format((float) $expense->amount, 0, ',', '.') }}
                                    </div>
                                    @if($expense->status === 'posted')
                                        <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold text-emerald-600 ring-1 ring-emerald-500/20 dark:text-emerald-400">
                                            <span class="h-1 w-1 rounded-full bg-emerald-500"></span>Tercatat
                                        </span>
                                    @else
                                        <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-rose-500/10 px-2 py-0.5 text-[10px] font-bold text-rose-600 ring-1 ring-rose-500/20 dark:text-rose-400">
                                            <span class="h-1 w-1 rounded-full bg-rose-500"></span>Dibatalkan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-1.5 text-[11px]">
                                <span class="rounded-md bg-cyan-500/10 px-2 py-1 font-bold text-cyan-700 dark:bg-cyan-400/10 dark:text-cyan-300">
                                    {{ $expense->category }}
                                </span>
                                <span class="rounded-md bg-slate-100 px-2 py-1 font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $expense->payment_method_label }}
                                </span>
                                <span class="text-slate-300 dark:text-slate-600">•</span>
                                <span class="max-w-full truncate text-slate-500 dark:text-slate-400">
                                    Dicatat oleh <strong class="font-bold text-slate-600 dark:text-slate-300">{{ $expense->createdBy?->name ?? 'Data lama' }}</strong>
                                    @if($expense->createdBy?->role)
                                        <span class="text-slate-400 dark:text-slate-500">({{ str_replace('_', ' ', $expense->createdBy->role) }})</span>
                                    @endif
                                </span>
                            </div>

                            @if($expense->description)
                                <p class="mt-2 line-clamp-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">{{ $expense->description }}</p>
                            @endif

                            @if($expense->status === 'voided' && $expense->voided_at)
                                <p class="mt-2 text-[11px] font-semibold text-rose-500 dark:text-rose-400">
                                    Dibatalkan pada {{ $expense->voided_at->translatedFormat('d M Y, H:i') }}
                                </p>
                            @endif
                        </div>

                        
                        @if($expense->status === 'posted')
                            <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                                <a href="{{ route('expenses.edit', $expense) }}"
                                   class="inline-flex items-center justify-center rounded-lg bg-cyan-50 px-3 py-2 text-xs font-bold text-cyan-700 transition hover:bg-cyan-100 focus:outline-none focus:ring-4 focus:ring-cyan-200/60 dark:bg-cyan-400/10 dark:text-cyan-300 dark:hover:bg-cyan-400/20 dark:focus:ring-cyan-400/15">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('expenses.destroy', $expense) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Batalkan pengeluaran ini? Data tetap tersimpan sebagai riwayat dan tidak lagi dihitung sebagai pengeluaran aktif.')"
                                            class="inline-flex items-center justify-center rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100 focus:outline-none focus:ring-4 focus:ring-rose-200/60 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20 dark:focus:ring-rose-400/15">
                                        Batalkan
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>
                </article>
            @empty
                <div class="px-5 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-500/10 text-2xl">💸</div>
                    <h2 class="mt-4 font-bold text-slate-800 dark:text-slate-100">Belum ada pengeluaran</h2>
                    <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">
                        Tambahkan pengeluaran pertama untuk mulai memantau biaya operasional.
                    </p>
                    <a href="{{ route('expenses.create') }}"
                       class="mt-5 inline-flex rounded-xl bg-cyan-500 px-4 py-2.5 text-sm font-bold text-slate-950 transition hover:bg-cyan-400">
                        + Tambah Pengeluaran
                    </a>
                </div>
            @endforelse
        </div>

        @if($expenses->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 dark:border-slate-800">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
