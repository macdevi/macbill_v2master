@extends('layouts.app')

@section('title', 'Edit Pelanggan')

@section('content')
<form method="post"
      action="{{ route('customers.update', $customer) }}"
      class="max-w-3xl space-y-5"
      id="customer-edit-form">
    @csrf
    @method('PUT')

    @include('customers.form')

    <input type="hidden" name="save_target" id="save_target" value="billing">

    <div class="flex justify-end">
        <button type="button"
                id="open-save-modal"
                class="rounded-xl bg-cyan-500 px-6 py-3 font-bold text-slate-950 hover:bg-cyan-400">
            SIMPAN
        </button>
    </div>
</form>

<div id="save-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-900">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">
            Simpan Perubahan Pelanggan
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
    const form = document.getElementById('customer-edit-form');
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
