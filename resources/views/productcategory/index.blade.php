<x-app-layout>
    <x-slot name="header">Catégories</x-slot>

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Gestion des catégories de produits</p>
        <a href="{{ route('productcategory.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Ajouter
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">Nom</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($productcategories as $category)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $category->name }}</td>
                    <td class="px-6 py-4 flex gap-3">
                        <a href="{{ route('productcategory.edit', $category->id) }}" class="text-blue-600 hover:underline">Modifier</a>
                        <form action="{{ route('productcategory.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-6 py-8 text-center text-gray-400">Aucune catégorie.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
