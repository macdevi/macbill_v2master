@extends('layouts.app')

@section('title', 'Riwayat Aktivitas Keuangan')

@section('content')
@php
    $currentUser = auth()->user();
    $isSuperAdmin = $currentUser?->isSuperAdmin() ?? false;
    $isCashier = !$isSuperAdmin && $currentUser?->role === 'kasir';

    $dashboardUrl = $isSuperAdmin
        ? route('dashboard')
        : ($isCashier ? route('staff.home') : route('admin.dashboard'));

    $from = $activities->firstItem() ?? 0;
    $to = $activities->lastItem() ?? 0;
    $total = $activities->total();

    $pageIncome = (float) $activities
        ->where('activity_type', 'income')
        ->sum('activity_amount');

    $pageExpense = (float) $activities
        ->where('activity_type', 'expense')
        ->sum('activity_amount');
@endphp



<div class="fa2">
    <section class="fa2-card" aria-labelledby="finance-activity-page-title">
        <header class="fa2-card__header">
            <div>
<h2 id="finance-activity-page-title" class="fa2-card__title">Aktivitas terbaru</h2> </div>

<span class="fa2-page-badge">
    {{ number_format($total, 0, ',', '.') }} transaksi
</span>
        </header>

        <div class="fa2-table-scroll">
            <table class="fa2-table">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Jenis</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Referensi</th>
                        <th scope="col">Metode</th>
                        <th scope="col">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        @php
                            $isIncome = $activity->activity_type === 'income';
                            $formattedMethod = $activity->activity_method
                                ? ucfirst(str_replace('_', ' ', $activity->activity_method))
                                : '—';
                        @endphp
                        <tr>
                            <td class="fa2-table__date">
                                <strong>{{ \Carbon\Carbon::parse($activity->activity_date)->translatedFormat('d M Y') }}</strong>
                                <span>{{ \Carbon\Carbon::parse($activity->activity_date)->translatedFormat('H:i') }}</span>
                            </td>
                            <td>
                                <span class="fa2-type {{ $isIncome ? 'fa2-type--income' : 'fa2-type--expense' }}">
                                    <i aria-hidden="true">{{ $isIncome ? '+' : '−' }}</i>
                                    {{ $isIncome ? 'Pemasukan' : 'Pengeluaran' }}
                                </span>
                            </td>
                            <td class="fa2-table__description">
                                <strong>{{ $activity->activity_title }}</strong>
                            </td>
                            <td class="fa2-table__reference">
                                {{ $activity->activity_reference ?: '—' }}
                            </td>
                            <td>
                                <span class="fa2-method">{{ $formattedMethod }}</span>
                            </td>
                            <td class="fa2-table__amount {{ $isIncome ? 'is-income' : 'is-expense' }}">
                                {{ $isIncome ? '+ ' : '− ' }}Rp {{ number_format($activity->activity_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="fa2-empty">
                                    <span class="fa2-empty__icon" aria-hidden="true">⌁</span>
                                    <strong>Belum ada aktivitas keuangan</strong>
                                    <p>Pembayaran terverifikasi atau pengeluaran terposting akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($activities->hasPages())
            <div class="fa2-pagination">
                {{ $activities->onEachSide(1)->links() }}
            </div>
        @endif
    </section>
</div>
@include('partials.dashboard-styles')
@endsection
