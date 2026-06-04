<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Produits
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <a href="{{ route('product.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">
                    Ajouter un produit
                </a>

                <table class="w-full mt-4 text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Nom</th>
                            <th class="py-2">Catégorie</th>
                            <th class="py-2">Stock</th>
                            <th class="py-2">Actif</th>
                            <th class="py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                        <tr class="border-b">
                            <td class="py-2">{{ $product->name }}</td>
                            <td class="py-2">{{ $product->product_category_id }}</td>
                            <td class="py-2">{{ $product->stock }}</td>
                            <td class="py-2">{{ $product->is_active ? 'Ja' : 'Neen' }}</td>
                            <td class="py-2 flex gap-2">
                                <a href="{{ route('product.edit', $product->id) }}" class="text-blue-500">Modifier</a>
                                <form action="{{ route('product.destroy', $product->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500">Supprimer</button>

                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
