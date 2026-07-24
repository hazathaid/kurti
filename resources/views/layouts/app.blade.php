<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurti SAI</title>
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
    <nav class="sticky top-0 z-40 border-b border-slate-700 bg-slate-900 text-white shadow-sm">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-6">
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <img src="{{ asset('logo.png') }}" alt="Kurti SAI" class="h-9 w-9 rounded-md bg-white object-contain">
            <span class="mr-1 text-lg font-bold tracking-wide">Kurti SAI</span>
            <a href="#" target="_blank"
               onclick="event.preventDefault(); document.getElementById('comingSoonModal').classList.remove('hidden');"
               class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium transition hover:bg-blue-500">
                Bukom
            </a>
            @if(in_array(Auth::user()->type, ['fasil', 'orangtua']))
                <a href="{{ route('weekly-reports.index') }}"
                   class="{{ request()->routeIs('weekly-reports.*') ? 'bg-emerald-500 ring-2 ring-emerald-300' : 'bg-slate-700 hover:bg-slate-600' }} rounded-lg px-3 py-2 text-sm font-medium transition">
                    Weekly Report
                </a>
            @endif
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
            <span class="hidden text-sm text-slate-200 sm:inline">{{ Auth::user()->name }}</span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="rounded-lg bg-red-500 px-3 py-2 text-sm font-medium transition hover:bg-red-600">
                    Logout
                </button>
            </form>
            <a href="https://wa.me/6285603155491" target="_blank"
               class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium transition hover:bg-blue-500">
                <span class="hidden sm:inline">Sampaikan </span>Masukan
            </a>
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
                class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 shadow"
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
                class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 shadow"
                role="alert"
            >
                {{ session('error') }}
            </div>
        @endif
        <div id="comingSoonModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Coming Soon 🚀</h2>
                <p class="text-gray-600 mb-6">Fitur ini sedang dalam pengembangan, nantikan ya!</p>
                <div class="flex justify-end">
                    <button onclick="document.getElementById('comingSoonModal').classList.add('hidden');"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        @yield('content')
    </main>
</body>
</html>
