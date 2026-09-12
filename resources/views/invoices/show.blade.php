@extends('layouts.app')

@section('title', 'Detail Invoice')

@section('content')
@php
    $statusClass = match($invoice->status) {
        'paid' => 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-400/15 dark:text-emerald-300 dark:border-emerald-500/20',
        'isolated' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-400/15 dark:text-amber-300 dark:border-amber-500/20',
        default => 'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-400/15 dark:text-rose-300 dark:border-rose-500/20',
    };

    $isTaxSnapshotAvailable = (float) $invoice->gross_amount > 0;
    $grossAmount = $isTaxSnapshotAvailable
        ? (float) $invoice->gross_amount
        : (float) $invoice->amount;
    $serviceAmount = $isTaxSnapshotAvailable
        ? (float) $invoice->service_amount
        : (float) $invoice->amount;
    $subtotal = $isTaxSnapshotAvailable
        ? (float) $invoice->subtotal
        : $serviceAmount;
    $taxAmount = $isTaxSnapshotAvailable
        ? (float) $invoice->tax_amount
        : 0;
    $taxRate = $isTaxSnapshotAvailable
        ? (float) $invoice->tax_rate
        : 0;
    $creditUsed = $isTaxSnapshotAvailable
        ? (float) $invoice->credit_used
        : 0;

    $verifiedPaid = (float) $invoice->payments
        ->where('status', 'verified')
        ->sum('amount');

    $amountAfterCredit = max($grossAmount - $creditUsed, 0);
    $remainingAmount = max((float) $invoice->amount, 0);

    $taxLabel = trim((string) $invoice->tax_name) ?: 'Pajak';
    $taxModeLabel = match($invoice->tax_mode) {
        'inclusive' => 'Termasuk harga',
        'exclusive' => 'Ditambahkan ke tagihan',
        default => 'Tidak dikenakan',
    };
@endphp

