<x-app-layout>
    <x-slot name="header">Mijn mandje</x-slot>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if (empty($cart))
        <div class="bg-white rounded-xl shadow-sm p-10 text-center text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/>
            </svg>
            <p class="font-medium text-gray-500">Je mandje is leeg</p>
            <a href="{{ route('product.index') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">← Terug naar catalogus</a>
        </div>

        @if (Auth::user()?->role === 'admin' && $orderHistory->isNotEmpty())
            <div class="mt-8">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Eerdere bestellingen</h3>
                <div class="space-y-2">
                    @foreach ($orderHistory as $groupId => $orders)
                        @php $firstOrder = $orders->first(); @endphp
                        <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <button @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <span class="font-medium">{{ $firstOrder->created_at->isoFormat('D MMMM YYYY') }}</span>
                                <svg x-bind:class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse class="border-t border-gray-100">
                                <ul class="divide-y divide-gray-50">
                                    @foreach ($orders as $order)
                                        <li class="flex items-center justify-between px-4 py-2.5 text-sm">
                                            <span class="text-gray-600">{{ $order->product?->name ?? 'Onbekend product' }}</span>
                                            <span class="font-semibold text-gray-800">{{ $order->quantity }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Producten --}}
        <div class="lg:col-span-2 space-y-3">
            @foreach ($products as $product)
            @php $qty = $cart[$product->id] ?? 1; @endphp
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">

                {{-- Foto --}}
                @if ($product->image)
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                        class="w-16 h-16 object-contain rounded-lg bg-gray-50 flex-shrink-0">
                @else
                    <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $product->name }}</p>
                    <p class="text-xs text-gray-400">{{ optional($product->category)->name ?? '—' }}</p>
                </div>

               {{-- Aantal --}}
<div class="flex items-center gap-1">
    <form action="{{ route('cart.update', $product->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <input type="hidden" name="quantity" value="{{ max(1, $qty - 1) }}">
        <button type="submit"
            class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 font-bold transition">
            −
        </button>
    </form>

    <form action="{{ route('cart.update', $product->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <input type="number" name="quantity" value="{{ $qty }}" min="1"
            class="w-14 text-center text-sm font-semibold text-gray-800 border border-gray-200 rounded-lg px-1 py-1"
            onchange="this.form.submit()">
    </form>

    <form action="{{ route('cart.update', $product->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <input type="hidden" name="quantity" value="{{ $qty + 1 }}">
        <button type="submit"
            class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 font-bold transition">
            +
        </button>
    </form>
</div>

                {{-- Verwijderen --}}
                <form action="{{ route('cart.remove', $product->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 text-gray-300 hover:text-red-500 transition rounded-lg hover:bg-red-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </form>
            </div>
            @endforeach
        </div>

        {{-- Checkout --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-5 sticky top-6">
                <h2 class="font-semibold text-gray-800 text-sm mb-4">Bestelling plaatsen</h2>

                <form action="{{ route('cart.checkout') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5 font-medium">Leveringsmagazijn</label>
                        @if ($warehouses->isEmpty())
                            <p class="text-xs text-red-500">Geen magazijnen beschikbaar. Vraag een beheerder er een toe te voegen.</p>
                        @else
                            <select name="warehouse_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">Kies een magazijn...</option>
                                @foreach ($warehouses as $w)
                                    <option value="{{ $w->id }}">{{ $w->name }}{{ $w->address ? ' — ' . $w->address : '' }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    {{-- URGENCE --}}
                    <div class="border border-red-200 bg-red-50 rounded-lg p-3">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="urgent" value="1" class="mt-0.5 accent-red-600 w-4 h-4">
                            <div>
                                <p class="text-sm font-semibold text-red-700">🚨 Urgente levering</p>
                                <p class="text-xs text-red-500 mt-0.5">Vraag een voorrangsbehandeling aan. Het magazijn wordt op de hoogte gesteld.</p>
                            </div>
                        </label>
                    </div>

                    <div class="border-t border-gray-100 pt-3">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Producten</span>
                            <span>{{ count($cart) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Totaal stuks</span>
                            <span>{{ array_sum($cart) }}</span>
                        </div>
                    </div>

                    <button type="submit" @if($warehouses->isEmpty()) disabled @endif
                        class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white text-sm font-medium py-2.5 rounded-lg transition">
                        Bestelling plaatsen
                    </button>
                </form>

                <a href="{{ route('product.index') }}" class="block text-center text-xs text-gray-400 hover:text-gray-600 mt-3">
                    ← Verder winkelen
                </a>
            </div>
        </div>
    </div>
    @endif

</x-app-layout>
