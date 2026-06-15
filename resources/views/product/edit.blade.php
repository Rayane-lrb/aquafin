<x-app-layout>
    <x-slot name="header">Product bewerken</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white shadow-sm rounded-xl p-6">
            <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Naam</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('barcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Afbeelding</label>
                    @if ($product->image)
                        <img src="{{ asset($product->image) }}" class="h-24 rounded-lg mb-2 object-cover">
                    @endif
                    <input type="file" name="image" accept="image/*"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categorie</label>
                    <select name="product_category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Kies een categorie --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('product_category_id', $product->product_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    @error('product_category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Weersomstandigheden</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input name="is_flood_tool" type="checkbox" value="1" {{ $product->is_flood_tool ? 'checked' : '' }}>
                            Overstromingsmateriaal
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input name="needed_on_rain" type="checkbox" value="1" {{ $product->needed_on_rain ? 'checked' : '' }}>
                            Regenmateriaal
                        </label>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Bijwerken
                    </button>
                    <a href="{{ route('product.index') }}" class="text-sm text-gray-500 hover:underline px-3 py-2">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
