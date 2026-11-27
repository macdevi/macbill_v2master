@extends('layouts.app')
@section('title', 'Koreksi jadwal tagihan PSB')
@section('content')
<style>
.psbc{max-width:850px}.psbcbox{border:1px solid #cbd5e1;border-radius:16px;padding:22px;margin-bottom:18px;background:#fff;color:#17243a}.dark .psbcbox{background:#0f172a;color:#e2e8f0;border-color:#334155}.psbc h1{font-size:23px;font-weight:800}.psbc h2{font-size:17px;font-weight:750;margin-bottom:12px}.psbc p{font-size:14px;margin:8px 0}.psbc dl{display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:14px;margin-top:14px}.psbc dt{color:#64748b;font-size:12px}.dark .psbc dt{color:#94a3b8}.psbc dd{font-weight:650}.psbc label{display:block;margin:13px 0;font-size:14px}.psbc input[type=number],.psbc textarea{display:block;width:100%;border:1px solid #94a3b8;border-radius:9px;padding:10px;margin-top:7px;background:transparent;color:inherit}.psbcbutton{display:inline-block;padding:11px 15px;border-radius:9px;background:#6652cf!important;color:#fff!important;font-size:14px;font-weight:700;border:0;cursor:pointer}.psbcback{display:inline-block;padding:11px 15px;border:1px solid #94a3b8;border-radius:9px;font-size:14px}.psbcnotice{padding:13px;border-radius:10px;background:#f1f5f9;color:#334155;font-size:13px;margin:12px 0}.dark .psbcnotice{background:#1e293b;color:#cbd5e1}.psbcerror{border:1px solid #e11d48;color:#be123c;padding:14px;border-radius:10px;margin-bottom:15px}.dark .psbcerror{color:#fda4af}.psbcrow{display:flex;justify-content:space-between;gap:12px;padding:9px 0;border-bottom:1px solid #cbd5e1;font-size:14px}.psbcactions{display:flex;flex-wrap:wrap;gap:9px;margin-top:17px}.psbcbutton:disabled{opacity:.5;cursor:wait}.psbc :focus-visible{outline:3px solid #a78bfa;outline-offset:3px}@media(max-width:550px){.psbcbox{padding:16px}.psbc dl{grid-template-columns:1fr}.psbc h1{font-size:20px}}
</style>
@php
    $c = $installation->customer;
    $money = fn($v) => 'Rp '.number_format((float)$v, 2, ',', '.');
    $blocked = !$c || $c->status !== 'active' || $c->billing_resumed_on !== null || $c->invoices()->exists();
@endphp
<div class="psbc">
    @if($errors->any())
        <div class="psbcerror" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        <p>Belum ada koreksi yang disimpan. Muat ulang atau buat pratinjau baru.</p></div>
    @endif
    <section class="psbcbox">
        <h1>Koreksi jadwal tagihan PSB</h1>
        <p>{{ $installation->receipt_number }} · {{ $installation->customer_name }}</p>
        <div class="psbcnotice">Khusus koreksi jadwal awal PSB sebelum ada invoice bulanan. Tidak mengubah aktivasi, tarif, biaya pemasangan, pembayaran PSB, atau MikroTik.</div>
        <dl>
            <div><dt>Tanggal aktivasi PSB</dt><dd>{{ $installation->activated_on?->format('d/m/Y') ?? '-' }}</dd></div>
            <div><dt>ID pelanggan</dt><dd>{{ $c?->id ?? '-' }}</dd></div>
            <div><dt>Tanggal tagihan pada PSB</dt><dd>{{ $installation->billing_day }}</dd></div>
            <div><dt>Tanggal tagihan pada pelanggan</dt><dd>{{ $c?->due_day ?? '-' }}</dd></div>
            <div><dt>Tagihan pertama pada PSB</dt><dd>{{ $installation->first_billing_on?->format('d/m/Y') ?? '-' }}</dd></div>
            <div><dt>Tagihan pertama pada pelanggan</dt><dd>{{ $c?->first_billing_on?->format('d/m/Y') ?? '-' }}</dd></div>
        </dl>
    </section>
    @if($blocked)
        <section class="psbcbox"><h2>Koreksi otomatis tidak tersedia</h2><p>Pelanggan tidak aktif/tidak tersedia, sudah mulai ulang billing, atau telah memiliki invoice bulanan. Invoice yang ada tidak diubah.</p><a class="psbcback" href="{{ route('installations.show', $installation) }}">Kembali ke nota</a></section>
    @else
        <section class="psbcbox">
            <h2>Jadwal yang benar</h2>
            <form method="POST" action="{{ route('installations.billing-correction.preview', $installation) }}">
                @csrf
                <label>Tanggal tagihan bulanan (1–28)
                    <input name="billing_day" type="number" min="1" max="28" required value="{{ old('billing_day', $preview['plan']['billing_day'] ?? $c->due_day) }}">
                </label>
                <label>Alasan koreksi
                    <textarea name="reason" required minlength="5" maxlength="500" rows="3">{{ old('reason', $preview['plan']['reason'] ?? '') }}</textarea>
                </label>
                <div class="psbcactions"><button type="submit" class="psbcbutton">Lihat pratinjau</button><a class="psbcback" href="{{ route('installations.show', $installation) }}">Batal</a></div>
            </form>
        </section>
    @endif
    @if($preview && !$blocked)
        @php $p = $preview['plan']; $tax = $p['tax']; @endphp
        <section class="psbcbox" id="psbc-preview">
            <h2>Pratinjau koreksi — belum disimpan</h2>
            <div class="psbcrow"><span>Pelanggan</span><strong>{{ $p['customer_name'] }} · ID {{ $p['customer_id'] }}</strong></div>
            <div class="psbcrow"><span>Tanggal tagihan baru</span><strong>{{ $p['billing_day'] }}</strong></div>
            <div class="psbcrow"><span>Tagihan pertama</span><strong>{{ $p['first_billing_on'] }}</strong></div>
            <div class="psbcrow"><span>Periode layanan</span><strong>{{ $tax['service_period_start'] }} s.d. {{ $tax['service_period_end'] }}</strong></div>
            <div class="psbcrow"><span>Hari layanan / siklus</span><strong>{{ $tax['charged_days'] }} / {{ $tax['cycle_days'] }} hari</strong></div>
            <div class="psbcrow"><span>Tarif bulanan dasar</span><strong>{{ $money($p['monthly_price']) }}</strong></div>
            <div class="psbcrow"><span>Biaya layanan {{ $tax['is_prorated'] ? 'prorata' : '' }}</span><strong>{{ $money($tax['service_amount']) }}</strong></div>
            <div class="psbcrow"><span>Pajak · mode {{ $tax['tax_mode'] }}</span><strong>{{ $money($tax['tax_amount']) }}</strong></div>
            <div class="psbcrow"><span>Total sebelum saldo</span><strong>{{ $money($tax['gross_amount']) }}</strong></div>
            <div class="psbcrow"><span>Titip saldo {{ $p['create_now'] ? 'dipakai' : '(perkiraan)' }}</span><strong>{{ $money($p['credit_used']) }}</strong></div>
            <div class="psbcrow"><span>Tagihan akhir {{ $p['create_now'] ? '' : '(perkiraan)' }}</span><strong>{{ $money($p['amount']) }}</strong></div>
            <p>Alasan: {{ $p['reason'] }}</p>
            <div class="psbcnotice">
                @if($p['create_now'])
                    Setelah dikonfirmasi, jadwal PSB dan pelanggan diperbarui serta satu invoice dibuat sekarang. Saldo hanya dipotong jika invoice berhasil dibuat.
                @else
                    Setelah dikonfirmasi, hanya jadwal PSB dan pelanggan diperbarui. Invoice dan pemakaian saldo menunggu {{ $p['first_billing_on'] }} melalui generator terjadwal. Tarif, pajak, dan saldo saat penerbitan dapat berubah.
                @endif
                <br>Dasar tanggal server: {{ $p['today'] }} · {{ $p['timezone'] }}. Pratinjau berlaku 10 menit.
            </div>
            <form method="POST" action="{{ route('installations.billing-correction.update', $installation) }}" id="psbc-confirm">
                @csrf @method('PUT')
                <input type="hidden" name="billing_day" value="{{ $p['billing_day'] }}">
                <input type="hidden" name="reason" value="{{ $p['reason'] }}">
                <input type="hidden" name="preview_token" value="{{ $preview['token'] }}">
                <label><input type="checkbox" name="confirmed" value="1" required> Saya menyetujui jadwal, alasan, dan hasil pratinjau di atas.</label>
                <button class="psbcbutton" type="submit" id="psbc-save">{{ $p['create_now'] ? 'Simpan koreksi dan buat invoice' : 'Simpan koreksi jadwal' }}</button>
            </form>
            <p style="font-size:12px">Jika mengubah tanggal/alasan pada form atas, klik Lihat pratinjau lagi sebelum konfirmasi.</p>
        </section>
        <script>
        (()=>{const form=document.getElementById('psbc-confirm');let busy=false;form.addEventListener('submit',e=>{if(busy){e.preventDefault();return;}if(!form.reportValidity()){e.preventDefault();return;}busy=true;const b=document.getElementById('psbc-save');b.disabled=true;b.textContent='Menyimpan, jangan kirim ulang…';});window.addEventListener('pageshow',()=>{busy=false;const b=document.getElementById('psbc-save');b.disabled=false;b.textContent=@json($p['create_now'] ? 'Simpan koreksi dan buat invoice' : 'Simpan koreksi jadwal');});})();
        </script>
    @endif
</div>
@endsection
