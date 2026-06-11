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
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (localStorage.getItem('darkMode') === null) {
                    document.documentElement.classList.toggle('dark', e.matches);
                }
            });
        })();
    </script>
    <style>
        body { font-family: 'Figtree', sans-serif; }
        [x-cloak] { display: none !important; }
        :root { --dm-bg: #f0f4f8; --dm-surface: #ffffff; --dm-border: #e5e7eb; --dm-text: #111827; --dm-muted: #6b7280; }
        html.dark { --dm-bg: #0f172a; --dm-surface: #1e293b; --dm-border: #334155; --dm-text: #f1f5f9; --dm-muted: #94a3b8; }
        html.dark body { background: var(--dm-bg) !important; }
        html.dark .bg-white { background-color: var(--dm-surface) !important; }
        html.dark .bg-gray-50 { background-color: #1e293b !important; }
        html.dark .bg-gray-100 { background-color: #1e293b !important; }
        html.dark .border-gray-100, html.dark .border-gray-200, html.dark .border-gray-300 { border-color: var(--dm-border) !important; }
        html.dark .text-gray-400, html.dark .text-gray-500 { color: var(--dm-muted) !important; }
        html.dark .text-gray-600, html.dark .text-gray-700, html.dark .text-gray-800, html.dark .text-gray-900 { color: var(--dm-text) !important; }
        html.dark .shadow-sm { box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.3) !important; }
        html.dark input, html.dark select, html.dark textarea { background-color: #0f172a !important; border-color: #334155 !important; color: #f1f5f9 !important; }
        html.dark .divide-gray-100 > * + * { border-color: #334155 !important; }
        html.dark table thead { background-color: #1e293b !important; }
        html.dark table tbody tr:hover { background-color: #1e293b !important; }
        html.dark .bg-blue-50 { background-color: #1e3a5f !important; }
        html.dark .bg-green-50, html.dark .bg-green-100, html.dark .bg-emerald-100 { background-color: #065f46 !important; }
        html.dark .bg-red-50 { background-color: #450a0a !important; }
        html.dark .bg-yellow-100 { background-color: #422006 !important; }
        html.dark .bg-blue-100 { background-color: #1e3a5f !important; }
        html.dark .bg-red-100 { background-color: #450a0a !important; }
        html.dark .border-green-200, html.dark .border-red-200, html.dark .border-blue-200 { border-color: #334155 !important; }
        html.dark .text-red-500, html.dark .text-red-600 { color: #fca5a5 !important; }
        html.dark .text-green-600, html.dark .text-green-700, html.dark .text-emerald-700 { color: #6ee7b7 !important; }
        html.dark .text-blue-600, html.dark .text-blue-700 { color: #93c5fd !important; }
        html.dark .text-yellow-700 { color: #fde68a !important; }
        html.dark .text-orange-400 { color: #fb923c !important; }
        html.dark .text-sky-500 { color: #38bdf8 !important; }
        html.dark .bg-orange-50 { background-color: #431407 !important; }
        html.dark .bg-sky-50 { background-color: #0c4a6e !important; }
        html.dark .hover\:bg-green-100:hover { background-color: #064e3b !important; }
        html.dark .hover\:bg-blue-50:hover { background-color: #1e3a5f !important; }
        html.dark .hover\:bg-blue-100:hover { background-color: #2563eb !important; }
        html.dark .hover\:bg-red-50:hover { background-color: #450a0a !important; }
        html.dark .hover\:bg-red-100:hover { background-color: #dc2626 !important; }
        html.dark .bg-blue-600 { background-color: #2563eb !important; }
        html.dark .bg-emerald-600 { background-color: #059669 !important; }
        html.dark .bg-green-600 { background-color: #16a34a !important; }
        html.dark .hover\:bg-blue-700:hover { background-color: #1d4ed8 !important; }
        html.dark .hover\:bg-emerald-700:hover { background-color: #047857 !important; }
        html.dark .hover\:bg-gray-100:hover { background-color: #334155 !important; }
        html.dark .hover\:bg-gray-200:hover { background-color: #475569 !important; }
        html.dark .ring-white\/20 { --tw-ring-color: rgba(255,255,255,0.1) !important; }
        html.dark .text-white\/60, html.dark .text-white\/70 { color: rgba(255,255,255,0.7) !important; }
        html.dark .text-white\/30 { color: rgba(255,255,255,0.4) !important; }
        html.dark .text-white\/90 { color: rgba(255,255,255,0.9) !important; }
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
                    <button x-data x-init="$el.querySelector('svg path').setAttribute('d', document.documentElement.classList.contains('dark') ? 'M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z' : 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z')"
                            @click="const dark = document.documentElement.classList.toggle('dark'); localStorage.setItem('darkMode', dark); $el.querySelector('svg path').setAttribute('d', dark ? 'M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z' : 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z')"
                            class="p-1.5 text-gray-400 hover:text-yellow-500 hover:bg-gray-100 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                        </svg>
                    </button>
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

<script>
/* ===================================================
   Tri alphabétique global sur toutes les tables
   Clic sur un <th> → tri asc, 2e clic → tri desc
   =================================================== */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('table').forEach(table => {
        const headers = table.querySelectorAll('thead th');
        headers.forEach((th, colIndex) => {
            th.style.cursor = 'pointer';
            th.style.userSelect = 'none';
            th._sortDir = 0; // 0=neutre 1=asc -1=desc

            th.addEventListener('click', () => {
                // Reset les autres colonnes
                headers.forEach((h, i) => {
                    if (i !== colIndex) {
                        h._sortDir = 0;
                        const a = h.querySelector('.sort-arrow');
                        if (a) a.textContent = '';
                    }
                });

                th._sortDir = th._sortDir === 1 ? -1 : 1;

                // Flèche indicatrice
                let arrow = th.querySelector('.sort-arrow');
                if (!arrow) {
                    arrow = document.createElement('span');
                    arrow.className = 'sort-arrow ml-1 text-blue-500';
                    th.appendChild(arrow);
                }
                arrow.textContent = th._sortDir === 1 ? ' ↑' : ' ↓';

                // Trier chaque tbody de la table
                table.querySelectorAll('tbody').forEach(tbody => {
                    const rows = [...tbody.querySelectorAll('tr')];
                    rows.sort((a, b) => {
                        const aCell = a.querySelectorAll('td')[colIndex];
                        const bCell = b.querySelectorAll('td')[colIndex];
                        if (!aCell || !bCell) return 0;
                        const aText = aCell.innerText.trim().toLowerCase();
                        const bText = bCell.innerText.trim().toLowerCase();
                        return th._sortDir * aText.localeCompare(bText, 'nl', { numeric: true });
                    });
                    rows.forEach(r => tbody.appendChild(r));
                });
            });
        });
    });
});
</script>
</body>
</html>
