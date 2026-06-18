<x-app-layout>
    <x-slot name="header">Magazijn bewerken</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('warehouse.update', $warehouse->id) }}" method="POST" class="space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Naam</label>
                    <input type="text" name="name" value="{{ old('name', $warehouse->name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adres <span class="text-gray-400">(optioneel)</span></label>
                    <input type="text" name="address" value="{{ old('address', $warehouse->address) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Coördinaten <span class="text-gray-400">(voor weersvoorspelling)</span></label>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <input type="number" name="latitude" value="{{ old('latitude', $warehouse->latitude) }}" step="0.0000001" placeholder="Breedtegraad (bv. 51.2194)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @error('latitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex-1">
                            <input type="number" name="longitude" value="{{ old('longitude', $warehouse->longitude) }}" step="0.0000001" placeholder="Lengtegraad (bv. 4.4025)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @error('longitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Coordonnées GPS → utilisées pour la météo/neerslag de ce site.</p>
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

</x-app-layout>
