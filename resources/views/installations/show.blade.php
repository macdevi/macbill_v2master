@extends('layouts.app')
@section('title', 'Nota Pemasangan')
@section('content')
@php
    $money = fn ($value) => 'Rp '.number_format((float) $value, 2, ',', '.');
    $remaining = max((float) $installation->total_amount - (float) $installation->paid_amount, 0);
    $paymentStatus = $remaining <= 0 ? 'LUNAS' : 'BELUM LUNAS';
    $number = preg_replace('/\D+/', '', $installation->customer_phone);
    if (str_starts_with($number, '0')) {
        $number = '62'.substr($number, 1);
    } elseif (str_starts_with($number, '8')) {
        $number = '62'.$number;
    }
    $validNumber = preg_match('/^[1-9][0-9]{7,14}$/', $number);
    $message = "NOTA PEMASANGAN\nNo: {$installation->receipt_number}\n"
        ."Pelanggan: {$installation->customer_name}\n"
        ."Alamat: ".($installation->customer_address ?? '-')."\n"
        ."Paket: {$installation->package_name}\n"
        ."Internet aktif: ".$installation->activated_on->format('d/m/Y')."\n\n"
        ."Pemasangan: ".$money($installation->installation_fee)."\n"
        ."Perangkat: ".$money($installation->equipment_fee)."\n"
        ."Biaya lain: ".$money($installation->other_fee)."\n"
        ."Total: ".$money($installation->total_amount)."\n"
        ."Dibayar: ".$money($installation->paid_amount)."\n"
        ."Sisa: ".$money($remaining)."\nStatus: {$paymentStatus}\n\n"
        ."Tarif langganan dasar: ".$money($installation->monthly_price)."/bulan\n"
        ."Mode pajak langganan: {$installation->tax_mode}\n"
        ."Tagihan pertama: ".$installation->first_billing_on->format('d/m/Y')."\n"
        ."Langganan pertama dihitung sejak aktivasi sesuai periode layanan.\n"
        ."Biaya pemasangan terpisah dari tagihan langganan."
        .($installation->notes ? "\nCatatan: ".$installation->notes : '');
    $whatsappUrl = $validNumber
        ? 'https://wa.me/'.$number.'?text='.rawurlencode($message)
        : null;
    $syncStatus = $installation->customer?->mikrotik_sync_status ?? 'tidak tersedia';
@endphp

<style>
@media print {
    body { background:white !important; color:black !important; }
    aside, header, .installation-actions { display:none !important; }
    main, main > section { margin:0 !important; padding:0 !important; }
    main > section > :not(.installation-receipt) { display:none !important; }
    .installation-receipt { max-width:none !important; border:none !important; background:white !important; color:black !important; }
}
</style>

