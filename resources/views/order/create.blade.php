<x-app-layout>
    <x-slot name="header">Nieuwe bestelling</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white shadow-sm rounded-xl p-6">
            <form action="{{ route('order.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                    <select name="product_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Kies een product --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                {{ (old('product_id', $productId) == $product->id) ? 'selected' : '' }}>
                                {{ $product->name }} (stock: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Aantal</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Leveringsmagazijn</label>
                    @if ($warehouses->isEmpty())
                        <p class="text-xs text-red-500 py-2">Geen magazijnen beschikbaar. Vraag een beheerder er een toe te voegen.</p>
                    @else
                        <select name="warehouse_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Kies een magazijn --</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->name }}{{ $w->address ? ' — ' . $w->address : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('warehouse_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @endif
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Bestelling plaatsen
                    </button>
                    <a href="{{ route('order.index') }}" class="text-sm text-gray-500 hover:underline px-3 py-2">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
