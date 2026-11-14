@extends('layouts.app')
@section('title', 'MikroTik')
@section('content')
@include('routers.partials.theme')
<div class="router-page">
    <header class="r-heading">
        <div>
            <p class="r-eyebrow">Pengaturan jaringan</p>
            <h1 class="r-title">Router MikroTik</h1>
            <p class="r-muted">Status diperiksa otomatis saat halaman dibuka atau di-refresh. Gunakan Tes koneksi untuk memeriksa ulang.</p>
        </div>
        <a class="r-btn r-btn-primary" href="{{ route('routers.create') }}">@include('routers.partials.icon', ['name'=>'plus', 'iconClass'=>'r-icon']) Tambah router</a>
    </header>
    <div class="r-counter">
        <span class="r-badge">{{ $routers->count() }} router terdaftar</span>
        <span class="r-badge">{{ $routers->where('active', true)->count() }} aktif di billing</span>
    </div>
    <div class="r-grid">
    @forelse ($routers as $r)
        @php
            $check = $resourceChecks[$r->id] ?? null;
            $old = $check && now()->timestamp - ($check['checked_timestamp'] ?? 0) > 60;
            $state = $check ? ($old ? 'stale' : $check['state']) : 'unknown';
            $cpu = isset($check['cpu_load']) ? (float) $check['cpu_load'] : null;
            $cpuLevel = $cpu !== null && $cpu >= 85 ? 'high' : ($cpu !== null && $cpu >= 65 ? 'medium' : 'normal');
        @endphp
        <article class="r-card" data-router-card data-resource-checked="{{ $check['checked_timestamp'] ?? '' }}">
            <div class="r-card-body">
                <div class="r-row">
                    <div><h2 class="r-name">{{ $r->name }}</h2><p class="r-address">{{ $r->host }}:{{ $r->port }}</p></div>
                    @include('routers.partials.icon', ['name'=>'router','iconClass'=>'r-icon'])
                </div>
                <p class="r-operation">Pengaturan billing: {{ $r->active ? 'Aktif' : 'Nonaktif' }}. Berbeda dari status koneksi API.</p>
                <div class="r-resource">
                    <span class="r-badge" data-resource-badge data-state="{{ $state }}" aria-live="polite"><i aria-hidden="true"></i><span data-resource-status>
                        @if (!$check) Belum diperiksa
                        @elseif ($old) Hasil lama, perlu diperbarui
                        @elseif ($check['state'] === 'success') API terhubung saat diperiksa
                        @else Tidak dapat membaca resource
                        @endif
                    </span></span>
                    <div class="r-metrics">
                        <div>
                            <p class="r-metric-label">@include('routers.partials.icon', ['name'=>'clock','iconClass'=>'r-icon']) Uptime sejak boot</p>
                            <p class="r-value" data-resource-uptime>{{ $check['uptime'] ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="r-metric-label">@include('routers.partials.icon', ['name'=>'cpu','iconClass'=>'r-icon']) Beban CPU</p>
                            <p class="r-value" data-resource-cpu>{{ $cpu !== null ? rtrim(rtrim(number_format($cpu, 1, '.', ''), '0'), '.') . '%' : '—' }}</p>
                            <div class="r-bar" data-resource-bar aria-hidden="true" @if ($cpu === null) hidden @endif><span data-resource-fill data-level="{{ $cpuLevel }}" style="width: {{ $cpu ?? 0 }}%"></span></div>
                        </div>
                    </div>
                    <p class="r-last" data-resource-note>
                        @if ($check)
                            Pemeriksaan: {{ $check['checked_display'] }} ({{ config('app.timezone', 'UTC') }}).
                            @if ($check['state'] !== 'success') Koneksi API, izin baca, atau respons perlu diperiksa. Perangkat belum tentu mati.
                            @elseif ($old) Uptime dan CPU di atas adalah snapshot lama, bukan nilai saat ini.
                            @endif
                        @else Menunggu pemeriksaan otomatis. Tes koneksi tersedia untuk pemeriksaan ulang.
                        @endif
                    </p>
                </div>
                <div class="r-actions">
                    <form method="post" data-resource-form action="{{ route('routers.connection-status', $r) }}">
                        @csrf
                        <button type="submit" class="r-btn r-btn-primary" @disabled(!$r->active)>
                            @include('routers.partials.icon', ['name'=>'test','iconClass'=>'r-icon']) <span data-resource-button-label>Tes koneksi</span>
                        </button>
                    </form>
                    <a class="r-btn" href="{{ route('routers.edit', $r) }}">@include('routers.partials.icon', ['name'=>'edit','iconClass'=>'r-icon']) Edit</a>
                </div>
                @if (!$r->active)<p class="r-last">Pemeriksaan baru tidak dijalankan untuk router nonaktif.</p>@endif
            </div>
            <footer class="r-danger"><span class="r-muted">Penghapusan permanen</span><a href="{{ route('routers.delete-preview', $r) }}">@include('routers.partials.icon', ['name'=>'trash','iconClass'=>'r-icon']) Tinjau hapus</a></footer>
        </article>
    @empty
        <div class="r-empty"><h2 class="r-name">Belum ada router</h2><p class="r-muted">Tambahkan router untuk mulai mengelola koneksi.</p></div>
    @endforelse
    </div>
</div>
<script>
(() => {
    const root = document.querySelector('.router-page');
    if (!root || root.dataset.autoStatusReady === '1') return;
    root.dataset.autoStatusReady = '1';
    const forms = Array.from(root.querySelectorAll('[data-resource-form]'));
    let queue = Promise.resolve();
    const text = (card, selector, value) => {
        const node = card.querySelector(selector);
        if (node) node.textContent = value;
    };
    function render(card, check) {
        const good = check.state === 'success';
        const cpu = typeof check.cpu_load === 'number' && Number.isFinite(check.cpu_load)
            && check.cpu_load >= 0 && check.cpu_load <= 100 ? check.cpu_load : null;
        const badge = card.querySelector('[data-resource-badge]');
        badge.dataset.state = good ? 'success' : 'failed';
        text(card, '[data-resource-status]', good ? 'API terhubung saat diperiksa' : 'Tidak dapat membaca resource');
        text(card, '[data-resource-uptime]', good ? (check.uptime || '—') : '—');
        text(card, '[data-resource-cpu]', good && cpu !== null ? cpu + '%' : '—');
        const bar = card.querySelector('[data-resource-bar]');
        const fill = card.querySelector('[data-resource-fill]');
        bar.hidden = !good || cpu === null;
        fill.style.width = (good && cpu !== null ? cpu : 0) + '%';
        fill.dataset.level = cpu !== null && cpu >= 85 ? 'high' : (cpu !== null && cpu >= 65 ? 'medium' : 'normal');
        const stamp = 'Pemeriksaan: ' + check.checked_display + ' (' + check.timezone + ').';
        text(card, '[data-resource-note]', good ? stamp : stamp + ' Koneksi API, izin baca, atau respons perlu diperiksa. Perangkat belum tentu mati.');
        card.dataset.resourceChecked = String(check.checked_timestamp || 0);
        card.dataset.resourceLastState = good ? 'success' : 'failed';
    }
    function unconfirmed(card, message) {
        card.querySelector('[data-resource-badge]').dataset.state = 'unknown';
        text(card, '[data-resource-status]', 'Pemeriksaan belum terkonfirmasi');
        text(card, '[data-resource-uptime]', '—');
        text(card, '[data-resource-cpu]', '—');
        card.querySelector('[data-resource-bar]').hidden = true;
        text(card, '[data-resource-note]', message);
        card.dataset.resourceChecked = '';
    }
    function schedule(form) {
        const button = form.querySelector('button[type=submit]');
        if (!button || form.dataset.resourcePending === '1' || button.disabled) return;
        const card = form.closest('[data-router-card]');
        const label = button.querySelector('[data-resource-button-label]');
        form.dataset.resourcePending = '1';
        button.disabled = true;
        label.textContent = 'Menunggu giliran…';
        queue = queue.catch(() => {}).then(async () => {
            label.textContent = 'Memeriksa…';
            card.setAttribute('aria-busy', 'true');
            card.querySelector('[data-resource-badge]').dataset.state = 'unknown';
            text(card, '[data-resource-status]', 'Sedang memeriksa API…');
            text(card, '[data-resource-uptime]', '—');
            text(card, '[data-resource-cpu]', '—');
            card.querySelector('[data-resource-bar]').hidden = true;
            text(card, '[data-resource-note]', 'Membaca uptime dan CPU dari MikroTik. Konfigurasi tidak diubah.');
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 35000);
            try {
                const response = await fetch(form.action, {
                    method: 'POST', credentials: 'same-origin', cache: 'no-store',
                    headers: {
                        'Accept': 'application/json', 'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: '{}', signal: controller.signal
                });
                if (response.redirected) throw new Error('Sesi mungkin berakhir. Muat ulang halaman atau login kembali.');
                if (!response.ok) {
                    const messages = {
                        401: 'Sesi berakhir. Login kembali.',
                        403: 'Anda tidak memiliki izin pemeriksaan.',
                        419: 'Sesi atau token kedaluwarsa. Muat ulang halaman.',
                        422: 'Router nonaktif atau permintaan tidak valid. Muat ulang halaman.',
                        429: 'Terlalu banyak pemeriksaan. Tunggu sekitar satu menit, lalu gunakan Tes koneksi.'
                    };
                    throw new Error(messages[response.status] || 'Aplikasi gagal memproses pemeriksaan (HTTP ' + response.status + '). Periksa log.');
                }
                const data = await response.json();
                if (!data.ok || !data.check || !['success', 'failed'].includes(data.check.state)) {
                    throw new Error('Respons aplikasi tidak valid. Muat ulang halaman.');
                }
                render(card, data.check);
            } catch (error) {
                unconfirmed(card, error.name === 'AbortError'
                    ? 'Waktu tunggu browser habis. Ini bukan bukti router mati. Coba Tes koneksi setelah menunggu.'
                    : (error.message || 'Pemeriksaan belum terkonfirmasi.'));
            } finally {
                clearTimeout(timer);
                card.removeAttribute('aria-busy');
                form.dataset.resourcePending = '0';
                button.disabled = false;
                label.textContent = 'Tes koneksi';
            }
        });
    }
    for (const form of forms) {
        form.addEventListener('submit', event => {
            event.preventDefault();
            schedule(form);
        });
    }
    const runAll = () => forms.forEach(schedule);
    runAll();
    window.addEventListener('pageshow', event => {
        if (event.persisted) runAll();
    });
    // Hanya memperbarui label usia data; tidak mengirim permintaan jaringan.
    setInterval(() => {
        for (const card of root.querySelectorAll('[data-router-card]')) {
            const stamp = Number(card.dataset.resourceChecked || 0);
            if (!stamp || card.getAttribute('aria-busy') === 'true') continue;
            if (Date.now() / 1000 - stamp > 60) {
                card.querySelector('[data-resource-badge]').dataset.state = 'stale';
                text(card, '[data-resource-status]', 'Hasil lama, perlu diperbarui');
                const note = card.querySelector('[data-resource-note]');
                if (!note.dataset.staleStamp || note.dataset.staleStamp !== String(stamp)) {
                    note.textContent += ' Ini snapshot lama. Gunakan Tes koneksi untuk data baru.';
                    note.dataset.staleStamp = String(stamp);
                }
            }
        }
    }, 10000);
})();
</script>

@endsection
