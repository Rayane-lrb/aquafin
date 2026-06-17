<x-app-layout>
    <x-slot name="header">Nieuwe suggestie</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white shadow-sm rounded-xl p-6">
            <form action="{{ route('suggestion.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titel</label>
                    <input type="text" name="title" value="{{ old('title', request('title')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Beschrijving</label>
                    <textarea name="description" rows="4"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Foto --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto (optioneel)</label>

                    {{-- Preview --}}
                    <div id="preview-wrapper" class="hidden mb-3">
                        <img id="preview-img" src="" alt="Preview"
                            class="w-full max-h-48 object-cover rounded-lg border border-gray-200">
                    </div>

                    <div class="flex gap-2">
                        {{-- Foto nemen met camera --}}
                        <label class="flex-1 flex items-center justify-center gap-2 cursor-pointer border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Foto nemen
                            <input type="file" name="image" accept="image/*" capture="environment"
                                class="hidden" id="camera-input">
                        </label>

                        {{-- Uploaden uit galerij --}}
                        <label class="flex-1 flex items-center justify-center gap-2 cursor-pointer border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Uploaden
                            <input type="file" name="image" accept="image/*"
                                class="hidden" id="gallery-input">
                        </label>
                    </div>

                    <p id="file-name" class="text-xs text-gray-400 mt-1 hidden"></p>
                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Indienen
                    </button>
                    <a href="{{ route('suggestion.index') }}" class="text-sm text-gray-500 hover:underline px-3 py-2">Annuleren</a>
                </div>
            </form>

            <script>
                // Les deux boutons partagent le même champ `image` dans le form.
                // Quand l'un est utilisé, on synchronise l'autre et on affiche la preview.
                const cameraInput  = document.getElementById('camera-input');
                const galleryInput = document.getElementById('gallery-input');
                const previewWrapper = document.getElementById('preview-wrapper');
                const previewImg   = document.getElementById('preview-img');
                const fileName     = document.getElementById('file-name');

                function handleFile(file, sourceInput, targetInput) {
                    if (!file) return;

                    // Sync le fichier vers l'autre input (on ne peut pas, mais on cache l'autre)
                    // On renomme l'input source "image" pour que le form l'envoie
                    cameraInput.name  = '';
                    galleryInput.name = '';
                    sourceInput.name  = 'image';

                    // Preview
                    const reader = new FileReader();
                    reader.onload = e => {
                        previewImg.src = e.target.result;
                        previewWrapper.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);

                    fileName.textContent = file.name;
                    fileName.classList.remove('hidden');
                }

                cameraInput.addEventListener('change', () => {
                    if (cameraInput.files[0]) handleFile(cameraInput.files[0], cameraInput, galleryInput);
                });

                galleryInput.addEventListener('change', () => {
                    if (galleryInput.files[0]) handleFile(galleryInput.files[0], galleryInput, cameraInput);
                });
            </script>
        </div>
    </div>
</x-app-layout>
