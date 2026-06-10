<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">


        

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">Weergave</h2>
                            <p class="mt-1 text-sm text-gray-600">Kies of je de website in lichte of donkere modus wil gebruiken.</p>
                        </header>
                        <div class="mt-6" x-data="{ dark: document.documentElement.classList.contains('dark') }">
                            <div class="flex items-center gap-4">
                                <button @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('darkMode', dark);"
                                    :class="dark ? 'bg-blue-600' : 'bg-gray-300'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                    <span :class="dark ? 'translate-x-6' : 'translate-x-1'"
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition"></span>
                                </button>
                                <span class="text-sm text-gray-700" x-text="dark ? 'Donkere modus' : 'Lichte modus'"></span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('userzone.profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('userzone.profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('userzone.profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
