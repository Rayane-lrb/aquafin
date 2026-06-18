<x-app-layout>
    <x-slot name="header">Nieuw magazijn</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('warehouse.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Naam</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adres <span class="text-gray-400">(optioneel)</span></label>
                    <div class="flex gap-2">
                        <input id="address-input" type="text" name="address" value="{{ old('address') }}"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                            placeholder="Bv. Dijkstraat 8, Antwerpen">
                        <button type="button" onclick="geocode()"
                            class="shrink-0 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition">
                            📍 GPS ophalen
                        </button>
                    </div>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Coördinaten <span class="text-gray-400">(voor weersvoorspelling)</span></label>
                    <div class="flex gap-2">
                        <input id="lat-input" type="number" name="latitude" value="{{ old('latitude') }}" step="0.0000001"
                            placeholder="Breedtegraad"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <input id="lon-input" type="number" name="longitude" value="{{ old('longitude') }}" step="0.0000001"
                            placeholder="Lengtegraad"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <p id="geo-status" class="text-xs text-gray-400 mt-1">Klik op "GPS ophalen" na het invullen van het adres.</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Opslaan
                    </button>
                    <a href="{{ route('warehouse.index') }}" class="text-sm text-gray-500 hover:underline px-3 py-2">Annuleren</a>
                </div>
            </form>
        </div>
    </div>

<script>
async function geocode() {
    const address = document.getElementById('address-input').value.trim();
    const status  = document.getElementById('geo-status');
    if (!address) { status.textContent = 'Vul eerst een adres in.'; return; }

    status.textContent = 'Zoeken...';
    try {
        const res  = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`, {
            headers: { 'Accept-Language': 'nl' }
        });
        const data = await res.json();
        if (!data.length) { status.textContent = 'Adres niet gevonden. Probeer een preciezer adres.'; return; }

        document.getElementById('lat-input').value = parseFloat(data[0].lat).toFixed(7);
        document.getElementById('lon-input').value = parseFloat(data[0].lon).toFixed(7);
        status.textContent = '✅ Coördinaten gevonden: ' + data[0].display_name;
        status.className = 'text-xs text-green-600 mt-1';
    } catch {
        status.textContent = '❌ Fout bij ophalen van coördinaten.';
        status.className = 'text-xs text-red-500 mt-1';
    }
}
</script>

</x-app-layout>
