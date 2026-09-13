@csrf
@if(isset($area) && $area->exists)
    @method('PUT')
@endif

<div class="grid gap-5">
    <div class="grid gap-5 sm:grid-cols-[minmax(0,0.6fr)_minmax(0,1fr)]">
        <div>
            <label for="code" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
                Kode Wilayah
            </label>
            <input id="code"
                   name="code"
                   type="text"
                   value="{{ old('code', $area->code) }}"
                   placeholder="Contoh: DESA-A"
                   maxlength="50"
                   required
                   class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm font-bold uppercase text-slate-800 shadow-sm outline-none transition placeholder:normal-case placeholder:font-normal placeholder:text-slate-400 focus:border-violet-500 focus:ring-4 focus:ring-violet-100 @error('code') border-rose-400 focus:border-rose-500 focus:ring-rose-100 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-600 dark:focus:border-violet-400 dark:focus:ring-violet-400/15">
            @error('code')
                <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                Gunakan huruf, angka, titik, garis bawah, atau tanda hubung.
            </p>
        </div>

        <div>
            <label for="name" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
                Nama Wilayah
            </label>
            <input id="name"
                   name="name"
                   type="text"
                   value="{{ old('name', $area->name) }}"
                   placeholder="Contoh: Desa Sukamaju"
                   maxlength="150"
                   required
                   class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-violet-500 focus:ring-4 focus:ring-violet-100 @error('name') border-rose-400 focus:border-rose-500 focus:ring-rose-100 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-600 dark:focus:border-violet-400 dark:focus:ring-violet-400/15">
            @error('name')
                <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
            Keterangan <span class="font-medium text-slate-400">(opsional)</span>
        </label>
        <textarea id="description"
                  name="description"
                  rows="4"
                  maxlength="2000"
                  placeholder="Contoh: Wilayah layanan jaringan di Desa Sukamaju dan sekitarnya."
                  class="w-full resize-y rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-violet-500 focus:ring-4 focus:ring-violet-100 @error('description') border-rose-400 focus:border-rose-500 focus:ring-rose-100 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-600 dark:focus:border-violet-400 dark:focus:ring-violet-400/15">{{ old('description', $area->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/70 p-4 transition hover:border-violet-200 dark:border-slate-700 dark:bg-slate-800/45 dark:hover:border-violet-400/30">
        <input type="hidden" name="active" value="0">
        <input type="checkbox"
               name="active"
               value="1"
               @checked(old('active', $area->exists ? $area->active : true))
               class="mt-0.5 h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500 dark:border-slate-600 dark:bg-slate-900">
        <span>
            <span class="block text-sm font-bold text-slate-700 dark:text-slate-200">Wilayah aktif</span>
            <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">
                Wilayah aktif dapat dipakai untuk penugasan pengguna dan data operasional baru.
            </span>
        </span>
    </label>
</div>

<div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end dark:border-slate-800">
    <a href="{{ route('areas.index') }}"
       class="inline-flex justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
        Batal
    </a>
    <button type="submit"
            class="inline-flex justify-center rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-violet-700 focus:outline-none focus:ring-4 focus:ring-violet-300/50 dark:bg-violet-500 dark:hover:bg-violet-400 dark:focus:ring-violet-400/20">
        {{ isset($area) && $area->exists ? 'Simpan Perubahan' : 'Simpan Wilayah' }}
    </button>
</div>
