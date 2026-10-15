@extends('layouts.app')
@section('title', 'Pasang Baru')
@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-2xl font-black">Pasang Baru</h1>
        <a href="{{ route('installations.create') }}"
           class="rounded-xl bg-cyan-500 px-4 py-3 font-bold text-slate-950">
            Tambah Pemasangan
        </a>
    </div>
    @forelse($installations as $item)
        <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="font-bold">{{ $item->customer_name }}</div>
                    <div class="text-sm text-slate-500">
                        {{ $item->receipt_number }} · Aktif {{ $item->activated_on->format('d/m/Y') }}
                    </div>
                    <div class="mt-1 text-sm">
                        Pemasangan: Rp {{ number_format((float) $item->total_amount, 0, ',', '.') }}
                        · {{ (float) $item->paid_amount >= (float) $item->total_amount ? 'Lunas' : 'Belum lunas' }}
                    </div>
                </div>
                <a href="{{ route('installations.show', $item) }}"
                   class="rounded-xl border border-slate-300 px-4 py-2 font-semibold dark:border-slate-600">
                    Nota
                </a>
            </div>
        </div>
    @empty
        <div class="rounded-2xl bg-white p-6 text-slate-500 dark:bg-slate-900">
            Belum ada pemasangan baru.
        </div>
    @endforelse
    {{ $installations->links() }}
</div>
@endsection
