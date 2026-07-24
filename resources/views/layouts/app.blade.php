<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kurti SAI')</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="icon" type="image/png" href="/icon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/icon/favicon.svg" />
    <link rel="shortcut icon" href="/icon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/icon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Kurti SAIS" />
    <link rel="manifest" href="/icon/site.webmanifest" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased bg-slate-50 text-slate-800">
    <nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-slate-700 bg-slate-900 text-white shadow-sm">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-6">
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <img src="{{ asset('logo.png') }}" alt="Kurti SAI" class="h-9 w-9 rounded-md bg-white object-contain">
                <span class="mr-1 text-lg font-bold tracking-wide">Kurti SAI</span>
            </a>
            <div class="hidden items-center gap-2 md:flex">
            <a href="#" target="_blank"
               onclick="event.preventDefault(); document.getElementById('comingSoonModal').classList.remove('hidden');"
               class="rounded-lg bg-slate-700 px-3 py-2 text-sm font-medium transition hover:bg-slate-600">
                Bukom
            </a>
            @if(in_array(Auth::user()->type, ['fasil', 'orangtua']))
                <a href="{{ route('weekly-reports.index') }}"
                   class="{{ request()->routeIs('weekly-reports.*') ? 'bg-emerald-500 ring-2 ring-emerald-300' : 'bg-slate-700 hover:bg-slate-600' }} rounded-lg px-3 py-2 text-sm font-medium transition">
                    Weekly Report
                </a>
            @endif
            </div>
        </div>

        <div class="hidden items-center justify-end gap-2 md:flex">
            <span class="hidden text-sm text-slate-200 sm:inline">{{ Auth::user()->name }}</span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-700 hover:text-white">
                    Logout
                </button>
            </form>
            <a href="https://wa.me/6285603155491" target="_blank"
               class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium transition hover:bg-emerald-500">
                Sampaikan Masukan
            </a>
        </div>
        <button type="button" @click="open = !open" class="rounded-lg p-2 text-slate-200 hover:bg-slate-700 md:hidden" aria-label="Buka menu">
            <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-cloak x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
        </div>
        <div x-cloak x-show="open" x-transition class="border-t border-slate-700 px-4 py-3 md:hidden">
            <div class="mx-auto grid max-w-7xl gap-2">
                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ Auth::user()->name }}</p>
                <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm hover:bg-slate-800">Dashboard</a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('comingSoonModal').classList.remove('hidden'); open = false" class="rounded-lg px-3 py-2 text-sm hover:bg-slate-800">Bukom</a>
                @if(in_array(Auth::user()->type, ['fasil', 'orangtua']))
                    <a href="{{ route('weekly-reports.index') }}" class="rounded-lg px-3 py-2 text-sm hover:bg-slate-800">Weekly Report</a>
                @endif
                <a href="https://wa.me/6285603155491" target="_blank" class="rounded-lg px-3 py-2 text-sm hover:bg-slate-800">Sampaikan Masukan</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-rose-300 hover:bg-slate-800">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="px-4 py-6 sm:px-6">
        @if (session('success'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3000)"
                x-show="show"
                x-transition
                class="mx-auto mb-4 max-w-7xl rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 shadow-sm"
                role="alert"
            >
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3000)"
                x-show="show"
                x-transition
                class="mx-auto mb-4 max-w-7xl rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800 shadow-sm"
                role="alert"
            >
                {{ session('error') }}
            </div>
        @endif
        <div id="comingSoonModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-emerald-100 text-xl">🚀</div>
                <h2 class="text-xl font-bold text-slate-900">Segera Hadir</h2>
                <p class="mb-6 mt-2 text-sm leading-6 text-slate-500">Fitur ini sedang dalam pengembangan. Nantikan pembaruan berikutnya.</p>
                <div class="flex justify-end">
                    <button onclick="document.getElementById('comingSoonModal').classList.add('hidden');"
                            class="btn-primary">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        @isset($header)
            <div class="mx-auto mb-6 max-w-7xl">{{ $header }}</div>
        @endisset

        {{ $slot ?? '' }}
        @yield('content')
    </main>
</body>
</html>