<div class="mx-auto max-w-6xl space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-cyan-600 dark:text-cyan-400">
                Detail Tagihan
            </div>
            <h1 class="mt-1 truncate text-2xl font-black text-slate-900 dark:text-white">
                {{ $invoice->invoice_number }}
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Rincian tagihan, pajak, titip saldo, dan riwayat pembayaran pelanggan.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('invoices.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                Kembali
            </a>

            <a href="{{ route('invoices.print', $invoice) }}"
               target="_blank"
               class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                Print Invoice
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(320px,0.72fr)]">
        <div class="space-y-5">
            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
                                Status Tagihan
                            </div>
                            <div class="mt-2">
                                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $statusClass }}">
                                    {{ $invoice->status }}
                                </span>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Sisa Tagihan
                            </div>
                            <div class="mt-1 text-2xl font-black text-slate-900 dark:text-white sm:text-3xl">
                                Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-700">
                            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Tanggal Tagihan
                            </div>
                            <div class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $invoice->billing_date->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-700">
                            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Jatuh Tempo
                            </div>
                            <div class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $invoice->due_date->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-800 sm:px-5">
                    <h2 class="text-base font-black text-slate-900 dark:text-white">
                        Rincian Tagihan
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Nilai pada invoice disimpan saat tagihan diterbitkan dan tidak berubah mengikuti pengaturan terbaru.
                    </p>
                </div>

                <dl class="space-y-3 p-4 text-sm sm:p-5">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-slate-500 dark:text-slate-400">Harga layanan</dt>
                        <dd class="font-semibold text-slate-900 dark:text-white">
                            Rp {{ number_format($serviceAmount, 0, ',', '.') }}
                        </dd>
                    </div>

                    @if ($isTaxSnapshotAvailable && $invoice->tax_mode !== 'none' && $taxRate > 0)
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Dasar sebelum pajak</dt>
                            <dd class="font-semibold text-slate-900 dark:text-white">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">
                                {{ $taxLabel }} {{ rtrim(rtrim(number_format($taxRate, 4, '.', ''), '0'), '.') }}%
                                <span class="text-[11px]">({{ $taxModeLabel }})</span>
                            </dt>
                            <dd class="font-semibold text-slate-900 dark:text-white">
                                Rp {{ number_format($taxAmount, 0, ',', '.') }}
                            </dd>
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-4 border-t border-slate-100 pt-3 dark:border-slate-800">
                        <dt class="font-bold text-slate-700 dark:text-slate-200">Total tagihan awal</dt>
                        <dd class="font-black text-slate-900 dark:text-white">
                            Rp {{ number_format($grossAmount, 0, ',', '.') }}
                        </dd>
                    </div>

                    @if ($creditUsed > 0)
                        <div class="flex items-center justify-between gap-4 text-emerald-700 dark:text-emerald-300">
                            <dt class="font-semibold">Titip saldo digunakan</dt>
                            <dd class="font-bold">- Rp {{ number_format($creditUsed, 0, ',', '.') }}</dd>
                        </div>
                    @endif

                    @if ($creditUsed > 0)
                        <div class="flex items-center justify-between gap-4 border-t border-slate-100 pt-3 dark:border-slate-800">
                            <dt class="font-bold text-slate-700 dark:text-slate-200">Tagihan setelah titip saldo</dt>
                            <dd class="font-black text-slate-900 dark:text-white">
                                Rp {{ number_format($amountAfterCredit, 0, ',', '.') }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col gap-2 border-b border-slate-200 px-4 py-4 dark:border-slate-800 sm:flex-row sm:items-end sm:justify-between sm:px-5">
                    <div>
                        <h2 class="text-base font-black text-slate-900 dark:text-white">
                            Riwayat Pembayaran
                        </h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Seluruh pembayaran yang tercatat untuk invoice ini.
                        </p>
                    </div>

                    <div class="text-sm font-semibold text-slate-500 dark:text-slate-400">
                        {{ $invoice->payments->count() }} transaksi
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="space-y-3">
                        @forelse ($invoice->payments as $payment)
                            @php
                                $paymentStatusClass = match($payment->status) {
                                    'verified' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300',
                                    'rejected' => 'bg-rose-100 text-rose-700 dark:bg-rose-400/15 dark:text-rose-300',
                                    default => 'bg-amber-100 text-amber-700 dark:bg-amber-400/15 dark:text-amber-300',
                                };
                            @endphp

                            <article class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="space-y-1 text-sm text-slate-600 dark:text-slate-300">
                                        <div>
                                            <span class="font-semibold text-slate-900 dark:text-white">Metode:</span>
                                            {{ $payment->method === 'bank_transfer' ? 'Transfer Bank' : 'Tunai' }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-900 dark:text-white">Nominal:</span>
                                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                        </div>
                                        @if (!empty($payment->notes))
                                            <div>
                                                <span class="font-semibold text-slate-900 dark:text-white">Catatan:</span>
                                                {{ $payment->notes }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="sm:text-right">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $paymentStatusClass }}">
                                            {{ $payment->status }}
                                        </span>

                                        <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                            {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') : '-' }}
                                        </div>
                                    </div>
                                </div>

                                @if (!empty($payment->proof_path))
                                    <div class="mt-3 border-t border-slate-100 pt-3 dark:border-slate-800">
                                        <a href="{{ asset('storage/' . $payment->proof_path) }}"
                                           target="_blank"
                                           class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                                            Lihat Bukti Pembayaran
                                        </a>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                Belum ada pembayaran yang tercatat untuk invoice ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <aside class="space-y-5">
            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-800 sm:px-5">
                    <h2 class="text-base font-black text-slate-900 dark:text-white">
                        Informasi Pelanggan
                    </h2>
                </div>

                <dl class="space-y-4 p-4 text-sm sm:p-5">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Nama Pelanggan
                        </dt>
                        <dd class="mt-1 font-semibold text-slate-900 dark:text-white">
                            {{ $invoice->customer->name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Kode Pelanggan
                        </dt>
                        <dd class="mt-1 font-semibold text-slate-800 dark:text-slate-100">
                            {{ $invoice->customer->customer_code ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Nomor Telepon
                        </dt>
                        <dd class="mt-1 font-semibold text-slate-800 dark:text-slate-100">
                            {{ $invoice->customer->phone ?: '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Alamat
                        </dt>
                        <dd class="mt-1 whitespace-pre-line text-slate-700 dark:text-slate-200">
                            {{ $invoice->customer->address ?: '-' }}
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-800 sm:px-5">
                    <h2 class="text-base font-black text-slate-900 dark:text-white">
                        Ringkasan Pembayaran
                    </h2>
                </div>

                <dl class="space-y-3 p-4 text-sm sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">Total tagihan awal</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">
                            Rp {{ number_format($grossAmount, 0, ',', '.') }}
                        </dd>
                    </div>

                    @if ($creditUsed > 0)
                        <div class="flex items-center justify-between gap-3 text-emerald-700 dark:text-emerald-300">
                            <dt>Titip saldo digunakan</dt>
                            <dd class="font-bold">- Rp {{ number_format($creditUsed, 0, ',', '.') }}</dd>
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">Pembayaran tercatat</dt>
                        <dd class="font-bold text-emerald-700 dark:text-emerald-300">
                            Rp {{ number_format($verifiedPaid, 0, ',', '.') }}
                        </dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-3 dark:border-slate-800">
                        <dt class="font-semibold text-slate-700 dark:text-slate-200">Sisa pembayaran</dt>
                        <dd class="font-black {{ $remainingAmount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </section>
        </aside>
    </div>
</div>
@endsection
