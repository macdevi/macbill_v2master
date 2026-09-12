@extends('layouts.app')

@section('title', 'Bayar Tagihan')

@section('content')
@php
    $statusClass = match($invoice->status) {
        'paid' => 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-400/15 dark:text-emerald-300 dark:border-emerald-500/20',
        'isolated' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-400/15 dark:text-amber-300 dark:border-amber-500/20',
        default => 'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-400/15 dark:text-rose-300 dark:border-rose-500/20',
    };

    $paymentReceiptWhatsApp = session('payment_receipt_whatsapp');

    $paymentReceiptMessage = $paymentReceiptWhatsApp
        ? "Halo Bapak/Ibu {$paymentReceiptWhatsApp['customer_name']},\n\n"
            . "Pembayaran Anda telah kami terima.\n"
            . "No. Invoice: {$paymentReceiptWhatsApp['invoice_number']}\n"
            . "Jumlah Pembayaran: Rp " . number_format((float) $paymentReceiptWhatsApp['amount'], 0, ',', '.') . "\n"
            . "Waktu Pembayaran: {$paymentReceiptWhatsApp['paid_at']}\n\n"
            . "Status tagihan Anda: LUNAS.\n"
            . "Invoice: {$paymentReceiptWhatsApp['invoice_url']}\n\n"
            . "Terima kasih."
        : null;

    $paymentReceiptWhatsappUrl = $paymentReceiptWhatsApp
        ? 'https://wa.me/' . $paymentReceiptWhatsApp['whatsapp_number']
            . '?text=' . rawurlencode($paymentReceiptMessage)
        : null;
@endphp

@if ($paymentReceiptWhatsApp && $paymentReceiptWhatsappUrl)
    <div id="payment-receipt-modal"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/60 p-4"
         role="dialog"
         aria-modal="true"
         aria-labelledby="payment-receipt-modal-title">
        <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-5 shadow-2xl dark:border-slate-700 dark:bg-slate-900 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-emerald-600 dark:text-emerald-400">
                        Pembayaran Berhasil
                    </div>
                    <h2 id="payment-receipt-modal-title" class="mt-1 text-xl font-black text-slate-900 dark:text-white">
                        Kirim bukti bayar?
                    </h2>
                </div>

                <button type="button"
                        data-close-payment-receipt-modal
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white text-lg font-bold text-slate-500 shadow-sm transition hover:bg-slate-50 hover:text-slate-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white"
                        aria-label="Tutup popup bukti pembayaran">
                    ×
                </button>
            </div>

            <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600 dark:bg-slate-800/70 dark:text-slate-300">
                <div class="font-semibold text-slate-900 dark:text-white">
                    {{ $paymentReceiptWhatsApp['customer_name'] }}
                </div>
                <div class="mt-1">
                    Invoice {{ $paymentReceiptWhatsApp['invoice_number'] }} telah dibayar sebesar
                    <span class="font-bold text-emerald-700 dark:text-emerald-300">
                        Rp {{ number_format((float) $paymentReceiptWhatsApp['amount'], 0, ',', '.') }}
                    </span>.
                </div>
            </div>

            <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">
                Kirim pemberitahuan bukti pembayaran kepada pelanggan melalui WhatsApp, atau lewati jika belum diperlukan.
            </p>

            <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-2">
                <button type="button"
                        data-close-payment-receipt-modal
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                    Lewati
                </button>

                <a href="{{ $paymentReceiptWhatsappUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   data-send-payment-receipt
                   class="inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-bold transition"
                   style="display:inline-flex;background:#16a34a;color:#ffffff;border:1px solid #16a34a;box-shadow:0 1px 2px rgba(0,0,0,.08);text-decoration:none;">
                    <img src="https://cdn.simpleicons.org/whatsapp/FFFFFF"
                         alt=""
                         class="h-4 w-4"
                         width="16"
                         height="16"
                         loading="lazy">
                    Kirim Bukti Bayar
                </a>
            </div>
        </div>
    </div>
@endif

