<x-app-layout>
    <x-slot name="header">Leveringen vandaag</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Datum navigatie --}}
        <div class="bg-white rounded-2xl shadow-sm px-4 py-3 flex items-center gap-3">
            <a href="{{ route('order.levering', ['date' => $prevDate]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-gray-100 transition text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <div class="flex-1 text-center">
                <p class="font-bold text-gray-900 text-sm">
                    {{ $carbon->translatedFormat('l d F Y') }}
                    @if ($date === $today)
                        <span class="ml-1 bg-blue-100 text-blue-600 text-xs font-semibold px-2 py-0.5 rounded-full">Vandaag</span>
                    @elseif ($date === now()->addDay()->format('Y-m-d'))
                        <span class="ml-1 bg-orange-100 text-orange-600 text-xs font-semibold px-2 py-0.5 rounded-full">Morgen</span>
                    @endif
                </p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $groupedByWarehouse->flatten()->count() }} bestelling(en)</p>
            </div>

            <a href="{{ route('order.levering', ['date' => $nextDate]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-gray-100 transition text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            {{-- Datumkiezer --}}
            <input type="date" value="{{ $date }}"
                   onchange="window.location='{{ route('order.levering') }}?date='+this.value"
                   class="border border-gray-200 rounded-xl text-sm px-3 py-1.5 text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">

            @if ($date !== $today)
                <a href="{{ route('order.levering') }}"
                   class="text-xs text-blue-600 hover:underline whitespace-nowrap">Vandaag</a>
            @endif
        </div>

        @if ($groupedByWarehouse->isEmpty() && $deliveredByWarehouse->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                <p class="text-gray-500 font-medium">Geen leveringen gepland voor {{ $carbon->translatedFormat('d F Y') }}.</p>
            </div>
        @else
            @foreach ($groupedByWarehouse as $warehouseName => $orders)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

                    {{-- Werfplaats header --}}
                    <div class="bg-gray-800 text-white px-5 py-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="font-bold text-base">{{ $warehouseName }}</span>
                        @php $wh = $orders->first()->warehouse; @endphp
                        @if ($wh?->address)
                            <span class="text-gray-400 text-sm ml-1">— {{ $wh->address }}</span>
                        @endif
                    </div>

                    {{-- Bestellingen --}}
                    <ul class="divide-y divide-gray-100">
                        @foreach ($orders as $order)
                            <li class="px-5 py-4 flex items-start gap-4">
                                {{-- Urgent badge --}}
                                <div class="flex-shrink-0 pt-0.5">
                                    @if ($order->urgent)
                                        <span class="inline-block bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">DRINGEND</span>
                                    @else
                                        <span class="inline-block bg-blue-50 text-blue-500 text-xs font-medium px-2 py-0.5 rounded-full">Normaal</span>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $order->product->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $order->quantity }}× &nbsp;·&nbsp; voor <span class="font-medium text-gray-700">{{ $order->user->name }}</span>
                                    </p>
                                </div>

                                {{-- Afgeleverd knop --}}
                                <button onclick="markDelivered(this, {{ $order->id }})"
                                    class="flex-shrink-0 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                    ✓ Afgeleverd
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        @endif

        {{-- Geleverd vandaag (inklapbaar) --}}
        @if ($deliveredByWarehouse->isNotEmpty())
        <div>
            <button type="button" onclick="toggleGeleverd()"
                class="w-full flex items-center justify-between px-4 py-3 bg-white rounded-2xl shadow-sm text-sm font-semibold text-gray-600 hover:bg-gray-50 transition select-none">
                <span>Geleverd — {{ $deliveredByWarehouse->flatten()->count() }} bestelling(en)</span>
                <svg id="geleverd-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200 rotate-[-90deg]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div id="geleverd-section" class="hidden mt-2 space-y-3">
                @foreach ($deliveredByWarehouse as $warehouseName => $orders)
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden opacity-75">
                        <div class="bg-gray-600 text-white px-5 py-2 text-sm font-bold">{{ $warehouseName }}</div>
                        <ul class="divide-y divide-gray-100">
                            @foreach ($orders as $order)
                                <li class="px-5 py-3 flex items-center gap-4">
                                    <span class="text-xs bg-emerald-100 text-emerald-700 font-semibold px-2 py-0.5 rounded-full flex-shrink-0">Geleverd</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-700 text-sm">{{ $order->product->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $order->quantity }}× · {{ $order->user->name }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="text-center pb-4">
            <a href="{{ route('order.magazijn') }}" class="text-sm text-gray-400 hover:text-gray-600 transition">← Terug naar Bestellingen</a>
        </div>
    </div>

<script>
function toggleGeleverd() {
    const section = document.getElementById('geleverd-section');
    const arrow   = document.getElementById('geleverd-arrow');
    const hidden  = section.classList.toggle('hidden');
    arrow.style.transform = hidden ? 'rotate(-90deg)' : 'rotate(0deg)';
}

function markDelivered(btn, orderId) {
    btn.disabled = true;
    btn.textContent = '…';

    fetch(`/order/${orderId}/deliver`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const li = btn.closest('li');
            li.style.transition = 'opacity 0.3s';
            li.style.opacity = '0';
            setTimeout(() => {
                const ul = li.closest('ul');
                li.remove();
                // Als de lijst leeg is, verberg de hele kaart
                if (ul && ul.querySelectorAll('li').length === 0) {
                    const card = ul.closest('.bg-white');
                    if (card) {
                        card.style.transition = 'opacity 0.3s';
                        card.style.opacity = '0';
                        setTimeout(() => card.remove(), 300);
                    }
                }
            }, 300);
        } else {
            btn.disabled = false;
            btn.textContent = '✓ Afgeleverd';
            alert(data.message ?? 'Fout bij afleveren.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = '✓ Afgeleverd';
    });
}
</script>
</x-app-layout>
