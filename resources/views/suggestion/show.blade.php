<x-app-layout>
    <x-slot name="header">{{ $suggestion->title }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white shadow-sm rounded-xl p-6 space-y-4">
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Ingediend door</span>
                <p class="text-gray-800 font-medium">{{ $suggestion->user->name }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Beschrijving</span>
                <p class="text-gray-700 mt-1">{{ $suggestion->description }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Status</span>
                <p>
                    @if ($suggestion->status === 'goedgekeurd')
                        <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-1 rounded-full">Goedgekeurd</span>
                    @elseif ($suggestion->status === 'afgekeurd')
                        <span class="bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full">Afgekeurd</span>
                    @else
                        <span class="bg-yellow-100 text-yellow-700 text-xs font-medium px-2 py-1 rounded-full">In behandeling</span>
                    @endif
                </p>
            </div>
            <div class="pt-4">
                <a href="{{ route('suggestion.index') }}" class="text-sm text-gray-500 hover:underline">← Terug</a>
            </div>
        </div>
    </div>
</x-app-layout>
