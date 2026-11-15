{{-- psb-billing-correction-v1: entry and revision history --}}
@php
    $psbcLogs = \App\Models\ActivityLog::query()
        ->where('action', 'installation.billing_corrected')
        ->where('description', 'like', '{"installation_id":'.(int)$installation->id.',%')
        ->orderByDesc('id')->limit(10)->get();
    $psbcCustomer = $installation->customer;
    $psbcEligible = $psbcCustomer && $psbcCustomer->status === 'active'
        && $psbcCustomer->billing_resumed_on === null
        && !$psbcCustomer->invoices()->exists();
@endphp
@if($psbcLogs->isNotEmpty())
<div style="max-width:48rem;border:1px solid #94a3b8;border-radius:12px;padding:16px;margin-top:18px;font-size:13px">
    <h2 style="font-weight:700">Nota dengan koreksi jadwal PSB</h2>
    <p>Jadwal pada nota ini telah dikoreksi. Riwayat tersimpan pada log aktivitas.</p>
    @foreach($psbcLogs as $psbcLog)
        @php $psbcRevision = json_decode($psbcLog->description, true); @endphp
        @if(is_array($psbcRevision) && isset($psbcRevision['before']))
            <p style="margin-top:8px">{{ $psbcLog->created_at?->format('d/m/Y H:i') }} · {{ $psbcRevision['actor_name'] ?? '-' }}:
                tanggal PSB {{ $psbcRevision['before']['psb_billing_day'] }} → {{ $psbcRevision['billing_day'] }};
                tagihan pertama {{ $psbcRevision['before']['psb_first_billing_on'] ?? '-' }} → {{ $psbcRevision['first_billing_on'] }}.
                Alasan: {{ $psbcRevision['reason'] }}.
                @if($psbcRevision['invoice_number'] ?? null) Invoice: {{ $psbcRevision['invoice_number'] }}. @endif
            </p>
        @endif
    @endforeach
</div>
@endif
<div class="installation-actions" style="max-width:48rem;border:1px solid #94a3b8;border-radius:12px;padding:16px;margin-top:18px">
    <h2 style="font-weight:700">Koreksi jadwal tagihan PSB</h2>
    @if($psbcEligible)
        <p style="font-size:13px;margin:8px 0 14px">Samakan jadwal nota dan pelanggan. Periksa pratinjau sebelum menyimpan. Biaya pemasangan dan MikroTik tidak diubah.</p>
        <a href="{{ route('installations.billing-correction.edit', $installation) }}" style="display:inline-block;background:#6652cf;color:#fff;border-radius:9px;padding:11px 15px;font-weight:700;font-size:13px">Koreksi jadwal tagihan PSB</a>
    @else
        <p style="font-size:13px;margin-top:8px">Koreksi otomatis tidak tersedia: pelanggan tidak aktif/tidak tersedia, telah mulai ulang billing, atau sudah memiliki invoice bulanan. Invoice lama tidak diubah.</p>
    @endif
</div>
