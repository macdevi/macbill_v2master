@extends('layouts.app')
@section('title', 'Riwayat Aktivitas Keuangan')
@section('content')
    <div class="dashboard-page">
        <header class="dashboard-header">
            <div>
                <h1 class="dashboard-header__title">Riwayat Aktivitas Keuangan</h1>
                <p class="dashboard-header__time">
                    Seluruh pembayaran terverifikasi dan pengeluaran terposting, diurutkan dari yang terbaru.
                </p>
            </div>
        </header>

        <section class="financial-summary-card financial-summary-card--area" aria-labelledby="finance-activity-page-title">
            <header class="financial-summary-card__header">
                <div>
                    <p class="financial-summary-card__eyebrow">KEUANGAN</p>
                    <h2 id="finance-activity-page-title" class="financial-summary-card__title">
                        Daftar Transaksi
                    </h2>
                    <p class="financial-summary-card__period">
                        Menampilkan {{ $activities->firstItem() ?? 0 }}–{{ $activities->lastItem() ?? 0 }}
                        dari {{ number_format($activities->total(), 0, ',', '.') }} transaksi
                    </p>
                </div>
                <a href="{{ auth()->user()?->isSuperAdmin() ? route('dashboard') : route('admin.dashboard') }}" class="financial-summary-card__badge" style="text-decoration:none;">
                    <span class="financial-summary-card__badge-dot"></span>
                    Kembali ke Dashboard
                </a>
            </header>
            <div class="financial-summary-card__body">
                <div class="area-financial-table-wrapper">
                    <table class="area-financial-table">
                        <thead>
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Jenis</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Referensi</th>
                                <th scope="col">Metode</th>
                                <th scope="col" class="area-financial-table__number">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($activity->activity_date)->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td>
                                        @if ($activity->activity_type === 'income')
                                            <span class="area-financial-table__income">Pemasukan</span>
                                        @else
                                            <span class="area-financial-table__expense">Pengeluaran</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ $activity->activity_title }}</strong></td>
                                    <td>{{ $activity->activity_reference ?: '—' }}</td>
                                    <td>{{ $activity->activity_method ? ucfirst(str_replace('_', ' ', $activity->activity_method)) : '—' }}</td>
                                    <td class="area-financial-table__number {{ $activity->activity_type === 'income' ? 'area-financial-table__income' : 'area-financial-table__expense' }}">
                                        {{ $activity->activity_type === 'income' ? '+ ' : '- ' }}Rp {{ number_format($activity->activity_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; padding: 1.5rem; color:#94a3b8;">
                                        Belum ada aktivitas keuangan tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 1.25rem;">
                    {{ $activities->onEachSide(1)->links() }}
                </div>
            </div>
        </section>
    </div>
@endsection
