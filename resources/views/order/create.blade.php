<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nouvelle commande
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('order.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Produit</label>
                        <select name="product_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Choisir --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (stock: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Quantité</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('quantity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Envoyer la commande
                    </button>
                    <a href="{{ route('order.index') }}" class="ml-2 text-gray-500">Annuler</a>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>