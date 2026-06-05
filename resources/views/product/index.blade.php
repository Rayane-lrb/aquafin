<x-app-layout>
    <x-slot name="header">Producten</x-slot>

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Overzicht van alle beschikbare producten</p>
        @if (Auth::user()?->role === 'admin' || Auth::user()?->role === 'magazijnBeheerder')
            <a href="{{ route('product.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Product toevoegen
            </a>
        @endif
    </div>

    @if ($products->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
            Geen producten gevonden.
        </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach ($products as $product)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">

            {{-- Image --}}
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                    class="w-full h-40 object-cover">
            @else
                <div class="w-full h-40 bg-gray-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif

            {{-- Info --}}
            <div class="p-4 flex-1 flex flex-col">
                <h3 class="font-semibold text-gray-900 text-sm">{{ $product->name }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ optional($product->category)->name ?? '—' }}</p>

                <div class="mt-3 flex items-center justify-between">
                    <span class="text-xs {{ $product->stock <= 5 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        Stock: {{ $product->stock }}
                    </span>
                    @if ($product->is_active)
                        <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-0.5 rounded-full">Actief</span>
                    @else
                        <span class="bg-gray-100 text-gray-500 text-xs font-medium px-2 py-0.5 rounded-full">Inactief</span>
                    @endif
                </div>

                {{-- Buttons - only for admin and magazijnBeheerder --}}
                @if (Auth::user()?->role === 'admin' || Auth::user()?->role === 'magazijnBeheerder')
                <div class="mt-4 flex gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('product.edit', $product->id) }}"
                        class="flex-1 text-center text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 py-1.5 rounded-lg transition">
                        Bewerken
                    </a>
                    <form action="{{ route('product.destroy', $product->id) }}" method="POST"
                        onsubmit="return confirm('Dit product verwijderen?')" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full text-xs font-medium bg-red-50 text-red-500 hover:bg-red-100 py-1.5 rounded-lg transition">
                            Verwijderen
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

</x-app-layout>
