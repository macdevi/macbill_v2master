@extends('layouts.app')
@section('title', 'Pasang Baru')
@section('content')
<form method="POST" action="{{ route('installations.store') }}"
      id="installation-form" class="max-w-3xl space-y-5">
    @csrf
    <input type="hidden" name="submission_key"
           value="{{ old('submission_key', $submissionKey) }}">
    <input type="hidden" name="save_target" id="save_target" value="">

    <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-4 text-sm dark:border-cyan-600 dark:bg-slate-900">
        Simpan setelah layanan siap digunakan. Pilih Billing saja jika internet
        sudah aktif secara manual, atau Billing dan mikrotik untuk sinkronisasi PPPoE.
        Tanggal aktivasi menjadi dasar hitungan langganan.
    </div>


    @if(auth()->user()?->isSuperAdmin())
        <label class="block rounded-xl border border-amber-400 p-4 text-sm">
            <input type="checkbox" name="psb_is_test" value="1"
                   @checked(old('psb_is_test'))>
            <span class="ml-2 font-bold">Data uji — bukan penerimaan uang nyata</span>
            <span class="mt-2 block text-xs text-slate-500">
                Pembayaran dicatat sebagai data uji dan tidak dihitung dalam pemasukan.
                Penanda ini tidak mencegah operasi MikroTik jika target tersebut dipilih.
            </span>
        </label>
    @endif

    @include('customers.form')

    <label class="block">
        <span class="text-sm font-bold">Tanggal internet aktif</span>
        <input type="date" name="activated_on" required
               max="{{ now()->toDateString() }}"
               value="{{ old('activated_on', now()->toDateString()) }}"
               class="mt-2 w-full rounded-xl border border-slate-300 bg-transparent px-4 py-3 dark:border-slate-700">
        <span class="mt-2 block text-xs text-slate-500">
            Tagihan pertama terbit pada tanggal tagihan berikutnya.
            Periode pertama dihitung prorata bila kurang dari satu siklus.
        </span>
    </label>

    <div class="space-y-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
        <h2 class="font-bold">Nota biaya pemasangan</h2>
        <p class="text-xs text-slate-500">
            Isi nominal akhir biaya pemasangan. Mode pajak pelanggan di atas
            berlaku untuk langganan, bukan otomatis ditambahkan ke biaya pemasangan.
        </p>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach([
                'installation_fee' => 'Biaya pemasangan',
                'equipment_fee' => 'Biaya perangkat',
                'other_fee' => 'Biaya lain',
                'paid_amount' => 'Pembayaran diterima',
            ] as $field => $label)
                <label class="block">
                    <span class="text-sm font-semibold">{{ $label }} (Rp)</span>
                    <input type="number" name="{{ $field }}" required
                           min="0" max="999999999" step="0.01"
                           value="{{ old($field, '0') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent px-4 py-3 dark:border-slate-700">
                </label>
            @endforeach
        </div>
        <div class="rounded-xl bg-slate-100 p-3 dark:bg-slate-800">
            Total pemasangan: <span id="installation-total" class="font-bold">Rp 0</span>
            <div class="mt-1 text-sm">Sisa: <span id="installation-balance">Rp 0</span></div>
        </div>
        <label class="block">
            <span class="text-sm font-semibold">Metode pembayaran</span>
            <select name="payment_method"
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                <option value="">Belum ada pembayaran</option>
                <option value="cash" @selected(old('payment_method') === 'cash')>Tunai</option>
                <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer</option>
            </select>
        </label>
        <label class="block">
            <span class="text-sm font-semibold">Catatan / rincian biaya lain</span>
            <textarea name="notes" rows="3" maxlength="3000"
                      class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent px-4 py-3 dark:border-slate-700">{{ old('notes') }}</textarea>
        </label>
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('installations.index') }}"
           class="rounded-xl border border-slate-300 px-4 py-3 dark:border-slate-700">Kembali</a>
        <button type="button" id="open-save-modal"
                class="rounded-xl bg-cyan-500 px-5 py-3 font-bold text-slate-950">
            Simpan
        </button>
    </div>
</form>

