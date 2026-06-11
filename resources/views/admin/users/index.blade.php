<x-admin-layout>
    <x-slot name="header">Gebruikersbeheer</x-slot>

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Overzicht van alle gebruikers</p>
        @if (Auth::user()?->role === 'admin')
            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Gebruiker aanmaken
            </a>
        @endif
    </div>

    <div class="bg-white shadow-sm rounded-xl overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">Naam</th>
                    <th class="px-6 py-3">E-mail</th>
                    <th class="px-6 py-3">Rol</th>
                    <th class="px-6 py-3">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @if ($user->role === 'admin')
                            <span class="bg-[#526a8e] text-white text-xs font-medium px-2 py-1 rounded-full">Admin</span>
                        @elseif ($user->role === 'magazijnBeheerder')
                            <span class="bg-[#34c49a] text-white text-xs font-medium px-2 py-1 rounded-full">Magazijnbeheerder</span>
                        @else
                            <span class="bg-[#2d57cc] text-white text-xs font-medium px-2 py-1 rounded-full">Technieker</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 flex items-center gap-3">
                        @if (Auth::user()?->role === 'admin')
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 hover:underline">Bewerken</a>

                            @if (Auth::user()?->id !== $user->id)
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Ben je zeker dat je deze gebruiker wil verwijderen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Verwijderen</button>
                                </form>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">Geen gebruikers gevonden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>