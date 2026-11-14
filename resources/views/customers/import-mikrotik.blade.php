@extends('layouts.app')

@section('title', 'Impor Pelanggan MikroTik')

@section('content')
{{-- mikrotik-import-simple-v1 --}}
<div class="mx-auto max-w-3xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">
            Impor Pelanggan MikroTik
        </h1>
        <a href="{{ route('customers.index') }}"
           class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:text-slate-200">
            Kembali
        </a>
    </div>

    @error('customers')
        <div role="alert"
             class="rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-800 dark:bg-rose-950 dark:text-rose-200">
            {{ $message }}
        </div>
    @enderror

    <div class="rounded-2xl border border-cyan-200 bg-cyan-50 p-4 text-sm leading-6 text-cyan-900 dark:border-cyan-800 dark:bg-cyan-950 dark:text-cyan-200">
        <h2 class="font-bold">Cara impor pelanggan</h2>
        <ol class="mt-2 list-decimal space-y-2 pl-5">
            <li>Pilih router dan wilayah tujuan pelanggan.</li>
            <li>Klik Ambil Akun PPPoE untuk membuka preview.</li>
            <li>Di preview, centang pelanggan yang ingin diimpor. Periksa nama, paket, dan jatuh tempo. Isi nomor HP dan alamat jika tersedia, lalu klik Konfirmasi &amp; Import Terpilih.</li>
        </ol>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
<form method="POST" action="{{ route('customers.import.mikrotik.preview') }}" class="p-5 sm:p-6">
            @csrf

            <div>
                <label for="router_id" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
                    Router MikroTik
                </label>
                <select id="router_id" name="router_id" required
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm font-medium text-slate-800 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('router_id') border-rose-400 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    <option value="">Pilih router aktif</option>
                    @foreach($routers as $router)
                        <option value="{{ $router->id }}" data-ra-areas="{{ $router->accessibleAreas->filter(fn($area)=>$area->active)->pluck('id')->implode(',') }}" {{ old('router_id') == $router->id ? 'selected' : '' }}>
                            {{ $router->name }} — {{ $router->host }}:{{ $router->port }}
                        </option>
                    @endforeach
                </select>
                @error('router_id')
                    <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-5">
                <label for="area_id" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">
                    Wilayah Tujuan
                </label>
                <select id="area_id" name="area_id" required
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm font-medium text-slate-800 shadow-sm outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100 @error('area_id') border-rose-400 @enderror dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    <option value="">Pilih wilayah tujuan import</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>
                            {{ $area->name }} — {{ $area->code }}
                        </option>
                    @endforeach
                </select>
                @error('area_id')
                    <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror

            </div>



            <div class="mt-7 flex justify-end border-t border-slate-100 pt-5 dark:border-slate-800">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-cyan-300/40 dark:bg-cyan-500 dark:hover:bg-cyan-400">
                    Ambil Akun PPPoE
                    <span aria-hidden="true">→</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(() => {
    const router = document.getElementById('router_id');
    const area = document.getElementById('area_id');
    if (!router || !area) return;
    function updateArea() {
        const selected = router.selectedOptions[0];
        const allowed = selected && selected.value ? (selected.dataset.raAreas || '').split(',') : [];
        Array.from(area.options).forEach(option => {
            if (!option.value) return;
            option.hidden = !allowed.includes(option.value);
            option.disabled = option.hidden;
        });
        if (area.selectedOptions[0] && area.selectedOptions[0].disabled) area.value = '';
        area.disabled = !router.value;
    }
    router.addEventListener('change', updateArea);
    updateArea();
})();
</script>

@endsection
