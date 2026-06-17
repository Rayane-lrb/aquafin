<x-app-layout>
    <x-slot name="header">Categorieën</x-slot>

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Beheer van productcategorieën</p>
        @if (Auth::user()?->role !== 'technieker')
        <a href="{{ route('productcategory.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Categorie toevoegen
        </a>
        @else
        <a href="{{ route('product.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            ← Catalogus
        </a>
        @endif
    </div>

    <div class="bg-white shadow-sm rounded-xl overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">Naam</th>
                    @if (Auth::user()?->role !== 'technieker')
                    <th class="px-6 py-3">Acties</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($productCategories as $category)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $category->name }}</td>
                    @if (Auth::user()?->role !== 'technieker')
                    <td class="px-6 py-4 flex gap-3">
                        <a href="{{ route('productcategory.edit', $category->id) }}" class="text-blue-600 hover:underline">Bewerken</a>
                        <form action="{{ route('productcategory.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Deze categorie verwijderen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline">Verwijderen</button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ Auth::user()?->role !== 'technieker' ? '2' : '1' }}" class="px-6 py-8 text-center text-gray-400">Geen categorieën gevonden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
