<x-app-layout>
    <x-slot name="header">Bestellingen — Magazijn</x-slot>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

    {{-- Zoekbalk --}}
    <form method="GET" action="{{ route('order.magazijn') }}" class="mb-6">
        <div class="flex gap-2">
            <div class="relative flex-1 max-w-sm">
                <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                </div>
                <input type="text" name="q" value="{{ $query ?? '' }}"
                    placeholder="Zoek op persoon, product of magazijn…"
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
            </div>
            <button type="submit"
                class="text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                Zoeken
            </button>
            @if($query)
            <a href="{{ route('order.magazijn') }}"
                class="text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg transition">
                ✕ Wissen
            </a>
            @endif
        </div>
        @if($query)
        <p class="mt-2 text-xs text-gray-400">Resultaten voor "<span class="font-medium text-gray-600">{{ $query }}</span>"</p>
        @endif
    </form>

    {{-- Tellers --}}
    @php
        $urgentCount   = $pendingGroups->filter(fn($g) => $g->first()->urgent)->count()
                       + $approvedGroups->filter(fn($g) => $g->first()->urgent)->count();
        $pendingCount  = $pendingGroups->count();
        $approvedCount = $approvedGroups->count();
        $archiveCount  = $archiveGroups->count();
    @endphp

    <div class="flex flex-wrap gap-3 mb-8">
        @if($urgentCount > 0)
        <div class="flex items-center gap-2 bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-xl shadow animate-pulse">
            🚨 {{ $urgentCount }} DRINGEND{{ $urgentCount > 1 ? 'E' : '' }}
        </div>
        @endif
        <div class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm font-medium px-4 py-2 rounded-xl">
            ⏳ {{ $pendingCount }} te beslissen
        </div>
        <div class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 text-sm font-medium px-4 py-2 rounded-xl">
            📦 {{ $approvedCount }} te leveren
        </div>
        <div class="ml-auto">
            <a href="{{ route('order.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Nieuwe bestelling
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         SECTIE 1 : ⏳ TE BESLISSEN
    ══════════════════════════════════════════ --}}
    <div class="mb-8">
        <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">
            ⏳ Te beslissen — {{ $pendingCount }} dossier{{ $pendingCount !== 1 ? 's' : '' }}
        </h2>

        @forelse ($pendingGroups as $groupId => $items)
            @php
                $first   = $items->first();
                $urgent  = $first->urgent ?? false;
                $isSolo  = str_starts_with((string)$groupId, 'solo-');
                $canGroup = !$isSolo;
            @endphp

            <div class="mb-4 rounded-xl shadow-sm overflow-hidden {{ $urgent ? 'border-2 border-red-500' : 'border border-gray-200' }}">

                {{-- Bannière urgent --}}
                @if($urgent)
                    <div class="bg-red-600 text-white text-xs font-bold px-4 py-1.5 flex items-center gap-2">
                        🚨 DRINGENDE BESTELLING — voorrangsbehandeling vereist
                    </div>
                @endif

                {{-- Header dossier --}}
                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 {{ $urgent ? 'bg-red-50' : 'bg-gray-50' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $urgent ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }} flex items-center justify-center font-bold text-sm shrink-0">
                            {{ strtoupper(substr($first->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <span class="font-semibold text-sm text-gray-900">{{ $first->user->name ?? '—' }}</span>
                            <span class="text-gray-400 text-xs ml-2">{{ $first->created_at->format('d/m/Y H:i') }}</span>
                            @if($first->warehouse)
                                <span class="text-gray-400 text-xs ml-2">· {{ $first->warehouse->name }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions groupe --}}
                    @if($canGroup)
                    <div class="flex items-center gap-2 flex-wrap">
                        <form action="{{ route('order.group.approve', $groupId) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="text-xs font-semibold bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg transition">
                                ✓ Alles goedkeuren
                            </button>
                        </form>
                        <form action="{{ route('order.group.reject', $groupId) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="text-xs font-medium bg-white text-red-600 hover:bg-red-50 px-4 py-1.5 rounded-lg border border-red-200 transition">
                                ✗ Alles weigeren
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                {{-- Artikelen --}}
                <div class="bg-white divide-y divide-gray-100">
                    @foreach($items as $order)
                    <div class="flex items-center justify-between px-4 py-3 gap-4">
                        <div class="flex-1 min-w-0">
                            <span class="font-medium text-gray-900 text-sm">{{ $order->product->name ?? '—' }}</span>
                            @if(!empty($order->product->barcode))
                                <span class="ml-2 font-mono text-xs text-gray-400 tracking-widest">{{ $order->product->barcode }}</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 font-semibold shrink-0">× {{ $order->quantity }}</div>
                        <div class="flex gap-1.5 shrink-0">
                            <form action="{{ route('order.approve', $order->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button title="Goedkeuren" class="w-7 h-7 flex items-center justify-center bg-green-50 text-green-600 hover:bg-green-100 rounded-lg border border-green-200 text-xs font-bold">✓</button>
                            </form>
                            <form action="{{ route('order.reject', $order->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button title="Weigeren" class="w-7 h-7 flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-100 rounded-lg border border-red-200 text-xs font-bold">✗</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Leverdatum instellen --}}
                @if($canGroup)
                <div class="px-4 py-2 bg-gray-50 border-t border-gray-100 flex items-center gap-3">
                    <span class="text-xs text-gray-500">📅 Leverdatum:</span>
                    <form action="{{ route('order.group.deliveryDate', $groupId) }}" method="POST" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="date" name="delivery_date"
                            value="{{ $first->delivery_date ?? '' }}"
                            min="{{ date('Y-m-d') }}"
                            class="text-xs border border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <button type="submit" class="text-xs font-medium text-blue-600 hover:underline">Opslaan</button>
                    </form>
                    @if($first->delivery_date)
                        <span class="text-xs font-semibold text-blue-600">→ {{ \Carbon\Carbon::parse($first->delivery_date)->format('d/m/Y') }}</span>
                    @endif
                </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center text-gray-400 text-sm">
                Geen openstaande bestellingen.
            </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════════
         SECTIE 2 : 📦 TE LEVEREN
    ══════════════════════════════════════════ --}}
    <div class="mb-8">
        <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">
            📦 Te leveren — {{ $approvedCount }} dossier{{ $approvedCount !== 1 ? 's' : '' }}
        </h2>

        @forelse ($approvedGroups as $groupId => $items)
            @php
                $first  = $items->first();
                $urgent = $first->urgent ?? false;
                $isSolo = str_starts_with((string)$groupId, 'solo-');
            @endphp

            <div class="mb-4 rounded-xl shadow-sm overflow-hidden {{ $urgent ? 'border-2 border-red-500' : 'border border-gray-200' }}">

                @if($urgent)
                    <div class="bg-red-600 text-white text-xs font-bold px-4 py-1.5 flex items-center gap-2">
                        🚨 DRINGEND
                    </div>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 {{ $urgent ? 'bg-red-50' : 'bg-blue-50' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $urgent ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center font-bold text-sm shrink-0">
                            {{ strtoupper(substr($first->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <span class="font-semibold text-sm text-gray-900">{{ $first->user->name ?? '—' }}</span>
                            @if($first->warehouse)
                                <span class="text-gray-500 text-xs ml-2">→ {{ $first->warehouse->name }}</span>
                            @endif
                            @if($first->delivery_date)
                                @php $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($first->delivery_date), false); @endphp
                                <span class="ml-2 text-xs font-semibold {{ $daysLeft <= 1 ? 'text-red-600' : ($daysLeft <= 3 ? 'text-orange-500' : 'text-green-600') }}">
                                    📅 {{ \Carbon\Carbon::parse($first->delivery_date)->format('d/m/Y') }}
                                    @if($daysLeft < 0) (te laat!) @elseif($daysLeft === 0) (vandaag!) @elseif($daysLeft === 1) (morgen) @endif
                                </span>
                            @else
                                <span class="ml-2 text-xs text-gray-400 italic">geen datum</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        {{-- Date instellen --}}
                        @if(!$isSolo)
                        <form action="{{ route('order.group.deliveryDate', $groupId) }}" method="POST" class="flex items-center gap-1.5">
                            @csrf @method('PATCH')
                            <input type="date" name="delivery_date"
                                value="{{ $first->delivery_date ?? '' }}"
                                min="{{ date('Y-m-d') }}"
                                class="text-xs border border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <button type="submit" class="text-xs text-blue-600 hover:underline font-medium">📅</button>
                        </form>
                        @endif

                        {{-- Afleveren --}}
                        @if(!$isSolo)
                        <form action="{{ route('order.group.deliver', $groupId) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-1.5 rounded-lg transition">
                                ✓ Afgeleverd
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Artikelen avec barcodes (picklist) --}}
                <div class="bg-white divide-y divide-gray-100">
                    @foreach($items as $order)
                    <div class="flex items-center justify-between px-4 py-3 gap-4">
                        <div class="flex-1 min-w-0">
                            <span class="font-medium text-gray-900 text-sm">{{ $order->product->name ?? '—' }}</span>
                            @if(!empty($order->product->barcode))
                                <span class="ml-3 font-mono text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded tracking-widest">{{ $order->product->barcode }}</span>
                            @endif
                        </div>
                        <div class="text-sm font-bold text-gray-800 shrink-0">× {{ $order->quantity }}</div>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full shrink-0">Goedgekeurd</span>
                    </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center text-gray-400 text-sm">
                Niets klaar om te leveren.
            </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════════
         SECTIE 3 : ✓ ARCHIEF (collapsed)
    ══════════════════════════════════════════ --}}
    <div>
        <button onclick="document.getElementById('archive').classList.toggle('hidden')"
            class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-gray-600 transition mb-3">
            <span>✓ Archief — {{ $archiveCount }} dossier{{ $archiveCount !== 1 ? 's' : '' }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div id="archive" class="hidden space-y-3">
            @forelse ($archiveGroups as $groupId => $items)
                @php $first = $items->first(); @endphp
                <div class="rounded-xl border border-gray-200 overflow-hidden opacity-70">
                    <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-2 bg-gray-50">
                        <div>
                            <span class="text-sm font-medium text-gray-700">{{ $first->user->name ?? '—' }}</span>
                            <span class="text-xs text-gray-400 ml-2">{{ $first->created_at->format('d/m/Y') }}</span>
                            @if($first->warehouse)
                                <span class="text-xs text-gray-400 ml-2">· {{ $first->warehouse->name }}</span>
                            @endif
                        </div>
                        @php
                            $allDelivered = $items->every(fn($o) => $o->status === 'geleverd');
                        @endphp
                        @if($allDelivered)
                            <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">✓ Geleverd</span>
                        @else
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">✗ Afgekeurd</span>
                        @endif
                    </div>
                    <div class="bg-white divide-y divide-gray-100">
                        @foreach($items as $order)
                        <div class="flex items-center justify-between px-4 py-2 gap-4">
                            <span class="text-sm text-gray-600">{{ $order->product->name ?? '—' }}</span>
                            @if(!empty($order->product->barcode))
                                <span class="font-mono text-xs text-gray-400">{{ $order->product->barcode }}</span>
                            @endif
                            <span class="text-sm text-gray-500">× {{ $order->quantity }}</span>
                            @if($order->status === 'geleverd')
                                <span class="text-xs text-emerald-600">✓</span>
                            @else
                                <span class="text-xs text-red-500">✗</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 text-sm py-4">Geen archief.</div>
            @endforelse
        </div>
    </div>

</x-app-layout>
