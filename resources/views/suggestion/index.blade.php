<x-app-layout>
    <x-slot name="header">Suggesties</x-slot>

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Overzicht van alle suggesties</p>
        <a href="{{ route('suggestion.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Nieuwe suggestie
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">Titel</th>
                    <th class="px-6 py-3">Ingediend door</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($suggestions as $suggestion)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $suggestion->title }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $suggestion->user->name }}</td>
                    <td class="px-6 py-4">
                        @if ($suggestion->status === 'goedgekeurd')
                            <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-1 rounded-full">Goedgekeurd</span>
                        @elseif ($suggestion->status === 'afgekeurd')
                            <span class="bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full">Afgekeurd</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 text-xs font-medium px-2 py-1 rounded-full">In behandeling</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 flex gap-3">
                        <a href="{{ route('suggestion.show', $suggestion->id) }}" class="text-blue-600 hover:underline">Bekijken</a>
                        @if (Auth::user()?->role === 'admin' && $suggestion->status === 'in behandeling')
                            <form action="{{ route('suggestion.approve', $suggestion->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-green-600 hover:underline">Goedkeuren</button>
                            </form>
                            <form action="{{ route('suggestion.reject', $suggestion->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-red-500 hover:underline">Afkeuren</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">Geen suggesties gevonden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
