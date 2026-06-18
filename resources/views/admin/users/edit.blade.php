<x-admin-layout>
    <x-slot name="header">Gebruiker bewerken</x-slot>

    @if (Auth::user()?->role !== 'admin')
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg">
            U heeft geen toegang tot deze pagina.
        </div>
    @else
    <div class="max-w-2xl">
        <div class="bg-white shadow-sm rounded-xl p-6">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Naam</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                    <select name="role" id="role-select"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="magazijnBeheerder" {{ old('role', $user->role) === 'magazijnBeheerder' ? 'selected' : '' }}>Magazijnbeheerder</option>
                        <option value="technieker" {{ old('role', $user->role) === 'technieker' ? 'selected' : '' }}>Technieker</option>
                    </select>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Standaard leveringsadres (enkel voor techniekers) --}}
                <div class="mb-6" id="warehouse-field" style="{{ old('role', $user->role) !== 'technieker' ? 'display:none' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Standaard leveringsadres
                        <span class="ml-1 text-xs font-normal text-gray-400">(wordt automatisch ingevuld bij bestellingen)</span>
                    </label>
                    <select name="default_warehouse_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Geen standaard —</option>
                        @foreach ($warehouses as $w)
                            <option value="{{ $w->id }}"
                                {{ old('default_warehouse_id', $user->default_warehouse_id) == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}{{ $w->address ? ' — ' . $w->address : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('default_warehouse_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Opslaan
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:underline px-3 py-2">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
    @endif

<script>
    const roleSelect = document.getElementById('role-select');
    const warehouseField = document.getElementById('warehouse-field');
    if (roleSelect) {
        roleSelect.addEventListener('change', () => {
            warehouseField.style.display = roleSelect.value === 'technieker' ? '' : 'none';
        });
    }
</script>
</x-admin-layout>
