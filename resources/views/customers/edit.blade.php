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
                <span class="block">1. Simpan Billing</span>
                <span class="mt-1 block text-xs font-normal text-slate-500 dark:text-slate-400">
                    Simpan hanya ke database billing.
                </span>
            </button>

            <button type="button"
                    id="save-billing-mikrotik"
                    class="w-full rounded-xl border border-cyan-500 bg-cyan-50 px-4 py-3 text-left font-semibold text-slate-800 hover:bg-cyan-100 dark:border-cyan-600 dark:bg-cyan-900/30 dark:text-slate-100 dark:hover:bg-cyan-900/50">
                <span class="block">2. Simpan Billing dan MikroTik</span>
                <span class="mt-1 block text-xs font-normal text-slate-500 dark:text-slate-400">
                    Simpan ke billing dan sinkronkan PPPoE secret ke MikroTik.
                </span>
            </button>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button"
                    id="close-save-modal"
                    class="rounded-xl px-4 py-2 font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                Batal
            </button>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('customer-edit-form');
    const modal = document.getElementById('save-modal');
    const saveTarget = document.getElementById('save_target');

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('open-save-modal').addEventListener('click', openModal);
    document.getElementById('close-save-modal').addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    document.getElementById('save-billing-only').addEventListener('click', function () {
        saveTarget.value = 'billing';
        form.submit();
    });

    document.getElementById('save-billing-mikrotik').addEventListener('click', function () {
        saveTarget.value = 'billing_mikrotik';
        form.submit();
    });
</script>
@endsection
