@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Detail Pelanggan: {{ $customer->name }}</h4>
        <div>
            <a href="{{ url('/customers') }}" class="btn btn-secondary btn-sm">Kembali</a>
            @if((auth()->user()->role ?? null) !== 'kasir')
                <a href="{{ url('/customers/'.$customer->id.'/edit') }}" class="btn btn-primary btn-sm">Edit</a>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <table class="table table-sm mb-0">
                <tr><th style="width:220px">Nama</th><td>{{ $customer->name }}</td></tr>
                <tr><th>Telepon</th><td>{{ $customer->phone ?? '-' }}</td></tr>
                <tr><th>Alamat</th><td>{{ $customer->address ?? '-' }}</td></tr>
                <tr><th>Wilayah</th><td>{{ $customer->area->name ?? '-' }}</td></tr>
                <tr><th>Paket</th><td>{{ $customer->internetPackage->name ?? '-' }}</td></tr>
                <tr><th>Router</th><td>{{ $customer->router->name ?? '-' }}</td></tr>
                <tr><th>Harga Override</th><td>{{ $customer->monthly_price_override !== null ? 'Rp '.number_format($customer->monthly_price_override, 0, ',', '.') : '-' }}</td></tr>
                <tr><th>Mode Pajak</th><td>{{ $customer->tax_mode ?? '-' }}</td></tr>
                <tr><th>Status</th><td>{{ $customer->status ?? ($customer->active ? 'aktif' : 'nonaktif') }}</td></tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Riwayat Invoice ({{ $customer->invoices->count() }})</div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
                <thead>
                    <tr><th>#</th><th>Periode / Jatuh Tempo</th><th>Total</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                @forelse($customer->invoices->sortByDesc('id') as $inv)
                    <tr>
                        <td>{{ $inv->invoice_number ?? $inv->number ?? $inv->id }}</td>
                        <td>{{ $inv->period ?? $inv->due_date ?? '-' }}</td>
                        <td>Rp {{ number_format($inv->total ?? $inv->amount ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $inv->status ?? '-' }}</td>
                        <td><a href="{{ url('/invoices/'.$inv->id) }}" class="btn btn-outline-primary btn-sm">Lihat</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Belum ada invoice.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
