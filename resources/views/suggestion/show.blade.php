<x-app-layout>
    <x-slot name="header">{{ $suggestion->title }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white shadow-sm rounded-xl p-6 space-y-4">
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Ingediend door</span>
                <p class="text-gray-800 font-medium">{{ $suggestion->user->name }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Beschrijving</span>
                <p class="text-gray-700 mt-1">{{ $suggestion->description }}</p>
            </div>
            @if ($suggestion->image)
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Foto</span>
                <img src="{{ asset('storage/' . $suggestion->image) }}" alt="Foto suggestie"
                    class="mt-2 w-full max-h-72 object-cover rounded-lg border border-gray-200">
            </div>
            @endif
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Status</span>
                <p>
                    @if ($suggestion->status === 'goedgekeurd')
                        <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-1 rounded-full">Goedgekeurd</span>
                    @elseif ($suggestion->status === 'afgekeurd')
                        <span class="bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full">Afgekeurd</span>
                    @else
                        <span class="bg-yellow-100 text-yellow-700 text-xs font-medium px-2 py-1 rounded-full">In behandeling</span>
                    @endif
                </p>
            </div>
            {{-- Toevoegen aan catalogus (alleen als goedgekeurd + heeft image) --}}
            @if ($suggestion->status === 'goedgekeurd' && $suggestion->image)
            @if (Auth::user()?->role === 'admin' || Auth::user()?->role === 'magazijnBeheerder')
            <div class="border-t border-gray-100 pt-4">
                <p class="text-sm font-medium text-gray-700 mb-3">Toevoegen aan productcatalogus</p>
                <form action="{{ route('suggestion.addToCatalog', $suggestion->id) }}" method="POST" class="flex gap-3 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500 mb-1">Categorie</label>
                        <select name="product_category_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Kies een categorie...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('product_category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit"
                        onclick="return confirm('\'{{ $suggestion->title }}\' toevoegen aan de catalogus?')"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition whitespace-nowrap">
                        + Toevoegen
                    </button>
                </form>
            </div>
            @endif
            @endif

            <div class="pt-2">
                <a href="{{ route('suggestion.index') }}" class="text-sm text-gray-500 hover:underline">← Terug</a>
            </div>
        </div>
    </div>
</x-app-layout>
