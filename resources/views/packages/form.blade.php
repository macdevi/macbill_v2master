@include('packages.partials.editor-theme')
@include('packages.partials.catalog-theme')
@php
    $editingPackage = $package ?? null;
    $isEdit = $editingPackage !== null;
    $value = fn ($field, $default = '') => old($field, $editingPackage?->getAttribute($field) ?? $default);
    $selected = array_map('intval', session()->hasOldInput() ? (array) old('router_ids', []) : (array) $selectedRouterIds);
@endphp
<div class="package-editor">
    <header class="p-head">
        <a href="{{ route('packages.index') }}">@include('packages.partials.catalog-icon',['name'=>'back']) Kembali ke paket internet</a>
        <h1>{{ $isEdit ? 'Edit paket internet' : 'Tambah paket internet' }}</h1>
        <p class="p-note">Atur paket dan router pemakainya. Menyimpan form tidak mengubah konfigurasi MikroTik.</p>
    </header>
    @if ($errors->any())<div class="p-error-box" role="alert">@foreach ($errors->all() as $message)<p>{{ $message }}</p>@endforeach</div>@endif
    <form method="post" action="{{ $isEdit ? route('packages.update', $editingPackage) : route('packages.store') }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif
        <section class="p-panel">
            <h2>@include('packages.partials.catalog-icon',['name'=>'package']) 1. Identitas paket</h2>
            <p class="p-note">Nama paket untuk billing. Nama profil harus sesuai profil yang digunakan di MikroTik.</p>
            <div class="p-fields">
                <label class="p-field">Nama paket<input name="name" required maxlength="100" value="{{ $value('name') }}" placeholder="Contoh: Rumah 20 Mbps"></label>
                <label class="p-field">Profil MikroTik<input name="mikrotik_profile" required maxlength="100" value="{{ $value('mikrotik_profile') }}" placeholder="Contoh: HOME-20M" spellcheck="false"></label>
            </div>
        </section>
        <section class="p-panel">
            <h2>@include('packages.partials.catalog-icon',['name'=>'router']) 2. Router pemakai paket</h2>
            <p class="p-note">Pilih satu router untuk paket khusus, atau beberapa router untuk paket bersama dengan profil, harga, dan pengaturan paket yang sama.</p>
            <div class="p-router-list">
                @forelse ($routers as $router)
                    @php
                        $usage = (int) ($routerUsage[$router->id] ?? 0);
                        $locked = $usage > 0 || !$router->active;
                        $checked = in_array((int) $router->id, $selected, true) || $usage > 0 || (!$router->active && in_array((int) $router->id, $selectedRouterIds, true));
                    @endphp
                    @if ($locked && $checked)<input type="hidden" name="router_ids[]" value="{{ $router->id }}">@endif
                    <label class="p-router">
                        <input type="checkbox" name="router_ids[]" value="{{ $router->id }}" @checked($checked) @disabled($locked)>
                        <span><strong>{{ $router->name }}</strong>
                            <small>{{ $router->active ? 'Aktif di billing' : 'Nonaktif, hubungan lama dipertahankan' }}</small>
                            @if ($usage > 0)<small>{{ $usage }} pelanggan memakai paket ini. Router tidak dapat dilepas.</small>@endif
                        </span>
                    </label>
                @empty
                    <p class="p-note">Belum ada router aktif. Tambahkan router terlebih dahulu.</p>
                @endforelse
            </div>
            @error('router_ids')<p class="p-error">{{ $message }}</p>@enderror
        </section>
        <section class="p-panel">
            <h2>@include('packages.partials.catalog-icon',['name'=>'download']) 3. Kecepatan dan harga</h2>
            <div class="p-fields">
                <label class="p-field">Download (Mbps)<input type="number" name="download_speed" required min="{{ $isEdit ? 0 : 1 }}" max="2147483647" step="1" value="{{ $value('download_speed') }}"></label>
                <label class="p-field">Upload (Mbps)<input type="number" name="upload_speed" required min="{{ $isEdit ? 0 : 1 }}" max="2147483647" step="1" value="{{ $value('upload_speed') }}"></label>
                <label class="p-field">Harga bulanan (Rp)<input type="number" name="monthly_price" required min="0" max="9999999999999.99" step="0.01" value="{{ $value('monthly_price') }}" placeholder="Isi 0 jika gratis"></label>
            </div>
            @if ($isEdit)<p class="p-note" style="margin-top:12px">Nilai kecepatan 0 dari hasil impor dapat dipertahankan. Perubahan harga tidak menghitung ulang invoice yang sudah ada.</p>@endif
        </section>
        <details class="p-panel">
            <summary>@include('packages.partials.catalog-icon',['name'=>'settings']) Pengaturan teknis lanjutan</summary>
            <p class="p-note" style="margin-top:12px">Kosongkan burst jika tidak digunakan. Priority antara 1 sampai 8.</p>
            <div class="p-fields">
                @foreach (['burst_limit'=>'Burst limit', 'burst_threshold'=>'Burst threshold', 'burst_time'=>'Burst time'] as $field => $label)
                    <label class="p-field">{{ $label }}<input name="{{ $field }}" maxlength="50" value="{{ $value($field) }}"></label>
                @endforeach
                <label class="p-field">Priority<input type="number" name="priority" min="1" max="8" step="1" value="{{ $value('priority', 8) }}"></label>
            </div>
        </details>
        <p class="p-hint" style="margin-bottom:18px">Nama profil boleh sama di router berbeda, tetapi tidak boleh dipakai dua paket pada router yang sama. Setelah mengubah pengaturan, lakukan sync secara terpisah jika memang diperlukan. Melepas hubungan router tidak menghapus profil pada perangkat.</p>
        <div class="p-actions">
            <button class="p-btn p-btn-primary" type="submit" @disabled($routers->isEmpty())>@include('packages.partials.catalog-icon',['name'=>'save']) {{ $isEdit ? 'Simpan perubahan' : 'Buat paket' }}</button>
            <a class="p-btn" href="{{ route('packages.index') }}">Batal</a>
        </div>
    </form>
</div>
