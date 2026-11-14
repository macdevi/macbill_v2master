@extends('layouts.app')
@section('title',isset($router)?'Edit Router':'Tambah Router')
@section('content')
@php
$isEdit=isset($router) && $router->exists;
$selectedAreas=array_map('strval',(array)old('area_ids',old('area_access_present')!==null?[]:($isEdit?$router->accessibleAreas->pluck('id')->all():[])));
@endphp
<style>
.router-access-editor{--ra-bg:#fff;--ra-text:#0f172a;--ra-muted:#475569;--ra-line:#cbd5e1;--ra-soft:#f1f5f9;max-width:860px;margin:auto;color:var(--ra-text)}.dark .router-access-editor{--ra-bg:#151e30;--ra-text:#edf2f7;--ra-muted:#b8c5d6;--ra-line:#41516a;--ra-soft:#202c40}.ra-panel{padding:20px;background:var(--ra-bg);border:1px solid var(--ra-line);border-radius:16px;margin-top:18px}.ra-panel h2{font-size:14px;font-weight:750;margin-bottom:6px}.ra-muted{font-size:12px;line-height:1.7;color:var(--ra-muted)}.ra-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px}.ra-label{display:block;font-size:12px;color:var(--ra-muted)}.ra-input{display:block;width:100%;box-sizing:border-box;margin-top:6px;padding:11px 12px;border:1px solid var(--ra-line);border-radius:10px;background:var(--ra-bg);color:var(--ra-text);font-size:14px}.ra-input:focus-visible{outline:3px solid #22d3ee;outline-offset:1px}.ra-area{display:flex;align-items:flex-start;gap:10px;padding:13px;border:1px solid var(--ra-line);border-radius:10px;background:var(--ra-soft);font-size:13px}.ra-area input{margin-top:3px;width:18px;height:18px;accent-color:#0891b2}.ra-area:has(input:checked){border-color:#0891b2}.ra-btn{display:inline-flex;align-items:center;justify-content:center;min-height:42px;border-radius:10px;padding:10px 16px;font-size:13px;font-weight:700;border:1px solid var(--ra-line);background:var(--ra-bg);color:var(--ra-text)}.ra-primary{background:#0e7490;color:#fff;border-color:#0e7490}.ra-error{padding:14px;background:#fff1f2;color:#9f1239;border:1px solid #fda4af;border-radius:12px;margin-top:16px}.dark .ra-error{background:#3d202e;color:#fecdd3}.ra-editor-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px}@media(max-width:640px){.ra-grid{grid-template-columns:1fr}.ra-panel{padding:16px}.ra-editor-head{flex-wrap:wrap}}
</style>
<div class="router-access-editor">
<header class="ra-editor-head"><div><h1 style="font-size:25px;font-weight:800">{{ $isEdit?'Edit router':'Tambah router' }}</h1><p class="ra-muted">Konfigurasi koneksi billing dan wilayah yang boleh memakai router.</p></div><a class="ra-btn" href="{{ route('routers.index') }}">Kembali</a></header>
@if($errors->any())<div class="ra-error" role="alert"><ul style="list-style:disc;padding-left:18px">@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>@endif
<form method="post" action="{{ $isEdit?route('routers.update',$router):route('routers.store') }}">
@csrf @if($isEdit) @method('PUT') @endif
<section class="ra-panel"><h2>Koneksi MikroTik</h2><p class="ra-muted">Menyimpan form ini tidak menjalankan tes koneksi atau mengubah pengaturan perangkat.</p>
<div class="ra-grid">
@foreach([['name','Nama router'],['host','Host / IP'],['port','Port API'],['username','Username API'],['password','Password API']] as [$field,$label])
<label class="ra-label">{{ $label }}{{ $field==='password' && $isEdit?' (kosongkan jika tetap)':'' }}
<input class="ra-input" name="{{ $field }}" type="{{ $field==='password'?'password':($field==='port'?'number':'text') }}" value="{{ $field==='password'?'':old($field,($isEdit?$router->$field:($field==='port'?8728:''))) }}" @if($field!=='password' || !$isEdit) required @endif @if($field==='port') min="1" max="65535" @endif @if($field==='password') autocomplete="new-password" @endif>
</label>
@endforeach
</div>
<label style="display:flex;gap:9px;align-items:center;margin-top:16px;font-size:13px"><input type="checkbox" name="ssl" value="1" @checked(old('ssl',old('_token')!==null?false:($router->ssl??false)))> Gunakan SSL</label>
</section>
<section class="ra-panel"><h2>Wilayah yang boleh menggunakan router</h2><p class="ra-muted">Boleh memilih lebih dari satu wilayah. Admin memperoleh akses melalui wilayah penugasannya. Satu wilayah juga boleh memakai beberapa router. Tanpa pilihan, router hanya tersedia untuk Super Admin.</p>
<input type="hidden" name="area_access_present" value="1">
<div class="ra-grid">
@forelse($areas as $area)
<label class="ra-area"><input type="checkbox" name="area_ids[]" value="{{ $area->id }}" @checked(in_array((string)$area->id,$selectedAreas,true))><span><span style="font-weight:700">{{ $area->name }}</span><span class="ra-muted" style="display:block">{{ $area->code }}{{ $area->active?'':' · nonaktif' }}</span></span></label>
@empty <p class="ra-muted">Belum ada wilayah aktif. Buat wilayah terlebih dahulu.</p> @endforelse
</div>
<p class="ra-muted" style="margin-top:12px">Wilayah yang masih memiliki pelanggan pada router ini tidak dapat dilepas melalui form ini.</p>
</section>
<section class="ra-panel"><h2>Profil isolasi PPPoE</h2><label class="ra-label" for="isolation_profile">Nama profil pada MikroTik<input class="ra-input" id="isolation_profile" name="isolation_profile" type="text" maxlength="100" value="{{ old('isolation_profile',$router->isolation_profile??'') }}" placeholder="Contoh: ISOLIR"></label><p class="ra-muted" style="margin-top:8px">Gunakan nama profil isolasi yang tersedia pada perangkat.</p></section>
<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px"><a class="ra-btn" href="{{ route('routers.index') }}">Batal</a><button class="ra-btn ra-primary" type="submit">Simpan router dan akses wilayah</button></div>
</form>
</div>
@endsection
