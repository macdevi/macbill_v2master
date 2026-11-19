@extends('layouts.app')

@section('title', 'Update Jatuh Tempo Excel')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <h1 class="text-2xl font-bold">Update Jatuh Tempo via Excel</h1>

    <p>
        Upload hasil Export Excel aplikasi. Hanya Kode Pelanggan dan
        Tanggal Jatuh Tempo yang dibaca. Isi tanggal dengan angka 1 sampai 28.
        Kolom lain diabaikan. Invoice lama tidak diubah.
    </p>

    <a href="{{ route('customers.index') }}" class="inline-block underline">
        Kembali ke pelanggan
    </a>

    @if(session('status'))
        <div class="rounded border border-green-500 p-4">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded border border-red-500 p-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('customers.due-day-excel.preview') }}"
          enctype="multipart/form-data"
          class="space-y-4 rounded border p-4">
        @csrf
        <label class="block">
            <span class="mb-2 block">File hasil ekspor (.xlsx atau .xls, maksimal 5 MB)</span>
            <input type="file" name="file" accept=".xlsx,.xls" required>
        </label>
        <button type="submit" class="rounded bg-cyan-500 px-4 py-2 text-slate-950">
            Upload dan lihat preview
        </button>
    </form>

    @if($preview)
        <div class="space-y-4 rounded border p-4">
            <p>
                {{ count($preview['changes']) }} pelanggan akan berubah.
                {{ $preview['unchanged'] }} pelanggan tetap.
                Preview berlaku 30 menit sejak upload.
            </p>

            <div class="max-h-96 overflow-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="p-2">Kode</th>
                            <th class="p-2">Nama</th>
                            <th class="p-2">Tanggal lama</th>
                            <th class="p-2">Tanggal baru</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($preview['changes'] as $change)
                        <tr class="border-t">
                            <td class="p-2">{{ $change['code'] }}</td>
                            <td class="p-2">{{ $change['name'] }}</td>
                            <td class="p-2">{{ $change['old'] }}</td>
                            <td class="p-2">{{ $change['new'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <form method="POST" action="{{ route('customers.due-day-excel.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $preview['token'] }}">
                <button type="submit"
                        class="rounded bg-cyan-500 px-4 py-2 text-slate-950">
                    Konfirmasi simpan jatuh tempo
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
