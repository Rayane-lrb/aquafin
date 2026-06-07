@php
    $role = Auth::user()?->role;
    $colors = match ($role) {
        'admin' => (object) ['bg' => '#1e293b', 'bg2' => '#334155', 'border' => '#334155', 'text' => '#94a3b8', 'hover' => '#475569', 'active' => 'white'],
        'magazijnBeheerder' => (object) ['bg' => '#065f46', 'bg2' => '#047857', 'border' => '#047857', 'text' => '#6ee7b7', 'hover' => '#059669', 'active' => 'white'],
        default => (object) ['bg' => '#1e3a8a', 'bg2' => '#1e40af', 'border' => '#1e40af', 'text' => '#93c5fd', 'hover' => '#2563eb', 'active' => 'white'],
    };
@endphp

<aside class="w-64 flex flex-col h-screen sticky top-0" style="background: linear-gradient(180deg, {{ $colors->bg }} 0%, {{ $colors->bg2 }} 100%);">

    <div class="px-6 py-6" style="border-bottom: 1px solid {{ $colors->border }};">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color: {{ $colors->bg }}" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6 8 4 12 4 15a8 8 0 0016 0c0-3-2-7-8-13z"/>
                </svg>
            </div>
            <div>
                <div class="font-bold text-white text-base">Aquafin</div>
                <div class="text-xs" style="color: {{ $colors->text }}">{{ $role ?? 'Gebruiker' }}</div>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <p class="text-xs font-semibold uppercase tracking-widest px-3 mb-3" style="color: {{ $colors->text }}">Menu</p>

        @php
            $linkClasses = 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition';
            $activeBg = 'bg-white';
            $inactiveStyle = "color: {$colors->text}";
            $hoverStyle = "style=\"color: {$colors->text}\" onmouseover=\"this.style.backgroundColor='{$colors->hover}'\" onmouseout=\"this.style.backgroundColor='transparent'\"";
        @endphp

        <a href="{{ route('product.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('product.*') ? 'bg-white text-gray-800 shadow' : '' }}" style="{{ request()->routeIs('product.*') ? '' : "color: {$colors->text}" }}" {{ request()->routeIs('product.*') ? '' : "onmouseover=\"this.style.backgroundColor='{$colors->hover}'\" onmouseout=\"this.style.backgroundColor='transparent'\"" }}>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
            Catalogus
        </a>

        <a href="{{ route('order.create') }}" class="{{ $linkClasses }} {{ request()->routeIs('order.create') ? 'bg-white text-gray-800 shadow' : '' }}" style="{{ request()->routeIs('order.create') ? '' : "color: {$colors->text}" }}" {{ request()->routeIs('order.create') ? '' : "onmouseover=\"this.style.backgroundColor='{$colors->hover}'\" onmouseout=\"this.style.backgroundColor='transparent'\"" }}>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11"/></svg>
            Bestellen
        </a>

        <a href="{{ route('order.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('order.index') ? 'bg-white text-gray-800 shadow' : '' }}" style="{{ request()->routeIs('order.index') ? '' : "color: {$colors->text}" }}" {{ request()->routeIs('order.index') ? '' : "onmouseover=\"this.style.backgroundColor='{$colors->hover}'\" onmouseout=\"this.style.backgroundColor='transparent'\"" }}>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Mijn Orders
        </a>

        <a href="{{ route('suggestion.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('suggestion.*') ? 'bg-white text-gray-800 shadow' : '' }}" style="{{ request()->routeIs('suggestion.*') ? '' : "color: {$colors->text}" }}" {{ request()->routeIs('suggestion.*') ? '' : "onmouseover=\"this.style.backgroundColor='{$colors->hover}'\" onmouseout=\"this.style.backgroundColor='transparent'\"" }}>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            Suggesties
        </a>

        <a href="{{ route('productcategory.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('productcategory.*') ? 'bg-white text-gray-800 shadow' : '' }}" style="{{ request()->routeIs('productcategory.*') ? '' : "color: {$colors->text}" }}" {{ request()->routeIs('productcategory.*') ? '' : "onmouseover=\"this.style.backgroundColor='{$colors->hover}'\" onmouseout=\"this.style.backgroundColor='transparent'\"" }}>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5l6 6v11a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
            Categorieën
        </a>

        @if ($role === 'admin')
        <a href="{{ route('admin.users.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('admin.*') ? 'bg-white text-gray-800 shadow' : '' }}" style="{{ request()->routeIs('admin.*') ? '' : "color: {$colors->text}" }}" {{ request()->routeIs('admin.*') ? '' : "onmouseover=\"this.style.backgroundColor='{$colors->hover}'\" onmouseout=\"this.style.backgroundColor='transparent'\"" }}>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Gebruikers
        </a>
        @endif
    </nav>

    <div class="px-4 py-4" style="border-top: 1px solid {{ $colors->border }};">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition" style="color: {{ $colors->text }}" onmouseover="this.style.backgroundColor='{{ $colors->hover }}'" onmouseout="this.style.backgroundColor='transparent'">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Mijn profiel
        </a>
    </div>

</aside>
