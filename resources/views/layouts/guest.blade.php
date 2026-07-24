<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Kurti SAI</title>
        <link rel="icon" type="image/png" href="/icon/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/icon/favicon.svg" />
        <link rel="shortcut icon" href="/icon/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/icon/apple-touch-icon.png" />
        <meta name="apple-mobile-web-app-title" content="Kurti SAIS" />
        <link rel="manifest" href="/icon/site.webmanifest" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-950 px-4 py-10">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.18),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(59,130,246,0.12),_transparent_35%)]"></div>
            <div class="relative w-full sm:max-w-md">
                <a href="/" class="mb-7 flex items-center justify-center gap-3 text-white">
                    <img src="{{ asset('logo.png') }}" alt="Kurti SAI" class="h-14 w-14 rounded-xl bg-white object-contain shadow-lg">
                    <div><div class="text-xl font-bold">Kurti SAI</div><div class="text-xs text-slate-400">Sekolah Alam Indonesia</div></div>
                </a>
                <div class="overflow-hidden rounded-2xl border border-white/10 bg-white p-6 shadow-2xl sm:p-8">
                {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
