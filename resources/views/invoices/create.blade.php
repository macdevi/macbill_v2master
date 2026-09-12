@extends('layouts.app')

@section('title', 'Buat Tagihan')

@section('content')
@php
    $selectedPeriod = old('billing_period', now()->format('Y-m'));

    $periodOptions = collect(range(0, 12))->map(function ($monthsAgo) {
        $date = now()->copy()->startOfMonth()->subMonths($monthsAgo);

        return [
            'value' => $date->format('Y-m'),
            'label' => ucfirst($date->translatedFormat('F Y')),
        ];
    });
@endphp

<div class="mx-auto max-w-5xl pb-24 lg:pb-6">
    <div class="mb-5 flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl font-black tracking-tight text-slate-900 dark:text-white sm:text-2xl">
                Buat Tagihan
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Pilih pelanggan dan bulan tagihan yang akan dibuat.
            </p>
        </div>

        <a
            href="{{ route('invoices.index') }}"
            class="shrink-0 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
        >
            ← Tagihan
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900/70 dark:bg-red-950/30 dark:text-red-200">
            <p class="font-black">Tagihan belum dapat dibuat</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-2 lg:gap-5">
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 bg-slate-50 px-4 py-4 dark:border-slate-800 dark:bg-slate-800/50 sm:px-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-100 text-lg dark:bg-cyan-950/60">
                        👤
                    </div>
                    <div class="min-w-0">
                        <h2 class="font-black text-slate-900 dark:text-white">Satu pelanggan</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Untuk membuat atau menguji tagihan satu pelanggan.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('invoices.generate.manual') }}" class="space-y-4 p-4 sm:p-5">
                @csrf

                <div>
                    <label for="manual_customer_id" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Pelanggan</label>
                    <select
                        id="manual_customer_id"
                        name="customer_id"
                        required
                        class="w-full rounded-xl border-slate-300 bg-white px-3 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                        <option value="">Pilih pelanggan</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((string) old('customer_id') === (string) $customer->id)>
                                {{ $customer->name }} — {{ $customer->customer_code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="manual_billing_period" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Bulan tagihan</label>
                    <select
                        id="manual_billing_period"
                        name="billing_period"
                        required
                        class="w-full rounded-xl border-slate-300 bg-white px-3 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                        @foreach ($periodOptions as $option)
                            <option value="{{ $option['value'] }}" @selected($selectedPeriod === $option['value'])>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button
                    type="submit"
                    class="block w-full rounded-xl bg-cyan-500 px-4 py-3 text-center font-black text-slate-950 shadow-sm transition hover:bg-cyan-400 active:scale-[0.99]"
                >
                    Buat Tagihan
                </button>
            </form>
        </section>

        <section class="rounded-2xl border-2 border-cyan-300 bg-white shadow-sm dark:border-cyan-800 dark:bg-slate-900">
            <div class="border-b border-cyan-200 bg-cyan-50 px-4 py-4 dark:border-cyan-900/70 dark:bg-cyan-950/30 sm:px-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-200 text-lg dark:bg-cyan-900/70">
                        👥
                    </div>
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-black text-slate-900 dark:text-white">Semua pelanggan aktif</h2>
                            <span class="rounded-full bg-cyan-600 px-2 py-0.5 text-[10px] font-black uppercase tracking-wide text-white">Massal</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300">Membuat tagihan untuk seluruh pelanggan aktif.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('invoices.generate.mass') }}" class="space-y-4 p-4 sm:p-5">
                @csrf

                <div>
                    <label for="mass_billing_period" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Bulan tagihan</label>
                    <select
                        id="mass_billing_period"
                        name="billing_period"
                        required
                        class="w-full rounded-xl border-slate-300 bg-white px-3 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                        @foreach ($periodOptions as $option)
                            <option value="{{ $option['value'] }}" @selected($selectedPeriod === $option['value'])>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-relaxed text-amber-900 dark:border-amber-900/70 dark:bg-amber-950/30 dark:text-amber-100">
                    Tagihan yang sudah ada tidak akan dibuat ulang. Gunakan hanya jika Anda ingin membuat tagihan untuk semua pelanggan aktif.
                </div>

                <button
                    type="submit"
                    class="block w-full rounded-xl bg-cyan-600 px-4 py-4 text-center text-base font-black text-white shadow-lg transition hover:bg-cyan-500 active:scale-[0.99]"
                    style="display: block !important; visibility: visible !important; opacity: 1 !important; min-height: 56px; position: relative; z-index: 30;"
                    onclick="return confirm('Buat tagihan untuk semua pelanggan aktif pada bulan yang dipilih? Tagihan yang sudah ada tidak akan dibuat ulang.')"
                >
                    Generate Tagihan Massal
                </button>
            </form>
        </section>
    </div>
</div>
@endsection