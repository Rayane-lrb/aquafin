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
                    <p class="font-mono font-semibold tracking-widest text-gray-800 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                        {{ $product->barcode ?? '—' }}
                    </p>
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

                <div class="mb-4 flex flex-col gap-3">
                    {{-- Actief --}}
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <div class="w-10 h-5 bg-gray-200 peer-checked:bg-blue-500 rounded-full transition"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-5"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Actief</p>
                            <p class="text-xs text-gray-400">Zichtbaar voor techniekers</p>
                        </div>
                    </label>
                    {{-- Neerslag --}}
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative">
                            <input type="hidden" name="needed_on_rain" value="0">
                            <input type="checkbox" name="needed_on_rain" value="1" class="sr-only peer"
                                {{ old('needed_on_rain', $product->needed_on_rain) ? 'checked' : '' }}>
                            <div class="w-10 h-5 bg-gray-200 peer-checked:bg-blue-500 rounded-full transition"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-5"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">🌧️ Aanbevolen bij neerslag</p>
                            <p class="text-xs text-gray-400">Verschijnt in de neerslagbanner op de cataloguspagina</p>
                        </div>
                    </label>
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
