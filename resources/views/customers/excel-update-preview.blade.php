{{-- excel-roundtrip-v1 --}}
<div class="mb-6 space-y-4 rounded-xl border border-cyan-500 p-4">
    <h2 class="text-lg font-bold">Import baru atau update hasil ekspor</h2>
    <ul class="list-disc space-y-1 pl-5 text-sm">
        <li>Template impor: menambah pelanggan baru, wilayah tujuan wajib dipilih.</li>
        <li>Hasil ekspor: memperbarui pelanggan berdasarkan Kode Pelanggan, lalu menampilkan preview.</li>
        <li>Untuk update, wilayah tujuan pada form diabaikan. Wilayah dibaca dari Kode Wilayah pada Excel.</li>
        <li>Harga Custom: kosong untuk memakai harga paket, atau angka seperti 150000.</li>
        <li>Password kosong mempertahankan password lama. Nomor telepon dan alamat kosong mengosongkan datanya.</li>
        <li>Jangan mengubah Kode Pelanggan. Data hanya disimpan ke billing, tidak ke MikroTik atau invoice lama.</li>
    </ul>

    @if(session('excel_update_status'))
        <p class="rounded border border-green-500 p-3">
            {{ session('excel_update_status') }}
        </p>
    @endif

    @if($preview = session('customer_excel_roundtrip_v1'))
        <h3 class="font-bold">Preview update pelanggan</h3>
        <p class="text-sm">
            {{ count($preview['display']) }} pelanggan berubah.
            {{ $preview['unchanged'] }} pelanggan tetap.
            Preview berlaku 30 menit sejak upload.
        </p>

        <div class="max-h-96 overflow-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="p-2">Pelanggan</th>
                        <th class="p-2">Kolom</th>
                        <th class="p-2">Lama</th>
                        <th class="p-2">Baru</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($preview['display'] as $item)
                        @foreach($item['changes'] as $change)
                            <tr class="border-t">
                                <td class="p-2">{{ $item['code'] }} / {{ $item['name'] }}</td>
                                <td class="p-2">{{ $change['field'] }}</td>
                                <td class="p-2">{{ $change['old'] ?? '(kosong)' }}</td>
                                <td class="p-2">{{ $change['new'] ?? '(kosong)' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <form method="POST" action="{{ route('customers.import.excel.store') }}">
            @csrf
            <input type="hidden" name="_excel_update_token" value="{{ $preview['token'] }}">
            <button type="submit" class="rounded bg-cyan-500 px-4 py-2 font-bold text-slate-950">
                Konfirmasi simpan perubahan
            </button>
        </form>
    @endif
</div>
