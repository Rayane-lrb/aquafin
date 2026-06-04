<x-app-layout>
    <x-slot name="header">{{ $product->name }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white shadow-sm rounded-xl p-6 space-y-4">
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Nom</span>
                <p class="text-gray-800 font-medium">{{ $product->name }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Catégorie</span>
                <p class="text-gray-800">{{ optional($product->category)->name ?? '—' }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Stock</span>
                <p class="text-gray-800">{{ $product->stock }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Statut</span>
                <p>
                    @if ($product->is_active)
                        <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-1 rounded-full">Actif</span>
                    @else
                        <span class="bg-gray-100 text-gray-500 text-xs font-medium px-2 py-1 rounded-full">Inactif</span>
                    @endif
                </p>
            </div>
            <div class="pt-4 flex gap-3">
                <a href="{{ route('product.edit', $product->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">Modifier</a>
                <a href="{{ route('product.index') }}" class="text-sm text-gray-500 hover:underline px-3 py-2">Retour</a>
            </div>
        </div>
    </div>
</x-app-layout>
