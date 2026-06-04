<x-app-layout>
    <x-slot name="header">Producten</x-slot>

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Overzicht van alle beschikbare producten</p>
        <a href="{{ route('product.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Product toevoegen
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">Naam</th>
                    <th class="px-6 py-3">Categorie</th>
                    <th class="px-6 py-3">Stock</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($products as $product)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $product->name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ optional($product->category)->name ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="{{ $product->stock <= 5 ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if ($product->is_active)
                            <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-1 rounded-full">Actief</span>
                        @else
                            <span class="bg-gray-100 text-gray-500 text-xs font-medium px-2 py-1 rounded-full">Inactief</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 flex gap-3">
                        <a href="{{ route('product.edit', $product->id) }}" class="text-blue-600 hover:underline">Bewerken</a>
                        <form action="{{ route('product.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Dit product verwijderen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline">Verwijderen</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Geen producten gevonden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
