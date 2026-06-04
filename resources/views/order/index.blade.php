<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Commandes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <a href="{{ route('order.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">
                    Nouvelle commande
                </a>

                <table class="w-full mt-4 text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Produit</th>
                            <th class="py-2">Demandé par</th>
                            <th class="py-2">Quantité</th>
                            <th class="py-2">Statut</th>
                            <th class="py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                        <tr class="border-b">
                            <td class="py-2">{{ $order->product->name }}</td>
                            <td class="py-2">{{ $order->user->name }}</td>
                            <td class="py-2">{{ $order->quantity }}</td>
                            <td class="py-2">{{ $order->status }}</td>
                            <td class="py-2 flex gap-2">
                                @if ($order->status === 'pending')
                                    <form action="{{ route('order.approve', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-500">Approuver</button>
                                    </form>
                                    <form action="{{ route('order.reject', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-red-500">Rejeter</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>