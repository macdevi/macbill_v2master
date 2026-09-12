@extends('layouts.app') @section('title',isset($router)?'Edit Router':'Tambah Router') @section('content')<form method="post" action="{{isset($router)?route('routers.update',$router):route('routers.store')}}" class="max-w-xl space-y-4">@csrf @if(isset($router))@method('PUT')@endif @foreach([['name','Nama'],['host','Host/IP'],['port','Port'],['username','Username'],['password','Password']] as [$n,$l])<label class="block"><span class="text-xs text-slate-400">{{$l}}</span><input name="{{$n}}" type="{{$n==='password'?'password':'text'}}" value="{{$n==='password'?'':old($n,$router->$n??'')}}" class="mt-1 w-full rounded-xl border bg-transparent p-3"></label>@endforeach<label class="flex gap-2"><input type="checkbox" name="ssl" value="1" @checked(old('ssl',$router->ssl??false))> SSL</label>        <div>
            <label for="isolation_profile"
                   class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Profile Isolasi PPPoE
            </label>

            <input
                id="isolation_profile"
                name="isolation_profile"
                type="text"
                value="{{ old('isolation_profile', isset($router) ? $router->isolation_profile : '') }}"
                placeholder="Contoh: ISOLIR"
                maxlength="100"
                autocomplete="off"
                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
            >

            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                Nama PPP Profile isolasi yang tersedia pada MikroTik ini.
            </p>

            @error('isolation_profile')
                <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">
                    {{ $message }}
                </p>
            @enderror
        </div>
<button class="rounded-xl bg-cyan-500 px-5 py-3 font-bold">Simpan</button>


        </form>@endsection