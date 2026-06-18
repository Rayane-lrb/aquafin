<x-app-layout>
    <x-slot name="header">{{ $product->name }}</x-slot>

    @php $role = auth()->user()?->role; @endphp

    <div class="max-w-lg mx-auto">
        <div class="bg-white shadow-sm rounded-2xl overflow-hidden">

            {{-- Afbeelding --}}
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                     class="w-full h-52 object-contain bg-gray-50 p-4">
            @else
                <div class="w-full h-52 bg-gray-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
            @endif

            <div class="p-5 space-y-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $product->name }}</h2>
                    <p class="text-sm text-gray-400">{{ optional($product->category)->name ?? '—' }}</p>
                </div>

                <div class="flex items-center gap-4 text-sm">
                    <span class="{{ $product->stock <= 5 ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                        Stock: {{ $product->stock }}
                    </span>
                    <span class="font-mono text-xs text-gray-400 tracking-widest">{{ $product->barcode ?? '' }}</span>
                    @if ($product->is_active)
                        <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-0.5 rounded-full">Actief</span>
                    @else
                        <span class="bg-red-100 text-red-500 text-xs font-medium px-2 py-0.5 rounded-full">Inactief</span>
                    @endif
                </div>

                {{-- ── TECHNIEKER: bestellen ── --}}
                @if ($role === 'technieker' && $product->is_active && $product->stock > 0)
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="pt-3 border-t border-gray-100">
                    @csrf
                    <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Aantal bestellen</label>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                            <button type="button" onclick="changeQty(-1)"
                                class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition font-bold text-lg">−</button>
                            <input type="number" id="qty" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                class="w-14 text-center text-sm font-semibold border-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <button type="button" onclick="changeQty(1)"
                                class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition font-bold text-lg">+</button>
                        </div>
                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/>
                            </svg>
                            Toevoegen aan mandje
                        </button>
                    </div>
                </form>
                @elseif ($role === 'technieker' && (!$product->is_active || $product->stock == 0))
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-sm text-red-500 font-medium">
                        {{ !$product->is_active ? 'Dit product is niet beschikbaar.' : 'Geen stock beschikbaar.' }}
                    </p>
                </div>
                @endif

                {{-- ── ADMIN / MAG: bewerken ── --}}
                @if (in_array($role, ['admin', 'magazijnBeheerder']))
                <div class="pt-3 border-t border-gray-100 flex gap-3">
                    <a href="{{ route('product.edit', $product->id) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Bewerken
                    </a>
                </div>
                @endif

                <div class="pt-1">
                    <a href="{{ route('product.index') }}" class="text-sm text-gray-400 hover:text-gray-600 transition">← Terug</a>
                </div>
            </div>
        </div>
    </div>

<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    input.value = Math.max(1, Math.min(parseInt(input.max), (parseInt(input.value) || 1) + delta));
}
</script>
</x-app-layout>
