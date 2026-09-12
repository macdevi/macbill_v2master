@extends('layouts.app') @section('title','Dashboard') @section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
@foreach([['Pemasukan',$income,'text-emerald-500'],['Pengeluaran',$expense,'text-rose-500'],['Saldo Bersih',$balance,'text-cyan-500'],['Piutang',$unpaid,'text-amber-500']] as [$label,$value,$color])
<div class="neon rounded-2xl bg-white dark:bg-slate-900 p-5 border border-slate-200 dark:border-slate-800"><div class="text-xs uppercase tracking-widest text-slate-400">{{$label}}</div><div class="mt-3 text-2xl font-black {{$color}}">Rp {{number_format($value,0,',','.')}}</div></div>
@endforeach
</div>

<div class="grid xl:grid-cols-3 gap-5 mt-5">

<div class="xl:col-span-2 neon rounded-2xl bg-white dark:bg-slate-900 border p-5"
x-data="pppoeMonitor()"
x-init="load()">


<div class="grid grid-cols-3 gap-3">

<button @click="modal=true;title='Semua PPPoE';detail=allUsers"
class="p-4 rounded-xl border text-left">
<div class="text-xs text-slate-400">
TOTAL PPPoE
</div>
<div class="text-3xl font-black" x-text="total"></div>
<div class="text-xs">
Klik detail
</div>
</button>


<button @click="modal=true;title='PPPoE Online';detail=onlineUsers"
class="p-4 rounded-xl border text-left">
<div class="text-xs text-slate-400">
ONLINE
</div>
<div class="text-3xl font-black text-cyan-400" x-text="online"></div>
<div class="text-xs">
Klik detail
</div>
</button>


<button @click="modal=true;title='PPPoE Offline';detail=offlineUsers"
class="p-4 rounded-xl border text-left">
<div class="text-xs text-slate-400">
OFFLINE
</div>
<div class="text-3xl font-black text-rose-400" x-text="offline"></div>
<div class="text-xs">
Klik detail
</div>
</button>

</div>



<div x-show="modal"
@click.self="modal=false"
class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">

<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden">

<div class="flex items-center justify-between p-5 border-b bg-white dark:bg-slate-900">

<div>
<h3 class="text-xl font-black" x-text="title"></h3>
<p class="text-xs text-slate-400">
Monitoring PPPoE
</p>
</div>

<button
@click="modal=false"
class="w-10 h-10 rounded-full bg-rose-500 text-white text-xl font-bold">
×
</button>

</div>


<div class="overflow-y-auto p-5 space-y-3">

<template x-for="u in detail">

<div class="rounded-2xl border p-4 hover:shadow-md transition">

<div class="flex justify-between items-center">

<div class="font-black text-lg"
x-text="u.username">
</div>

<span
class="text-xs px-3 py-1 rounded-full font-bold"
:class="u.status=='online'?'bg-cyan-100 text-cyan-700':'bg-rose-100 text-rose-700'"
x-text="u.status">
</span>

</div>


<div class="mt-3 text-sm text-slate-500">

<div>
IP :
<span class="font-semibold"
x-text="u.address ?? '-'">
</span>
</div>

<div>
Info :
<span class="font-semibold"
x-text="u.uptime ?? u.profile ?? '-'">
</span>
</div>

</div>

</div>

</template>

</div>


<div class="p-4 border-t bg-white dark:bg-slate-900">

<button
@click="modal=false"
class="w-full py-3 rounded-xl bg-slate-800 text-white font-bold">
Tutup
</button>

</div>

</div>

</div>

<div class="neon rounded-2xl bg-white dark:bg-slate-900 border p-5">

<h2 class="font-bold mb-4">
Overview
</h2>

<div class="space-y-4 text-sm">

<div class="flex justify-between">
<span>Pelanggan</span>
<b>{{$customers}}</b>
</div>

<div class="flex justify-between">
<span>Isolir</span>
<b class="text-rose-400">{{$isolated}}</b>
</div>

</div>

</div>


</div>
</div>
@endsection