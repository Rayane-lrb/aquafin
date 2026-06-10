<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/aquafinlogo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script>
            tailwind.config = { darkMode: 'class' };
        </script>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
        <script>
            (function() {
                const stored = localStorage.getItem('darkMode');
                const pref = stored !== null ? stored === 'true' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (pref) document.documentElement.classList.add('dark');
            })();
        </script>
        <style>
            :root { --dm-surface: #ffffff; --dm-text: #111827; }
            html.dark { --dm-surface: #1e293b; --dm-text: #f1f5f9; }
            html.dark .bg-white { background-color: var(--dm-surface) !important; }
            html.dark .text-gray-600, html.dark .text-gray-700, html.dark .text-gray-800, html.dark .text-gray-900 { color: var(--dm-text) !important; }
            html.dark input { background-color: #0f172a !important; border-color: #334155 !important; color: #f1f5f9 !important; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-[#0ea5e9] via-[#0369a1] to-[#0c4a6e]">
            <div>
                <a href="/">
                    <x-breeze.application-logo class="w-20 h-20 fill-current text-gray-500 bg-white rounded-full" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-4 sm:px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
