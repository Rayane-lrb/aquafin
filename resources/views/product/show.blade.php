<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p><strong>Nom :</strong> {{ $product->name }}</p>
                <p><strong>Catégorie :</strong> {{ $product->category->name }}</p>
                <p><strong>Stock :</strong> {{ $product->stock }}</p>
                <p><strong>Actif :</strong> {{ $product->is_active ? 'Oui' : 'Non' }}</p>
                <p><strong>Outil d'inondation :</strong> {{ $product->is_flood_tool ? 'Oui' : 'Non' }}</p>

                <div class="mt-4 flex gap-2">
                    <a href="{{ route('product.edit', $product->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Modifier</a>
                    <a href="{{ route('product.index') }}" class="text-gray-500 px-4 py-2">Retour</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>