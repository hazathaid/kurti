<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurti SAI</title>
    <script src="//unpkg.com/alpinejs" defer></script>
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
