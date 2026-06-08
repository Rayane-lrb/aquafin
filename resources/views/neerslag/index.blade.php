<x-app-layout>
    <x-slot name="header">Neerslag</x-slot>

    @if (isset($error))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            {{ $error }}
        </div>
    @else

    {{-- Huidige situatie --}}
    <div class="grid grid-cols-3 gap-4 mb-6">

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

    {{-- Komende 24 uur --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-sm font-semibold text-gray-800">Komende 24 uur</h2>
            <span class="text-xs text-gray-400">mm · kans%</span>
        </div>
        <div class="flex gap-1 w-full pb-2">
                @foreach ($hoursToday as $hour)
                @php
                    $height = min(100, ($hour['precip'] / 5) * 100);
                    $barColor = $hour['precip'] > 2 ? '#3b82f6' : ($hour['precip'] > 0.5 ? '#93c5fd' : '#dbeafe');
                    $isNow = $loop->first;
                @endphp
                <div class="flex flex-col items-center gap-1.5 flex-1 min-w-0 {{ $isNow ? 'opacity-100' : 'opacity-80 hover:opacity-100' }} transition-opacity">
                    <span class="text-xs {{ $isNow ? 'text-blue-600 font-semibold' : 'text-gray-400' }}">
                        {{ $isNow ? 'Nu' : $hour['time'] }}
                    </span>
                    <div class="w-full bg-gray-100 rounded-full overflow-hidden flex items-end" style="height: 64px;">
                        <div class="w-full rounded-full transition-all" style="height: {{ max(3, $height) }}%; background: {{ $barColor }};"></div>
                    </div>
                    <span class="text-xs font-semibold text-gray-600">{{ $hour['precip'] }}</span>
                    <span class="text-xs text-blue-400 font-medium">{{ $hour['probability'] }}%</span>
                </div>
                @endforeach
        </div>
    </div>

    {{-- 7-daagse voorspelling --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-5">7-daagse voorspelling</h2>
        <div class="space-y-1">
            @foreach ($days as $i => $day)
            @php
                $pct      = min(100, ($day['precip_sum'] / 20) * 100);
                $barColor = $day['precip_sum'] > 5 ? '#3b82f6' : ($day['precip_sum'] > 1 ? '#93c5fd' : '#dbeafe');
                $probColor = $day['probability'] >= 70 ? 'text-blue-600' : ($day['probability'] >= 40 ? 'text-amber-500' : 'text-gray-300');
                $isToday  = $i === 0;
            @endphp
            <div class="flex items-center gap-4 px-3 py-3 rounded-xl {{ $isToday ? 'bg-blue-50' : 'hover:bg-gray-50' }} transition-colors">

                <span class="text-sm w-20 capitalize {{ $isToday ? 'text-blue-700 font-semibold' : 'text-gray-600 font-medium' }}">
                    {{ $isToday ? 'Vandaag' : $day['date'] }}
                </span>

                <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="h-1.5 rounded-full transition-all" style="width: {{ max(2, $pct) }}%; background: {{ $barColor }};"></div>
                </div>

                <div class="flex items-center gap-4 text-right">
                    <span class="text-sm font-semibold text-gray-700 w-12">{{ $day['precip_sum'] }} <span class="text-xs font-normal text-gray-400">mm</span></span>
                    <span class="text-xs text-gray-400 w-8">{{ $day['precip_h'] }}u</span>
                    <span class="text-xs font-bold w-8 {{ $probColor }}">{{ $day['probability'] }}%</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-50">
            <p class="text-xs text-gray-300">mm = neerslag · u = uren · % = kans</p>
            <p class="text-xs text-gray-300">Open-Meteo · Antwerpen</p>
        </div>
    </div>

    @endif
</x-app-layout>
