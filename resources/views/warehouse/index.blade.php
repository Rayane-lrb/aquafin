<x-app-layout>
    <x-slot name="header">Magazijnen</x-slot>

    <div class="flex justify-between items-center mb-5">
        <p class="text-sm text-gray-500">{{ $warehouses->count() }} magazijn(en)</p>
        @if (Auth::user()?->role === 'admin')
        <a href="{{ route('warehouse.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Toevoegen
        </a>
        @endif
    </div>

    @if ($warehouses->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
            Nog geen magazijnen. Voeg er een toe.
        </div>
    @else
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @foreach ($warehouses as $i => $warehouse)
        <div class="flex items-center gap-4 px-5 py-4 {{ $i < $warehouses->count() - 1 ? 'border-b border-gray-50' : '' }}">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">{{ $warehouse->name }}</p>
                <p class="text-xs text-gray-400">{{ $warehouse->address ?? 'Geen adres' }} · {{ $warehouse->orders_count }} orders</p>
            </div>
            @if (Auth::user()?->role === 'admin')
            <div class="flex gap-2">
                <a href="{{ route('warehouse.edit', $warehouse->id) }}"
                    class="text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                    Bewerken
                </a>
                <form action="{{ route('warehouse.destroy', $warehouse->id) }}" method="POST"
                    onsubmit="return confirm('Magazijn verwijderen?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="text-xs font-medium bg-red-50 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                        Verwijderen
                    </button>
                </form>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</x-app-layout>
