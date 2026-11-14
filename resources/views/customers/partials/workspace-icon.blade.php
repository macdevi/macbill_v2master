@php
$paths = [
'users'=>'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M16 3a4 4 0 0 1 0 8M22 21v-2a4 4 0 0 0-3-3.87M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z',
'router'=>'M5 12h14a2 2 0 0 1 2 2v5H3v-5a2 2 0 0 1 2-2ZM7 12V8m10 4V8M7 16h.01M11 16h.01M8 5a7 7 0 0 1 8 0',
'account'=>'M8 11V7a4 4 0 0 1 8 0v4M5 11h14v10H5V11Zm7 4v2',
'billing'=>'M5 3h14v18l-3-2-4 2-4-2-3 2V3Zm3 5h8M8 12h8',
'back'=>'m12 5-7 7 7 7M5 12h14',
'save'=>'M5 3h12l4 4v14H3V3h2Zm2 0v6h10V3M7 21v-8h10v8',
'exchange'=>'M4 7h16m-4-4 4 4-4 4M20 17H4m4-4-4 4 4 4',
'download'=>'M12 3v12m-5-5 5 5 5-5M4 17v4h16v-4',
'upload'=>'M12 16V4m-5 5 5-5 5 5M4 17v4h16v-4',
'file'=>'M14 2H4v20h16V8l-6-6Zm0 0v6h6M8 13h8M8 17h6',
'plus'=>'M12 5v14M5 12h14',
'info'=>'M12 11v6M12 7h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
'check'=>'m5 12 4 4L19 6',
'close'=>'m6 6 12 12M6 18 18 6',
];
@endphp
<svg class="cc-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $paths[$name] ?? $paths['info'] }}" /></svg>
