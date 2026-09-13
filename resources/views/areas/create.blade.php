@extends('layouts.app')

@section('title', 'Tambah Wilayah Operasional')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-violet-100 bg-violet-50 px-3 py-1 text-xs font-bold text-violet-700 dark:border-violet-400/15 dark:bg-violet-400/10 dark:text-violet-300">
                <span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>
                Wilayah Operasional
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                Tambah Wilayah
            </h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Buat wilayah baru sebelum menugaskan user dan memetakan data pelanggan.
            </p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-slate-800 dark:bg-slate-800/40 sm:px-6">
            <h2 class="text-sm font-extrabold text-slate-800 dark:text-slate-100">Informasi Wilayah</h2>
        </div>
        <form method="POST" action="{{ route('areas.store') }}" class="p-5 sm:p-6">
            @include('areas._form')
        </form>
    </div>
</div>
@endsection
