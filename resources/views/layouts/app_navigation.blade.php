@php
    $user = Auth::user();
    $role = $user?->role;

    $theme = match ($role) {
        'admin'             => ['from' => '#0f172a', 'to' => '#1e293b', 'border' => '#334155', 'muted' => '#64748b', 'pill' => '#1e293b'],
        'magazijnBeheerder' => ['from' => '#052e16', 'to' => '#065f46', 'border' => '#047857', 'muted' => '#6ee7b7', 'pill' => '#065f46'],
        default             => ['from' => '#0c1a3d', 'to' => '#1e3a8a', 'border' => '#1e40af', 'muted' => '#93c5fd', 'pill' => '#1e40af'],
    };

    $roleLabel = match ($role) {
        'admin'             => 'Beheerder',
        'magazijnBeheerder' => 'Magazijn',
        'technieker'        => 'Technieker',
        default             => 'Gebruiker',
    };

    $cartCount = array_sum(session('cart', []));

    $nav = [
        ['route' => 'product.*',        'href' => route('product.index'),        'label' => 'Catalogus',   'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10'],
        ...($role !== 'magazijnBeheerder' ? [['route' => 'cart.*', 'href' => route('cart.index'), 'label' => 'Mandje', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z', 'badge' => $cartCount]] : []),
        ['route' => 'order.index',      'href' => route('order.index'),          'label' => $role === 'magazijnBeheerder' ? 'Orders' : 'Mijn Orders', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],

        ['route' => 'neerslag.*',       'href' => route('neerslag.index'),       'label' => 'Neerslag',    'icon' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z'],
        ['route' => 'productcategory.*','href' => route('productcategory.index'),'label' => 'Categorieën', 'icon' => 'M7 7h.01M7 3h5l6 6v11a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z'],
        ...($role === 'admin' ? [['route' => 'warehouse.*', 'href' => route('warehouse.index'), 'label' => 'Werfplaatsen', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4']] : []),
        ...($role === 'magazijnBeheerder' ? [['route' => 'warehouse.*', 'href' => route('warehouse.index'), 'label' => 'Werfplaatsen', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4']] : []),
    ];

    if ($role === 'admin') {
        $nav[] = ['route' => 'admin.*', 'href' => route('admin.users.index'), 'label' => 'Gebruikers', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'];
    }
@endphp

<aside class="w-64 flex flex-col h-screen sticky top-0 select-none"
       style="background: linear-gradient(180deg, {{ $theme['from'] }} 0%, {{ $theme['to'] }} 100%);">

    {{-- Logo & titel --}}
    <div class="px-5 py-5 flex items-center gap-3" style="border-bottom: 1px solid {{ $theme['border'] }}20;">
        <img src="{{ asset('images/aquafinlogo.png') }}" alt="Aquafin" class="h-8 w-auto flex-shrink-0">
        <p class="text-xs truncate" style="color: {{ $theme['muted'] }}">{{ $roleLabel }}</p>
    </div>

    {{-- Navigatie --}}
    <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-0.5">
        <p class="text-xs font-semibold uppercase tracking-widest px-3 py-2 mb-1" style="color: {{ $theme['muted'] }}4d; letter-spacing: .12em;">Menu</p>

        @foreach ($nav as $item)
            @php $active = request()->routeIs($item['route']); @endphp
            <a href="{{ $item['href'] }}"
               class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ $active ? 'bg-white shadow-sm' : 'hover:bg-white/10' }}">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4.5 w-4.5 flex-shrink-0 transition-colors
                            {{ $active ? 'text-gray-700' : 'text-white/60 group-hover:text-white' }}"
                     style="width:18px;height:18px;"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                </svg>

                <span class="{{ $active ? 'text-gray-800' : 'text-white/70 group-hover:text-white' }} transition-colors">
                    {{ $item['label'] }}
                </span>

                @if (!empty($item['badge']) && $item['badge'] > 0)
                    <span class="ml-auto bg-blue-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center leading-none">
                        {{ $item['badge'] }}
                    </span>
                @elseif ($active)
                    <span class="ml-auto w-1.5 h-1.5 rounded-full" style="background: {{ $theme['to'] }}"></span>
                @endif
            </a>
        @endforeach
    </nav>

    {{-- Gebruiker onderaan --}}
    <div class="px-3 py-3 space-y-1" style="border-top: 1px solid {{ $theme['border'] }}30;">
        <a href="{{ route('profile.edit') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 hover:bg-white/10">
            <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">
                {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-white/90 truncate leading-tight">{{ $user?->name }}</p>
                <p class="text-xs truncate" style="color: {{ $theme['muted'] }}">Mijn profiel</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/30 group-hover:text-white/60 transition-colors flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="group w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm transition-all duration-150 hover:bg-red-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/30 group-hover:text-red-400 transition-colors flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="text-white/40 group-hover:text-red-400 transition-colors">Uitloggen</span>
            </button>
        </form>
    </div>

</aside>
