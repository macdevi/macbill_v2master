@extends('layouts.app')

@section('title', $expense->exists ? 'Edit Pengeluaran' : 'Tambah Pengeluaran')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-rose-500/10 text-xl text-rose-600 ring-1 ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300">
                💸
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                    {{ $expense->exists ? 'Edit Pengeluaran' : 'Tambah Pengeluaran' }}
                </h1>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                    {{ $expense->exists ? 'Perbarui informasi transaksi pengeluaran.' : 'Catat biaya operasional bisnis Anda.' }}
                </p>
            </div>
        </div>

        <a href="{{ route('expenses.index') }}"
           class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-cyan-700 dark:hover:bg-slate-800 dark:hover:text-cyan-300">
            ← Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-200">
            <div class="font-bold">Data belum dapat disimpan:</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $expense->exists ? route('expenses.update', $expense) : route('expenses.store') }}"
          class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @csrf
        @if($expense->exists)
            @method('PUT')
        @endif

        <div class="space-y-5 p-5 sm:p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="expense_date" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">
                        Tanggal pengeluaran <span class="text-rose-500">*</span>
                    </label>
                    <input id="expense_date"
                           type="date"
                           name="expense_date"
                           value="{{ old('expense_date', optional($expense->expense_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                           required
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
                </div>

                <div>
                    <label for="category" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <select id="category"
                            name="category"
                            required
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @selected(old('category', $expense->category) === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="title" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">
                    Judul pengeluaran <span class="text-rose-500">*</span>
                </label>
                <input id="title"
                       type="text"
                       name="title"
                       value="{{ old('title', $expense->title) }}"
                       maxlength="255"
                       required
                       autofocus
                       placeholder="Contoh: Pembayaran listrik kantor"
                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="vendor" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">
                        Penerima / vendor
                    </label>
                    <input id="vendor"
                           type="text"
                           name="vendor"
                           value="{{ old('vendor', $expense->vendor) }}"
                           maxlength="255"
                           placeholder="Contoh: PLN atau nama teknisi"
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
                </div>

                <div>
                    <label for="payment_method" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">
                        Metode pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <select id="payment_method"
                            name="payment_method"
                            required
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
                        @foreach($paymentMethods as $key => $label)
                            <option value="{{ $key }}" @selected(old('payment_method', $expense->payment_method) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="amount" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">
                    Nominal <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-500 dark:text-slate-400">Rp</span>
                    <input id="amount"
                           type="number"
                           name="amount"
                           value="{{ old('amount', $expense->amount !== null ? (float) $expense->amount : '') }}"
                           min="1"
                           max="999999999999.99"
                           step="0.01"
                           inputmode="decimal"
                           required
                           placeholder="0"
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-3 text-sm font-bold text-slate-700 outline-none transition placeholder:font-normal placeholder:text-slate-400 focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">
                </div>
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Masukkan nominal tanpa titik pemisah, misalnya 150000.</p>
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">
                    Keterangan
                </label>
                <textarea id="description"
                          name="description"
                          rows="4"
                          maxlength="5000"
                          placeholder="Tambahkan catatan jika diperlukan"
                          class="block w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-300/30 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-cyan-500 dark:focus:bg-slate-900 dark:focus:ring-cyan-400/15">{{ old('description', $expense->description) }}</textarea>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-end dark:border-slate-800 dark:bg-slate-800/50">
            <a href="{{ route('expenses.index') }}"
               class="inline-flex items-center justify-center rounded-xl px-4 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-200 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-500 px-5 py-3 text-sm font-black text-slate-950 shadow-sm shadow-cyan-500/20 transition hover:bg-cyan-400 focus:outline-none focus:ring-4 focus:ring-cyan-300/40">
                <span>✓</span>
                {{ $expense->exists ? 'Simpan Perubahan' : 'Simpan Pengeluaran' }}
            </button>
        </div>
    </form>
</div>
@endsection
