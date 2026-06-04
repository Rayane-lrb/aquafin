<x-app-layout>
    <x-slot name="header">Mijn Orders</x-slot>

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Overzicht van alle bestellingen</p>
        <a href="{{ route('order.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Nieuwe bestelling
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">Product</th>
                    <th class="px-6 py-3">Aangevraagd door</th>
                    <th class="px-6 py-3">Aantal</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $order->product->name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $order->user->name }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $order->quantity }}</td>
                    <td class="px-6 py-4">
                        @if ($order->status === 'approved')
                            <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-1 rounded-full">Goedgekeurd</span>
                        @elseif ($order->status === 'rejected')
                            <span class="bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full">Geweigerd</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 text-xs font-medium px-2 py-1 rounded-full">In behandeling</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 flex gap-3">
                        @if ($order->status === 'pending')
                            <form action="{{ route('order.approve', $order->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 hover:underline">Goedkeuren</button>
                            </form>
                            <form action="{{ route('order.reject', $order->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-red-500 hover:underline">Weigeren</button>
                            </form>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Geen bestellingen gevonden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
