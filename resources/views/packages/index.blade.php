@extends('layouts.app')
@section('title', 'Paket Internet')
@section('content')
@include('packages.partials.catalog-theme')
@php $canManage = auth()->user()?->isSuperAdmin() ?? false; @endphp
<div class="package-catalog">
    <header class="pc-header">
        <div><p class="pc-eyebrow">Manajemen layanan</p><h1 class="pc-title">Paket Internet</h1><p class="pc-muted">Harga, kecepatan, dan router pemakai paket dalam satu tempat.</p></div>
        @if ($canManage)
        <div class="pc-actions">
            @if ($routers->isNotEmpty())
            <details class="pc-picker"><summary class="pc-btn">@include('packages.partials.catalog-icon',['name'=>'download']) Impor dari MikroTik</summary>
                <div class="pc-picker-menu"><p class="pc-muted">Pilih router sumber</p>@foreach ($routers as $sourceRouter)<a class="pc-btn" href="{{ route('packages.import', $sourceRouter) }}">@include('packages.partials.catalog-icon',['name'=>'router']) {{ $sourceRouter->name }}</a>@endforeach</div>
            </details>
            @endif
            <a class="pc-btn pc-btn-primary" href="{{ route('packages.create') }}">@include('packages.partials.catalog-icon',['name'=>'plus']) Tambah paket</a>
        </div>
        @endif
    </header>
    <div class="pc-notice">@include('packages.partials.catalog-icon',['name'=>'info'])<p>Menyimpan paket hanya memperbarui billing. <span style="font-weight:700">Kirim pengaturan</span> mengubah profil pada MikroTik tujuan. Status pengiriman adalah catatan aplikasi, bukan pemeriksaan langsung kondisi router.</p></div>
    <div class="pc-toolbar">
        <div><p style="font-size:13px;font-weight:700">{{ $packages->total() }} paket terdaftar</p><p class="pc-muted">{{ $routers->count() }} router aktif · <span data-pc-visible>{{ $packages->count() }}</span> paket terlihat di halaman ini</p></div>
        <label class="pc-search">@include('packages.partials.catalog-icon',['name'=>'search'])<input data-pc-search type="search" aria-label="Cari paket di halaman ini" placeholder="Cari nama, profil, atau router di halaman ini"></label>
    </div>
    <div class="pc-grid">
    @forelse ($packages as $package)
        @php
            $linkedRouters = $package->routers;
            $syncRouters = $linkedRouters->filter(fn ($router) => $router->active);
            $fingerprint = \App\Http\Controllers\PackageController::syncFingerprint($package);
            $price = (float) $package->monthly_price;
            $decimals = round($price, 2) == floor($price) ? 0 : 2;
        @endphp
        <article class="pc-card" data-pc-card data-pc-search-text="{{ $package->name.' '.$package->mikrotik_profile.' '.$linkedRouters->pluck('name')->implode(' ') }}">
            <div class="pc-card-main">
                <div class="pc-card-heading"><h2 class="pc-name">{{ $package->name }}</h2><span class="pc-badge" data-tone="{{ $package->active ? 'good' : 'neutral' }}">{{ $package->active ? 'Aktif di billing' : 'Nonaktif' }}</span></div>
                <div><p class="pc-price">Rp {{ number_format($price, $decimals, ',', '.') }}</p><p class="pc-muted">per bulan</p></div>
                <div class="pc-speed-grid">
                    <div class="pc-speed"><p class="pc-speed-label">@include('packages.partials.catalog-icon',['name'=>'download']) Download</p><p class="pc-speed-value">{{ $package->download_speed }} <small>Mbps</small></p></div>
                    <div class="pc-speed"><p class="pc-speed-label">@include('packages.partials.catalog-icon',['name'=>'upload']) Upload</p><p class="pc-speed-value">{{ $package->upload_speed }} <small>Mbps</small></p></div>
                </div>
                <section><p class="pc-section-label">@include('packages.partials.catalog-icon',['name'=>'router'])
                    @if ($linkedRouters->count() === 1) Khusus {{ $linkedRouters->first()->name }}
                    @elseif ($linkedRouters->count() > 1) Dipakai bersama {{ $linkedRouters->count() }} router
                    @else Belum terhubung ke router
                    @endif
                </p><div class="pc-router-tags">@foreach ($linkedRouters as $linkedRouter)<span class="pc-badge" data-tone="{{ $linkedRouter->active ? 'info' : 'neutral' }}">{{ $linkedRouter->name }}{{ $linkedRouter->active ? '' : ' · nonaktif' }}</span>@endforeach</div></section>
                <section><p class="pc-section-label">Catatan pengiriman ke MikroTik</p><div class="pc-sync-list">
                    @forelse ($linkedRouters as $statusRouter)
                        @php
                            $state = $syncStates->get($package->id.':'.$statusRouter->id);
                            $label = 'Belum ada catatan'; $tone = 'neutral'; $statusIcon = 'clock';
                            if ($state && $state->status === 'success') {
                                $same = hash_equals((string) $state->fingerprint, $fingerprint);
                                $label = $same ? 'Tercatat berhasil' : 'Pengaturan berubah, perlu dikirim ulang';
                                $tone = $same ? 'good' : 'warn'; $statusIcon = $same ? 'check' : 'alert';
                            } elseif ($state && $state->status === 'failed') {
                                $label = 'Pengiriman terakhir gagal'; $tone = 'bad'; $statusIcon = 'alert';
                            } elseif ($state && $state->status === 'pending') {
                                $label = 'Hasil belum terkonfirmasi'; $tone = 'warn'; $statusIcon = 'clock';
                            }
                        @endphp
                        <div class="pc-sync-row"><p class="pc-sync-name">{{ $statusRouter->name }}</p><p class="pc-sync-state" data-tone="{{ $tone }}">@include('packages.partials.catalog-icon',['name'=>$statusIcon]) {{ $label }}</p>
                            @if ($state && $state->synced_at)<p class="pc-small">Berhasil terakhir: {{ \Illuminate\Support\Carbon::parse($state->synced_at)->format('d/m/Y H:i:s') }} ({{ config('app.timezone','UTC') }})</p>@endif
                        </div>
                    @empty <p class="pc-small">Hubungkan paket ke router melalui Edit sebelum mengirim pengaturan.</p>
                    @endforelse
                </div></section>
                <details class="pc-details"><summary>Rincian teknis</summary><dl class="pc-detail-list">
                    <div><dt>Profil MikroTik</dt><dd>{{ $package->mikrotik_profile ?: 'Belum diatur' }}</dd></div>
                    <div><dt>Priority</dt><dd>{{ $package->priority ?? 8 }}</dd></div>
                    <div><dt>Burst limit</dt><dd>{{ $package->burst_limit ?: 'Tidak diatur' }}</dd></div>
                    <div><dt>Burst threshold</dt><dd>{{ $package->burst_threshold ?: 'Tidak diatur' }}</dd></div>
                    <div><dt>Burst time</dt><dd>{{ $package->burst_time ?: 'Tidak diatur' }}</dd></div>
                </dl><p class="pc-small" style="margin-top:10px">Kecepatan di kartu adalah pengaturan paket, bukan hasil pengukuran internet.</p></details>
            </div>
            @if ($canManage)
            <div class="pc-card-controls">
                <a class="pc-btn pc-btn-primary" href="{{ route('packages.edit', $package) }}">@include('packages.partials.catalog-icon',['name'=>'edit']) Edit paket</a>
                @if ($package->active && $syncRouters->isNotEmpty())
                <details><summary class="pc-btn">@include('packages.partials.catalog-icon',['name'=>'send']) Kirim pengaturan ke MikroTik</summary>
                    <form class="pc-sync-form" method="post" action="{{ route('packages.sync', $package) }}" data-pc-sync data-package-name="{{ $package->name }}">
                        @csrf
                        <label class="pc-muted" for="pc-router-{{ $package->id }}">Pilih router yang akan diubah</label>
                        <select class="pc-input" name="router_id" id="pc-router-{{ $package->id }}" required><option value="">Pilih router tujuan</option>@foreach ($syncRouters as $syncRouter)<option value="{{ $syncRouter->id }}">{{ $syncRouter->name }}</option>@endforeach</select>
                        <p class="pc-small">Tindakan ini membuat atau memperbarui profil pada router pilihan.</p>
                        <button class="pc-btn" type="submit">@include('packages.partials.catalog-icon',['name'=>'send']) Kirim ke router pilihan</button>
                    </form>
                </details>
                @else <p class="pc-small">Pengiriman tersedia jika paket aktif dan memiliki router aktif.</p>
                @endif
            </div>
            <div class="pc-danger"><details><summary>Hapus paket dari billing</summary><form method="post" action="{{ route('packages.destroy', $package) }}" data-pc-delete data-package-name="{{ $package->name }}">@csrf @method('DELETE')<p class="pc-small">Terpisah dari penghapusan router. Tindakan ini tidak menghapus profil pada perangkat MikroTik.</p><button type="submit" class="pc-btn pc-btn-danger">@include('packages.partials.catalog-icon',['name'=>'trash']) Hapus paket</button></form></details></div>
            @endif
        </article>
    @empty
        <div class="pc-empty">@include('packages.partials.catalog-icon',['name'=>'package'])<h2 class="pc-name" style="margin:12px 0">Belum ada paket internet</h2><p class="pc-muted">Gunakan Tambah paket atau Impor dari MikroTik jika Anda memiliki akses pengelolaan.</p></div>
    @endforelse
    </div>
    <div class="pc-empty pc-filter-empty" data-pc-empty hidden><h2 class="pc-name">Tidak ditemukan di halaman ini</h2><p class="pc-muted">Ubah pencarian atau buka halaman paket lainnya.</p></div>
    @if ($packages->hasPages())<div class="pc-pagination">{{ $packages->links() }}</div>@endif
