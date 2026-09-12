@foreach([
['name','Nama Paket'],
['mikrotik_profile','Profile MikroTik'],
['download_speed','Download Mbps'],
['upload_speed','Upload Mbps'],
['burst_limit','Burst Limit'],
['burst_threshold','Burst Threshold'],
['burst_time','Burst Time'],
['priority','Priority'],
['monthly_price','Harga Bulanan']
] as [$n,$l])

<label class="block">
<span class="text-xs text-slate-400">{{$l}}</span>

<input
name="{{$n}}"
type="{{in_array($n,['monthly_price','download_speed','upload_speed','priority'])?'number':'text'}}"
value="{{old($n,$package->$n??($n=='priority'?8:($n=='monthly_price'?0:'')))}}"
class="mt-1 w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-transparent px-4 py-3">

</label>

@endforeach
