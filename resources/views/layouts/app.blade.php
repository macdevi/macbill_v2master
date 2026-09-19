<!doctype html>
<html lang="id"
      x-data="{
          dark: false,
          menu: false,
          init() {
              const savedTheme = localStorage.getItem('macbilling-theme');
              this.dark = savedTheme
                  ? savedTheme === 'dark'
                  : window.matchMedia('(prefers-color-scheme: dark)').matches;

              this.$watch('dark', value => {
                  localStorage.setItem('macbilling-theme', value ? 'dark' : 'light');
              });
          }
      }"
      x-init="init()"
      :class="{ dark: dark }">
<head>
    <style>[x-cloak] { display: none !important; }</style>
    <script>(()=>{try{const t=localStorage.getItem("macbilling-theme");document.documentElement.classList.toggle("dark",t?t==="dark":matchMedia("(prefers-color-scheme: dark)").matches)}catch(e){}})();</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', config('app.name', 'macbilling_v2'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-800 antialiased transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100">
@php
    $currentUser = auth()->user();
    $isSuperAdmin = $currentUser?->isSuperAdmin() ?? false;

    if ($isSuperAdmin) {
        $navItems = [
            [
                'label' => 'Dashboard',
                'href' => route('dashboard'),
                'active' => request()->routeIs('dashboard'),
                'icon' => 'https://img.icons8.com/color/48/dashboard-layout.png',
                'tone' => 'sky',
            ],
            [
                'label' => 'Pelanggan',
                'href' => route('customers.index'),
                'active' => request()->routeIs('customers.*'),
                'icon' => 'https://img.icons8.com/color/48/conference-call.png',
                'tone' => 'emerald',
            ],
            [
                'label' => 'Paket Internet',
                'href' => route('packages.index'),
                'active' => request()->routeIs('packages.*'),
                'icon' => 'https://img.icons8.com/color/48/package.png',
                'tone' => 'violet',
            ],
            [
                'label' => 'MikroTik',
                'href' => route('routers.index'),
                'active' => request()->routeIs('routers.*'),
                'icon' => 'https://img.icons8.com/color/48/wifi-router.png',
                'tone' => 'amber',
            ],
            [
                'label' => 'Pembayaran',
                'href' => route('invoices.index'),
                'active' => request()->routeIs('invoices.index') || request()->routeIs('invoices.show') || request()->routeIs('invoices.print'),
                'icon' => 'https://img.icons8.com/color/48/bill.png',
                'tone' => 'rose',
            ],
            [
                'label' => 'Buat Tagihan',
                'href' => route('invoices.create-page'),
                'active' => request()->routeIs('invoices.create-page') || request()->routeIs('invoices.generate.manual') || request()->routeIs('invoices.generate.mass'),
                'icon' => 'https://img.icons8.com/color/48/add-file.png',
                'tone' => 'pink',
            ],
            [
                'label' => 'Titip Saldo',
                'href' => route('billing.credit-balance'),
                'active' => request()->routeIs('billing.credit-balance'),
                'icon' => 'https://img.icons8.com/color/48/money-transfer.png',
                'tone' => 'pink',
            ],
            [
                'label' => 'Pengeluaran',
                'href' => route('expenses.index'),
                'active' => request()->routeIs('expenses.*'),
                'icon' => 'https://img.icons8.com/color/48/wallet.png',
                'tone' => 'orange',
            ],
            [
                'label' => 'Manajemen User',
                'href' => url('/users'),
                'active' => request()->is('users*'),
                'icon' => 'https://img.icons8.com/color/48/admin-settings-male.png',
                'tone' => 'indigo',
            ],
            [
                'label' => 'Wilayah Operasional',
                'href' => route('areas.index'),
                'active' => request()->routeIs('areas.*'),
                'icon' => 'https://img.icons8.com/color/48/marker.png',
                'tone' => 'violet',
            ],
            [
                'label' => 'Pengaturan Billing',
                'href' => route('settings.billing.edit'),
                'active' => request()->routeIs('settings.billing.*'),
                'icon' => 'https://img.icons8.com/color/48/settings.png',
                'tone' => 'indigo',
            ],
        ];

        $sections = [
            ['title' => null, 'items' => [$navItems[0]]],
            ['title' => 'Master Data', 'items' => array_slice($navItems, 1, 3)],
            ['title' => 'Billing', 'items' => array_slice($navItems, 4, 4)],
            ['title' => 'Administrasi', 'items' => array_slice($navItems, 8, 3)],
        ];
    } elseif ($currentUser?->role === 'admin') {
        $navItems = [
            [
                'label' => 'Dashboard',
                'href' => route('admin.dashboard'),
                'active' => request()->routeIs('admin.dashboard'),
                'icon' => 'https://img.icons8.com/color/48/dashboard-layout.png',
                'tone' => 'sky',
            ],
            [
                'label' => 'Pelanggan',
                'href' => route('customers.index'),
                'active' => request()->routeIs('customers.*'),
                'icon' => 'https://img.icons8.com/color/48/conference-call.png',
                'tone' => 'emerald',
            ],
            [
                'label' => 'Paket Internet',
                'href' => route('packages.index'),
                'active' => request()->routeIs('packages.*'),
                'icon' => 'https://img.icons8.com/color/48/package.png',
                'tone' => 'violet',
            ],
            [
                'label' => 'Tagihan/Pembayaran',
                'href' => route('invoices.index'),
                'active' => request()->routeIs('invoices.*'),
                'icon' => 'https://img.icons8.com/color/48/bill.png',
                'tone' => 'amber',
            ],
            [
                'label' => 'Titip Saldo',
                'href' => route('billing.credit-balance'),
                'active' => request()->routeIs('billing.credit-balance'),
                'icon' => 'https://img.icons8.com/color/48/wallet.png',
                'tone' => 'cyan',
            ],
            [
                'label' => 'Pengeluaran',
                'href' => route('expenses.index'),
                'active' => request()->routeIs('expenses.*'),
                'icon' => 'https://img.icons8.com/color/48/money-bag.png',
                'tone' => 'rose',
            ],
        ];

        $sections = [
            ['title' => null, 'items' => [$navItems[0]]],
            ['title' => 'Master Data', 'items' => array_slice($navItems, 1, 2)],
            ['title' => 'Billing', 'items' => array_slice($navItems, 3, 3)],
        ];
    } else {
        $navItems = [
            [
                'label' => 'Dashboard Operasional',
                'href' => route('staff.home'),
                'active' => request()->routeIs('staff.home'),
                'icon' => 'https://img.icons8.com/color/48/dashboard-layout.png',
                'tone' => 'sky',
            ],
            {{-- KASIR_MENU_E3 --}}
            [
                'label' => 'Pelanggan',
                'href' => route('customers.index'),
                'active' => request()->routeIs('customers.*'),
                'icon' => 'https://img.icons8.com/color/48/conference-call.png',
                'tone' => 'emerald',
            ],
            [
                'label' => 'Invoice & Pembayaran',
                'href' => route('invoices.index'),
                'active' => request()->routeIs('invoices.*'),
                'icon' => 'https://img.icons8.com/color/48/bill.png',
                'tone' => 'amber',
            ],
        ];

        $sections = [
            ['title' => null, 'items' => $navItems],
        ];
    }
