<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Aquafin') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/aquafinlogo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Figtree', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased" style="background: #f0f4f8;">

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 -translate-x-full lg:static lg:inset-auto lg:z-auto lg:block lg:translate-x-0 transition-transform duration-200 lg:transition-none"
         :class="{ 'translate-x-0': sidebarOpen }">
        @include('layouts.app_navigation')
    </div>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Topbar -->
        <div class="bg-white border-b border-gray-200 px-4 sm:px-8 py-4 sticky top-0 z-10 shadow-sm">
            <div class="flex items-center justify-between">

                <div class="flex items-center gap-3">
                    <!-- Hamburger Menu -->
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="sidebarOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    @isset($header)
                        <h1 class="text-lg sm:text-xl font-bold text-gray-800">{{ $header }}</h1>
                    @endisset
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    <div class="flex items-center gap-2 bg-gray-100 px-2 sm:px-3 py-1.5 rounded-full">
                        <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(Auth::user()?->name, 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden sm:inline">{{ Auth::user()?->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-1.5 text-sm text-gray-400 hover:text-red-500 transition font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                            </svg>
                            <span class="hidden sm:inline">Afmelden</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>

        <!-- Content -->
        <main class="flex-1 p-4 sm:p-8">
            {{ $slot }}
        </main>

    </div>
</div>

</body>
</html>
