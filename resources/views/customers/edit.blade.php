@extends('layouts.app')
@section('title','Edit Pelanggan')
@section('content')
@include('customers.partials.workspace-theme')
<div class="customer-workspace cc-form-wrap">
<header class="cc-head"><div><h1 class="cc-title">Edit pelanggan</h1><p class="cc-subtitle">{{ $customer->name }} · {{ $customer->customer_code }}</p></div><a class="cc-btn" href="{{ route('customers.index') }}">@include('customers.partials.workspace-icon',['name'=>'back']) Kembali</a></header>
@include('customers.partials.workspace-messages')
<div class="cc-note">@include('customers.partials.workspace-icon',['name'=>'info'])<p>Router tidak dapat dipindahkan melalui edit biasa. Saat menyimpan, pilih billing saja atau billing dan MikroTik.</p></div>
<form method="post" action="{{ route('customers.update',$customer) }}" id="customer-edit-form">@csrf @method('PUT')
@include('customers.form')
<input type="hidden" name="save_target" id="save_target" value="billing">
<div class="cc-savebar"><a class="cc-btn" href="{{ route('customers.index') }}">Batal</a><button type="submit" class="cc-btn cc-primary" id="open-save-modal">@include('customers.partials.workspace-icon',['name'=>'save']) Simpan perubahan</button></div>
</form>
<dialog class="cc-dialog" id="save-modal" aria-labelledby="cc-save-title">
<h2 id="cc-save-title" style="font-size:18px;font-weight:750">Simpan perubahan pelanggan</h2>
<p class="cc-subtitle">Tujuan MikroTik: {{ $customer->router?->name ?: 'Router tidak tersedia' }}. Pilih tindakan secara sadar.</p>
<label class="cc-choice"><input type="radio" name="cc_save_choice" value="billing"><span style="font-weight:700">Billing saja<small>Simpan data billing tanpa mengirim perubahan ke MikroTik.</small></span></label>
<label class="cc-choice"><input type="radio" name="cc_save_choice" value="billing_mikrotik"><span style="font-weight:700">Billing dan MikroTik<small>Simpan billing, lalu perbarui akun PPPoE pada router pelanggan. Jika pengiriman gagal, perubahan billing tetap tersimpan.</small></span></label>
<p class="cc-row-note" data-cc-sync-warning hidden>Pengiriman tidak tersedia untuk pilihan router/paket ini. Gunakan billing saja untuk mempertahankan relasi lama, atau pilih paket aktif yang sesuai.</p>
<div class="cc-savebar"><button type="button" class="cc-btn" id="close-save-modal">Batal</button><button type="button" class="cc-btn cc-primary" id="confirm-save" disabled>Konfirmasi simpan</button></div>
</dialog>
</div>
<script>
(() => {
    const form = document.getElementById('customer-edit-form');
    const dialog = document.getElementById('save-modal');
    const confirmButton = document.getElementById('confirm-save');
    const cancel = document.getElementById('close-save-modal');
    const choices = Array.from(dialog.querySelectorAll('input[name="cc_save_choice"]'));
    const editor = form.querySelector('[data-cc-editor]');
    let approved = false;
    let submitting = false;
    function refresh() {
        const allowed = editor.dataset.syncAvailable === '1';
        const deviceChoice = choices.find(choice => choice.value === 'billing_mikrotik');
        deviceChoice.disabled = !allowed;
        if (!allowed) deviceChoice.checked = false;
        dialog.querySelector('[data-cc-sync-warning]').hidden = allowed;
        confirmButton.disabled = !choices.some(choice => choice.checked && !choice.disabled) || submitting;
    }
    choices.forEach(choice => choice.addEventListener('change', refresh));
    cancel.addEventListener('click', () => {if (!submitting) dialog.close();});
    dialog.addEventListener('cancel', event => {if (submitting) event.preventDefault();});
    form.addEventListener('submit', event => {
        if (submitting) {event.preventDefault();return;}
        if (!approved) {
            event.preventDefault();
            choices.forEach(choice => choice.checked = false);
            refresh();
            dialog.showModal();
            return;
        }
        submitting = true;
        confirmButton.disabled = true;
        cancel.disabled = true;
        document.getElementById('open-save-modal').disabled = true;
        confirmButton.textContent = 'Menyimpan...';
    });
    confirmButton.addEventListener('click', () => {
        const selected = choices.find(choice => choice.checked && !choice.disabled);
        if (!selected || submitting) return;
        if (!form.checkValidity()) {dialog.close();form.reportValidity();return;}
        document.getElementById('save_target').value = selected.value;
        approved = true;
        form.requestSubmit();
        approved = false;
    });
})();
</script>
@endsection
