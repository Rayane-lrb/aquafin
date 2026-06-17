<x-app-layout>
    <x-slot name="header">Suggesties</x-slot>

    <div class="mb-4">
        <form method="GET" class="flex gap-2">
            <select name="status" onchange="this.form.submit()"
                    class="rounded-xl border-gray-200 text-sm">
                <option value="">Alle statussen</option>
                <option value="in behandeling" {{ request('status') === 'in behandeling' ? 'selected' : '' }}>In behandeling</option>
                <option value="goedgekeurd" {{ request('status') === 'goedgekeurd' ? 'selected' : '' }}>Goedgekeurd</option>
                <option value="afgekeurd" {{ request('status') === 'afgekeurd' ? 'selected' : '' }}>Afgekeurd</option>
            </select>
        </form>
    </div>

    <div class="space-y-3">
        @forelse ($suggestions as $suggestion)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-semibold text-gray-800">{{ $suggestion->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $suggestion->description }}</p>
                        <p class="text-xs text-gray-400 mt-2">
                            Door {{ $suggestion->user?->name ?? 'Onbekend' }} ·
                            {{ $suggestion->created_at->isoFormat('D MMM YYYY') }}
                        </p>
                    </div>
                    <span class="shrink-0 text-xs font-medium px-2.5 py-1 rounded-full
                        {{ $suggestion->status === 'in behandeling' ? 'bg-yellow-50 text-yellow-700' : '' }}
                        {{ $suggestion->status === 'goedgekeurd' ? 'bg-green-50 text-green-700' : '' }}
                        {{ $suggestion->status === 'afgekeurd' ? 'bg-gray-100 text-gray-500' : '' }}">
                        {{ $suggestion->status }}
                    </span>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                <p class="text-gray-400">Geen suggesties gevonden.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $suggestions->links() }}
    </div>
</x-app-layout>
