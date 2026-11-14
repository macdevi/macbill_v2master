@php
    $paths = [
        'refresh' => 'M20 7v5h-5M4 17v-5h5M6 7a7 7 0 0 1 12-1l2 2M4 16l2 2a7 7 0 0 0 12-1',
        'clock' => 'M12 8v5l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'cpu' => 'M7 7h10v10H7V7ZM10 1v6M14 1v6M10 17v6M14 17v6M1 10h6M1 14h6M17 10h6M17 14h6M10 10h4v4h-4v-4Z',
        'router' => 'M5 12h14a2 2 0 0 1 2 2v5H3v-5a2 2 0 0 1 2-2Zm2 0V8m10 4V8M7 16h.01M11 16h.01M8 5a7 7 0 0 1 8 0M10 8a3 3 0 0 1 4 0',
        'plus' => 'M12 5v14M5 12h14',
        'edit' => 'm16 4 4 4M4 20l4-1L20 7a2.1 2.1 0 0 0-3-3L4 17v3Z',
        'test' => 'm13 2-8 12h6l-1 8 9-13h-6l1-7Z',
        'trash' => 'M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7M14 10v7',
        'back' => 'm12 5-7 7 7 7M5 12h14',
        'shield' => 'm12 3 8 3v5c0 5-4 8-8 10-4-2-8-5-8-10V6l8-3Zm-4 9 3 3 5-6',
        'users' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M16 3a4 4 0 0 1 0 8M22 21v-2a4 4 0 0 0-3-3.87M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z',
        'invoice' => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3Zm3 5h6M9 12h6',
        'payment' => 'M3 5h18v14H3V5Zm0 5h18M7 15h3',
        'package' => 'm12 3 9 5v8l-9 5-9-5V8l9-5Zm0 10v8M3 8l9 5 9-5M7 5l9 5',
        'check' => 'm5 12 4 4L19 6',
        'alert' => 'm12 3 10 18H2L12 3Zm0 6v5M12 17h.01',
    ];
@endphp
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true" class="{{ $iconClass ?? 'h-5 w-5 shrink-0' }}">
    <path d="{{ $paths[$name] ?? $paths['router'] }}" />
</svg>
