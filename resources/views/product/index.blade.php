<x-app-layout>
    <x-slot name="header">Producten</x-slot>

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('product.index') }}" class="mb-6 space-y-3">
        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-center sm:justify-between">
            <div class="flex gap-2 w-full sm:flex-1 sm:min-w-0">
                <input type="text" name="search" value="{{ $query }}"
                    placeholder="Zoek een product..."
                    class="flex-1 min-w-0 text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                    Zoeken
                </button>
                @if ($query || $selectedCategory)
                    <a href="{{ route('product.index') }}" class="text-sm text-gray-400 hover:text-gray-600 px-3 py-2 rounded-lg border border-gray-200 transition">
                        ✕ Reset
                    </a>
                @endif
            </div>
            @if (Auth::user()?->role === 'admin' || Auth::user()?->role === 'magazijnBeheerder')
                <a href="{{ route('product.create') }}"
                class="w-full sm:w-auto text-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition whitespace-nowrap">
                    + Product toevoegen
                </a>
            @endif
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('product.index', array_filter(['search' => $query])) }}"
               class="text-xs font-medium px-3 py-1.5 rounded-full border transition
                      {{ ! $selectedCategory ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:border-blue-400' }}">
                Alle
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('product.index', array_filter(['search' => $query, 'category' => $cat->id])) }}"
                   class="text-xs font-medium px-3 py-1.5 rounded-full border transition
                          {{ (string) $selectedCategory === (string) $cat->id ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:border-blue-400' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </form>

        {{-- Weersuggesties --}}
    @if(isset($weatherAlert) && $weatherAlert !== 'none' && $suggestedProducts->isNotEmpty())
    <div class="mb-4 p-4 rounded-xl border {{ $weatherAlert === 'flood' ? 'bg-red-50 border-red-200' : 'bg-blue-50 border-blue-200' }}">
        <div class="flex items-center gap-2 mb-2">
            <h2 class="font-semibold text-sm {{ $weatherAlert === 'flood' ? 'text-red-700' : 'text-blue-700' }}">
                {{ $weatherAlert === 'flood' ? 'Overstromingsrisico vandaag' : 'Regendag vandaag' }} — Aanbevolen materialen:
            </h2>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($suggestedProducts as $product)
                <span class="text-xs font-medium px-3 py-1.5 rounded-full border
                    {{ $weatherAlert === 'flood' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-blue-100 text-blue-700 border-blue-200' }}">
                     {{ $product->name }}
                </span>
            @endforeach
        </div>
    </div>
    @endif

    <p class="mb-4 text-sm text-gray-500">
        {{ $products->count() }} {{ $products->count() === 1 ? 'beschikbaar product' : 'beschikbare producten' }}
    </p>

    @if ($products->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
            Geen producten gevonden.
        </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2 sm:gap-5">
        @foreach ($products as $product)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-row sm:flex-col {{ !$product->is_active ? 'border-2 border-red-400' : '' }}">

            {{-- Afbeelding: links op mobiel, bovenaan op desktop --}}
            <div class="shrink-0 w-[4.5rem] h-[4.5rem] sm:w-full sm:h-40 {{ !$product->is_active ? 'opacity-50' : '' }}">
                @if ($product->image)
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                        class="w-full h-full object-contain object-center p-1.5 sm:p-3 bg-white">
                @else
                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-12 sm:w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
            </div>

            <div class="flex-1 min-w-0 p-2.5 sm:p-4 flex flex-col justify-center sm:justify-start">
                <h3 class="font-semibold text-gray-900 text-xs sm:text-sm leading-tight line-clamp-2 {{ !$product->is_active ? 'opacity-50' : '' }}">{{ $product->name }}</h3>
                <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5 truncate {{ !$product->is_active ? 'opacity-50' : '' }}">{{ optional($product->category)->name ?? '—' }}</p>

                <div class="mt-1 sm:mt-3 flex items-center gap-2 flex-wrap {{ !$product->is_active ? 'opacity-50' : '' }}">
                    <span class="text-[10px] sm:text-xs {{ $product->stock <= 5 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        Stock: {{ $product->stock }}
                    </span>
                    @if ($product->is_active)
                        <span class="bg-green-100 text-green-700 text-[10px] sm:text-xs font-medium px-1.5 py-0.5 rounded-full">Actief</span>
                    @else
                        <span class="bg-gray-100 text-gray-500 text-[10px] sm:text-xs font-medium px-1.5 py-0.5 rounded-full">Inactief</span>
                    @endif
                </div>

                @if ($product->barcode)
                <div class="mt-1 hidden sm:flex items-center gap-1.5 {{ !$product->is_active ? 'opacity-50' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h1v12H4V6zm3 0h1v12H7V6zm3 0h2v12h-2V6zm4 0h1v12h-1V6zm3 0h2v12h-2V6z"/>
                    </svg>
                    <span class="text-xs font-mono font-semibold text-gray-600 tracking-widest">{{ $product->barcode }}</span>
                </div>
                @endif

                {{-- Bestellen (technieker) --}}
                @if ((Auth::user()?->role === 'technieker' || Auth::user()?->role === 'admin') && $product->is_active)
                <div class="mt-2 sm:mt-4 pt-2 sm:pt-3 border-t border-gray-100">
                    <div class="flex flex-wrap items-center gap-1 sm:gap-2">
                        <button type="button" onclick="updateCart({{ $product->id }}, -1)"
                            class="w-6 h-6 sm:w-8 sm:h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-md sm:rounded-lg text-gray-600 font-bold text-sm transition">
                            −
                        </button>
                        <span id="qty-{{ $product->id }}" class="w-5 sm:w-8 text-center text-xs sm:text-sm font-semibold text-gray-800">
                            {{ $cartQty[$product->id] ?? 0 }}
                        </span>
                        <button type="button" onclick="updateCart({{ $product->id }}, 1)"
                            class="w-6 h-6 sm:w-8 sm:h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-md sm:rounded-lg text-gray-600 font-bold text-sm transition">
                            +
                        </button>
                        <a href="{{ route('cart.index') }}"
                            class="text-[10px] sm:text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 sm:px-3 sm:py-1.5 rounded-md sm:rounded-lg transition flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-3.5 sm:w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/>
                            </svg>
                            Mandje
                        </a>
                        <a href="{{ route('order.create', ['product_id' => $product->id]) }}"
                            class="text-[10px] sm:text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 sm:px-3 sm:py-1.5 rounded-md sm:rounded-lg transition whitespace-nowrap">
                            Direct
                        </a>
                    </div>
                </div>
                @endif

                {{-- Bewerken/Verwijderen/Toggle (admin + magazijnBeheerder) --}}
                @if (Auth::user()?->role === 'admin' || Auth::user()?->role === 'magazijnBeheerder')
                <div class="mt-1.5 sm:mt-2 flex flex-wrap gap-1 sm:gap-2">
                    <a href="{{ route('product.edit', $product->id) }}"
                        class="text-[10px] sm:text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 px-2 py-1 sm:flex-1 sm:text-center sm:py-1.5 rounded-md sm:rounded-lg transition">
                        Bewerken
                    </a>
                    <form action="{{ route('product.toggle', $product->id) }}" method="POST" class="sm:flex-1">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="text-[10px] sm:text-xs font-medium px-2 py-1 sm:w-full sm:py-1.5 rounded-md sm:rounded-lg transition
                                {{ $product->is_active
                                    ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100'
                                    : 'bg-green-200 text-green-800 hover:bg-green-300' }}">
                            {{ $product->is_active ? 'Verbergen' : 'Tonen' }}
                        </button>
                    </form>
                    <form action="{{ route('product.destroy', $product->id) }}" method="POST"
                        onsubmit="return confirm('Dit product verwijderen?')" class="sm:flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-[10px] sm:text-xs font-medium bg-red-50 text-red-500 hover:bg-red-100 px-2 py-1 sm:w-full sm:py-1.5 rounded-md sm:rounded-lg transition">
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

<script>
function updateCart(productId, change) {
    const qtyEl = document.getElementById('qty-' + productId);
    const currentQty = parseInt(qtyEl.textContent) || 0;
    const newQty = Math.max(0, currentQty + change);

    fetch('/cart/ajax/' + productId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ quantity: newQty })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            qtyEl.textContent = newQty;
        }
    });
}
</script>

</x-app-layout>
