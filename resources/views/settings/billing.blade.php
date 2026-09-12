@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div>
        <p class="text-sm font-semibold text-cyan-700 dark:text-cyan-300">Pengaturan</p>
        <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 dark:text-white">Pengaturan Usaha</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">Isi data usaha dan pesan tagihan di bawah ini. Informasi ini dipakai pada invoice cetak dan tombol WhatsApp pelanggan.</p>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
            <div class="font-bold">Data belum dapat disimpan.</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.billing.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <h2 class="text-base font-black text-slate-900 dark:text-white">1. Profil Usaha</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Data ini dapat tampil pada invoice dan pesan WhatsApp.</p>
            </div>
            <div class="grid gap-5 p-5 md:grid-cols-2">
                <div><label for="business_name" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Nama usaha <span class="text-rose-600">*</span></label><input id="business_name" name="business_name" type="text" maxlength="100" required value="{{ old('business_name', $businessName) }}" placeholder="Contoh: CV Internet Sejahtera" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
                <div><label for="business_owner" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Nama pemilik <span class="font-normal text-slate-400">(opsional)</span></label><input id="business_owner" name="business_owner" type="text" maxlength="100" value="{{ old('business_owner', $businessOwner) }}" placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
                <div><label for="business_phone" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Nomor WhatsApp usaha <span class="font-normal text-slate-400">(opsional)</span></label><input id="business_phone" name="business_phone" type="text" maxlength="30" value="{{ old('business_phone', $businessPhone) }}" placeholder="Contoh: 0812 3456 7890" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"><p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Boleh ditulis 0812..., +62812..., atau 62812....</p></div>
                <div><label for="business_email" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Email usaha <span class="font-normal text-slate-400">(opsional)</span></label><input id="business_email" name="business_email" type="email" maxlength="100" value="{{ old('business_email', $businessEmail) }}" placeholder="Contoh: admin@usahaanda.com" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
                <div class="md:col-span-2"><label for="business_address" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Alamat usaha <span class="font-normal text-slate-400">(opsional)</span></label><textarea id="business_address" name="business_address" rows="3" maxlength="500" placeholder="Contoh: Jl. Merdeka No. 10" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white">{{ old('business_address', $businessAddress) }}</textarea></div>
                <div><label for="business_city" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Kota / Kabupaten <span class="font-normal text-slate-400">(opsional)</span></label><input id="business_city" name="business_city" type="text" maxlength="100" value="{{ old('business_city', $businessCity) }}" placeholder="Contoh: Bandung" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h2 class="text-base font-black text-slate-900 dark:text-white">2. Pesan WhatsApp Tagihan</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pesan ini digunakan saat tombol WhatsApp pada daftar tagihan ditekan.</p></div>
            <div class="space-y-4 p-5">
                <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-4 text-sm text-cyan-900 dark:border-cyan-500/20 dark:bg-cyan-500/10 dark:text-cyan-100"><div class="font-bold">Kode yang dapat digunakan</div><p class="mt-1 leading-6"><code>{customer_name}</code>, <code>{invoice_number}</code>, <code>{amount}</code>, <code>{due_date}</code>, <code>{invoice_url}</code>, <code>{business_name}</code>, <code>{business_phone}</code>, dan <code>{credit_summary}</code>.</p><p class="mt-1 leading-6"><code>{credit_summary}</code> otomatis kosong bila pelanggan tidak memakai titip saldo.</p></div>
                <div><label for="whatsapp_invoice_template" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Isi pesan WhatsApp <span class="text-rose-600">*</span></label><textarea id="whatsapp_invoice_template" name="whatsapp_invoice_template" rows="13" maxlength="3000" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 font-mono text-sm leading-6 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white">{{ old('whatsapp_invoice_template', $whatsappInvoiceTemplate) }}</textarea></div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h2 class="text-base font-black text-slate-900 dark:text-white">3. Tampilan Invoice</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Teks ini tampil di bagian paling bawah invoice cetak.</p></div>
            <div class="p-5"><label for="invoice_footer" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Catatan bawah invoice <span class="font-normal text-slate-400">(opsional)</span></label><textarea id="invoice_footer" name="invoice_footer" rows="3" maxlength="500" placeholder="Contoh: Terima kasih telah menggunakan layanan kami." class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm leading-6 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white">{{ old('invoice_footer', $invoiceFooter) }}</textarea></div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h2 class="text-base font-black text-slate-900 dark:text-white">4. Pajak Invoice Baru</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Perubahan pajak hanya digunakan saat membuat invoice baru. Invoice yang sudah ada tidak berubah.</p></div>
            <div class="grid gap-5 p-5 md:grid-cols-2">
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 dark:border-slate-700"><input type="checkbox" name="billing_tax_enabled" value="1" @checked(old('billing_tax_enabled', $taxEnabled)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500"><span><span class="block text-sm font-bold text-slate-800 dark:text-slate-100">Gunakan pajak pada invoice baru</span><span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">Matikan bila usaha belum menerapkan pajak pada tagihan baru.</span></span></label>
                <div></div>
                <div><label for="billing_tax_name" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Nama pajak</label><input id="billing_tax_name" name="billing_tax_name" type="text" maxlength="50" value="{{ old('billing_tax_name', $taxName) }}" placeholder="Contoh: PPN" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
                <div><label for="billing_tax_rate" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Tarif pajak (%) <span class="text-rose-600">*</span></label><input id="billing_tax_rate" name="billing_tax_rate" type="number" min="0" max="100" step="0.0001" inputmode="decimal" required value="{{ old('billing_tax_rate', $taxRate) }}" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between"><p class="text-xs leading-5 text-slate-500 dark:text-slate-400">Field bertanda <span class="font-bold text-rose-600">*</span> wajib diisi.</p><button type="submit" class="inline-flex items-center justify-center rounded-xl bg-cyan-600 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-cyan-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Simpan Pengaturan</button></div>
    </form>
</div>
@endsection
