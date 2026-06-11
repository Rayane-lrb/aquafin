<x-app-layout>
    <x-slot name="header">Bestellingen</x-slot>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header acties --}}
    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">
            @if(Auth::user()->role === 'technieker')
                Jouw bestellingen en leveringsstatus
            @else
                Alle bestellingen beheren en afleveren
            @endif
        </p>
        <a href="{{ route('order.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Nieuwe bestelling
        </a>
    </div>

    {{-- Status filter tabs --}}
    @php
        $tab = request('tab', 'alle');
        $tabs = [
            'alle'          => ['label' => 'Alle',          'count' => $orders->count()],
            'in behandeling'=> ['label' => 'In behandeling','count' => $orders->where('status','in behandeling')->count()],
            'goedgekeurd'   => ['label' => 'Goedgekeurd',   'count' => $orders->where('status','goedgekeurd')->count()],
            'geleverd'      => ['label' => 'Geleverd',      'count' => $orders->where('status','geleverd')->count()],
            'afgekeurd'     => ['label' => 'Afgekeurd',     'count' => $orders->where('status','afgekeurd')->count()],
        ];
    @endphp

    <div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-xl overflow-x-auto w-full sm:w-fit">
        @foreach ($tabs as $key => $t)
            <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition whitespace-nowrap
                      {{ $tab === $key ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $t['label'] }}
                @if ($t['count'] > 0)
                    <span class="ml-1 {{ $tab === $key ? 'text-blue-600' : 'text-gray-400' }}">{{ $t['count'] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Tabel --}}
    <div class="bg-white shadow-sm rounded-xl overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">Product</th>
                    @if(Auth::user()->role !== 'technieker')
                        <th class="px-6 py-3">Aangevraagd door</th>
                    @endif
                    <th class="px-6 py-3">Aantal</th>
                    <th class="px-6 py-3">Leveringsadres</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php
                    $filtered = $tab === 'alle'
                        ? $orders
                        : $orders->where('status', $tab);
                @endphp

                @forelse ($filtered as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $order->product->name }}</td>

                    @if(Auth::user()->role !== 'technieker')
                        <td class="px-6 py-4">
                            <div class="text-gray-800 font-medium leading-tight">{{ $order->user->name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                    @endif

                    <td class="px-6 py-4 text-gray-700">{{ $order->quantity }}</td>

                    {{-- Leveringsadres --}}
                    <td class="px-6 py-4">
                        @if ($order->warehouse)
                            <div class="text-gray-800 font-medium leading-tight">{{ $order->warehouse->name }}</div>
                            @if ($order->warehouse->address)
                                <div class="text-xs text-gray-400 mt-0.5">{{ $order->warehouse->address }}</div>
                            @endif
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Status badge --}}
                    <td class="px-6 py-4">
                        @if ($order->status === 'geleverd')
                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Geleverd
                            </span>
                        @elseif ($order->status === 'goedgekeurd')
                            <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Goedgekeurd
                            </span>
                        @elseif ($order->status === 'afgekeurd')
                            <span class="inline-flex items-center gap-1 bg-red-100 text-red-600 text-xs font-medium px-2.5 py-1 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Afgekeurd
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                In behandeling
                            </span>
                        @endif
                    </td>

                    {{-- Acties --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-2 flex-wrap items-center">

                            {{-- Bewerken: enkel maker of admin, enkel in behandeling --}}
                            @if ($order->status === 'in behandeling')
                                @if ($order->user_id === Auth::id() || Auth::user()?->role === 'admin')
                                    <a href="{{ route('order.edit', $order->id) }}"
                                       class="text-xs font-medium bg-gray-50 text-gray-600 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition border border-gray-200">
                                        Bewerken
                                    </a>
                                @endif

                                {{-- Goedkeuren / Weigeren: admin en magazijnBeheerder --}}
                                @if (Auth::user()?->role === 'magazijnBeheerder' || Auth::user()?->role === 'admin')
                                    <form action="{{ route('order.approve', $order->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-xs font-medium bg-green-50 text-green-600 hover:bg-green-100 px-3 py-1.5 rounded-lg transition border border-green-200">
                                            Goedkeuren
                                        </button>
                                    </form>
                                    <form action="{{ route('order.reject', $order->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-xs font-medium bg-red-50 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-lg transition border border-red-200">
                                            Weigeren
                                        </button>
                                    </form>
                                @endif
                            @endif

                            {{-- Lever af: admin en magazijnBeheerder, enkel goedgekeurd --}}
                            @if ($order->status === 'goedgekeurd')
                                @if (Auth::user()?->role === 'magazijnBeheerder' || Auth::user()?->role === 'admin')
                                    <form action="{{ route('order.deliver', $order->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                            </svg>
                                            Lever af
                                        </button>
                                    </form>
                                @endif
                            @endif

                            {{-- Geen acties beschikbaar --}}
                            @if (!in_array($order->status, ['in behandeling', 'goedgekeurd']))
                                <span class="text-gray-300 text-xs">—</span>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto mb-2 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Geen bestellingen gevonden.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Legende voor technieker --}}
    @if(Auth::user()->role === 'technieker')
        <div class="mt-6 bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-700">
            <p class="font-medium mb-2">Wat betekent de status?</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="flex items-center gap-2">
                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">In behandeling</span>
                    <span class="text-blue-600">Wachten op goedkeuring</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Goedgekeurd</span>
                    <span class="text-blue-600">Wordt voorbereid</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Geleverd</span>
                    <span class="text-blue-600">Klaar op het magazijn</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Afgekeurd</span>
                    <span class="text-blue-600">Niet verwerkt</span>
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
