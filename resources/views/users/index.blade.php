@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="mx-auto max-w-6xl">
    <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-cyan-100 bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700 dark:border-cyan-400/15 dark:bg-cyan-400/10 dark:text-cyan-300">
                <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                Administrasi
            </div>

            <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                Manajemen User
            </h1>

            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Kelola akun pengguna dan perannya dengan aman.
            </p>
        </div>

        <a href="/users/create"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-cyan-300/40 dark:bg-cyan-500 dark:hover:bg-cyan-400 dark:focus:ring-cyan-400/20">
            <span class="text-lg leading-none" aria-hidden="true">+</span>
            Tambah User
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 shadow-sm dark:border-emerald-400/15 dark:bg-emerald-400/10 dark:text-emerald-300">
            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-xs font-black text-white">✓</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 flex items-center gap-3">
        <p class="shrink-0 text-sm font-semibold text-slate-500 dark:text-slate-400">
            {{ $users->count() }} user terdaftar
        </p>
        <div class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($users as $u)
            @php
                $role = $u->role ? str_replace('_', ' ', $u->role) : 'User';
                $initial = strtoupper(substr($u->name ? $u->name : 'U', 0, 1));
            @endphp

            <article class="group relative flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-cyan-200 hover:shadow-lg hover:shadow-slate-200/60 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-cyan-500/35 dark:hover:shadow-black/20">
                <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-cyan-400 via-sky-500 to-blue-500 opacity-70"></div>

                <div class="flex min-w-0 items-start gap-3.5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-sm font-black text-cyan-700 ring-1 ring-cyan-100 dark:bg-cyan-400/10 dark:text-cyan-300 dark:ring-cyan-400/15">
                        {{ $initial }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="truncate text-base font-extrabold text-slate-900 dark:text-white" title="{{ $u->name }}">
                            {{ $u->name }}
                        </h2>

                        <p class="mt-1 truncate text-sm text-slate-500 dark:text-slate-400" title="{{ $u->username ?? '-' }}">
                            {{ $u->username ? '@' . $u->username : 'Username belum diatur' }}
                        </p>

                        @if($u->isSuperAdmin())
                            <p class="mt-2 text-xs font-semibold text-violet-600 dark:text-violet-300">
                                Akses seluruh wilayah
                            </p>
                        @elseif($u->areas->isNotEmpty())
                            <p class="mt-2 truncate text-xs font-semibold text-slate-500 dark:text-slate-400"
                               title="{{ $u->areas->pluck('name')->join(', ') }}">
                                {{ $u->areas->count() }} wilayah · {{ $u->areas->pluck('name')->join(', ') }}
                            </p>
                        @else
                            <p class="mt-2 text-xs font-semibold text-amber-600 dark:text-amber-300">
                                Belum ada wilayah
                            </p>
                        @endif

                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                        {{ ucfirst($role) }}
                    </span>

                    <div class="flex items-center gap-3 text-sm font-semibold">
                        <a href="/users/{{ $u->id }}"
                           class="text-slate-500 transition hover:text-cyan-600 dark:text-slate-400 dark:hover:text-cyan-300">
                            Detail
                        </a>

                        <a href="/users/{{ $u->id }}/edit"
                           class="text-cyan-600 transition hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300">
                            Edit
                        </a>

                        <form method="POST"
                              action="/users/{{ $u->id }}"
                              onsubmit="return confirm('Hapus user ini?')"
                              class="m-0">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="text-rose-500 transition hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900 md:col-span-2 xl:col-span-3">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 text-xl font-black text-cyan-600 dark:bg-cyan-400/10 dark:text-cyan-300">
                    +
                </div>
                <h2 class="mt-4 text-lg font-extrabold text-slate-900 dark:text-white">
                    Belum ada user
                </h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Tambahkan akun pertama untuk mengatur akses sistem billing.
                </p>
                <a href="/users/create"
                   class="mt-5 inline-flex rounded-xl bg-cyan-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-cyan-700 dark:bg-cyan-500 dark:hover:bg-cyan-400">
                    Tambah User
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
