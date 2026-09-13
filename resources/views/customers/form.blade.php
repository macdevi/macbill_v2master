<div class="grid gap-4 md:grid-cols-2">
    <label class="block">
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
    </label>

    <label class="block">
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
    </label>

    <label class="block">
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
    </label>

    <label class="block">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Password PPPoE
            @if (isset($customer))
                <span class="font-normal text-slate-400">(kosongkan bila tidak diubah)</span>
            @endif
        </span>
        <input name="pppoe_password"
               type="password"
               value=""
               class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-700 dark:text-white dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
               {{ isset($customer) ? '' : 'required' }}>
        @error('pppoe_password')
            <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </label>

    <label class="block">
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
    </label>

    <label class="block">
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
    </label>

    <label class="block">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Router
        </span>
        <select name="router_id"
                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
                required>
            @foreach ($routers as $router)
                <option value="{{ $router->id }}"
                    @selected(old('router_id', $customer->router_id ?? '') == $router->id)>
                    {{ $router->name }} — {{ $router->host }}
                </option>
            @endforeach
        </select>
        @error('router_id')
            <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </label>

    <label class="block">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Paket Internet
        </span>
        <select name="internet_package_id"
                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
                required>
            @foreach ($packages as $package)
                <option value="{{ $package->id }}"
                    @selected(old('internet_package_id', $customer->internet_package_id ?? '') == $package->id)>
                    {{ $package->name }} — Rp {{ number_format((float) $package->monthly_price, 0, ',', '.') }}
                </option>
            @endforeach
        </select>
        @error('internet_package_id')
            <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </label>

    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-500/30 dark:bg-amber-500/10">
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
    </div>
</div>

@php
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
</fieldset>

<label class="block">
    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
        Alamat
    </span>
    <textarea name="address"
              rows="3"
              class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-700 dark:text-white dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20">{{ old('address', $customer->address ?? '') }}</textarea>
    @error('address')
        <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
    @enderror
</label>
