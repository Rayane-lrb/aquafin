<x-app-layout>
    <x-slot name="header">Producten</x-slot>

    <x-slot name="topbar">
        <form method="GET" action="{{ route('product.index') }}" class="flex items-center gap-2">
            <div class="relative flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $query }}"
                    placeholder="Zoek een product..."
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition">
            </div>
            @if ($query || $selectedCategory)
                <a href="{{ route('product.index') }}" class="text-xs text-gray-400 hover:text-gray-600 px-2 py-1.5 rounded-lg border border-gray-200 transition whitespace-nowrap">
                    ✕
                </a>
            @endif
        </form>
    </x-slot>

    {{-- ══════════════════════════════════════════════════════════
         NOTIFICATIES — zwevende kaart rechtsonder
         ══════════════════════════════════════════════════════════ --}}

    @php $role = auth()->user()?->role; @endphp

    {{-- ── TECHNIEKER: statusupdates van bestellingen ── --}}
    @if($role === 'technieker')
    @php $unread = auth()->user()->unreadNotifications->take(5); @endphp
    @if($unread->isNotEmpty())
    <div id="notif-card" class="fixed bottom-6 right-6 z-50 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 bg-blue-600">
            <div class="flex items-center gap-2 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="text-sm font-semibold">Meldingen</span>
                <span class="bg-white/30 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $unread->count() }}</span>
            </div>
            <button onclick="sluitAlleNotifs()" class="text-white/70 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        {{-- Notificaties --}}
        <div class="divide-y divide-gray-50 max-h-72 overflow-y-auto" id="notif-list">
            @foreach($unread as $notif)
            @php $d = $notif->data; $status = $d['status'] ?? ''; @endphp
            <div class="notif-item flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition" data-id="{{ $notif->id }}">
                <div class="mt-0.5 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                    {{ $status === 'goedgekeurd' ? 'bg-green-100' : ($status === 'geleverd' ? 'bg-blue-100' : 'bg-red-100') }}">
                    <span class="text-base">{{ $d['icon'] ?? '📦' }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-800 leading-snug">{{ $d['message'] ?? '' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
                <button onclick="sluitNotif(this)" class="text-gray-300 hover:text-gray-500 transition shrink-0 mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endforeach
        </div>
        {{-- Footer --}}
        <div class="px-4 py-2 border-t border-gray-100 bg-gray-50">
            <a href="{{ route('order.index') }}" class="text-xs text-blue-600 hover:underline font-medium">Alle bestellingen bekijken →</a>
        </div>
    </div>
    @endif
    @endif

    {{-- ── MAGAZIJNBEHEERDER: openstaande + urgente bestellingen ── --}}
    @if($role === 'magazijnBeheerder' && ($pendingCount > 0 || $urgentCount > 0))
    @php $unreadNotifs = auth()->user()->unreadNotifications->take(3); @endphp
    <div id="notif-card-mag" style="display:none"
         data-pending="{{ $pendingCount }}" data-urgent="{{ $urgentCount }}"
         class="fixed bottom-6 right-6 z-50 w-80 bg-white rounded-2xl shadow-xl border {{ $urgentCount > 0 ? 'border-red-200' : 'border-gray-100' }} overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 {{ $urgentCount > 0 ? 'bg-red-600' : 'bg-emerald-700' }}">
            <div class="flex items-center gap-2 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="text-sm font-semibold">Bestellingen</span>
            </div>
            <button onclick="sluitMagNotif()" class="text-white/70 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        {{-- Inhoud --}}
        <div class="divide-y divide-gray-50">
            {{-- Pending --}}
            <a href="{{ route('order.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-semibold text-gray-800">In behandeling</p>
                    <p class="text-xs text-gray-400">{{ $pendingCount }} bestelling{{ $pendingCount !== 1 ? 'en' : '' }} wacht{{ $pendingCount === 1 ? '' : 'en' }} op goedkeuring</p>
                </div>
                <span class="text-sm font-bold text-yellow-600">{{ $pendingCount }}</span>
            </a>
            {{-- Urgent --}}
            @if($urgentCount > 0)
            <a href="{{ route('order.index') }}" class="flex items-center gap-3 px-4 py-3 bg-red-50 hover:bg-red-100 transition">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-40"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600 relative" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-semibold text-red-700">🚨 DRINGEND</p>
                    <p class="text-xs text-red-500">{{ $urgentCount }} urgente bestelling{{ $urgentCount !== 1 ? 'en' : '' }}</p>
                </div>
                <span class="text-sm font-bold text-red-600">{{ $urgentCount }}</span>
            </a>
            @endif
            {{-- Nieuwe notifs (nieuwe bestellingen) --}}
            @foreach($unreadNotifs as $notif)
            @php $d = $notif->data; @endphp
            <div class="flex items-start gap-3 px-4 py-3">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-base">
                    {{ $d['icon'] ?? '📦' }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-800 leading-snug">{{ $d['message'] ?? '' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Footer --}}
        <div class="px-4 py-2 border-t border-gray-100 bg-gray-50">
            <a href="{{ route('order.index') }}" class="text-xs text-blue-600 hover:underline font-medium">Alle bestellingen beheren →</a>
        </div>
    </div>
    @endif

    {{-- ══ SUGGESTIES ══════════════════════════════════════════════ --}}
    @if($showCategories && $suggestedProducts->isNotEmpty())
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-gray-700">{{ $suggestLabel }}</h2>
            <span class="text-xs text-gray-400">{{ $suggestSub }}</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($suggestedProducts as $sp)
            <button type="button" onclick="openModal({{ $sp->id }})"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition p-3 flex flex-col items-center gap-2 text-left group">
                @if($sp->image)
                    <img src="{{ asset('storage/' . $sp->image) }}" class="w-14 h-14 object-cover rounded-xl" alt="{{ $sp->name }}">
                @else
                    <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                    </div>
                @endif
                <p class="text-xs font-semibold text-gray-700 text-center leading-tight group-hover:text-blue-700 transition line-clamp-2">{{ $sp->name }}</p>
                <span class="text-xs text-gray-400">Stock: {{ $sp->stock }}</span>
            </button>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ══ FAVORIETEN — alleen op de startpagina ══════════════════ --}}
    @if ($showCategories && $favoriteProducts->isNotEmpty())
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-sm font-semibold text-gray-700">❤️ Mijn favorieten</span>
            <span class="text-xs text-gray-400">({{ $favoriteProducts->count() }})</span>
        </div>

        {{-- Glissende kaarten (zelfde stijl als categoriekaarten) --}}
        <div class="flex gap-4 overflow-x-auto pb-3 snap-x snap-mandatory" style="scrollbar-width: none;">
            @foreach ($favoriteProducts as $fav)
            <div class="snap-start shrink-0 w-40 group relative">
                <a href="{{ route('product.show', $fav->id) }}"
                   class="block bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">

                    {{-- Afbeelding --}}
                    <div class="h-32 bg-gray-50 overflow-hidden flex items-center justify-center">
                        @if ($fav->image)
                            <img src="{{ asset('storage/' . $fav->image) }}"
                                 alt="{{ $fav->name }}"
                                 class="w-full h-full object-contain p-3 group-hover:scale-105 transition-transform duration-300">
                        @else
                            <svg class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-800 text-xs leading-tight line-clamp-2">{{ $fav->name }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Stock: {{ $fav->stock }}</p>
                    </div>
                </a>

                {{-- ❤️ knop om te verwijderen --}}
                <button type="button"
                        onclick="toggleFavorite({{ $fav->id }}, this)"
                        data-favorite="1"
                        class="fav-btn absolute top-2 right-2 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-sm text-pink-500 hover:text-pink-700 hover:bg-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-pink-500" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    <hr class="border-gray-100 mb-6">
    @endif

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('product.index') }}" class="mb-6 space-y-3">
        <div class="flex justify-end">
            @if (Auth::user()?->role === 'admin' || Auth::user()?->role === 'magazijnBeheerder')
                <a href="{{ route('product.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition whitespace-nowrap">
                    + Product toevoegen
                </a>
            @endif
        </div>

        @if (! $showCategories)
        {{-- Kleine categoriekaarten — horizontaal scrollbaar --}}
        <div class="flex gap-2 overflow-x-auto pb-1 snap-x" style="scrollbar-width: none;">
            <a href="{{ route('product.index', array_filter(['search' => $query])) }}"
               class="snap-start shrink-0 flex flex-col items-center gap-1 px-3 py-2 rounded-xl border transition text-center
                      {{ ! $selectedCategory ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-gray-200 text-gray-600 hover:border-blue-300 hover:shadow-sm' }}">
                <span class="text-lg">🗂️</span>
                <span class="text-xs font-medium whitespace-nowrap">Alle</span>
            </a>
            @foreach ($categories as $cat)
            @php $isActive = (string) $selectedCategory === (string) $cat->id; @endphp
            <a href="{{ route('product.index', array_filter(['search' => $query, 'category' => $cat->id])) }}"
               class="snap-start shrink-0 flex flex-col items-center gap-1 rounded-xl border transition overflow-hidden text-center
                      {{ $isActive ? 'border-blue-500 ring-2 ring-blue-300' : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm' }}"
               style="width: 72px;">
                {{-- Miniatuur afbeelding --}}
                <div class="w-full h-12 bg-gray-50 flex items-center justify-center overflow-hidden">
                    @if ($cat->preview_image)
                        <img src="{{ asset('storage/' . $cat->preview_image) }}"
                             alt="{{ $cat->name }}"
                             class="w-full h-full object-contain p-1">
                    @else
                        <svg class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                    @endif
                </div>
                <span class="text-xs font-medium px-1 pb-1.5 leading-tight line-clamp-2
                             {{ $isActive ? 'text-blue-700' : 'text-gray-600' }}">
                    {{ $cat->name }}
                </span>
            </a>
            @endforeach
        </div>
        @endif
    </form>

    @if ($showCategories)
    {{-- Categorie-kaarten --}}
    <p class="text-xs text-gray-400 mb-3">{{ $categories->count() }} categorieën</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($categories as $cat)
        <a href="{{ route('product.index', ['category' => $cat->id]) }}"
           class="group bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="h-36 bg-gray-50 overflow-hidden flex items-center justify-center">
                @if ($cat->preview_image)
                    <img src="{{ asset('storage/' . $cat->preview_image) }}"
                         alt="{{ $cat->name }}"
                         class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-300">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                @endif
            </div>
            <div class="p-3">
                <h3 class="font-semibold text-gray-800 text-sm truncate">{{ $cat->name }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $cat->product_count }} product{{ $cat->product_count !== 1 ? 'en' : '' }}</p>
            </div>
        </a>
        @endforeach
    </div>

    @elseif ($products->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
            Geen producten gevonden.
        </div>
    @else
    <p class="text-xs text-gray-400 mb-3">{{ $products->count() }} product{{ $products->count() !== 1 ? 'en' : '' }} beschikbaar</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach ($products as $product)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-row sm:flex-col {{ !$product->is_active ? 'border-2 border-red-400' : '' }} {{ $product->stock == 0 ? 'border-2 border-red-400' : '' }}">

            <div class="flex-shrink-0 {{ !$product->is_active ? 'opacity-50' : '' }} {{ $product->stock == 0 ? 'opacity-50' : '' }}">
            @if ($product->image)
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                    class="w-20 h-20 sm:w-full sm:h-40 object-contain object-center p-2 sm:p-3 bg-white">
            @else
                <div class="w-20 h-20 sm:w-full sm:h-40 bg-gray-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 sm:h-12 sm:w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
            </div>

            <div class="p-3 sm:p-4 flex-1 flex flex-col min-w-0">
                <div class="flex items-start justify-between gap-1">
                    <div class="min-w-0">
                        <h3 class="font-semibold text-gray-900 text-xs sm:text-sm truncate {{ !$product->is_active ? 'opacity-50' : '' }}">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5 truncate {{ !$product->is_active ? 'opacity-50' : '' }}">{{ optional($product->category)->name ?? '—' }}</p>
                    </div>
                    <button type="button"
                            onclick="toggleFavorite({{ $product->id }}, this)"
                            class="fav-btn shrink-0 mt-0.5 transition"
                            data-favorite="{{ isset($favoriteIds[$product->id]) ? '1' : '0' }}"
                            title="{{ isset($favoriteIds[$product->id]) ? 'Verwijder uit favorieten' : 'Voeg toe aan favorieten' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ isset($favoriteIds[$product->id]) ? 'text-pink-500 fill-pink-500' : 'text-gray-300 fill-none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                </div>

                <div class="mt-2 sm:mt-3 flex items-center justify-between {{ !$product->is_active ? 'opacity-50' : '' }}">
                    <span class="text-xs {{ $product->stock <= 5 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        Stock: {{ $product->stock }}
                    </span>
                    @if ($product->is_active)
                        <span class="bg-green-100 text-green-700 text-xs font-medium px-1.5 sm:px-2 py-0.5 rounded-full">Actief</span>
                    @else
                        <span class="bg-gray-100 text-gray-500 text-xs font-medium px-1.5 sm:px-2 py-0.5 rounded-full">Inactief</span>
                    @endif
                </div>

                @if ($product->barcode)
                <div class="mt-2 flex items-center gap-1 {{ !$product->is_active ? 'opacity-50' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h1v12H4V6zm3 0h1v12H7V6zm3 0h2v12h-2V6zm4 0h1v12h-1V6zm3 0h2v12h-2V6z"/>
                    </svg>
                    <span class="text-xs font-mono font-semibold text-gray-600 tracking-widest truncate">{{ $product->barcode }}</span>
                </div>
                @endif

                {{-- Bestellen (technieker) --}}
                @if ((Auth::user()?->role === 'technieker' || Auth::user()?->role === 'admin') && $product->is_active)
                <div class="mt-2 sm:mt-4 pt-2 sm:pt-3 border-t border-gray-100 space-y-2">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <button type="button" onclick="updateCart({{ $product->id }}, -1)"
                            class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 font-bold transition text-xs sm:text-sm">
                            −
                        </button>
                        <input type="number" id="qty-{{ $product->id }}"
                            value="{{ $cartQty[$product->id] ?? 0 }}"
                            min="0"
                            onchange="setCart({{ $product->id }}, this.value)"
                            class="w-12 text-center text-sm font-semibold text-gray-800 border border-gray-200 rounded-lg px-1 py-0.5 focus:outline-none focus:ring-2 focus:ring-blue-300 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <button type="button" onclick="updateCart({{ $product->id }}, 1)"
                            class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 font-bold transition text-xs sm:text-sm">
                            +
                        </button>
                        <button type="button" onclick="goToCart({{ $product->id }})"
                            class="ml-auto text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg transition flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-3.5 sm:w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/>
                            </svg>
                            Mandje
                        </button>
                    </div>
                </div>
                @endif

                {{-- Bewerken/Verwijderen/Toggle (admin + magazijnBeheerder) --}}
                @if (Auth::user()?->role === 'admin' || Auth::user()?->role === 'magazijnBeheerder')
                <div class="mt-2 flex gap-1.5 sm:gap-2">
                    <a href="{{ route('product.edit', $product->id) }}"
                        class="flex-1 text-center text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 py-1 sm:py-1.5 rounded-lg transition">
                        Bewerken
                    </a>
                    <form action="{{ route('product.toggle', $product->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="w-full text-xs font-medium py-1 sm:py-1.5 rounded-lg transition
                                {{ $product->is_active
                                    ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100'
                                    : 'bg-green-200 text-green-800 hover:bg-green-300' }}">
                            {{ $product->is_active ? 'Verbergen' : 'Tonen' }}
                        </button>
                    </form>
                    <button type="button"
                        onclick="deleteProduct({{ $product->id }}, this)"
                        class="flex-1 text-xs font-medium bg-red-50 text-red-500 hover:bg-red-100 py-1 sm:py-1.5 rounded-lg transition">
                        Verwijderen
                    </button>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

{{-- Modal aanbevolen producten --}}
<div id="recommended-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 relative">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <h2 class="text-lg font-bold text-gray-800 mb-4">Aanbevolen Producten</h2>
        <div id="recommended-list" class="grid grid-cols-3 gap-3 mb-6"></div>
        <div class="flex gap-3">
            <button onclick="closeModal()"
                class="flex-1 border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium py-2.5 rounded-lg transition">
                Nee, bedankt
            </button>
            <a href="{{ route('cart.index') }}"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2.5 rounded-lg transition text-center">
                Verder naar winkelmandje
            </a>
        </div>
    </div>
</div>

<script>
// ── Product verwijderen (AJAX) ────────────────────────────────────
function deleteProduct(id, btn) {
    if (!confirm('Dit product verwijderen?')) return;
    fetch('/product/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: '_method=DELETE'
    })
    .then(res => {
        if (res.ok || res.redirected) {
            btn.closest('.bg-white').remove();
        }
    });
}

// ── Toast notification ────────────────────────────────────────────
function showToast(message) {
    let toast = document.getElementById('cart-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'cart-toast';
        toast.className = 'fixed top-5 right-5 z-50 bg-green-600 text-white text-sm font-medium px-4 py-3 rounded-xl shadow-lg flex items-center gap-2 transition-all duration-300 opacity-0 translate-y-[-10px]';
        toast.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg><span id="cart-toast-msg"></span>`;
        document.body.appendChild(toast);
    }
    document.getElementById('cart-toast-msg').textContent = message;
    toast.classList.remove('opacity-0', 'translate-y-[-10px]');
    toast.classList.add('opacity-100', 'translate-y-0');
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-[-10px]');
        toast.classList.remove('opacity-100', 'translate-y-0');
    }, 3000);
}

// ── Favorieten toggle ─────────────────────────────────────────────
function toggleFavorite(productId, btn) {
    fetch('/favorites/' + productId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) return;

        const isFav = data.favorite;
        btn.dataset.favorite = isFav ? '1' : '0';

        // Icoon bijwerken
        const svg = btn.querySelector('svg');
        if (isFav) {
            svg.classList.remove('text-gray-300', 'fill-none');
            svg.classList.add('text-pink-500', 'fill-pink-500');
        } else {
            svg.classList.remove('text-pink-500', 'fill-pink-500');
            svg.classList.add('text-gray-300', 'fill-none');
        }

        // Toast + herladen om balk te vernieuwen
        showToast(isFav ? '❤️ Toegevoegd aan favorieten' : '🗑️ Verwijderd uit favorieten');
        setTimeout(() => window.location.reload(), 800);
    });
}

// ── Modal ─────────────────────────────────────────────────────────
function closeModal() {
    const modal = document.getElementById('recommended-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function showRecommended(products) {
    if (!products || products.length === 0) return;
    const list = document.getElementById('recommended-list');
    list.innerHTML = products.map(p => `
        <div class="border border-gray-100 rounded-xl p-3 flex flex-col items-center gap-2 text-center">
            ${p.image
                ? `<img src="${p.image}" class="w-16 h-16 object-contain rounded-lg bg-gray-50">`
                : `<div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                   </div>`
            }
            <p class="text-xs font-semibold text-gray-700 leading-tight">${p.name}</p>
            <div class="flex items-center gap-1 mt-auto">
                <input type="number" id="rec-qty-${p.id}" value="1" min="1" class="w-12 text-center text-xs border border-gray-200 rounded-lg px-1 py-1">
                <button onclick="addRecommended(${p.id})"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-2 py-1 rounded-lg transition whitespace-nowrap">
                    Voeg toe
                </button>
            </div>
        </div>
    `).join('');
    document.getElementById('recommended-modal').classList.remove('hidden');
    document.getElementById('recommended-modal').classList.add('flex');
}

// ── +/- knoppen ──────────────────────────────────────────────────
function updateCart(productId, change) {
    const qtyEl = document.getElementById('qty-' + productId);
    const currentQty = parseInt(qtyEl.value) || 0;
    const newQty = Math.max(0, currentQty + change);

    fetch('/cart/ajax/' + productId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ quantity: newQty })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) qtyEl.value = newQty;
    });
}

// ── Directe invoer in het veld ────────────────────────────────────
function setCart(productId, value) {
    const newQty = Math.max(0, parseInt(value) || 0);
    document.getElementById('qty-' + productId).value = newQty;
    fetch('/cart/ajax/' + productId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ quantity: newQty })
    });
}

// ── Mandje knop: toon aanbevelingen dan ga naar mandje ────────────
function goToCart(productId) {
    const qtyEl = document.getElementById('qty-' + productId);
    const currentQty = parseInt(qtyEl?.value) || 0;

    fetch('/cart/ajax/' + productId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ quantity: currentQty > 0 ? currentQty : 1 })
    })
    .then(res => res.json())
    .then(data => {
        if (data.recommended && data.recommended.length > 0) {
            showRecommended(data.recommended);
        } else {
            window.location.href = '{{ route("cart.index") }}';
        }
    });
}

// ── Aanbevolen product toevoegen vanuit modal ─────────────────────
function addRecommended(productId) {
    const qty = parseInt(document.getElementById('rec-qty-' + productId)?.value) || 1;
    fetch('/cart/ajax/' + productId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ quantity: qty })
    })
    .then(res => res.json())
    .then(() => { closeModal(); showToast('Product toegevoegd aan het mandje!'); });
}

// ── Notificaties ────────────────────────────────────────────────────────

// TECHNIEKER — mémorise les IDs fermés dans localStorage
const DISMISSED_KEY = 'notif_dismissed_ids_{{ auth()->id() }}';

function getDismissed() {
    try { return JSON.parse(localStorage.getItem(DISMISSED_KEY) || '[]'); } catch { return []; }
}
function saveDismissed(ids) {
    localStorage.setItem(DISMISSED_KEY, JSON.stringify(ids));
}

function sluitNotif(btn) {
    const item = btn.closest('.notif-item');
    const id   = item.dataset.id;
    fetch('/notifications/' + id + '/read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
    const dismissed = getDismissed();
    dismissed.push(id);
    saveDismissed(dismissed);
    item.remove();
    const list = document.getElementById('notif-list');
    if (list && list.querySelectorAll('.notif-item').length === 0) {
        document.getElementById('notif-card')?.remove();
    }
}

function sluitAlleNotifs() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
    // Sauvegarder tous les IDs visibles
    const items = document.querySelectorAll('#notif-card .notif-item');
    const dismissed = getDismissed();
    items.forEach(i => dismissed.push(i.dataset.id));
    saveDismissed(dismissed);
    document.getElementById('notif-card')?.remove();
}

// Filtrer les notifs déjà fermées au chargement
document.addEventListener('DOMContentLoaded', function () {
    const dismissed = getDismissed();
    const card = document.getElementById('notif-card');
    if (card) {
        dismissed.forEach(id => {
            document.querySelector(`.notif-item[data-id="${id}"]`)?.remove();
        });
        const list = document.getElementById('notif-list');
        if (!list || list.querySelectorAll('.notif-item').length === 0) {
            card.remove();
        }
        // Sinon afficher la carte
    }

    // MAG — afficher seulement si nouveau ou pas encore fermé
    const magCard = document.getElementById('notif-card-mag');
    if (magCard) {
        const MAG_KEY = 'mag_notif_{{ auth()->id() }}';
        const currentPending = parseInt(magCard.dataset.pending || '0');
        const currentUrgent  = parseInt(magCard.dataset.urgent  || '0');
        let show = true;
        try {
            const saved = JSON.parse(localStorage.getItem(MAG_KEY) || 'null');
            if (saved) {
                const age = Date.now() - saved.at;
                const sameOrLess = currentPending <= saved.pending && currentUrgent <= saved.urgent;
                // Cacher si fermé il y a moins de 30 min ET pas de nouvelles commandes
                if (age < 30 * 60 * 1000 && sameOrLess) show = false;
            }
        } catch {}
        if (show) magCard.style.display = '';
    }
});

function sluitMagNotif() {
    const magCard = document.getElementById('notif-card-mag');
    if (!magCard) return;
    const MAG_KEY = 'mag_notif_{{ auth()->id() }}';
    localStorage.setItem(MAG_KEY, JSON.stringify({
        at:      Date.now(),
        pending: parseInt(magCard.dataset.pending || '0'),
        urgent:  parseInt(magCard.dataset.urgent  || '0'),
    }));
    magCard.remove();
}

</script>

</x-app-layout>