<div class="installation-receipt max-w-3xl space-y-5 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900">
    <div>
        <h1 class="text-2xl font-black">Nota Pemasangan</h1>
        <p class="mt-1 text-sm">{{ $installation->receipt_number }}</p>
    </div>
    <dl class="grid gap-3 md:grid-cols-2">
        <div><dt class="text-sm text-slate-500">Pelanggan</dt><dd class="font-bold">{{ $installation->customer_name }}</dd></div>
        <div><dt class="text-sm text-slate-500">WhatsApp</dt><dd>{{ $installation->customer_phone }}</dd></div>
        <div><dt class="text-sm text-slate-500">Alamat</dt><dd>{{ $installation->customer_address ?? '-' }}</dd></div>
        <div><dt class="text-sm text-slate-500">Paket</dt><dd>{{ $installation->package_name }}</dd></div>
        <div><dt class="text-sm text-slate-500">Internet aktif</dt><dd>{{ $installation->activated_on->format('d/m/Y') }}</dd></div>
        <div><dt class="text-sm text-slate-500">Tagihan pertama</dt><dd>{{ $installation->first_billing_on->format('d/m/Y') }}</dd></div>
    </dl>

    <div class="space-y-3 border-t border-slate-200 pt-4 dark:border-slate-700">
        @foreach([
            'Biaya pemasangan' => $installation->installation_fee,
            'Biaya perangkat' => $installation->equipment_fee,
            'Biaya lain' => $installation->other_fee,
            'Total pemasangan' => $installation->total_amount,
            'Pembayaran diterima' => $installation->paid_amount,
            'Sisa pembayaran' => $remaining,
        ] as $label => $value)
            <div class="flex justify-between gap-3">
                <span>{{ $label }}</span><span class="font-bold">{{ $money($value) }}</span>
            </div>
        @endforeach
        <p class="font-bold">Status pemasangan: {{ $paymentStatus }}</p>
        <p class="text-sm">Metode: {{ $installation->payment_method ?? '-' }}</p>
    </div>

    <div class="rounded-xl bg-slate-100 p-4 text-sm dark:bg-slate-800">
        Tarif langganan dasar {{ $money($installation->monthly_price) }}/bulan.
        Mode pajak: {{ $installation->tax_mode }}.
        Biaya langganan tidak termasuk total pemasangan.
    </div>
    @if($installation->notes)
        <p class="whitespace-pre-line text-sm">{{ $installation->notes }}</p>
    @endif

    <div class="installation-actions space-y-3">
        <p class="text-sm">Status sinkronisasi MikroTik: {{ $syncStatus }}</p>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="window.print()"
                    class="rounded-xl border border-slate-300 px-4 py-3 dark:border-slate-600">Cetak nota</button>
            @if($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                   class="rounded-xl px-4 py-3 font-bold"
                   style="background:#25D366;color:white">Kirim WhatsApp</a>
            @else
                <span class="text-sm text-rose-600">Nomor WhatsApp tidak valid.</span>
            @endif
            <a href="{{ route('installations.index') }}"
               class="rounded-xl border border-slate-300 px-4 py-3 dark:border-slate-600">Kembali</a>
        </div>
    </div>
</div>

@php
    $cancelCustomer = $installation->customer;
    $cancelInvoiceCount = $cancelCustomer
        ? $cancelCustomer->invoices()->count()
        : 0;
@endphp

<div class="installation-actions mt-5 max-w-3xl rounded-2xl border border-rose-200 bg-white p-4 dark:border-rose-900 dark:bg-slate-900">
    <h2 class="font-bold">Pembatalan pemasangan</h2>
    <p class="mt-2 text-sm text-slate-500">
        Menghapus pelanggan, nota ini, dan invoice tanpa riwayat pembayaran.
        Kondisi pembayaran serta saldo diperiksa kembali oleh server.
    </p>

    @if($cancelCustomer)
        <button type="button" id="psb-open-cancel"
                class="mt-3 rounded-xl border border-rose-500 px-4 py-3 font-bold text-rose-600 dark:text-rose-400">
            Batalkan pemasangan
        </button>
    @else
        <p class="mt-3 text-sm text-rose-600 dark:text-rose-400">
            Pelanggan sudah tidak tersedia. Pembatalan otomatis tidak dapat dilakukan.
        </p>
    @endif
</div>

@if($cancelCustomer)
<style>
#psb-cancel-dialog {
    width: min(94vw, 38rem);
    max-height: 90vh;
    overflow-y: auto;
}
#psb-cancel-dialog::backdrop {
    background: rgba(15, 23, 42, .72);
}
#psb-cancel-dialog [data-cancel-target][aria-pressed="true"] {
    border-color: #f43f5e;
    box-shadow: inset 0 0 0 1px #f43f5e;
    background: rgba(244, 63, 94, .12);
}
@media print {
    #psb-cancel-dialog { display: none !important; }
}
</style>

<style id="psb-cancel-button-style">
#psb-cancel-dialog #psb-confirm-cancel {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    padding: 12px 16px;
    border: 1px solid #be123c !important;
    border-radius: 12px;
    background: #be123c !important;
    color: #ffffff !important;
    font-weight: 700;
    opacity: 1 !important;
    visibility: visible !important;
    cursor: pointer;
}
#psb-cancel-dialog #psb-confirm-cancel:disabled {
    background: #e2e8f0 !important;
    border-color: #94a3b8 !important;
    color: #475569 !important;
    opacity: 1 !important;
    cursor: not-allowed;
}
#psb-cancel-dialog #psb-confirm-cancel:enabled:hover {
    background: #9f1239 !important;
}
#psb-cancel-dialog #psb-confirm-cancel:focus-visible {
    outline: 3px solid #fb7185;
    outline-offset: 3px;
}
</style>