<script>
(() => {
    const form = document.getElementById('installation-form');
    form.elements.phone.required = true;
    if (!form.elements.due_day.value) form.elements.due_day.value = '1';

    const fields = ['installation_fee', 'equipment_fee', 'other_fee'];
    const rupiah = value => 'Rp ' + new Intl.NumberFormat('id-ID', {
        maximumFractionDigits: 2
    }).format(value);

    function refresh() {
        const total = fields.reduce((sum, key) =>
            sum + (Number(form.elements[key].value) || 0), 0);
        const paid = Number(form.elements.paid_amount.value) || 0;
        document.getElementById('installation-total').textContent = rupiah(total);
        document.getElementById('installation-balance').textContent = rupiah(total - paid);
    }
    [...fields, 'paid_amount'].forEach(key =>
        form.elements[key].addEventListener('input', refresh));
    refresh();
})();
</script>

<div id="save-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-900">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">
            Simpan Pemasangan Baru
        </h2>

        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
            Pilih lokasi penyimpanan data pelanggan.
        </p>

        <div class="mt-6 space-y-3">
            <button type="button"
                    id="save-billing-only"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-left font-semibold text-slate-800 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                <span class="block">1. Billing</span>
                <span class="mt-1 block text-xs font-normal text-slate-500 dark:text-slate-400">
                    Simpan hanya ke database billing.
                </span>
            </button>

            <button type="button"
                    id="save-billing-mikrotik"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-left font-semibold text-slate-800 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                <span class="block">2. Billing dan mikrotik</span>
                <span class="mt-1 block text-xs font-normal text-slate-500 dark:text-slate-400">
                    Simpan ke billing dan sinkronkan PPPoE secret ke MikroTik.
                </span>
            </button>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <button type="button"
                    id="confirm-save"
                    disabled
                    class="rounded-xl bg-cyan-500 px-5 py-2 font-bold text-slate-950 hover:bg-cyan-400 disabled:cursor-not-allowed disabled:opacity-40">
                Simpan
            </button>
            <button type="button"
                    id="close-save-modal"
                    class="rounded-xl px-4 py-2 font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                Batal
            </button>
        </div>
    </div>
</div>

<script>
(() => {
    const form = document.getElementById('installation-form');
    const modal = document.getElementById('save-modal');
    const saveTarget = document.getElementById('save_target');
    const openButton = document.getElementById('open-save-modal');
    const cancelButton = document.getElementById('close-save-modal');
    const confirmButton = document.getElementById('confirm-save');
    const choices = [
        { button: document.getElementById('save-billing-only'), value: 'billing' },
        { button: document.getElementById('save-billing-mikrotik'), value: 'billing_mikrotik' }
    ];
    let selected = null;
    let submitting = false;
    let confirmed = false;

    const originals = choices.map(({ button }) => ({
        background: button.style.backgroundColor,
        border: button.style.borderColor,
        shadow: button.style.boxShadow
    }));

    function renderSelection() {
        choices.forEach(({ button, value }, index) => {
            const active = selected === value;
            button.setAttribute('aria-pressed', String(active));
            button.style.backgroundColor = active
                ? (document.documentElement.classList.contains('dark') ? '#164e63' : '#cffafe')
                : originals[index].background;
            button.style.borderColor = active ? '#06b6d4' : originals[index].border;
            button.style.boxShadow = active
                ? '0 0 0 3px rgba(6,182,212,0.4)'
                : originals[index].shadow;
        });
        confirmButton.disabled = !selected || submitting;
    }

    function openModal() {
        if (submitting) return;
        selected = null;
        renderSelection();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        choices[0].button.focus();
    }

    function closeModal() {
        if (submitting) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        openButton.focus();
    }

    openButton.addEventListener('click', openModal);
    cancelButton.addEventListener('click', closeModal);

    choices.forEach(({ button, value }) => {
        button.addEventListener('click', () => {
            if (submitting) return;
            selected = value;
            renderSelection();
        });
    });

    modal.addEventListener('click', event => {
        if (event.target === modal) closeModal();
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    form.addEventListener('submit', event => {
        if (!confirmed) {
            event.preventDefault();
            if (!submitting) openModal();
            return;
        }
        confirmed = false;
        submitting = true;
        confirmButton.disabled = true;
        confirmButton.textContent = 'Menyimpan...';
        cancelButton.disabled = true;
        openButton.disabled = true;
        choices.forEach(({ button }) => button.disabled = true);
    });

    confirmButton.addEventListener('click', () => {
        if (!selected || submitting) return;
        if (!form.checkValidity()) {
            closeModal();
            form.reportValidity();
            return;
        }
        saveTarget.value = selected;
        confirmed = true;
        form.requestSubmit();
        confirmed = false;
    });

    renderSelection();
})();
</script>

@endsection
