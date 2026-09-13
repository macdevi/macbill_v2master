@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="mx-auto max-w-2xl">
    <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-cyan-100 bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700 dark:border-cyan-400/15 dark:bg-cyan-400/10 dark:text-cyan-300">
                <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                Administrasi
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                Tambah User Baru
            </h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Buat akun baru dan tentukan peran aksesnya pada sistem billing.
            </p>
        </div>

        <a href="/users"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white dark:focus:ring-slate-700">
            <span aria-hidden="true">←</span>
            Kembali
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-slate-800 dark:bg-slate-800/40 sm:px-6">
            <h2 class="text-sm font-extrabold text-slate-800 dark:text-slate-100">
                Informasi Akun
            </h2>
            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">
                Pastikan username dan email yang digunakan belum terdaftar.
            </p>
        </div>

        <form method="POST" action="/users" class="p-5 sm:p-6">
            @csrf

            <div class="grid gap-5">
                <div>
                    <label for="name" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
                        Nama User
                    </label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}"
                           placeholder="Contoh: Budi Santoso" autocomplete="name" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('name') border-rose-400 focus:border-rose-500 focus:ring-rose-100 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-600 dark:focus:border-cyan-400 dark:focus:ring-cyan-400/15">
                    @error('name')
                        <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="username" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
                        Username Login
                    </label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}"
                           placeholder="Contoh: budi.admin" autocomplete="username" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('username') border-rose-400 focus:border-rose-500 focus:ring-rose-100 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-600 dark:focus:border-cyan-400 dark:focus:ring-cyan-400/15">
                    @error('username')
                        <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
                        Email
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                           placeholder="nama@contoh.com" autocomplete="email" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('email') border-rose-400 focus:border-rose-500 focus:ring-rose-100 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-600 dark:focus:border-cyan-400 dark:focus:ring-cyan-400/15">
                    @error('email')
                        <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
                        Password
                    </label>
                    <input id="password" name="password" type="password"
                           placeholder="Buat password yang kuat" autocomplete="new-password" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('password') border-rose-400 focus:border-rose-500 focus:ring-rose-100 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-600 dark:focus:border-cyan-400 dark:focus:ring-cyan-400/15">
                    @error('password')
                        <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
                        Peran Akses
                    </label>
                    <select id="role" name="role" required
                            class="w-full cursor-pointer rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm font-medium text-slate-800 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('role') border-rose-400 focus:border-rose-500 focus:ring-rose-100 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-cyan-400 dark:focus:ring-cyan-400/15">
                        <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>
                            Admin — Kelola operasional billing
                        </option>
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>
                            Kasir — Kelola pembayaran
                        </option>
                    </select>
                    <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-slate-400">
                        Peran Super Admin tidak dapat dibuat dari halaman ini.
                    </p>
                    @error('role')
                        <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>


                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">
                            Wilayah Penugasan
                        </label>
                        <span class="rounded-full bg-violet-50 px-2 py-1 text-[10px] font-extrabold uppercase tracking-wide text-violet-700 dark:bg-violet-400/10 dark:text-violet-300">
                            Wajib dipilih
                        </span>
                    </div>

                    <p class="mb-3 text-xs leading-5 text-slate-500 dark:text-slate-400">
                        Admin dan kasir hanya dapat mengakses data dari wilayah yang dipilih.
                    </p>

                    @php
                        $selectedAreaIds = collect(old(
                            'area_ids',
                            isset($user) ? $user->areas->pluck('id')->all() : []
                        ))->map(fn ($id) => (int) $id)->all();
                    @endphp

                    @if($areas->isEmpty())
                        <div class="rounded-xl border border-dashed border-amber-300 bg-amber-50 px-3.5 py-3 text-xs font-medium text-amber-700 dark:border-amber-400/30 dark:bg-amber-400/10 dark:text-amber-300">
                            Belum ada wilayah aktif. Buat dan aktifkan wilayah terlebih dahulu.
                        </div>
                    @else
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach($areas as $area)
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white px-3.5 py-3 transition hover:border-violet-300 hover:bg-violet-50/40 dark:border-slate-700 dark:bg-slate-950 dark:hover:border-violet-400/40 dark:hover:bg-violet-400/5">
                                    <input type="checkbox"
                                           name="area_ids[]"
                                           value="{{ $area->id }}"
                                           @checked(in_array($area->id, $selectedAreaIds, true))
                                           class="mt-0.5 h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500 dark:border-slate-600 dark:bg-slate-900 dark:focus:ring-violet-400">
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-bold text-slate-800 dark:text-slate-100">
                                            {{ $area->name }}
                                        </span>
                                        <span class="mt-0.5 block font-mono text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                            {{ $area->code }}
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @error('area_ids')
                        <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('area_ids.*')
                        <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            <div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end dark:border-slate-800">
                <a href="/users"
                   class="inline-flex justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-cyan-300/40 dark:bg-cyan-500 dark:hover:bg-cyan-400 dark:focus:ring-cyan-400/20">
                    <span class="text-lg leading-none" aria-hidden="true">+</span>
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
