@extends('layouts.app') 
@section('title','Paket Internet') 

@section('content')


<div class="flex items-center justify-between mb-6">

<div>
<h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white">
📦 Paket Internet
</h1>

<p class="text-sm text-slate-400 mt-1">
Kelola profile dan bandwidth pelanggan
</p>

</div>


<div class="relative" x-data="{open:false}">

<button
@click="open=!open"
class="flex items-center gap-2 rounded-2xl px-5 py-3 font-black
bg-gradient-to-r from-cyan-400 to-blue-500
text-white shadow-lg shadow-cyan-500/30">

<span class="text-xl">
+
</span>

Paket

</button>


<div
x-show="open"
x-transition
@click.outside="open=false"
class="absolute right-0 mt-3 w-64 rounded-3xl
bg-white dark:bg-slate-900
border border-slate-200 dark:border-slate-700
shadow-2xl overflow-hidden z-50">


<a href="{{route('packages.create')}}"
class="flex items-center gap-3 px-5 py-4
hover:bg-cyan-50 dark:hover:bg-slate-800
transition">

<span class="w-10 h-10 rounded-xl bg-cyan-100 dark:bg-cyan-900/40 flex items-center justify-center text-cyan-500 text-xl">
＋
</span>

<div>
<div class="font-bold">
Tambah Paket
</div>

<div class="text-xs text-slate-400">
Buat paket manual
</div>

</div>

</a>



<a href="#"
class="flex items-center gap-3 px-5 py-4
hover:bg-cyan-50 dark:hover:bg-slate-800
transition">

<span class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-500 text-xl">
⌁
</span>


<div>

<div class="font-bold">
Import MikroTik
</div>

<div class="text-xs text-slate-400">
Ambil profile router
</div>

</div>


</a>


</div>


</div>

</div>


<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">

@foreach($packages as $p)

<div class="neon rounded-2xl bg-white dark:bg-slate-900 border p-5">

<div class="flex justify-between items-start">

<h2 class="font-black text-lg">
{{$p->name}}
</h2>

@if($p->active)
<span class="text-xs px-3 py-1 rounded-full bg-emerald-100 text-emerald-700">
ACTIVE
</span>
@endif

</div>


<div class="text-3xl font-black text-cyan-400 mt-3">
{{$p->download_speed}}/{{$p->upload_speed}} Mbps
</div>


<div class="mt-3 text-xl font-bold">
Rp {{number_format($p->monthly_price,0,',','.')}} 
<span class="text-sm font-normal">
/bulan
</span>
</div>


<div class="mt-4 space-y-2 text-sm text-slate-500">

<div>
Profile :
<b class="text-slate-700 dark:text-slate-300">
{{$p->mikrotik_profile}}
</b>
</div>


@if($p->burst_limit)
<div>
Burst :
<b class="text-slate-700 dark:text-slate-300">
{{$p->burst_limit}}
</b>
</div>
@endif


@if($p->burst_threshold)
<div>
Threshold :
<b class="text-slate-700 dark:text-slate-300">
{{$p->burst_threshold}}
</b>
</div>
@endif


@if($p->burst_time)
<div>
Time :
<b class="text-slate-700 dark:text-slate-300">
{{$p->burst_time}}
</b>
</div>
@endif


<div>
Priority :
<b class="text-slate-700 dark:text-slate-300">
{{$p->priority ?? 8}}
</b>
</div>

</div>


<div class="mt-5 flex flex-wrap gap-3">

<a 
class="text-cyan-400 font-bold"
href="{{route('packages.edit',$p)}}">
Edit
</a>


@foreach($routers as $r)

<form method="post" action="{{route('packages.sync',$p)}}">

@csrf

<input type="hidden" name="router_id" value="{{$r->id}}">

<button class="text-emerald-400 font-bold">
Sync {{$r->name}}
</button>

</form>

@endforeach

</div>

</div>

@endforeach

</div>

@endsection