<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-cyan-600 dark:text-cyan-400">
                Pembayaran Tagihan
            </div>
            <h1 class="mt-1 truncate text-2xl font-black text-slate-900 dark:text-white">
                {{ $invoice->invoice_number }}
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $invoice->customer->name }}
                @if (!empty($invoice->customer->phone))
                    · {{ $invoice->customer->phone }}
                @endif
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('invoices.show', $invoice) }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                Detail
            </a>

            <a href="{{ route('invoices.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                Kembali
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            <div class="font-semibold">Pembayaran belum dapat disimpan.</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-5">
            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
                                Status Invoice
                            </div>
                            <div class="mt-2">
                                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $statusClass }}">
                                    {{ $invoice->status }}
                                </span>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Total Tagihan
                            </div>
                            <div class="mt-1 text-2xl font-black text-slate-900 dark:text-white sm:text-3xl">
                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3">
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

            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-800 sm:px-5">
                    <h2 class="text-base font-black text-slate-900 dark:text-white">
                        Riwayat Pembayaran
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Pembayaran yang tercatat untuk tagihan ini.
                    </p>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="space-y-3">
                        @forelse ($invoice->payments as $payment)
                            <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="text-sm text-slate-600 dark:text-slate-300">
                                        <div>
                                            <span class="font-semibold text-slate-900 dark:text-white">Metode:</span>
                                            {{ str_replace('_', ' ', $payment->method) }}
                                        </div>
                                        <div class="mt-1">
                                            <span class="font-semibold text-slate-900 dark:text-white">Nominal:</span>
                                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="text-sm text-slate-500 dark:text-slate-400 sm:text-right">
                                        {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') : '-' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                Belum ada pembayaran yang tercatat.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <aside>
            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-800 sm:px-5">
                    <h2 class="text-base font-black text-slate-900 dark:text-white">
                        Form Bayar
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Catat pembayaran pelanggan.
                    </p>
                </div>

                <div class="p-4 sm:p-5">
                    @if ($invoice->status === 'paid')
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                            Invoice sudah lunas. Pembayaran baru tidak dapat ditambahkan.
                        </div>
                    @else
                        <form id="payment-form" method="POST"
                              enctype="multipart/form-data"
                              action="{{ route('payments.store', $invoice) }}"
                              class="space-y-4">
                            @csrf

                            <div>
                                <label for="method" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Metode Pembayaran
                                </label>

                                <select id="method"
                                        name="method"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20">
                                    <option value="cash" {{ old('method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                                    <option value="bank_transfer" {{ old('method') === 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                </select>
                            </div>

                            <div>
                                <label for="amount" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Jumlah Pembayaran
                                </label>

                                <input type="number"
                                       id="amount"
                                       name="amount"
                                       min="1"
                                       step="0.01"
                                       inputmode="decimal"
                                       value="{{ old('amount', $invoice->amount) }}"
                                       class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
                                       required>
                            </div>

                            <div>
                                <label for="proof" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Bukti Pembayaran
                                </label>

                                <input type="file"
                                       id="proof"
                                       name="proof"
                                       accept="image/*"
                                       class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20">
                            </div>

                            <div>
                                <label for="notes" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Catatan
                                </label>

                                <input type="text"
                                       id="notes"
                                       name="notes"
                                       value="{{ old('notes') }}"
                                       placeholder="Catatan pembayaran bila ada"
                                       class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20">
                            </div>

                            <button type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-2xl px-4 py-3 text-sm font-bold transition"
                                    style="display:inline-flex;background:#16a34a;color:#ffffff;border:1px solid #16a34a;box-shadow:0 1px 2px rgba(0,0,0,.08);">
                                Simpan Pembayaran
                            </button>
                        </form>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // payment-loading-controller
        const paymentForm = document.getElementById('payment-form');

        if (paymentForm) {
            const submitButton = paymentForm.querySelector('button[type="submit"]');

            if (submitButton) {
                const originalButtonHtml = submitButton.innerHTML;
                const loadingOverlay = document.createElement('div');

                loadingOverlay.id = 'payment-saving-overlay';
                loadingOverlay.className = 'fixed inset-0 z-[110] hidden items-center justify-center bg-slate-950/60 p-4';
                loadingOverlay.setAttribute('role', 'status');
                loadingOverlay.setAttribute('aria-live', 'polite');
                loadingOverlay.setAttribute('aria-busy', 'false');
                loadingOverlay.innerHTML = `
                    <div class="w-full max-w-sm rounded-3xl border border-slate-200 bg-white p-6 text-center shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300">
                            <svg class="payment-loading-spinner h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke-opacity=".25"></circle>
                                <path d="M21 12a9 9 0 0 0-9-9"></path>
                            </svg>
                        </div>
                        <h2 class="mt-4 text-lg font-black text-slate-900 dark:text-white">
                            Menyimpan pembayaran
                        </h2>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                            Mohon tunggu, pembayaran sedang diproses.
                        </p>
                        <div class="payment-loading-progress mt-5 h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-full w-1/2 animate-pulse rounded-full bg-emerald-600 dark:bg-emerald-400"></div>
                        </div>
                    </div>
                `;

                document.body.appendChild(loadingOverlay);

                let paymentIsSubmitting = false;

                paymentForm.addEventListener('submit', function (event) {
                    if (paymentIsSubmitting) {
                        event.preventDefault();
                        return;
                    }

                    if (!paymentForm.checkValidity()) {
                        event.preventDefault();
                        paymentForm.reportValidity();
                        return;
                    }

                    event.preventDefault();
                    paymentIsSubmitting = true;

                    submitButton.disabled = true;
                    submitButton.setAttribute('aria-busy', 'true');
                    submitButton.classList.add('cursor-not-allowed', 'opacity-80');
                    submitButton.innerHTML = `
                        <svg class="payment-loading-spinner h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke-opacity=".25"></circle>
                            <path d="M21 12a9 9 0 0 0-9-9"></path>
                        </svg>
                        <span>Menyimpan...</span>
                    `;

                    loadingOverlay.classList.remove('hidden');
                    loadingOverlay.classList.add('flex');
                    loadingOverlay.setAttribute('aria-busy', 'true');
                    document.body.classList.add('overflow-hidden');

                    window.setTimeout(function () {
                        paymentForm.submit();
                    }, 1000);
                });

                window.addEventListener('pageshow', function () {
                    paymentIsSubmitting = false;
                    submitButton.disabled = false;
                    submitButton.removeAttribute('aria-busy');
                    submitButton.classList.remove('cursor-not-allowed', 'opacity-80');
                    submitButton.innerHTML = originalButtonHtml;
                    loadingOverlay.classList.add('hidden');
                    loadingOverlay.classList.remove('flex');
                    loadingOverlay.setAttribute('aria-busy', 'false');
                    document.body.classList.remove('overflow-hidden');
                });
            }
        }

        const modal = document.getElementById('payment-receipt-modal');

        if (!modal) {
            return;
        }

        const closeModal = function () {
            modal.remove();

            window.setTimeout(function () {
                window.location.href = "{{ route('invoices.index') }}";
            }, 100);
        };

        modal.querySelectorAll('[data-close-payment-receipt-modal]').forEach(function (button) {
            button.addEventListener('click', closeModal);
        });

        const sendButton = modal.querySelector('[data-send-payment-receipt]');

        if (sendButton) {
            sendButton.addEventListener('click', function (event) {
                event.preventDefault();

                const whatsappUrl = sendButton.href;

                window.open(whatsappUrl, '_blank', 'noopener,noreferrer');
                closeModal();

                
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    });
</script>

    <style id="payment-loading-animation-style">
        @keyframes paymentLoadingSpin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes paymentLoadingShimmer {
            0% {
                transform: translateX(-130%);
            }

            100% {
                transform: translateX(230%);
            }
        }

        .payment-loading-spinner {
            animation: paymentLoadingSpin 0.72s linear infinite !important;
            transform-origin: center;
        }

        .payment-loading-progress {
            position: relative;
            overflow: hidden;
        }

        .payment-loading-progress::after {
            position: absolute;
            inset: 0 auto 0 0;
            width: 48%;
            content: "";
            border-radius: inherit;
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0),
                rgba(255, 255, 255, .78),
                rgba(255, 255, 255, 0)
            );
            animation: paymentLoadingShimmer 1s ease-in-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .payment-loading-spinner,
            .payment-loading-progress::after {
                animation: none !important;
            }
        }
    </style>

@endsection
