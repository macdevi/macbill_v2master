@php $isEdit = isset($customer) && $customer->exists; @endphp
<div class="cc-editor" data-cc-editor data-edit="{{ $isEdit ? '1' : '0' }}">
<section class="cc-section"><h2>@include('customers.partials.workspace-icon',['name'=>'users']) 1. Identitas pelanggan</h2><p>Wilayah menentukan pembatasan akses petugas terhadap pelanggan ini.</p><div class="cc-field-grid">    <label class="block">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Nama Pelanggan
        </span>
        <input name="name"
               type="text"
               value="{{ old('name', $customer->name ?? '') }}"
               class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-700 dark:text-white dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
               required>
        @error('name')
            <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </label>    <label class="block">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            No. Telepon
        </span>
        <input name="phone"
               type="text"
               value="{{ old('phone', $customer->phone ?? '') }}"
               class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-700 dark:text-white dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20">
        @error('phone')
            <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </label>    <label class="block">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Wilayah Operasional
        </span>
        <select name="area_id"
                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100 @error('area_id') border-rose-400 @enderror dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-violet-400 dark:focus:ring-violet-500/20"
                required>
            <option value="">Pilih wilayah operasional</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}"
                    @selected(old('area_id', $customer->area_id ?? '') == $area->id)>
                    {{ $area->name }} — {{ $area->code }}
                </option>
            @endforeach
        </select>
        @error('area_id')
            <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </label></div><div style="margin-top:17px"><label class="block">
    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
        Alamat
    </span>
    <textarea name="address"
              rows="3"
              class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-700 dark:text-white dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20">{{ old('address', $customer->address ?? '') }}</textarea>
    @error('address')
        <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
    @enderror