</div>
<script>
(() => {
    const root = document.querySelector('.package-catalog');
    if (!root) return;
    const input = root.querySelector('[data-pc-search]');
    const cards = Array.from(root.querySelectorAll('[data-pc-card]'));
    input.addEventListener('input', () => {
        const term = input.value.trim().toLocaleLowerCase('id');
        let visible = 0;
        cards.forEach(card => {
            const show = (card.dataset.pcSearchText || '').toLocaleLowerCase('id').includes(term);
            card.hidden = !show;
            if (show) visible++;
        });
        root.querySelector('[data-pc-visible]').textContent = visible;
        root.querySelector('[data-pc-empty]').hidden = visible !== 0 || cards.length === 0;
    });
    root.querySelectorAll('[data-pc-sync]').forEach(form => form.addEventListener('submit', event => {
        const select = form.querySelector('select[name="router_id"]');
        if (!select.value || !confirm('Kirim pengaturan paket "' + form.dataset.packageName + '" ke ' + select.options[select.selectedIndex].text + '? Konfigurasi profil router akan berubah.')) {
            event.preventDefault(); return;
        }
        form.querySelector('button[type=submit]').disabled = true;
    }));
    root.querySelectorAll('[data-pc-delete]').forEach(form => form.addEventListener('submit', event => {
        if (!confirm('Hapus paket "' + form.dataset.packageName + '" dari billing? Profil pada perangkat tidak dihapus.')) event.preventDefault();
    }));
})();
</script>
@endsection
