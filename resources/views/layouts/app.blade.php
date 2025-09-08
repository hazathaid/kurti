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
<body class="antialiased bg-gray-100">
    <nav class="bg-gray-800 text-white p-4 flex justify-between">
        <div class="text-lg font-semibold">
            {{ config('app.name', 'Laravel') }}
        </div>

        <div class="flex items-center gap-4">
            <span>{{ Auth::user()->name }}</span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded-lg text-sm">
                    Logout
                </button>
            </form>
            <a href="{{ route('masukan') }}" href="https://wa.me/6285603155491" target="_blank"
               class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded-lg text-sm">
                Sampaikan Masukan
            </a>
        </div>
    </nav>

    <main class="p-6">
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

        @yield('content')
    </main>
</body>
</html>
