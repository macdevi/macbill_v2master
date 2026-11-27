@php
$paths = [
 'package'=>'m12 3 9 5v8l-9 5-9-5V8l9-5Zm0 10v8M3 8l9 5 9-5M7 5l9 5',
 'plus'=>'M12 5v14M5 12h14',
 'download'=>'M12 3v12m-5-5 5 5 5-5M4 17v4h16v-4',
 'upload'=>'M12 16V4m-5 5 5-5 5 5M4 17v4h16v-4',
 'router'=>'M5 12h14a2 2 0 0 1 2 2v5H3v-5a2 2 0 0 1 2-2Zm2 0V8m10 4V8M7 16h.01M11 16h.01M8 5a7 7 0 0 1 8 0',
 'edit'=>'m16 4 4 4M4 20l4-1L20 7a2.1 2.1 0 0 0-3-3L4 17v3Z',
 'send'=>'m22 2-7 20-4-9-9-4 20-7ZM22 2 11 13',
 'check'=>'m5 12 4 4L19 6',
 'clock'=>'M12 8v5l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
 'alert'=>'m12 3 10 18H2L12 3Zm0 6v5M12 17h.01',
 'trash'=>'M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7M14 10v7',
 'info'=>'M12 11v6M12 7h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
 'search'=>'m21 21-5-5M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z',
 'back'=>'m12 5-7 7 7 7M5 12h14',
 'settings'=>'M4 6h16M4 12h16M4 18h16M8 3v6M16 9v6M10 15v6',
 'save'=>'M5 3h12l4 4v14H3V3h2Zm2 0v6h10V3M7 21v-8h10v8',
];
@endphp
<svg class="pc-icon {{ $extraClass ?? '' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $paths[$name] ?? $paths['package'] }}" /></svg>
