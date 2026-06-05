<x-technieker-layout>
    <x-slot name="header">Technieker Dashboard</x-slot>

    <form method="GET" action="{{ route('technieker') }}" class="mb-8 flex gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek materiaal..."
            class="flex-1 border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="category" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Alle categorieën</option>
            <option value="1" {{ request('category') == 1 ? 'selected' : '' }}>Bevestigingsmateriaal</option>
            <option value="2" {{ request('category') == 2 ? 'selected' : '' }}>Persoonlijke beschermingsmiddelen (PBM)</option>
            <option value="3" {{ request('category') == 3 ? 'selected' : '' }}>Gereedschap (manueel & elektrisch)</option>
            <option value="4" {{ request('category') == 4 ? 'selected' : '' }}>Technische onderhoudsmaterialen</option>
            <option value="5" {{ request('category') == 5 ? 'selected' : '' }}>Specifieke Aquafin/riolering gerelateerde tools</option>
            <option value="6" {{ request('category') == 6 ? 'selected' : '' }}>Diversen / Verbruiksgoederen</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700 transition">Zoeken</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($products as $product)
        <div class="bg-white p-5 rounded-xl shadow">
            <h3 class="font-semibold text-lg">{{ $product->name }}</h3>
            <p class="text-gray-600 mt-2">Voorraad: {{ $product->stock }}</p>
            <div class="mt-4">
                <button onclick="showQuantitySelector(this)" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Toevoegen</button>
                <div class="quantity-selector hidden mt-3" data-name="{{ $product->name }}" data-id="{{ $product->id }}">
                    <input type="number" min="1" max="{{ $product->stock }}" value="1" class="quantity-input border rounded px-3 py-2 w-24">
                    <button onclick="confirmAdd(this)" class="bg-blue-600 text-white px-3 py-2 rounded ml-2 hover:bg-blue-700">Bevestigen</button>
                    <button onclick="cancelAdd(this)" class="bg-red-600 text-white px-3 py-2 rounded ml-2 hover:bg-red-700">Annuleren</button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-gray-500">Geen producten gevonden.</div>
        @endforelse
    </div>

    <div class="mt-8 p-6 bg-white rounded-xl shadow text-center">
        <p class="text-gray-600 mb-2">Niet gevonden wat je zoekt?</p>
        <a href="{{ route('suggestion.create') }}?title={{ request('search') }}" class="inline-block bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
            Stel een nieuw materiaal voor
        </a>
    </div>
</x-technieker-layout>
