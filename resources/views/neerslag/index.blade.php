<x-app-layout>
    <x-slot name="header">Neerslag</x-slot>

    {{-- Locatiekiezer --}}
    @if (isset($warehouses) && $warehouses->count() > 1)
    <form method="GET" action="{{ route('neerslag.index') }}" class="mb-5">
        <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">📍 Locatie:</label>
            <select name="warehouse_id" onchange="this.form.submit()"
                class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none">
                @foreach ($warehouses as $wh)
                    <option value="{{ $wh->id }}" {{ isset($warehouse) && $warehouse->id === $wh->id ? 'selected' : '' }}>
                        {{ $wh->name }}
                    </option>
                @endforeach
            </select>
            @if (isset($warehouse))
                <span class="text-xs text-gray-400">{{ $warehouse->latitude }}, {{ $warehouse->longitude }}</span>
            @endif
        </div>
    </form>
    @endif

    @if (isset($error))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            {{ $error }}
        </div>
    @else

    {{-- Huidige situatie --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Neerslag nu</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ $current['precipitation'] ?? 0 }}<span class="text-sm font-normal text-gray-400 ml-1">mm</span>
                </p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-10h-1M4.34 12h-1m15.07-6.07l-.71.71M6.34 17.66l-.71.71m12.02 0l-.71-.71M6.34 6.34l-.71-.71M12 7a5 5 0 100 10A5 5 0 0012 7z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Temperatuur</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ $current['temperature_2m'] ?? '—' }}<span class="text-sm font-normal text-gray-400 ml-1">°C</span>
                </p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Regen nu</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ $current['rain'] ?? 0 }}<span class="text-sm font-normal text-gray-400 ml-1">mm</span>
                </p>
            </div>
        </div>

    </div>

    {{-- Aanbevolen producten op basis van neerslag --}}
    @if ($rainLevel !== 'droog' && $recommendedProducts->isNotEmpty())
    @php
        $levelConfig = match ($rainLevel) {
            'zwaar' => ['label' => 'Zware regen verwacht', 'icon' => '⛈️', 'color' => 'blue', 'tip' => 'Bij zware neerslag zijn pompen, leidingen en inspectiecamera\'s essentieel.'],
            'matig' => ['label' => 'Matige regen verwacht', 'icon' => '🌧️', 'color' => 'sky', 'tip' => 'Controleer afvoerleidingen en zorg voor afdichtingsmaterialen.'],
            'licht' => ['label' => 'Lichte regen verwacht', 'icon' => '🌦️', 'color' => 'indigo', 'tip' => 'Handige materialen voor lichte weersomstandigheden.'],
            default => ['label' => '', 'icon' => '', 'color' => 'gray', 'tip' => ''],
        };
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-{{ $levelConfig['color'] }}-200 p-6 mb-6">
        <div class="flex items-center gap-3 mb-1">
            <span class="text-2xl">{{ $levelConfig['icon'] }}</span>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">{{ $levelConfig['label'] }} — Aanbevolen producten</h2>
                <p class="text-xs text-gray-400">{{ $levelConfig['tip'] }}</p>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach ($recommendedProducts as $product)
            <a href="{{ route('product.show', $product->id) }}"
               class="group bg-gray-50 rounded-xl p-3 hover:bg-{{ $levelConfig['color'] }}-50 hover:shadow-sm transition flex flex-col items-center text-center gap-2">
                <div class="w-14 h-14 flex items-center justify-center">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200">
                    @else
                        <div class="w-14 h-14 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/></svg>
                        </div>
                    @endif
                </div>
                <p class="text-xs font-medium text-gray-700 leading-tight line-clamp-2">{{ $product->name }}</p>
                <span class="text-xs text-gray-400">{{ $product->stock }} op stock</span>
            </a>
            @endforeach
        </div>

        <div class="mt-4 text-right">
            <a href="{{ route('product.index') }}" class="text-xs text-{{ $levelConfig['color'] }}-600 hover:underline font-medium">
                Alle producten bekijken →
            </a>
        </div>
    </div>
    @endif

    {{-- Gemiddelde neerslag deze week --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-5">Gemiddelde neerslag deze week</h2>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-blue-700">{{ $weekTotal }}<span class="text-base font-normal text-blue-400 ml-1">mm</span></p>
                <p class="text-xs text-gray-400 mt-1">Totaal deze week</p>
            </div>
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-blue-700">{{ $weekAvg }}<span class="text-base font-normal text-blue-400 ml-1">mm</span></p>
                <p class="text-xs text-gray-400 mt-1">Gemiddeld per dag</p>
            </div>
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-blue-700">{{ $rainyDays }}<span class="text-base font-normal text-blue-400">/7</span></p>
                <p class="text-xs text-gray-400 mt-1">Dagen met neerslag</p>
            </div>
        </div>

        @php $maxMM = max(collect($days)->max('precip_sum'), 0.1); @endphp
        <div class="flex items-end gap-2">
            @foreach ($days as $i => $day)
            @php
                $height   = max(($day['precip_sum'] / $maxMM) * 100, $day['precip_sum'] > 0 ? 4 : 0);
                $barColor = $day['precip_sum'] > 5 ? '#1d4ed8' : ($day['precip_sum'] > 0 ? '#60a5fa' : '#e5e7eb');
                $isToday  = $i === 0;
            @endphp
            <div class="flex flex-col items-center gap-1 flex-1">
                <span class="text-xs font-medium text-blue-600">{{ $day['precip_sum'] > 0 ? $day['precip_sum'] : '' }}</span>
                <div class="w-full bg-gray-100 rounded-t-lg flex items-end" style="height: 80px;">
                    <div class="w-full rounded-t-lg" style="height: {{ $height }}%; background: {{ $barColor }};"></div>
                </div>
                <span class="text-xs {{ $isToday ? 'text-blue-700 font-semibold' : 'text-gray-400' }} text-center">
                    {{ $isToday ? 'Vandaag' : $day['date'] }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Voorspelling komende 3 maanden --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-1">Neerslag voorspelling komende 2 weken</h2>
        <p class="text-xs text-gray-400 mb-5">Open-Meteo voorspelling per week · Antwerpen</p>

        @php $maxMonthly = max(collect($monthlyAvg)->max('total'), 0.1); @endphp

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach ($monthlyAvg as $month)
           @php
            $isCurrent = false;
            @endphp
            <div class="bg-blue-50 rounded-xl p-5 border {{ $isCurrent ? 'border-blue-300' : 'border-transparent' }}">
                <p class="text-sm font-semibold text-gray-700 capitalize mb-3">{{ $month['name'] }}</p>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Totaal neerslag</span>
                        <span class="text-sm font-bold text-blue-700">{{ $month['total'] }} mm</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Gem. per dag</span>
                        <span class="text-sm font-bold text-blue-700">{{ $month['avg'] }} mm</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Regendagen</span>
                        <span class="text-sm font-bold text-blue-700">{{ $month['rainy'] }}/{{ $month['days'] }}</span>
                    </div>
                </div>
                @php
                    $pct = min(100, ($month['total'] / $maxMonthly) * 100);
                @endphp
                <div class="mt-3 bg-blue-100 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full bg-blue-500" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-300 mt-4 text-right">mm/maand · Open-Meteo</p>
    </div>

    @endif
</x-app-layout>