<!-- Sidebar -->
<aside class="w-64 bg-white border-r border-gray-200 flex flex-col min-h-screen">

    <!-- Logo -->
    <div class="px-6 py-5 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C6 8 4 12 4 15a8 8 0 0016 0c0-3-2-7-8-13z" />
                </svg>
            </div>
            <div>
                <div class="font-bold text-gray-900 text-sm">Aquafin</div>
                <div class="text-xs text-gray-400">{{ Auth::user()->role ?? 'Gebruiker' }}</div>
            </div>
        </div>
    </div>

    <!-- Nav links -->
    <nav class="flex-1 px-3 py-4 space-y-1">

        <a href="{{ route('product.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
            {{ request()->routeIs('product.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
            </svg>
            Catalogus
        </a>

        <a href="{{ route('order.create') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
            {{ request()->routeIs('order.create') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11" />
            </svg>
            Bestellen
        </a>

        <a href="{{ route('order.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
            {{ request()->routeIs('order.index') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Mijn Orders
        </a>

        <a href="{{ route('productcategory.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
            {{ request()->routeIs('productcategory.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5l6 6v11a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
            </svg>
            Catégories
        </a>

    </nav>

    <!-- Profile link -->
    <div class="px-3 py-4 border-t border-gray-100">
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Mon profil
        </a>
    </div>

</aside>
