@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
@php
    $initial = strtoupper(substr($user->name ? $user->name : 'U', 0, 1));
    $roleLabel = $user->role ? ucfirst(str_replace('_', ' ', $user->role)) : 'User';
@endphp

<div class="mx-auto max-w-3xl">
    <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-cyan-100 bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700 dark:border-cyan-400/15 dark:bg-cyan-400/10 dark:text-cyan-300">
                <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                Administrasi
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                Detail User
            </h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Ringkasan profil dan hak akses pengguna.
            </p>
        </div>

        <a href="/users"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white dark:focus:ring-slate-700">
            <span aria-hidden="true">←</span>
            Semua User
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="relative overflow-hidden border-b border-slate-100 bg-gradient-to-br from-cyan-50 via-white to-sky-50 px-5 py-7 dark:border-slate-800 dark:from-cyan-500/10 dark:via-slate-900 dark:to-blue-500/10 sm:px-7">
            <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-cyan-200/40 blur-3xl dark:bg-cyan-400/10"></div>

            <div class="relative flex min-w-0 items-center gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 text-lg font-black text-white shadow-lg shadow-cyan-500/20">
                    {{ $initial }}
                </div>

                <div class="min-w-0">
                    <h2 class="truncate text-xl font-black tracking-tight text-slate-900 dark:text-white">
                        {{ $user->name }}
                    </h2>
                    <p class="mt-1 truncate text-sm font-medium text-slate-500 dark:text-slate-400">
                        {{ $user->username ? '@' . $user->username : 'Username belum diatur' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="p-5 sm:p-7">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        Nama Lengkap
                    </dt>
                    <dd class="mt-2 break-words text-sm font-bold text-slate-800 dark:text-slate-100">
                        {{ $user->name }}
                    </dd>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        Username
                    </dt>
                    <dd class="mt-2 break-words text-sm font-bold text-slate-800 dark:text-slate-100">
                        {{ $user->username ?? '-' }}
                    </dd>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        Email
                    </dt>
                    <dd class="mt-2 break-all text-sm font-bold text-slate-800 dark:text-slate-100">
                        {{ $user->email ?? '-' }}
                    </dd>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        Peran Akses
                    </dt>
                    <dd class="mt-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-cyan-50 px-3 py-1.5 text-xs font-extrabold text-cyan-700 dark:bg-cyan-400/10 dark:text-cyan-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                            {{ $roleLabel }}
                        </span>
                    </dd>
                </div>
            </dl>

            <div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end dark:border-slate-800">
                <a href="/users"
                   class="inline-flex justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                    Kembali
                </a>

                <a href="/users/{{ $user->id }}/edit"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-cyan-300/40 dark:bg-cyan-500 dark:hover:bg-cyan-400 dark:focus:ring-cyan-400/20">
                    <span aria-hidden="true">✎</span>
                    Edit User
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
