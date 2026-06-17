<x-app-layout>
    <x-slot name="header">Producten</x-slot>

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
        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-center sm:justify-between">
            <div class="flex gap-2 flex-1 min-w-0">
                <input type="text" name="search" value="{{ $query }}"
                    placeholder="Zoek een product..."
                    class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition whitespace-nowrap">
                    Zoeken
                </button>
                @if ($query || $selectedCategory)
                    <a href="{{ route('product.index') }}" class="text-sm text-gray-400 hover:text-gray-600 px-3 py-2 rounded-lg border border-gray-200 transition whitespace-nowrap">
                        ✕ Reset
                    </a>
                @endif
            </div>
            @if (Auth::user()?->role === 'admin' || Auth::user()?->role === 'magazijnBeheerder')
                <a href="{{ route('product.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition whitespace-nowrap text-center w-full sm:w-auto">
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
                        <span id="qty-{{ $product->id }}" class="w-6 sm:w-8 text-center text-sm font-semibold text-gray-800">
                            {{ $cartQty[$product->id] ?? 0 }}
                        </span>
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
                    <form action="{{ route('product.destroy', $product->id) }}" method="POST"
                        onsubmit="return confirm('Dit product verwijderen?')" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full text-xs font-medium bg-red-50 text-red-500 hover:bg-red-100 py-1 sm:py-1.5 rounded-lg transition">
                            Verwijderen
                        </button>
                    </form>
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
    const currentQty = parseInt(qtyEl.textContent) || 0;
    const newQty = Math.max(0, currentQty + change);

    fetch('/cart/ajax/' + productId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ quantity: newQty })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) qtyEl.textContent = newQty;
    });
}

// ── Mandje knop: toon aanbevelingen dan ga naar mandje ────────────
function goToCart(productId) {
    const qtyEl = document.getElementById('qty-' + productId);
    const currentQty = parseInt(qtyEl?.textContent) || 0;

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
</script>

</x-app-layout>
