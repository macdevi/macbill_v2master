@extends('layouts.app')
@section('title','Tambah Pelanggan')
@section('content')
@include('customers.partials.workspace-theme')
<div class="customer-workspace cc-form-wrap">
<header class="cc-head"><div><h1 class="cc-title">Tambah pelanggan</h1><p class="cc-subtitle">Tentukan wilayah, router, paket, dan pengaturan billing pelanggan.</p></div><a class="cc-btn" href="{{ route('customers.index') }}">@include('customers.partials.workspace-icon',['name'=>'back']) Kembali</a></header>
@include('customers.partials.workspace-messages')
<div class="cc-note">@include('customers.partials.workspace-icon',['name'=>'info'])<p>Penyimpanan ini hanya membuat data pelanggan di billing. Akun PPPoE pada MikroTik tidak dibuat atau diubah.</p></div>
<form method="post" action="{{ route('customers.store') }}" id="customer-create-form">@csrf
@include('customers.form')
<div class="cc-savebar"><a class="cc-btn" href="{{ route('customers.index') }}">Batal</a><button type="submit" class="cc-btn cc-primary">@include('customers.partials.workspace-icon',['name'=>'save']) Simpan ke billing</button></div>
</form>
</div>
@endsection