@endphp

<div class="flex min-h-screen">
    <div x-show="menu"
         x-cloak
         x-transition.opacity
         @click="menu = false"
         class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden"
         aria-hidden="true">
    </div>

    <aside :class="menu ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white shadow-2xl shadow-slate-900/10 transition-transform duration-300 ease-out lg:static lg:translate-x-0 lg:shadow-none lg:transition-none dark:border-slate-800 dark:bg-slate-900">

        <div class="flex h-full min-h-0 flex-col p-4">
            <div class="mb-5 rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 px-4 py-4 shadow-sm dark:border-slate-700 dark:from-slate-900 dark:to-slate-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#4318E4] to-[#7E57FF] text-sm font-black text-white shadow-lg shadow-violet-500/25">
                        MB
                    </div>
                    <div class="min-w-0">
                        <div class="truncate text-base font-black tracking-[0.13em] text-slate-900 dark:text-white">
                            MAC<span class="text-[#4318E4] dark:text-violet-300">BILLING</span>
                        </div>
                        <div class="mt-0.5 text-xs font-medium text-slate-500 dark:text-slate-400">
                            Panel Administrasi
                        </div>
                    </div>
                </div>
            </div>

            <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto px-1 pr-2 text-sm">
                @foreach($sections as $section)
                    @if($section['title'])
                        <div class="px-3 pb-1.5 pt-4 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400 dark:text-slate-500">
                            {{ $section['title'] }}
                        </div>
                    @endif

                    @foreach($section['items'] as $item)
                        @php
                            $linkClass = $item['active']
                                ? 'text-[#4318E4] dark:text-violet-300'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white';

                            $iconClass = $item['active']
                                ? 'bg-violet-100 ring-1 ring-violet-200 dark:bg-violet-400/15 dark:ring-violet-400/20'
                                : match ($item['tone']) {
                                    'sky' => 'bg-sky-50 ring-1 ring-sky-100 dark:bg-sky-400/10 dark:ring-sky-400/15',
                                    'emerald' => 'bg-emerald-50 ring-1 ring-emerald-100 dark:bg-emerald-400/10 dark:ring-emerald-400/15',
                                    'violet' => 'bg-violet-50 ring-1 ring-violet-100 dark:bg-violet-400/10 dark:ring-violet-400/15',
                                    'amber' => 'bg-amber-50 ring-1 ring-amber-100 dark:bg-amber-400/10 dark:ring-amber-400/15',
                                    'rose' => 'bg-rose-50 ring-1 ring-rose-100 dark:bg-rose-400/10 dark:ring-rose-400/15',
                                    'orange' => 'bg-orange-50 ring-1 ring-orange-100 dark:bg-orange-400/10 dark:ring-orange-400/15',
                                    default => 'bg-indigo-50 ring-1 ring-indigo-100 dark:bg-indigo-400/10 dark:ring-indigo-400/15',
                                };
                        @endphp

                        <a href="{{ $item['href'] }}"
                           @click="menu = false"
                           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-200 focus:outline-none focus:ring-4 focus:ring-violet-300/45 dark:focus:ring-violet-400/25 {{ $linkClass }}"
                           @if($item['active']) aria-current="page" @endif>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg transition {{ $iconClass }}">
                                <img src="{{ $item['icon'] }}"
                                     alt=""
                                     class="h-5 w-5 object-contain"
                                     loading="lazy">
                            </span>
                            <span class="truncate">{{ $item['label'] }}</span>

                            @if($item['active'])
                                <span class="ml-auto h-2 w-2 shrink-0 rounded-full bg-[#4318E4] shadow-sm shadow-violet-500/50 dark:bg-violet-300"></span>
                            @endif
                        </a>
                    @endforeach
                @endforeach
            </nav>

            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-xs text-slate-500 dark:border-slate-700 dark:bg-slate-800/70 dark:text-slate-400">
                <div class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500">
                    Environment
                </div>
                <div class="mt-1.5 text-sm font-bold text-slate-800 dark:text-slate-100">
                    macbilling_v2
                </div>
                <div class="mt-0.5">Manajemen layanan pelanggan</div>
            </div>

            <form method="POST" action="{{ url('/logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                        class="group flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 dark:focus:ring-slate-600">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 transition group-hover:bg-white/15">
                        <img src="https://img.icons8.com/fluency/48/logout-rounded-left.png"
                             alt=""
                             class="h-4 w-4 object-contain"
                             loading="lazy">
                    </span>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="min-w-0 flex-1 bg-slate-100 transition-colors duration-300 dark:bg-slate-950">
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur lg:px-8 dark:border-slate-800 dark:bg-slate-950/85">
            <div class="flex min-w-0 items-center gap-3">
                <button @click="menu = true"
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-xl text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-violet-300/45 lg:hidden dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                        aria-label="Buka menu navigasi">
                    <span aria-hidden="true">☰</span>
                </button>

                <div class="min-w-0">
                    <div class="hidden text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400 sm:block dark:text-slate-500">
                        Finance &amp; Operations
                    </div>
                    <div class="truncate text-sm font-bold text-slate-800 sm:text-base dark:text-slate-100">
                        @yield('title', 'Dashboard')
                    </div>
                </div>
            </div>

            <button @click="dark = !dark"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-violet-300/45 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:focus:ring-violet-400/25"
                    :aria-label="dark ? 'Aktifkan tema terang' : 'Aktifkan tema gelap'">
                <svg x-show="!dark" class="h-4 w-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                </svg>
                <svg x-show="dark" x-cloak class="h-4 w-4 text-violet-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"></path>
                </svg>
                <span x-text="dark ? 'Dark' : 'Light'"></span>
            </button>
        </header>

        <section class="p-4 lg:p-8">
            @if(session('success'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-white p-4 text-emerald-700 shadow-sm dark:border-emerald-900/60 dark:bg-slate-900 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-2xl border border-rose-200 bg-white p-4 text-rose-700 shadow-sm dark:border-rose-900/60 dark:bg-slate-900 dark:text-rose-300">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </section>
    </main>
</div>
</body>
</html>
