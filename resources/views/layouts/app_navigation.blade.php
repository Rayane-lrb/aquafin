<aside class="w-64 flex flex-col min-h-screen sticky top-0" style="background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);">

    <!-- Logo -->
    <div class="px-6 py-6 border-b border-blue-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-700" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6 8 4 12 4 15a8 8 0 0016 0c0-3-2-7-8-13z"/>
                </svg>
            </div>
            <div>
                <div class="font-bold text-white text-base">Aquafin</div>
                <div class="text-blue-200 text-xs">{{ Auth::user()->role ?? 'Gebruiker' }}</div>
            </div>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-4 py-6 space-y-1">

        <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest px-3 mb-3">Menu</p>

        <a href="{{ route('product.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
            {{ request()->routeIs('product.*') ? 'bg-white text-blue-700 shadow' : 'text-blue-100 hover:bg-blue-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
            </svg>
            Catalogus
        </a>

        <a href="{{ route('order.create') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
            {{ request()->routeIs('order.create') ? 'bg-white text-blue-700 shadow' : 'text-blue-100 hover:bg-blue-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11" />
            </svg>
            Bestellen
        </a>

        <a href="{{ route('order.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
            {{ request()->routeIs('order.index') ? 'bg-white text-blue-700 shadow' : 'text-blue-100 hover:bg-blue-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Mijn Orders
        </a>

        <a href="{{ route('suggestion.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
            {{ request()->routeIs('suggestion.*') ? 'bg-white text-blue-700 shadow' : 'text-blue-100 hover:bg-blue-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            Suggesties
        </a>

        <a href="{{ route('productcategory.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
            {{ request()->routeIs('productcategory.*') ? 'bg-white text-blue-700 shadow' : 'text-blue-100 hover:bg-blue-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5l6 6v11a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
            </svg>
            Categorieën
        </a>

        @if (Auth::user()->role === 'admin')
        <a href="{{ route('admin.users.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
            {{ request()->routeIs('admin.*') ? 'bg-white text-blue-700 shadow' : 'text-blue-100 hover:bg-blue-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Gebruikers
        </a>
        @endif

    </nav>

    <!-- Bottom -->
    <div class="px-4 py-4 border-t border-blue-700">
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-blue-100 hover:bg-blue-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Mijn profiel
        </a>
    </div>

</aside>
