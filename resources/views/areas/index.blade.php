@extends('layouts.app')

@section('title', 'Wilayah Operasional')

@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-violet-600 dark:text-violet-400">
                Administrasi Operasional
            </div>

            <h1 class="mt-1 text-2xl font-black text-slate-900 dark:text-white sm:text-3xl">
                Wilayah Operasional
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Kelola area layanan untuk pembatasan akses data pelanggan dan pengguna.
            </p>
        </div>

        <a href="{{ route('areas.create') }}"
           style="background:#7c3aed !important;color:#ffffff !important;border:1px solid #6d28d9 !important;box-shadow:0 4px 12px rgba(124,58,237,.22) !important;"
           class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-black transition hover:brightness-110 sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path d="M12 5v14M5 12h14"></path>
            </svg>
            Tambah Wilayah
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">
            Total <span class="font-black text-slate-900 dark:text-white">{{ $areas->count() }}</span> wilayah
        </div>

        <div class="text-xs text-slate-500 dark:text-slate-400">
            Area layanan dan akses operasional
        </div>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($areas as $area)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="truncate text-base font-black text-slate-900 dark:text-white">
                            {{ $area->name }}
                        </h2>

                        <p class="mt-0.5 truncate font-mono text-xs text-slate-500 dark:text-slate-400">
                            {{ $area->code ?: 'Kode wilayah belum diatur' }}
                        </p>
                    </div>

                    <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide
                        {{ $area->active
                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300'
                            : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                        {{ $area->active ? 'Aktif' : 'Off' }}
                    </span>
                </div>

                <div class="mt-3 flex items-end justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
                            {{ number_format($area->active_users_count, 0, ',', '.') }}
                        </div>

                        <div class="text-[11px] text-slate-500 dark:text-slate-400">
                            admin aktif
                        </div>
                    </div>

                    <div class="flex shrink-0 overflow-hidden rounded-xl border border-slate-200 text-center text-xs dark:border-slate-700">
                        <div class="min-w-16 bg-violet-50 px-2 py-1.5 dark:bg-violet-400/10">
                            <div class="font-black text-violet-700 dark:text-violet-300">
                                {{ number_format($area->customers_count, 0, ',', '.') }}
                            </div>
                            <div class="mt-0.5 text-[9px] font-bold uppercase tracking-wide text-violet-600/80 dark:text-violet-300/80">
                                Pelanggan
                            </div>
                        </div>

                        <div class="min-w-16 border-l border-slate-200 bg-cyan-50 px-2 py-1.5 dark:border-slate-700 dark:bg-cyan-400/10">
                            <div class="font-black text-cyan-700 dark:text-cyan-300">
                                {{ number_format($area->routers_count, 0, ',', '.') }}
                            </div>
                            <div class="mt-0.5 text-[9px] font-bold uppercase tracking-wide text-cyan-600/80 dark:text-cyan-300/80">
                                Router
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                    <details class="group min-w-0">
                        <summary class="cursor-pointer list-none text-xs font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                            <span class="inline-flex items-center gap-1">
                                Selengkapnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </span>
                        </summary>

                        <div class="mt-2 w-56 rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-[11px] text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <div class="flex justify-between gap-3">
                                <span>Status</span>
                                <span class="font-bold {{ $area->active ? 'text-emerald-700 dark:text-emerald-300' : '' }}">
                                    {{ $area->active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            <div class="mt-1 flex justify-between gap-3">
                                <span>Kode</span>
                                <span class="max-w-[65%] break-all text-right font-mono font-bold">
                                    {{ $area->code ?: '-' }}
                                </span>
                            </div>

                            <div class="mt-1.5 border-t border-slate-200 pt-1.5 dark:border-slate-700">
                                <div class="font-semibold text-slate-500 dark:text-slate-400">
                                    Keterangan
                                </div>

                                <p class="mt-1 leading-5 text-slate-600 dark:text-slate-300">
                                    {{ $area->description ?: 'Belum ada keterangan wilayah.' }}
                                </p>
                            </div>
                        </div>
                    </details>

                    <div class="flex shrink-0 items-center gap-1">
                        <a href="{{ route('areas.edit', $area) }}"
                           class="inline-flex h-8 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 px-2.5 text-[11px] font-bold text-violet-700 transition hover:bg-violet-100 dark:border-violet-500/30 dark:bg-violet-400/10 dark:text-violet-300 dark:hover:bg-violet-400/20">
                            Edit
                        </a>

                        @if ($area->customers_count === 0 && $area->routers_count === 0)
                            <form method="POST"
                                  action="{{ route('areas.destroy', $area) }}"
                                  onsubmit="return confirm('Hapus wilayah {{ addslashes($area->name) }}?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        title="Hapus wilayah"
                                        class="inline-flex h-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 px-2.5 text-[11px] font-bold text-rose-700 transition hover:bg-rose-100 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-10 text-center dark:border-slate-700 dark:bg-slate-900 sm:col-span-2 xl:col-span-3">
                <h2 class="text-base font-black text-slate-900 dark:text-white">
                    Belum ada wilayah operasional
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Tambahkan wilayah sebelum membuat penugasan akses operasional.
                </p>

                <a href="{{ route('areas.create') }}"
                   style="background:#7c3aed !important;color:#ffffff !important;border:1px solid #6d28d9 !important;"
                   class="mt-4 inline-flex min-h-10 items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-bold transition hover:brightness-110">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg>
                    Tambah Wilayah
                </a>
            </div>
        @endforelse
    </div>

    <p class="px-1 text-xs leading-5 text-slate-500 dark:text-slate-400">
        Wilayah nonaktif tetap menyimpan data historis dan tidak dapat dipilih untuk penugasan operasional baru.
    </p>
</div>
@endsection