<dialog id="psb-cancel-dialog"
        aria-labelledby="psb-cancel-heading"
        class="installation-actions rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">

    <form id="psb-cancel-form" method="POST"
          action="{{ route('installations.cancel', $installation) }}"
          class="space-y-4">
        @csrf
        @method('DELETE')
        <input type="hidden" name="cancel_target" id="psb-cancel-target" value="">

        <div>
            <h2 id="psb-cancel-heading" class="text-xl font-black">
                Batalkan pemasangan
            </h2>
            <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">
                Penghapusan ini permanen. Jangan digunakan untuk pelanggan yang
                seharusnya hanya dinonaktifkan atau diisolir.
            </p>
        </div>

        <dl class="space-y-2 rounded-xl bg-slate-100 p-3 text-sm dark:bg-slate-800">
            <div>
                <dt class="text-slate-500">Pelanggan</dt>
                <dd class="font-bold">{{ $cancelCustomer->name }} · ID {{ $cancelCustomer->id }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Username PPPoE</dt>
                <dd>{{ $cancelCustomer->pppoe_username }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Nota</dt>
                <dd class="break-all">{{ $installation->receipt_number }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Invoice terkait saat halaman dibuka</dt>
                <dd>{{ $cancelInvoiceCount }}</dd>
            </div>
        </dl>

        <fieldset class="space-y-2">
            <legend class="mb-2 text-sm font-bold">Pilih target pembatalan</legend>

            <button type="button" data-cancel-target="billing"
                    aria-pressed="false"
                    class="block w-full rounded-xl border border-slate-300 p-3 text-left dark:border-slate-600">
                <span class="block font-bold">Billing saja</span>
                <span class="mt-1 block text-xs text-slate-500">
                    Menghapus pelanggan, nota, dan invoice yang diizinkan.
                    Akun MikroTik tidak disentuh dan mungkin tetap bisa digunakan.
                </span>
            </button>

            <button type="button" data-cancel-target="billing_mikrotik"
                    aria-pressed="false"
                    class="block w-full rounded-xl border border-slate-300 p-3 text-left dark:border-slate-600">
                <span class="block font-bold">Billing dan MikroTik</span>
                <span class="mt-1 block text-xs text-slate-500">
                    Memeriksa kepemilikan akun, menghapus secret PPPoE dan
                    memutus sesi aktifnya, lalu menghapus data billing.
                </span>
            </button>
        </fieldset>

        <p id="psb-cancel-target-hint"
           class="rounded-xl bg-slate-100 p-3 text-xs dark:bg-slate-800"
           aria-live="polite">
            Belum ada target dipilih. Memilih target belum menjalankan pembatalan.
        </p>

        <div class="space-y-3 rounded-xl border border-slate-300 p-4 dark:border-slate-600">
            <h3 class="font-bold">Pengembalian pembayaran pemasangan</h3>

            <p class="text-sm">
                Pembayaran diterima:
                <span class="font-bold">
                    Rp {{ number_format((float) $installation->paid_amount, 2, ',', '.') }}
                </span>
            </p>
            <p class="text-xs text-slate-500">
                Untuk transaksi nyata, pembatalan permanen memerlukan
                pengembalian penuh pembayaran pemasangan.
                Form ini mencatat pengembalian, bukan mengirim uang.
            </p>

            @if(auth()->user()?->isSuperAdmin())
                <label class="block rounded-xl border border-amber-400 p-3 text-sm">
                    <input type="checkbox" name="cancel_is_test"
                           id="psb-cancel-is-test" value="1">
                    <span class="ml-2 font-bold">
                        Data uji — tidak ada uang nyata diterima atau dikembalikan
                    </span>
                    <span class="mt-2 block text-xs text-slate-500">
                        Refund menjadi Rp 0. Tidak membuat pengeluaran dan
                        pembayaran uji tidak dihitung sebagai pemasukan.
                    </span>
                </label>
            @endif

            <label class="block">
                <span class="text-sm font-semibold">Nominal dikembalikan (Rp)</span>
                <input type="number" name="refund_amount" id="psb-refund-amount"
                       required min="0" step="0.01" value="0"
                       max="{{ $installation->paid_amount }}"
                       class="mt-2 w-full rounded-xl border border-slate-300 bg-transparent px-3 py-3 dark:border-slate-600">
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Metode pengembalian</span>
                <select name="refund_method" id="psb-refund-method"
                        class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-3 dark:border-slate-600 dark:bg-slate-900">
                    <option value="">Pilih metode</option>
                    <option value="cash">Tunai</option>
                    <option value="bank_transfer">Transfer Bank</option>
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Referensi pengembalian (opsional)</span>
                <input type="text" name="refund_reference" id="psb-refund-reference"
                       maxlength="255"
                       class="mt-2 w-full rounded-xl border border-slate-300 bg-transparent px-3 py-3 dark:border-slate-600"
                       placeholder="Nomor transfer atau keterangan penyerahan tunai">
            </label>

            <label class="block text-sm">
                <input type="checkbox" name="refund_confirmed"
                       id="psb-refund-confirmed" value="1">
                <span class="ml-2">
                    Saya memastikan uang benar-benar sudah dikembalikan.
                </span>
            </label>

            <p id="psb-refund-hint" class="text-xs text-slate-500" aria-live="polite"></p>
        </div>

        <label class="block">
            <span class="text-sm font-bold">Alasan pembatalan</span>
            <textarea name="cancel_reason" id="psb-cancel-reason"
                      required minlength="5" maxlength="1000" rows="3"
                      class="mt-2 w-full rounded-xl border border-slate-300 bg-transparent px-3 py-2 dark:border-slate-600"
                      placeholder="Contoh: pemasangan dibatalkan pelanggan."></textarea>
        </label>

        <input type="hidden"
               name="confirm_receipt"
               id="psb-cancel-receipt"
               value="{{ $installation->receipt_number }}">

        <div class="flex flex-wrap justify-end gap-2">
            <button type="submit" id="psb-confirm-cancel" disabled
                    class="rounded-xl bg-rose-600 px-4 py-3 font-bold text-white disabled:cursor-not-allowed disabled:opacity-40">
                Konfirmasi pembatalan
            </button>
            <button type="button" id="psb-close-cancel"
                    class="rounded-xl border border-slate-300 px-4 py-3 dark:border-slate-600">
                Batal
            </button>
        </div>

        <p id="psb-cancel-progress" class="hidden text-sm" role="status">
            Memproses pembatalan. Jangan mengirim ulang atau menutup halaman.
        </p>
    </form>
</dialog>

<script>
(() => {
    const dialog = document.getElementById('psb-cancel-dialog');
    const form = document.getElementById('psb-cancel-form');
    const open = document.getElementById('psb-open-cancel');
    const close = document.getElementById('psb-close-cancel');
    const confirm = document.getElementById('psb-confirm-cancel');
    const target = document.getElementById('psb-cancel-target');
    const reason = document.getElementById('psb-cancel-reason');
    const receipt = document.getElementById('psb-cancel-receipt');
    const hint = document.getElementById('psb-cancel-target-hint');
    const progress = document.getElementById('psb-cancel-progress');
    const choices = [...dialog.querySelectorAll('[data-cancel-target]')];
    const expected = @json($installation->receipt_number);

    const paidAmount = @json((string) $installation->paid_amount);
    const refundAmount = document.getElementById('psb-refund-amount');
    const refundMethod = document.getElementById('psb-refund-method');
    const refundReference = document.getElementById('psb-refund-reference');
    const refundConfirmed = document.getElementById('psb-refund-confirmed');
    const testMode = document.getElementById('psb-cancel-is-test');
    const refundHint = document.getElementById('psb-refund-hint');

    function cents(value) {
        const text = String(value).trim();
        if (!/^\d{1,9}(?:\.\d{1,2})?$/.test(text)) return null;
        const parts = text.split('.');
        return Number(parts[0]) * 100 +
            Number((parts[1] || '').padEnd(2, '0'));
    }

    let processing = false;

    function refresh() {
        const targetValid = ['billing', 'billing_mikrotik'].includes(target.value);
        const reasonValid = reason.value.trim().length >= 5;
        const receiptValid = receipt.value.trim() === expected;
        const isTest = Boolean(testMode && testMode.checked);
        const paidCents = cents(paidAmount);
        const requiresRefund = !isTest && paidCents !== null && paidCents > 0;

        refundAmount.readOnly = isTest;
        refundMethod.disabled = !requiresRefund;
        refundMethod.required = requiresRefund;
        refundReference.disabled = !requiresRefund;
        refundConfirmed.disabled = !requiresRefund;
        refundConfirmed.required = requiresRefund;

        if (isTest) {
            refundAmount.value = '0';
            refundConfirmed.checked = false;
        }

        const amountCents = cents(refundAmount.value);
        const amountValid = amountCents !== null && paidCents !== null &&
            amountCents === (isTest ? 0 : paidCents);
        const methodValid = !requiresRefund ||
            ['cash', 'bank_transfer'].includes(refundMethod.value);
        const confirmedValid = !requiresRefund || refundConfirmed.checked;

        reason.setCustomValidity(reasonValid ? '' : 'Isi alasan minimal 5 karakter.');
        receipt.setCustomValidity(receiptValid ? '' : 'Referensi nota tidak sesuai. Muat ulang halaman.');
        refundAmount.setCustomValidity(
            amountValid ? '' :
            'Isi nominal pengembalian penuh sesuai pembayaran diterima.'
        );

        refundHint.textContent = isTest
            ? 'Mode data uji: refund Rp 0; tidak ada pengeluaran kas nyata.'
            : requiresRefund
                ? 'Isi refund penuh, pilih metode, dan konfirmasikan uang sudah dikembalikan.'
                : 'Tidak ada pembayaran diterima; nominal pengembalian harus Rp 0.';

        confirm.disabled = processing || !targetValid || !reasonValid ||
            !receiptValid || !amountValid || !methodValid || !confirmedValid;
    }

    open.addEventListener('click', () => {
        if (processing) return;
        form.reset();
        target.value = '';
        choices.forEach(button => button.setAttribute('aria-pressed', 'false'));
        hint.textContent = 'Belum ada target dipilih. Memilih target belum menjalankan pembatalan.';
        progress.classList.add('hidden');
        refresh();
        dialog.showModal();
        close.focus();
    });

    choices.forEach(button => {
        button.addEventListener('click', () => {
            if (processing) return;
            target.value = button.dataset.cancelTarget;
            choices.forEach(item =>
                item.setAttribute('aria-pressed', item === button ? 'true' : 'false'));
            hint.textContent = target.value === 'billing'
                ? 'Billing saja dipilih. Akun dan sesi MikroTik tidak diubah.'
                : 'Billing dan MikroTik dipilih. Akun PPPoE milik pelanggan akan dihapus dan sesi aktif diputus.';
            refresh();
        });
    });

    reason.addEventListener('input', refresh);
    receipt.addEventListener('input', refresh);

    refundAmount.addEventListener('input', refresh);
    refundMethod.addEventListener('change', refresh);
    refundConfirmed.addEventListener('change', refresh);
    if (testMode) testMode.addEventListener('change', refresh);


    close.addEventListener('click', () => {
        if (!processing) dialog.close();
    });

    dialog.addEventListener('cancel', event => {
        if (processing) event.preventDefault();
    });

    form.addEventListener('submit', event => {
        if (processing) {
            event.preventDefault();
            return;
        }

        refresh();
        if (confirm.disabled || !form.reportValidity()) {
            event.preventDefault();
            return;
        }

        reason.value = reason.value.trim();
        receipt.value = receipt.value.trim();
        processing = true;
        confirm.disabled = true;
        close.disabled = true;
        choices.forEach(button => button.disabled = true);
        progress.classList.remove('hidden');
    });

    window.addEventListener('pageshow', event => {
        if (!event.persisted) return;
        processing = false;
        close.disabled = false;
        choices.forEach(button => button.disabled = false);
        progress.classList.add('hidden');
        refresh();
    });
})();
</script>
@endif

@endsection