</label></div></section>
<section class="cc-section"><h2>@include('customers.partials.workspace-icon',['name'=>'router']) 2. Router dan paket</h2><p>Pilih paket yang memang terhubung ke router pelanggan.</p><div class="cc-field-grid"><label class="block">
<span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Router pelanggan</span>
@if($isEdit)
<input type="hidden" name="router_id" value="{{ $customer->router_id }}">
<select data-cc-router data-active="{{ ($canSyncRouter ?? false) && $customer->router?->active ? '1' : '0' }}" disabled class="mt-1 w-full rounded-xl border px-4 py-3">
<option value="{{ $customer->router_id }}" selected>{{ $customer->router?->name ?: 'Router tidak tersedia' }}{{ $customer->router?->active ? '' : ' (nonaktif/tidak tersedia)' }}</option>
</select>
<p class="cc-row-note" style="margin-top:6px">Tetap. Pemindahan router tidak dilakukan melalui form ini.</p>
@else
<select name="router_id" data-cc-router required class="mt-1 w-full rounded-xl border px-4 py-3">
<option value="">Pilih router terlebih dahulu</option>
@foreach($routers as $router)<option value="{{ $router->id }}" data-cc-areas="{{ $router->accessibleAreas->filter(fn($area)=>$area->active)->pluck('id')->implode(',') }}" @selected((string) old('router_id','') === (string) $router->id)>{{ $router->name }}</option>@endforeach
</select>
@endif
@error('router_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
</label><label class="block">
<span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Paket internet</span>
<select name="internet_package_id" data-cc-package required class="mt-1 w-full rounded-xl border px-4 py-3">
<option value="">Pilih paket internet</option>
@foreach($packages as $package)
<option value="{{ $package->id }}" data-active="{{ $package->active ? '1' : '0' }}" data-routers="{{ $package->routers->filter(fn($router) => $router->active)->pluck('id')->implode(',') }}" data-original="{{ $isEdit && (int) $customer->internet_package_id === (int) $package->id ? '1' : '0' }}" @selected((string) old('internet_package_id',$customer->internet_package_id ?? '') === (string) $package->id)>{{ $package->name }} | Rp {{ number_format((float) $package->monthly_price,0,',','.') }}{{ $package->active ? '' : ' (nonaktif)' }}</option>
@endforeach
</select>
<p class="cc-row-note" style="margin-top:6px" data-cc-package-note>Pilih router terlebih dahulu.</p>
@error('internet_package_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
</label></div></section>
<section class="cc-section"><h2>@include('customers.partials.workspace-icon',['name'=>'account']) 3. Akun PPPoE</h2><p>Data akun koneksi. Menyimpan billing saja tidak mengubah akun pada perangkat.</p><div class="cc-field-grid">    <label class="block">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Username PPPoE
        </span>
        <input name="pppoe_username"
               type="text"
               value="{{ old('pppoe_username', $customer->pppoe_username ?? '') }}"
               class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-700 dark:text-white dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
               required>
        @error('pppoe_username')
            <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </label>    <label class="block">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Password PPPoE
            @if (isset($customer))
                <span class="font-normal text-slate-400">(kosongkan bila tidak diubah)</span>
            @endif
        </span>
        <input name="pppoe_password"
               type="password" autocomplete="new-password"
               value=""
               class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-700 dark:text-white dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
               {{ isset($customer) ? '' : 'required' }}>
        @error('pppoe_password')
            <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </label></div></section>
<section class="cc-section"><h2>@include('customers.partials.workspace-icon',['name'=>'billing']) 4. Pengaturan billing</h2><p>Harga khusus kosong mengikuti harga paket. Nilai 0 berarti harga khusus nol, bukan kosong.</p><div class="cc-field-grid">    <label class="block">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Tanggal Jatuh Tempo
        </span>
        <input name="due_day"
               type="number"
               min="1"
               max="28"
               value="{{ old('due_day', $customer->due_day ?? '') }}"
               class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-700 dark:text-white dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
               required>
        @error('due_day')
            <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </label>    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-500/30 dark:bg-amber-500/10">
        <label class="block">
            <span class="text-xs font-bold text-amber-800 dark:text-amber-200">
                Harga Khusus per Bulan
                <span class="font-normal">(opsional)</span>
            </span>

            <div class="mt-1 flex overflow-hidden rounded-lg border border-amber-200 bg-white dark:border-amber-500/30 dark:bg-slate-900">
                <span class="flex items-center border-r border-amber-200 bg-amber-50 px-3 text-sm font-bold text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                    Rp
                </span>

                <input name="monthly_price_override"
                       type="number"
                       min="0"
                       step="0.01"
                       inputmode="decimal"
                       value="{{ old('monthly_price_override', isset($customer) && $customer->monthly_price_override !== null ? $customer->monthly_price_override : '') }}"
                       placeholder="Ikuti harga paket"
                       class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2.5 text-sm font-bold text-slate-900 outline-none dark:text-white">
            </div>

            <p class="mt-1.5 text-[11px] leading-relaxed text-amber-700 dark:text-amber-300">
                Kosongkan agar invoice mengikuti harga Paket Internet yang dipilih.
            </p>

            @error('monthly_price_override')
                <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </label>
    </div></div>@php
    $selectedTaxMode = old(
        'tax_mode',
        isset($customer) ? ($customer->tax_mode ?? 'inclusive') : 'exclusive'
    );
@endphp

<fieldset class="rounded-xl border border-violet-200 bg-violet-50 p-4 dark:border-violet-500/30 dark:bg-violet-500/10">
    <legend class="px-1 text-xs font-bold text-violet-800 dark:text-violet-200">
        Pajak Pelanggan
    </legend>

    <p class="mb-3 text-[11px] leading-relaxed text-violet-700 dark:text-violet-300">
        Mode ini berlaku untuk harga paket normal maupun harga khusus pelanggan.
    </p>

    <div class="space-y-2.5">
        <label class="flex cursor-pointer items-start gap-3 rounded-lg px-2 py-1.5 transition hover:bg-violet-100/70 dark:hover:bg-violet-400/10">
            <input type="radio"
                   name="tax_mode"
                   value="none"
                   @checked($selectedTaxMode === 'none')
                   class="mt-0.5 h-4 w-4 border-slate-300 text-violet-600 focus:ring-violet-500 dark:border-slate-600 dark:bg-slate-800">
            <span>
                <span class="block text-sm font-bold text-slate-800 dark:text-slate-100">
                    Tidak dikenakan pajak
                </span>
                <span class="block text-[11px] text-slate-500 dark:text-slate-400">
                    Harga layanan menjadi total tagihan tanpa pajak.
                </span>
            </span>
        </label>

        <label class="flex cursor-pointer items-start gap-3 rounded-lg px-2 py-1.5 transition hover:bg-violet-100/70 dark:hover:bg-violet-400/10">
            <input type="radio"
                   name="tax_mode"
                   value="inclusive"
                   @checked($selectedTaxMode === 'inclusive')
                   class="mt-0.5 h-4 w-4 border-slate-300 text-violet-600 focus:ring-violet-500 dark:border-slate-600 dark:bg-slate-800">
            <span>
                <span class="block text-sm font-bold text-slate-800 dark:text-slate-100">
                    Harga sudah termasuk pajak
                </span>
                <span class="block text-[11px] text-slate-500 dark:text-slate-400">
                    Nominal pelanggan tidak berubah; sistem memisahkan dasar layanan dan pajak pada invoice.
                </span>
            </span>
        </label>

        <label class="flex cursor-pointer items-start gap-3 rounded-lg px-2 py-1.5 transition hover:bg-violet-100/70 dark:hover:bg-violet-400/10">
            <input type="radio"
                   name="tax_mode"
                   value="exclusive"
                   @checked($selectedTaxMode === 'exclusive')
                   class="mt-0.5 h-4 w-4 border-slate-300 text-violet-600 focus:ring-violet-500 dark:border-slate-600 dark:bg-slate-800">
            <span>
                <span class="block text-sm font-bold text-slate-800 dark:text-slate-100">
                    Tambahkan pajak ke tagihan
                </span>
                <span class="block text-[11px] text-slate-500 dark:text-slate-400">
                    Pajak dari Pengaturan Billing akan ditambahkan di atas harga layanan saat invoice baru dibuat.
                </span>
            </span>
        </label>
    </div>

    @error('tax_mode')
        <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
    @enderror
</fieldset></section>
</div>
<script>
(() => {
    const editor = document.querySelector('[data-cc-editor]');
    if (!editor) return;
    const router = editor.querySelector('[data-cc-router]');
    const packages = editor.querySelector('[data-cc-package]');
    const note = editor.querySelector('[data-cc-package-note]');
    const edit = editor.dataset.edit === '1';
    const area = editor.querySelector('select[name="area_id"]');
    function update() {
        if (!edit && area) {
            Array.from(router.options).forEach(option => {
                if (!option.value) return;
                const allowed = !!area.value && (option.dataset.ccAreas || '').split(',').includes(area.value);
                option.hidden = !allowed;
                option.disabled = !allowed;
            });
            if (router.selectedOptions[0] && router.selectedOptions[0].disabled) router.value = '';
            router.disabled = !area.value;
        }
        const id = router.value;
        let available = 0;
        Array.from(packages.options).forEach(option => {
            if (!option.value) return;
            const valid = option.dataset.active === '1' && (option.dataset.routers || '').split(',').includes(id);
            const original = edit && option.dataset.original === '1';
            option.hidden = !(id && (valid || original));
            option.disabled = option.hidden;
            if (valid) available++;
        });
        const selected = packages.selectedOptions[0];
        if (selected && selected.disabled) packages.value = '';
        packages.disabled = !id;
        const chosen = packages.selectedOptions[0];
        const syncAvailable = !!(id && router.dataset.active !== '0' && chosen && chosen.value && chosen.dataset.active === '1' && (chosen.dataset.routers || '').split(',').includes(id));
        editor.dataset.syncAvailable = syncAvailable ? '1' : '0';
        if (!id) note.textContent = 'Pilih router terlebih dahulu.';
        else if (chosen && chosen.value && !syncAvailable) note.textContent = 'Relasi lama tetap ditampilkan. Simpan billing saja dapat mempertahankannya; pengiriman ke MikroTik memerlukan router dan paket aktif yang sesuai.';
        else if (!available) note.textContent = 'Belum ada paket aktif untuk router ini. Hubungkan paket melalui menu Paket Internet.';
        else note.textContent = 'Hanya paket aktif yang terhubung ke router pilihan yang dapat dipilih.';
        editor.dispatchEvent(new CustomEvent('cc-package-change', {bubbles:true}));
    }
    if (area) area.addEventListener('change', update);
    router.addEventListener('change', update);
    packages.addEventListener('change', update);
    update();
})();
</script>
