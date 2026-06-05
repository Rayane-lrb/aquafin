<x-magazijnbeheerder-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nieuw product toevoegen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    
                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    
                    <form method="POST" action="{{ route('product.store') }}">
                        @csrf

                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Productnaam <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Naam van het product">
                        </div>

                       
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Productcode <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="productCode" value="{{ old('productCode') }}"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Bv. PRD-001">
                        </div>

                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Voorraad <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stock" value="{{ old('stock') }}"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Aantal" min="0">
                        </div>

                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Categorie <span class="text-red-500">*</span>
                            </label>
                            <select name="product_category_id"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Kies een categorie --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        
                        <div class="flex items-center gap-4">
                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                                Product toevoegen
                            </button>
                            <a href="{{ route('product.index') }}"
                                class="text-gray-600 hover:underline">
                                Annuleren
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-magazijnbeheerder-layout>