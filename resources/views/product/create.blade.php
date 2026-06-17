<x-app-layout>
    <x-slot name="header">Product toevoegen</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white shadow-sm rounded-xl p-6">
            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Naam</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Afbeelding</label>

                    <input type="file" name="image" id="image-input" accept="image/*" class="hidden" onchange="handleImagePreview(this)">

                    <label for="image-input" id="image-dropzone"
                        class="flex flex-col items-center justify-center gap-1.5 border-2 border-dashed border-gray-300 rounded-lg px-4 py-6 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 7.5L12 3m0 0L7.5 7.5M12 3v13.5" />
                        </svg>
                        <p class="text-sm text-gray-500">
                            <span class="font-medium text-blue-600">Klik om te uploaden</span> of sleep een afbeelding hierheen
                        </p>
                        <p class="text-xs text-gray-400">PNG of JPG tot 5MB</p>
                    </label>

                    <div id="image-preview-wrapper" class="hidden mt-2 flex items-center gap-3 border border-gray-200 rounded-lg p-2">
                        <img id="image-preview" src="" alt="Preview" class="w-14 h-14 object-contain rounded-lg bg-gray-50 border border-gray-100">
                        <div class="flex-1 min-w-0">
                            <p id="image-filename" class="text-sm font-medium text-gray-700 truncate"></p>
                            <p id="image-filesize" class="text-xs text-gray-400"></p>
                        </div>
                        <button type="button" onclick="removeImage()" class="text-gray-400 hover:text-red-500 transition p-1 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categorie</label>
                    <select name="product_category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Kies een categorie --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Opslaan
                    </button>
                    <a href="{{ route('product.index') }}" class="text-sm text-gray-500 hover:underline px-3 py-2">Annuleren</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const imageInput = document.getElementById('image-input');
        const dropzone = document.getElementById('image-dropzone');
        const previewWrapper = document.getElementById('image-preview-wrapper');
        const previewImg = document.getElementById('image-preview');
        const filenameEl = document.getElementById('image-filename');
        const filesizeEl = document.getElementById('image-filesize');

        function handleImagePreview(input) {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                filenameEl.textContent = file.name;
                filesizeEl.textContent = (file.size / 1024).toFixed(0) + ' KB';
                dropzone.classList.add('hidden');
                previewWrapper.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function removeImage() {
            imageInput.value = '';
            previewWrapper.classList.add('hidden');
            dropzone.classList.remove('hidden');
        }

        ['dragover', 'dragleave', 'drop'].forEach(evt => {
            dropzone.addEventListener(evt, e => e.preventDefault());
        });

        dropzone.addEventListener('dragover', () => {
            dropzone.classList.add('border-blue-400', 'bg-blue-50/40');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-blue-400', 'bg-blue-50/40');
        });

        dropzone.addEventListener('drop', e => {
            dropzone.classList.remove('border-blue-400', 'bg-blue-50/40');
            const file = e.dataTransfer.files[0];
            if (file) {
                imageInput.files = e.dataTransfer.files;
                handleImagePreview(imageInput);
            }
        });
    </script>
</x-app-layout>